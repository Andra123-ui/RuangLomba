<?php

namespace App\Controllers;

class Leaderboard extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $currentUserId = (int) session()->get('userId');

        // Ambil peserta, poin gamifikasi, dan jumlah lomba yang berhasil diverifikasi
        $rows = $db->table('users u')
            ->select('u.id, u.name, u.avatar, u.points, u.created_at, 
                     COUNT(DISTINCT r.id) AS verified_competitions,
                     up.preferred_level', false)
            ->join('registrations r', "r.user_id = u.id AND r.status = 'verified'", 'left')
            ->join('user_preferences up', 'up.user_id = u.id', 'left')
            ->where('u.role', 'participant')
            ->groupBy('u.id')
            ->orderBy('u.points', 'DESC')
            ->orderBy('verified_competitions', 'DESC')
            ->orderBy('u.id', 'ASC')
            ->get()
            ->getResultArray();

        $topThree = [];
        $rankings = [];
        $myRank   = null;

        foreach ($rows as $index => $row) {
            $rank = $index + 1;
            $row['rank'] = $rank;

            if ((int) $row['id'] === $currentUserId) {
                $myRank = $row;
            }

            if ($rank <= 3) {
                $topThree[] = $row;
            }
            $rankings[] = $row;
        }

        return view('leaderboard', [
            'title'      => 'Leaderboard & Prestasi Peserta',
            'topThree'   => $topThree,
            'rankings'   => $rankings,
            'myRank'     => $myRank,
            'totalUsers' => count($rows),
        ]);
    }
}
