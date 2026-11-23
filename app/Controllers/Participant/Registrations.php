<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Registrations extends BaseController
{
     public function index()
    {
        $rows = db_connect()->table('registrations r')
            ->select('r.id, r.status, r.reject_reason, r.registered_at, r.verified_at, r.placement,
                      c.id AS competition_id, c.title, c.type, c.poster, c.deadline, c.registration_mode,
                      u.name AS organizer_name')
            ->join('competitions c', 'c.id = r.competition_id')
            ->join('users u', 'u.id = c.organizer_id')
            ->where('r.user_id', $this->userId())
            ->orderBy('r.registered_at', 'DESC')
            ->get()->getResultArray();

        $summary = ['pending' => 0, 'verified' => 0, 'rejected' => 0];
        foreach ($rows as $r) {
            $summary[$r['status']]++;
        }

        return view('participant/my_registrations', [
            'title'         => 'Pendaftaran Saya',
            'registrations' => $rows,
            'summary'       => $summary,
        ]);
    }

    public function form(int $id)
    {
        $competition = $this->findInternal($id);

        if ($redirect = $this->guard($competition)) {
            return $redirect;
        }

        $user = db_connect()->table('users')
            ->select('name, email')
            ->where('id', $this->userId())
            ->get()->getRowArray();

        $teams = [];
        if (($competition['participation_type'] ?? 'Individu') === 'Tim') {
            $teams = db_connect()->table('teams')
                ->where('leader_id', $this->userId())
                ->get()->getResultArray();
        }

        return view('participant/register', [
            'title'       => 'Daftar - ' . $competition['title'],
            'competition' => $competition,
            'user'        => $user,
            'teams'       => $teams,
        ]);
    }

    public function store(int $id)
    {
        $competition = $this->findInternal($id);

        if ($redirect = $this->guard($competition)) {
            return $redirect;
        }

        $isTeam = (($competition['participation_type'] ?? 'Individu') === 'Tim');
        $isPaid = ((int) ($competition['registration_fee'] ?? 0) > 0);

        $rules = [
            'phone'       => 'required',
            'institution' => 'required|min_length[3]|max_length[150]',
            'consent'     => 'required',
        ];

        $messages = [
            'phone'       => ['required' => 'Nomor HP wajib diisi.'],
            'institution' => [
                'required'   => 'Sekolah/instansi wajib diisi.',
                'min_length' => 'Sekolah/instansi minimal 3 karakter.',
                'max_length' => 'Sekolah/instansi maksimal 150 karakter.',
            ],
            'consent'     => ['required' => 'Kamu harus menyetujui pembagian data ke penyelenggara.'],
        ];

        if ($isTeam) {
            $rules['team_id'] = 'required';
            $messages['team_id'] = ['required' => 'Pilih tim yang akan didaftarkan.'];
        }

        if ($isPaid) {
            $rules['payment_proof'] = 'uploaded[payment_proof]|is_image[payment_proof]|mime_in[payment_proof,image/jpg,image/jpeg,image/png,image/webp]|max_size[payment_proof,2048]';
            $messages['payment_proof'] = [
                'uploaded' => 'Bukti pembayaran wajib diunggah untuk lomba berbayar.',
                'is_image' => 'File bukti harus berupa gambar.',
                'mime_in'  => 'Format bukti pembayaran harus JPG, PNG, atau WebP.',
                'max_size' => 'Ukuran file bukti maksimal 2 MB.',
            ];
        }

        $errors = $this->validate($rules, $messages) ? [] : $this->validator->getErrors();

        // Normalisasi nomor HP: buang spasi dan tanda hubung
        $phone = preg_replace('/[\s\-]+/', '', (string) $this->request->getPost('phone'));
        if (! isset($errors['phone']) && ! preg_match('/^(\+62|62|0)8[0-9]{8,12}$/', $phone)) {
            $errors['phone'] = 'Nomor HP tidak valid. Contoh: 081234567890.';
        }

        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Simpan file bukti transfer jika ada
        $proofPath = null;
        if ($isPaid) {
            $file = $this->request->getFile('payment_proof');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $dir = FCPATH . 'assets/uploads/payments/';
                if (! is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $filename = $file->getRandomName();
                $file->move($dir, $filename);
                $proofPath = 'assets/uploads/payments/' . $filename;
            }
        }

        $teamId = $isTeam ? (int) $this->request->getPost('team_id') : null;

        $db = db_connect();
        $db->transStart();

        $db->table('registrations')->insert([
            'user_id'        => $this->userId(),
            'competition_id' => $id,
            'team_id'        => $teamId,
            'phone'          => $phone,
            'institution'    => trim((string) $this->request->getPost('institution')),
            'payment_proof'  => $proofPath,
            'status'         => 'pending',
        ]);

        // Notifikasi untuk penyelenggara
        $db->table('notifications')->insert([
            'user_id' => (int) $competition['organizer_id'],
            'title'   => 'Pendaftar baru',
            'message' => session()->get('userName') . ' mendaftar pada ' . $competition['title'] . '.',
            'type'    => 'registration',
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()
                ->with('errors', ['form' => 'Pendaftaran gagal disimpan. Coba lagi.']);
        }

        return redirect()->to(base_url('peluang/' . $id))
            ->with('success', 'Pendaftaran terkirim. Tunggu verifikasi dari penyelenggara.');
    }

    public function reportExternal(int $id)
    {
        $competition = $this->findExternal($id);
        $back = base_url('peluang/' . $id);

        if (strtotime($competition['deadline']) < time()) {
            return redirect()->to($back)->with('error', 'Pendaftaran sudah ditutup.');
        }

        if (! preg_match('#^https?://#i', trim((string) $competition['registration_link']))) {
            return redirect()->to($back)->with('error', 'Link pendaftaran eksternal belum tersedia.');
        }

        $db = db_connect();
        $exists = $db->table('registrations')
            ->where('user_id', $this->userId())
            ->where('competition_id', $id)
            ->countAllResults() > 0;

        if ($exists) {
            return redirect()->to($back)->with('error', 'Kamu sudah melaporkan atau mendaftar di peluang ini.');
        }

        $db->transStart();

        $db->table('registrations')->insert([
            'user_id'        => $this->userId(),
            'competition_id' => $id,
            'status'         => 'pending',
        ]);

        $db->table('notifications')->insert([
            'user_id' => (int) $competition['organizer_id'],
            'title'   => 'Laporan pendaftaran eksternal',
            'message' => session()->get('userName') . ' melaporkan sudah mendaftar pada ' . $competition['title'] . '.',
            'type'    => 'registration',
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to($back)->with('error', 'Laporan pendaftaran gagal disimpan. Coba lagi.');
        }

        return redirect()->to($back)
            ->with('success', 'Laporan terkirim. Status menunggu verifikasi penyelenggara.');
    }

    public function printCard(int $id)
    {
        $db = db_connect();
        $reg = $db->table('registrations r')
            ->select('r.*, c.title AS comp_title, c.type AS comp_type, c.education_level, 
                     c.competition_level, c.implementation_type, c.location, c.event_date,
                     u.name AS participant_name, u.email AS participant_email,
                     org.name AS organizer_name,
                     t.name AS team_name')
            ->join('competitions c', 'c.id = r.competition_id')
            ->join('users u', 'u.id = r.user_id')
            ->join('users org', 'org.id = c.organizer_id')
            ->join('teams t', 't.id = r.team_id', 'left')
            ->where('r.id', $id)
            ->where('r.user_id', $this->userId())
            ->where('r.status', 'verified')
            ->get()->getRowArray();

        if (! $reg) {
            throw PageNotFoundException::forPageNotFound('Kartu bukti pendaftaran tidak ditemukan atau belum diverifikasi.');
        }

        return view('participant/print_card', [
            'title' => 'Bukti Pendaftaran - ' . $reg['comp_title'],
            'reg'   => $reg,
        ]);
    }

    // ------------------------------------------------------------- HELPERS

    private function userId(): int
    {
        return (int) session()->get('userId');
    }

    /** Peluang tayang dengan pendaftaran internal, selain itu 404 */
    private function findInternal(int $id): array
    {
        $row = db_connect()->table('competitions')
            ->select('competitions.*, users.name AS organizer_name')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.id', $id)
            ->where('competitions.status', 'approved')
            ->where('competitions.registration_mode', 'internal')
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Peluang tayang dengan pendaftaran eksternal, selain itu 404 */
    private function findExternal(int $id): array
    {
        $row = db_connect()->table('competitions')
            ->select('competitions.*, users.name AS organizer_name')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.id', $id)
            ->where('competitions.status', 'approved')
            ->where('competitions.registration_mode', 'external')
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Deadline lewat atau sudah pernah mendaftar: kembali ke halaman detail */
    private function guard(array $competition)
    {
        $back = base_url('peluang/' . $competition['id']);

        if (strtotime($competition['deadline']) < time()) {
            return redirect()->to($back)->with('error', 'Pendaftaran sudah ditutup.');
        }

        $exists = db_connect()->table('registrations')
            ->where('user_id', $this->userId())
            ->where('competition_id', $competition['id'])
            ->countAllResults() > 0;

        if ($exists) {
            return redirect()->to($back)->with('error', 'Kamu sudah mendaftar di peluang ini.');
        }

        return null;
    }
}