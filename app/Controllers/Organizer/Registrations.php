<?php

namespace App\Controllers\Organizer;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Registrations extends BaseController
{
    private const STATUSES = ['pending', 'verified', 'rejected'];

    // ---------------------------------------------------------------- READ

    public function index(int $competitionId)
    {
        $competition = $this->findOwnedCompetition($competitionId);

        if (($competition['registration_mode'] ?? 'external') !== 'internal') {
            return redirect()->to(base_url('organizer/lomba'))
                ->with('error', 'Peluang ini memakai link pendaftaran eksternal, jadi tidak ada daftar pendaftar di RuangLomba.');
        }

        $status = $this->request->getGet('status');
        if (! in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $db = db_connect();

        $builder = $db->table('registrations r')
            ->select('r.*, u.name, u.email')
            ->join('users u', 'u.id = r.user_id')
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

    // ------------------------------------------------------------- ACTIONS

    public function verify(int $id)
    {
        $reg  = $this->findOwnedRegistration($id);
        $back = base_url('organizer/lomba/' . $reg['competition_id'] . '/pendaftar');

        if ($reg['status'] === 'verified') {
            return redirect()->to($back)->with('error', 'Pendaftar ini sudah terverifikasi.');
        }

        $this->changeStatus(
            $reg,
            [
                'status'        => 'verified',
                'verified_at'   => date('Y-m-d H:i:s'),
                'reject_reason' => null,
            ],
            'Pendaftaran diverifikasi',
            'Pendaftaranmu pada ' . $reg['competition_title'] . ' telah diverifikasi.'
        );

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

        $this->changeStatus(
            $reg,
            [
                'status'        => 'rejected',
                'verified_at'   => null,
                'reject_reason' => $reason,
            ],
            'Pendaftaran ditolak',
            'Pendaftaranmu pada ' . $reg['competition_title'] . ' ditolak. Alasan: ' . $reason
        );

        return redirect()->to($back)->with('success', 'Pendaftar ditolak.');
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
            ->select('r.*, c.title AS competition_title')
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
    private function changeStatus(array $reg, array $fields, string $title, string $message): void
    {
        $db = db_connect();
        $db->transStart();

        $db->table('registrations')->where('id', $reg['id'])->update($fields);

        $db->table('notifications')->insert([
            'user_id' => (int) $reg['user_id'],
            'title'   => $title,
            'message' => $message,
            'type'    => 'registration',
        ]);

        $db->transComplete();
    }
}