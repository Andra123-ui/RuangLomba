<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Organizers extends BaseController
{
    private const STATUSES = ['pending', 'verified', 'rejected', 'unverified'];

    public function index()
    {
        $status = $this->request->getGet('status');
        if (! in_array($status, self::STATUSES, true)) {
            $status = 'pending';
        }

        $db = db_connect();

        $rows = $db->table('organizer_profiles op')
            ->select('op.*, u.name, u.email, u.avatar, u.created_at AS registered_at,
                     (SELECT COUNT(*) FROM competitions WHERE organizer_id = u.id) AS total_competitions')
            ->join('users u', 'u.id = op.user_id')
            ->where('op.verification_status', $status)
            ->orderBy('op.id', $status === 'pending' ? 'ASC' : 'DESC')
            ->get()->getResultArray();

        $counts = array_fill_keys(self::STATUSES, 0);
        $grouped = $db->table('organizer_profiles')
            ->select('verification_status, COUNT(*) AS total')
            ->groupBy('verification_status')
            ->get()->getResultArray();
        foreach ($grouped as $g) {
            $counts[$g['verification_status']] = (int) $g['total'];
        }

        return view('admin/organizers/index', [
            'title'      => 'Verifikasi Instansi Penyelenggara',
            'organizers' => $rows,
            'status'     => $status,
            'counts'     => $counts,
        ]);
    }

    public function show(int $id)
    {
        $organizer = $this->findOrFail($id);

        return view('admin/organizers/show', [
            'title'     => 'Tinjau Verifikasi - ' . $organizer['name'],
            'organizer' => $organizer,
        ]);
    }

    public function verify(int $id)
    {
        $organizer = $this->findOrFail($id);
        $db = db_connect();

        $db->transStart();

        $db->table('organizer_profiles')->where('id', $id)->update([
            'verification_status' => 'verified',
            'verified_at'         => date('Y-m-d H:i:s'),
            'rejection_reason'    => null,
        ]);

        $db->table('notifications')->insert([
            'user_id' => (int) $organizer['user_id'],
            'title'   => 'Selamat! Instansi Terverifikasi Resmi',
            'message' => 'Pengajuan verifikasi instansi "' . $organizer['name'] . '" telah disetujui. Lencana Verified (Centang Biru) kini aktif!',
            'type'    => 'organizer_verification',
        ]);

        $db->transComplete();

        return redirect()->to(base_url('admin/organizer?status=verified'))
            ->with('success', 'Instansi "' . $organizer['name'] . '" berhasil diverifikasi resmi!');
    }

    public function reject(int $id)
    {
        $organizer = $this->findOrFail($id);
        $reason = trim((string) $this->request->getPost('reason'));

        if ($reason === '') {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $db = db_connect();
        $db->transStart();

        $db->table('organizer_profiles')->where('id', $id)->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $reason,
        ]);

        $db->table('notifications')->insert([
            'user_id' => (int) $organizer['user_id'],
            'title'   => 'Verifikasi Instansi Ditolak',
            'message' => 'Pengajuan verifikasi instansi "' . $organizer['name'] . '" ditolak. Alasan: ' . $reason,
            'type'    => 'organizer_verification',
        ]);

        $db->transComplete();

        return redirect()->to(base_url('admin/organizer?status=rejected'))
            ->with('success', 'Pengajuan verifikasi ditolak dengan catatan.');
    }

    private function findOrFail(int $id): array
    {
        $row = db_connect()->table('organizer_profiles op')
            ->select('op.*, u.name, u.email, u.avatar, u.created_at AS registered_at')
            ->join('users u', 'u.id = op.user_id')
            ->where('op.id', $id)
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }
}
