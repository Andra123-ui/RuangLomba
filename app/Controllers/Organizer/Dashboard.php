<?php

namespace App\Controllers\Organizer;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db     = db_connect();
        $userId = (int) session()->get('userId');

        $competitions = $db->table('competitions')
            ->select('status, views, likes')
            ->where('organizer_id', $userId)
            ->get()->getResultArray();

        // Ringkasan status + total dilihat & suka dari semua peluang milik penyelenggara
        $summary = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'expired' => 0];
        $views   = 0;
        $likes   = 0;

        foreach ($competitions as $c) {
            if (isset($summary[$c['status']])) {
                $summary[$c['status']]++;
            }
            $views += (int) $c['views'];
            $likes += (int) $c['likes'];
        }

        // Pendaftar: hanya peluang yang memakai pendaftaran RuangLomba,
        // karena hanya itu yang bisa diverifikasi dari halaman Pendaftar
        $reg = $db->table('registrations r')
            ->select("COUNT(*) AS total,
                      COALESCE(SUM(r.status = 'pending'), 0) AS pending,
                      COALESCE(SUM(r.status = 'verified'), 0) AS verified", false)
            ->join('competitions c', 'c.id = r.competition_id')
            ->where('c.organizer_id', $userId)
            ->where('c.registration_mode', 'internal')
            ->get()->getRowArray();

        return view('organizer/dashboard', [
            'title'   => 'Dashboard Penyelenggara',
            'summary' => $summary,
            'stats'   => [
                'registrants' => (int) ($reg['total'] ?? 0),
                'pending'     => (int) ($reg['pending'] ?? 0),
                'verified'    => (int) ($reg['verified'] ?? 0),
                'views'       => $views,
                'likes'       => $likes,
            ],
        ]);
    }
}