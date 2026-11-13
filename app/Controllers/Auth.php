<?php

namespace App\Controllers;

use App\Libraries\OtpService;
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

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // Email belum diverifikasi -> kirim OTP, arahkan ke halaman verifikasi
        if (empty($user['email_verified_at'])) {
            $sent = $this->sendOtp($user['email'], $user['name'], 'verify');
            session()->set('otpEmail', $user['email']);

            return redirect()->to(base_url('verifikasi'))
                ->with($sent ? 'success' : 'error', $sent
                    ? 'Email kamu belum diverifikasi. Kami sudah mengirim kode ke emailmu.'
                    : 'Email belum diverifikasi, tapi kode gagal dikirim. Coba kirim ulang.');
        }

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

        $name  = trim($this->request->getPost('name'));
        $email = strtolower(trim($this->request->getPost('email')));

        $db = db_connect();
        $db->transStart();

        $userModel = new UserModel();
        $userModel->insert([
            'name'          => $name,
            'email'         => $email,
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

        // Kirim OTP verifikasi, login setelah email terverifikasi
        $sent = $this->sendOtp($email, $name, 'verify');
        session()->set('otpEmail', $email);

        return redirect()->to(base_url('verifikasi'))
            ->with($sent ? 'success' : 'error', $sent
                ? 'Akun dibuat! Kami sudah mengirim kode verifikasi ke emailmu.'
                : 'Akun dibuat, tapi kode gagal dikirim. Tekan "Kirim ulang kode".');
    }

    // ------------------------------------------------------- VERIFIKASI EMAIL

    public function verify()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        $email = (string) session()->get('otpEmail');
        if ($email === '') {
            return redirect()->to(base_url('masuk'));
        }

        return view('auth/verify', [
            'title' => 'Verifikasi Email',
            'email' => $this->maskEmail($email),
            'wait'  => (new OtpService())->cooldownLeft($email, 'verify'),
        ]);
    }

    public function attemptVerify()
    {
        $email = (string) session()->get('otpEmail');
        if ($email === '') {
            return redirect()->to(base_url('masuk'));
        }

        if (service('throttler')->check(md5($this->request->getIPAddress() . '-verify'), 10, MINUTE) === false) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba lagi sebentar.');
        }

        $code = preg_replace('/\D/', '', (string) $this->request->getPost('otp'));
        if (strlen($code) !== 6) {
            return redirect()->back()->with('error', 'Masukkan 6 digit kode.');
        }

        $result = (new OtpService())->verify($email, 'verify', $code);
        if (! $result['ok']) {
            return redirect()->back()->with('error', $result['message']);
        }

        db_connect()->table('users')->where('email', $email)
            ->update(['email_verified_at' => date('Y-m-d H:i:s')]);

        $user = (new UserModel())->where('email', $email)->first();
        session()->remove('otpEmail');

        if (! $user) {
            return redirect()->to(base_url('masuk'));
        }

        $next = $this->afterAuthUrl($user['role']);
        $this->startSession($user);

        return redirect()->to($next)
            ->with('success', 'Email terverifikasi. Selamat datang di RuangLomba!');
    }

    public function resendVerify()
    {
        $email = (string) session()->get('otpEmail');
        if ($email === '') {
            return redirect()->to(base_url('masuk'));
        }

        $user = (new UserModel())->where('email', $email)->first();
        if ($user && empty($user['email_verified_at'])) {
            $sent = $this->sendOtp($email, $user['name'], 'verify');

            return redirect()->to(base_url('verifikasi'))
                ->with($sent ? 'success' : 'error', $sent
                    ? 'Kode baru sudah dikirim.'
                    : 'Kode belum bisa dikirim. Tunggu sebentar lalu coba lagi.');
        }

        return redirect()->to(base_url('masuk'));
    }

    // --------------------------------------------------------- LUPA PASSWORD

    public function forgot()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        return view('auth/forgot', ['title' => 'Lupa Password']);
    }

    public function attemptForgot()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        if (service('throttler')->check(md5($this->request->getIPAddress() . '-forgot'), 5, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan. Coba lagi sebentar.');
        }

        if (! $this->validate(['email' => 'required|valid_email|max_length[150]'])) {
            return redirect()->back()->withInput()->with('error', 'Masukkan email yang valid.');
        }

        $email = strtolower(trim($this->request->getPost('email')));
        $user  = (new UserModel())->where('email', $email)->first();

        // Reset status verifikasi OTP dari alur sebelumnya
        session()->remove('resetOtpVerified');

        // Respons sama baik email terdaftar maupun tidak (anti enumerasi)
        if ($user) {
            $this->sendOtp($email, $user['name'], 'reset');
        }

        session()->set('resetEmail', $email);

        return redirect()->to(base_url('reset-password'))
            ->with('success', 'Jika email terdaftar, kode reset sudah dikirim.');
    }

    public function reset()
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        $email = (string) session()->get('resetEmail');
        if ($email === '') {
            return redirect()->to(base_url('lupa-password'));
        }

        return view('auth/reset', [
            'title'    => 'Reset Password',
            'email'    => $this->maskEmail($email),
            'wait'     => (new OtpService())->cooldownLeft($email, 'reset'),
            'verified' => session()->get('resetOtpVerified') === $email,
        ]);
    }

    // Verifikasi OTP — dipanggil via AJAX, balas JSON
    public function attemptResetOtp()
    {
        $email = (string) session()->get('resetEmail');

        if ($email === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'      => false,
                'message' => 'Sesi habis. Ulangi dari halaman lupa password.',
            ]);
        }

        if (service('throttler')->check(md5($this->request->getIPAddress() . '-reset'), 10, MINUTE) === false) {
            return $this->response->setStatusCode(429)->setJSON([
                'ok'      => false,
                'message' => 'Terlalu banyak percobaan. Coba lagi sebentar.',
            ]);
        }

        $otp = preg_replace('/\D/', '', (string) $this->request->getPost('otp'));
        if (strlen($otp) !== 6) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => 'Masukkan 6 digit kode.',
            ]);
        }

        $result = (new OtpService())->verify($email, 'reset', $otp);
        if (! $result['ok']) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'message' => $result['message'],
            ]);
        }

        session()->set('resetOtpVerified', $email);

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'Kode benar. Silakan buat password baru.',
        ]);
    }

    // Simpan password — hanya boleh kalau OTP sudah lolos di sesi ini
    public function attemptResetPassword()
    {
        $email = (string) session()->get('resetEmail');
        if ($email === '' || session()->get('resetOtpVerified') !== $email) {
            return redirect()->to(base_url('reset-password'))
                ->with('error', 'Verifikasi kode dulu sebelum mengganti password.');
        }

        if (service('throttler')->check(md5($this->request->getIPAddress() . '-reset-pw'), 10, MINUTE) === false) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba lagi sebentar.');
        }

        $rules = [
            'password'         => 'required|min_length[8]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];
        $messages = [
            'password'         => ['required' => 'Password baru wajib diisi.', 'min_length' => 'Password minimal 8 karakter.'],
            'password_confirm' => ['required' => 'Konfirmasi password wajib diisi.', 'matches' => 'Konfirmasi password tidak sama.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        db_connect()->table('users')->where('email', $email)->update([
            'password_hash'     => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        session()->remove(['resetEmail', 'resetOtpVerified']);

        return redirect()->to(base_url('masuk'))
            ->with('success', 'Password berhasil diubah. Silakan masuk.');
    }

    public function resendReset()
    {
        $email = (string) session()->get('resetEmail');
        if ($email === '') {
            return redirect()->to(base_url('lupa-password'));
        }

        // OTP baru = verifikasi ulang
        session()->remove('resetOtpVerified');

        $user = (new UserModel())->where('email', $email)->first();
        if ($user) {
            $this->sendOtp($email, $user['name'], 'reset');
        }

        return redirect()->to(base_url('reset-password'))
            ->with('success', 'Jika email terdaftar, kode baru sudah dikirim.');
    }

    // --------------------------------------------------------------- LOGOUT

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('/'))->with('success', 'Kamu sudah keluar.');
    }

    // -------------------------------------------------------------- HELPERS

    private function sendOtp(string $email, string $name, string $purpose): bool
    {
        $otp    = new OtpService();
        $result = $otp->issue($email, $purpose);

        // Masih cooldown: kode sebelumnya masih berlaku, anggap berhasil
        if (! $result['ok']) {
            return true;
        }

        return $otp->sendMail($email, $name, $result['code'], $purpose);
    }

    private function maskEmail(string $email): string
    {
        [$user, $domain] = explode('@', $email) + ['', ''];
        $visible = mb_substr($user, 0, min(2, max(1, mb_strlen($user) - 1)));

        return $visible . str_repeat('•', max(2, mb_strlen($user) - mb_strlen($visible))) . '@' . $domain;
    }

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