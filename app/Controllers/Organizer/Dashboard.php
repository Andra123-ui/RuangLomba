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
            ->select('id, title, type, status, deadline, views, likes')
            ->where('organizer_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $summary = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'expired' => 0];
        foreach ($competitions as $c) {
            $summary[$c['status']]++;
        }

        return view('organizer/dashboard', [
            'title'        => 'Dashboard Penyelenggara',
            'competitions' => $competitions,
            'summary'      => $summary,
        ]);
    }
}