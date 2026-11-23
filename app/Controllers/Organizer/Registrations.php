<?php

namespace App\Controllers\Organizer;

use App\Controllers\BaseController;
use App\Libraries\LeaderboardScoring;
use CodeIgniter\Exceptions\PageNotFoundException;

class Registrations extends BaseController
{
    private const STATUSES = ['pending', 'verified', 'rejected'];

    // ---------------------------------------------------------------- READ

    public function index(int $competitionId)
    {
        $competition = $this->findOwnedCompetition($competitionId);

        $status = $this->request->getGet('status');
        if (! in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $db = db_connect();

        $builder = $db->table('registrations r')
            ->select('r.*, u.name, u.email, t.name AS team_name')
            ->join('users u', 'u.id = r.user_id')
            ->join('teams t', 't.id = r.team_id', 'left')
            ->where('r.competition_id', $competitionId)
            ->orderBy("r.status = 'pending'", 'DESC', false) // yang perlu ditindak di atas
            ->orderBy('r.registered_at', 'DESC');

        if ($status !== '') {
            $builder->where('r.status', $status);
        }

        $counts = array_fill_keys(self::STATUSES, 0);
        $grouped = $db->table('registrations')
            ->select('status, COUNT(*) AS total')
            ->where('competition_id', $competitionId)
            ->groupBy('status')
            ->get()->getResultArray();
        foreach ($grouped as $g) {
            $counts[$g['status']] = (int) $g['total'];
        }

        return view('organizer/registrations/index', [
            'title'         => 'Pendaftar',
            'competition'   => $competition,
            'registrations' => $builder->get()->getResultArray(),
            'status'        => $status,
            'counts'        => $counts,
        ]);
    }

    public function printList(int $competitionId)
    {
        $competition = $this->findOwnedCompetition($competitionId);
        $db = db_connect();

        $rows = $db->table('registrations r')
            ->select('r.*, u.name, u.email, t.name AS team_name')
            ->join('users u', 'u.id = r.user_id')
            ->join('teams t', 't.id = r.team_id', 'left')
            ->where('r.competition_id', $competitionId)
            ->orderBy('r.registered_at', 'ASC')
            ->get()->getResultArray();

        return view('organizer/registrations/print_list', [
            'title'         => 'Rekap Pendaftar - ' . $competition['title'],
            'competition'   => $competition,
            'registrations' => $rows,
        ]);
    }

    // ------------------------------------------------------------- ACTIONS

    public function verify(int $id)
    {
        $reg  = $this->findOwnedRegistration($id);
        $back = base_url('organizer/lomba/' . $reg['competition_id'] . '/pendaftar');

        if ($reg['status'] === 'verified') {
            return redirect()->to($back)->with('error', 'Pendaftar ini sudah terverifikasi.');
        }

        if (! $this->changeStatus(
            $reg,
            [
                'status'        => 'verified',
                'verified_at'   => date('Y-m-d H:i:s'),
                'reject_reason' => null,
            ],
            'Pendaftaran diverifikasi',
            'Pendaftaranmu pada ' . $reg['competition_title'] . ' telah diverifikasi.'
        )) {
            return redirect()->to($back)->with('error', 'Pendaftaran gagal diverifikasi. Silakan coba lagi.');
        }

        return redirect()->to($back)->with('success', 'Pendaftar berhasil diverifikasi.');
    }

    public function reject(int $id)
    {
        $reg  = $this->findOwnedRegistration($id);
        $back = base_url('organizer/lomba/' . $reg['competition_id'] . '/pendaftar');

        if ($reg['status'] === 'rejected') {
            return redirect()->to($back)->with('error', 'Pendaftar ini sudah ditolak.');
        }

        $reason = trim((string) $this->request->getPost('reason'));
        if (mb_strlen($reason) < 3 || mb_strlen($reason) > 255) {
            return redirect()->to($back)
                ->with('error', 'Alasan penolakan wajib diisi (3 sampai 255 karakter).');
        }

        $fields = [
            'status'        => 'rejected',
            'verified_at'   => null,
            'reject_reason' => $reason,
        ];
        if ($reg['competition_type'] === 'Lomba') {
            $fields['placement'] = null;
        }

        if (! $this->changeStatus(
            $reg,
            $fields,
            'Pendaftaran ditolak',
            'Pendaftaranmu pada ' . $reg['competition_title'] . ' ditolak. Alasan: ' . $reason
        )) {
            return redirect()->to($back)->with('error', 'Pendaftaran gagal ditolak. Silakan coba lagi.');
        }

        return redirect()->to($back)->with('success', 'Pendaftar ditolak.');
    }

    public function setPlacement(int $id)
    {
        $reg  = $this->findOwnedRegistration($id);
        $back = base_url('organizer/lomba/' . $reg['competition_id'] . '/pendaftar');

        if ($reg['competition_type'] !== 'Lomba') {
            return redirect()->to($back)->with('error', 'Hasil juara hanya dapat dicatat untuk lomba.');
        }

        if ($reg['status'] !== 'verified') {
            return redirect()->to($back)->with('error', 'Verifikasi pendaftaran sebelum mencatat hasil lomba.');
        }

        $rawPlacement = $this->request->getPost('placement');
        if (! is_string($rawPlacement) || ! in_array($rawPlacement, ['', '1', '2', '3'], true)) {
            return redirect()->to($back)->with('error', 'Pilih juara 1, 2, 3, atau bukan juara.');
        }

        $placement = $rawPlacement === '' ? null : (int) $rawPlacement;
        $db = db_connect();
        $db->transStart();

        $this->syncPlacementAwards($db, $reg, $placement);
        $db->table('registrations')
            ->where('id', $id)
            ->update(['placement' => $placement]);

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->to($back)->with('error', 'Hasil lomba gagal disimpan. Silakan coba lagi.');
        }

        $message = $placement === null
            ? 'Hasil juara berhasil dihapus.'
            : 'Hasil juara berhasil disimpan dan poin prestasi diperbarui.';

        return redirect()->to($back)->with('success', $message);
    }

