<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Notifications extends BaseController
{
    // ------------------------------------------------------------ DAFTAR

    public function index()
    {
        $db     = db_connect();
        $userId = $this->userId();

        $rows = $db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $unread = $db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return view('notifications/index', [
            'title'         => 'Notifikasi',
            'notifications' => $rows,
            'unread'        => $unread,
        ]);
    }

    // ----------------------------------- BACA SATU (lalu menuju halaman terkait)

    public function read(int $id)
    {
        $db = db_connect();

        // Hanya notifikasi milik pengguna yang login
        $notif = $db->table('notifications')
            ->where('id', $id)
            ->where('user_id', $this->userId())
            ->get()->getRowArray();

        if (! $notif) {
            throw PageNotFoundException::forPageNotFound();
        }

        $db->table('notifications')->where('id', $id)->update(['is_read' => 1]);

        return redirect()->to($this->targetFor($notif) ?? base_url('notifikasi'));
    }

    // ------------------------------------------------------- BACA SEMUA

    public function readAll()
    {
        db_connect()->table('notifications')
            ->where('user_id', $this->userId())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return redirect()->to(base_url('notifikasi'))
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    // ------------------------------------------------------------- HELPERS

    private function userId(): int
    {
        return (int) session()->get('userId');
    }

    /** Halaman tujuan saat notifikasi diklik, menyesuaikan jenis notifikasi dan peran */
    private function targetFor(array $n): ?string
    {
        $role = (string) session()->get('userRole');

        switch ($n['type']) {
            case 'registration':
                if ($role === 'organizer') {
                    return base_url('organizer/lomba');
                }
                return $role === 'participant' ? base_url('pendaftaran-saya') : null;

            case 'competition':
                return $role === 'organizer' ? base_url('organizer/lomba') : null;

            case 'deadline':
            case 'recommendation':
                return base_url('cari');
        }

        return null;
    }
}