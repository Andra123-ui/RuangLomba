<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Users extends BaseController
{
    public function index()
    {
        $db   = \Config\Database::connect();
        $q    = trim((string) $this->request->getGet('q'));
        $role = $this->request->getGet('role');

        $builder = $db->table('users')
            ->select('id, name, email, role, avatar, points, created_at')
            ->where('role !=', 'admin');

        if ($role && in_array($role, ['participant', 'organizer'], true)) {
            $builder->where('role', $role);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
                ->groupEnd();
        }

        $users = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('admin/users/index', [
            'title' => 'Manajemen Pengguna & Moderasi Nama',
            'users' => $users,
            'q'     => $q,
            'role'  => $role,
        ]);
    }

    public function resetName(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user || $user['role'] === 'admin') {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $defaultName = 'Peserta RuangLomba #' . $id;
        $userModel->update($id, ['name' => $defaultName]);

        $db = \Config\Database::connect();
        $db->table('notifications')->insert([
            'user_id' => $id,
            'title'   => 'Pemberitahuan Perubahan Nama Akun',
            'message' => 'Nama akun Anda telah direset oleh Admin menjadi "' . $defaultName . '" karena terindikasi tidak pantas/melanggar norma SARA. Silakan perbarui nama Anda dengan nama asli yang sopan di menu Profil.',
            'type'    => 'system',
        ]);

        return redirect()->to(base_url('admin/pengguna'))
            ->with('success', 'Nama akun #' . $id . ' berhasil direset menjadi "' . $defaultName . '".');
    }
}