    // ------------------------------------------------------------- HELPERS

    private function userId(): int
    {
        return (int) session()->get('userId');
    }

    /** Peluang milik penyelenggara yang login, selain itu 404 */
    private function findOwnedCompetition(int $id): array
    {
        $row = db_connect()->table('competitions')
            ->where('id', $id)
            ->where('organizer_id', $this->userId())
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Pendaftaran pada peluang milik penyelenggara yang login, selain itu 404 */
    private function findOwnedRegistration(int $id): array
    {
        $row = db_connect()->table('registrations r')
            ->select('r.*, c.title AS competition_title, c.type AS competition_type')
            ->join('competitions c', 'c.id = r.competition_id')
            ->where('r.id', $id)
            ->where('c.organizer_id', $this->userId())
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Ubah status dan kirim notifikasi ke peserta dalam satu transaksi */
    private function changeStatus(array $reg, array $fields, string $title, string $message): bool
    {
        $db = db_connect();
        $db->transStart();

        if ($reg['competition_type'] === 'Lomba' && array_key_exists('placement', $fields)) {
            $placement = $fields['placement'] === null ? null : (int) $fields['placement'];
            $this->syncPlacementAwards($db, $reg, $placement);
        }

        $db->table('registrations')->where('id', $reg['id'])->update($fields);

        if (
            ($fields['status'] ?? '') === 'verified'
            && $reg['competition_type'] !== 'Lomba'
            && ! $this->hasParticipationAward($db, (int) $reg['id'])
        ) {
            $points = LeaderboardScoring::NON_COMPETITION_VERIFIED_POINTS;
            $db->query('UPDATE `users` SET `points` = `points` + ? WHERE `id` = ?', [
                $points,
                (int) $reg['user_id'],
            ]);
            $db->table('registration_point_awards')->insert([
                'registration_id' => (int) $reg['id'],
                'user_id'         => (int) $reg['user_id'],
                'award_type'      => 'participation',
                'points'          => $points,
            ]);
        }

        $db->table('notifications')->insert([
            'user_id' => (int) $reg['user_id'],
            'title'   => $title,
            'message' => $message,
            'type'    => 'registration',
        ]);

        $db->transComplete();
        return $db->transStatus();
    }

    private function hasParticipationAward($db, int $registrationId): bool
    {
        return $db->table('registration_point_awards')
            ->where('registration_id', $registrationId)
            ->where('award_type', 'participation')
            ->countAllResults() > 0;
    }

    private function syncPlacementAwards($db, array $reg, ?int $placement): void
    {
        $awardQuery = $db->table('registration_point_awards')
            ->where('registration_id', (int) $reg['id'])
            ->where('award_type', 'placement');
        $existingAwards = $awardQuery->get()->getResultArray();

        foreach ($existingAwards as $award) {
            $db->query('UPDATE `users` SET `points` = GREATEST(`points` - ?, 0) WHERE `id` = ?', [
                (int) $award['points'],
                (int) $award['user_id'],
            ]);
        }

        $db->table('registration_point_awards')
            ->where('registration_id', (int) $reg['id'])
            ->where('award_type', 'placement')
            ->delete();

        $points = LeaderboardScoring::pointsForPlacement($placement);
        if ($points === 0) {
            return;
        }

        $recipientIds = [(int) $reg['user_id']];
        if (! empty($reg['team_id'])) {
            $members = $db->table('team_members')
                ->select('user_id')
                ->where('team_id', (int) $reg['team_id'])
                ->where('status', 'accepted')
                ->get()
                ->getResultArray();

            foreach ($members as $member) {
                $recipientIds[] = (int) $member['user_id'];
            }
        }

        foreach (array_unique($recipientIds) as $userId) {
            $db->query('UPDATE `users` SET `points` = `points` + ? WHERE `id` = ?', [$points, $userId]);
            $db->table('registration_point_awards')->insert([
                'registration_id' => (int) $reg['id'],
                'user_id'         => $userId,
                'award_type'      => 'placement',
                'points'          => $points,
            ]);
        }
    }
}