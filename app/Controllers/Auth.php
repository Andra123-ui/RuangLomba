<?php

namespace App\Controllers;

use App\Models\OrganizerProfileModel;
use App\Models\UserModel;
use App\Models\UserPreferenceModel;

class Auth extends BaseController
{
    // ---------------------------------------------------------------- LOGIN

    public function login()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        return view('auth/login', ['title' => 'Masuk']);
    }

    public function attemptLogin()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        // Batasi percobaan: 5x per menit per IP
        $throttler = service('throttler');
        if ($throttler->check(md5($this->request->getIPAddress() . '-login'), 5, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan. Coba lagi dalam satu menit.');
        }

        $rules = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[72]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah.');
        }

        $email    = strtolower(trim($this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->where('email', $email)->first();

        // Pesan sengaja sama untuk email salah maupun password salah
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah.');
        }

        $this->startSession($user);

        $next = $this->afterAuthUrl($user['role']);
        $this->startSession($user);

        return redirect()->to($next)
            ->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    // ------------------------------------------------------------- REGISTER

    public function register()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        $role = $this->request->getGet('role') === 'organizer' ? 'organizer' : 'participant';

        return view('auth/register', [
            'title' => 'Daftar',
            'role'  => $role,
        ]);
    }

    public function attemptRegister()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        $role = $this->request->getPost('role') === 'organizer' ? 'organizer' : 'participant';

        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password'         => 'required|min_length[8]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];

        if ($role === 'participant') {
            $rules['education_level'] = 'required|in_list[SD,SMP,SMA/SMK/MA,Mahasiswa,Umum]';
        } else {
            $rules['org_type']    = 'required|in_list[Sekolah,Kampus,Komunitas,Perusahaan,Lainnya]';
            $rules['phone']       = 'required|regex_match[/^(\+62|62|0)8[0-9]{8,12}$/]';
            $rules['description'] = 'permit_empty|max_length[500]';
            $rules['link']        = 'permit_empty|valid_url_strict|max_length[255]';
        }

        $messages = [
            'name' => [
                'required'   => 'Nama wajib diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Email ini sudah terdaftar.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 8 karakter.',
            ],
            'password_confirm' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak sama.',
            ],
            'education_level' => ['required' => 'Pilih jenjang pendidikan.'],
            'org_type'        => ['required' => 'Pilih jenis penyelenggara.'],
            'phone' => [
                'required'    => 'Nomor WhatsApp wajib diisi.',
                'regex_match' => 'Nomor tidak valid. Contoh: 081234567890.',
            ],
            'link' => ['valid_url_strict' => 'Link harus diawali http:// atau https://.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transStart();

        $userModel = new UserModel();
        $userModel->insert([
            'name'          => trim($this->request->getPost('name')),
            'email'         => strtolower(trim($this->request->getPost('email'))),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $role,
        ]);
        $userId = $userModel->getInsertID();

        if ($role === 'participant') {
            (new UserPreferenceModel())->insert([
                'user_id'         => $userId,
                'preferred_level' => $this->request->getPost('education_level'),
            ]);
        } else {
            (new OrganizerProfileModel())->insert([
                'user_id'     => $userId,
                'org_type'    => $this->request->getPost('org_type'),
                'phone'       => $this->request->getPost('phone'),
                'description' => trim((string) $this->request->getPost('description')) ?: null,
                'link'        => trim((string) $this->request->getPost('link')) ?: null,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Pendaftaran gagal. Silakan coba lagi.');
        }

         // Langsung login setelah daftar
        $next = $this->afterAuthUrl($role);
        $this->startSession($userModel->find($userId));

        return redirect()->to($next)
            ->with('success', 'Akun berhasil dibuat. Selamat datang di RuangLomba!');
    }

    // --------------------------------------------------------------- LOGOUT

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('/'))->with('success', 'Kamu sudah keluar.');
    }

    // -------------------------------------------------------------- HELPERS

    private function startSession(array $user): void
    {
        $session = session();
        $session->regenerate(true); // cegah session fixation
        $session->set([
            'isLoggedIn' => true,
            'userId'     => (int) $user['id'],
            'userName'   => $user['name'],
            'userEmail'  => $user['email'],
            'userRole'   => $user['role'],
        ]);
    }

        private function homeFor(string $role): string
    {
        return match ($role) {
            'organizer' => base_url('organizer/dashboard'),
            'admin'     => base_url('admin/peluang'),
            default     => base_url('/'),
        };
    }

    /** Halaman tujuan sebelum login (kalau ada dan masih satu situs), selain itu beranda sesuai peran */
    private function afterAuthUrl(string $role): string
    {
        $intended = session()->get('intendedUrl');
        session()->remove('intendedUrl');

        if (is_string($intended) && str_starts_with($intended, base_url())) {
            return $intended;
        }

        return $this->homeFor($role);
    }

    private function redirectIfLoggedIn()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->homeFor((string) session()->get('userRole')));
        }

        return null;
    }
}