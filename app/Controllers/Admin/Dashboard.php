<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $competitionCounts = array_fill_keys(['pending', 'approved', 'rejected', 'expired'], 0);
        $competitionRows = $db->table('competitions')
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()->getResultArray();
        foreach ($competitionRows as $row) {
            $competitionCounts[$row['status']] = (int) $row['total'];
        }

        $organizerCounts = array_fill_keys(['pending', 'verified', 'rejected', 'unverified'], 0);
        $organizerRows = $db->table('organizer_profiles')
            ->select('verification_status, COUNT(*) AS total')
            ->groupBy('verification_status')
            ->get()->getResultArray();
        foreach ($organizerRows as $row) {
            $organizerCounts[$row['verification_status']] = (int) $row['total'];
        }

        $pendingOrganizers = $db->table('organizer_profiles op')
            ->select('op.id, op.org_type, op.created_at, op.verification_document, u.name, u.email')
            ->join('users u', 'u.id = op.user_id')
            ->where('op.verification_status', 'pending')
            ->orderBy('op.created_at', 'ASC')
            ->limit(5)
            ->get()->getResultArray();

        $pendingCompetitions = $db->table('competitions c')
            ->select('c.id, c.title, c.created_at, c.deadline, u.name AS organizer_name')
            ->join('users u', 'u.id = c.organizer_id')
            ->where('c.status', 'pending')
            ->orderBy('c.created_at', 'ASC')
            ->limit(5)
            ->get()->getResultArray();

        return view('admin/dashboard', [
            'title'                => 'Dashboard Admin',
            'competitionCounts'    => $competitionCounts,
            'organizerCounts'      => $organizerCounts,
            'pendingOrganizers'    => $pendingOrganizers,
            'pendingCompetitions'  => $pendingCompetitions,
        ]);
    }
}
