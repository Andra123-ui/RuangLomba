<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\TeamMemberModel;
use App\Models\TeamModel;
use App\Models\UserModel;

class Teams extends BaseController
{
    public function index(): string
    {
        $userId = (int) session()->get('userId');
        $db = \Config\Database::connect();

        // Ambil semua tim tempat user menjadi ketua atau anggota
        $teams = $db->table('teams t')
            ->select('t.*, u.name AS leader_name, u.avatar AS leader_avatar')
            ->join('users u', 'u.id = t.leader_id')
            ->join('team_members tm', 'tm.team_id = t.id')
            ->where('tm.user_id', $userId)
            ->groupBy('t.id')
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil anggota untuk setiap tim
        foreach ($teams as &$team) {
            $members = $db->table('team_members tm')
                ->select('tm.*, u.name, u.email, u.avatar')
                ->join('users u', 'u.id = tm.user_id')
                ->where('tm.team_id', (int) $team['id'])
                ->orderBy("tm.role = 'leader'", 'DESC')
                ->orderBy('tm.joined_at', 'ASC')
                ->get()
                ->getResultArray();

            $team['members']   = $members;
            $team['is_leader'] = ((int) $team['leader_id'] === $userId);
        }
        unset($team);

        return view('participant/teams', [
            'title' => 'Tim Saya',
            'teams' => $teams,
        ]);
    }

    public function create()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama tim minimal 3 karakter.');
        }

        $teamName = trim((string) $this->request->getPost('name'));

        // Generate kode bergabung unik (contoh: RL-9F2B8)
        $joinCode = 'RL-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $db = \Config\Database::connect();
        $db->transStart();

        $teamModel = new TeamModel();
        $teamId = $teamModel->insert([
            'leader_id' => $userId,
            'name'      => $teamName,
            'join_code' => $joinCode,
        ]);

        $memberModel = new TeamMemberModel();
        $memberModel->insert([
            'team_id'   => (int) $teamId,
            'user_id'   => $userId,
            'role'      => 'leader',
            'status'    => 'accepted',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal membuat tim. Silakan coba lagi.');
        }

        return redirect()->to(base_url('tim'))
            ->with('success', 'Tim "' . esc($teamName) . '" berhasil dibuat! Bagikan Kode Undangan: ' . $joinCode);
    }

    public function join()
    {
        $userId = (int) session()->get('userId');
        $code   = strtoupper(trim((string) $this->request->getPost('join_code')));

        if ($code === '') {
            return redirect()->back()->with('error', 'Masukkan kode bergabung tim.');
        }

        $teamModel = new TeamModel();
        $team = $teamModel->where('join_code', $code)->first();

        if (! $team) {
            return redirect()->back()->with('error', 'Kode tim tidak valid atau tidak ditemukan.');
        }

        $memberModel = new TeamMemberModel();
        $exists = $memberModel->where('team_id', $team['id'])->where('user_id', $userId)->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Kamu sudah bergabung dalam tim "' . esc($team['name']) . '".');
        }

        $memberModel->insert([
            'team_id'   => (int) $team['id'],
            'user_id'   => $userId,
            'role'      => 'member',
            'status'    => 'accepted',
        ]);

        return redirect()->to(base_url('tim'))
            ->with('success', 'Selamat, kamu berhasil bergabung dengan tim "' . esc($team['name']) . '"!');
    }

    public function leave(int $teamId)
    {
        $userId = (int) session()->get('userId');

        $teamModel = new TeamModel();
        $team = $teamModel->find($teamId);

        if (! $team) {
            return redirect()->to(base_url('tim'))->with('error', 'Tim tidak ditemukan.');
        }

        $memberModel = new TeamMemberModel();
        $membership = $memberModel->where('team_id', $teamId)->where('user_id', $userId)->first();

        if (! $membership) {
            return redirect()->to(base_url('tim'))->with('error', 'Kamu bukan anggota tim ini.');
        }

        if ((int) $team['leader_id'] === $userId) {
            // Jika ketua tim keluar, tim dibubarkan
            $teamModel->delete($teamId);
            $msg = 'Tim "' . esc($team['name']) . '" telah dibubarkan.';
        } else {
            // Anggota biasa keluar dari tim
            $memberModel->where('team_id', $teamId)->where('user_id', $userId)->delete();
            $msg = 'Kamu telah keluar dari tim "' . esc($team['name']) . '".';
        }

        return redirect()->to(base_url('tim'))->with('success', $msg);
    }
}
