<?php

namespace App\Controllers;

class Leaderboard extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $currentUserId = (int) session()->get('userId');

        // Ambil total poin dan jumlah hasil podium yang tercatat untuk tiap peserta.
        $rows = $db->table('users u')
            ->select('u.id, u.name, u.avatar, u.points, u.created_at,
                     COUNT(DISTINCT pa.registration_id) AS podium_finishes,
                     up.preferred_level', false)
            ->join('registration_point_awards pa', "pa.user_id = u.id AND pa.award_type = 'placement'", 'left')
            ->join('user_preferences up', 'up.user_id = u.id', 'left')
            ->where('u.role', 'participant')
            ->groupBy('u.id')
            ->orderBy('u.points', 'DESC')
            ->orderBy('podium_finishes', 'DESC')
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

            if ($rank <= 3 && (int) $row['points'] > 0) {
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
