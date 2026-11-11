<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Registrations extends BaseController
{
     public function index()
    {
        $rows = db_connect()->table('registrations r')
            ->select('r.id, r.status, r.reject_reason, r.registered_at, r.verified_at,
                      c.id AS competition_id, c.title, c.type, c.poster, c.deadline,
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

        return view('participant/register', [
            'title'       => 'Daftar - ' . $competition['title'],
            'competition' => $competition,
            'user'        => $user,
        ]);
    }

    public function store(int $id)
    {
        $competition = $this->findInternal($id);

        if ($redirect = $this->guard($competition)) {
            return $redirect;
        }

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

        $errors = $this->validate($rules, $messages) ? [] : $this->validator->getErrors();

        // Normalisasi nomor HP: buang spasi dan tanda hubung
        $phone = preg_replace('/[\s\-]+/', '', (string) $this->request->getPost('phone'));
        if (! isset($errors['phone']) && ! preg_match('/^(\+62|62|0)8[0-9]{8,12}$/', $phone)) {
            $errors['phone'] = 'Nomor HP tidak valid. Contoh: 081234567890.';
        }

        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = db_connect();
        $db->transStart();

        $db->table('registrations')->insert([
            'user_id'        => $this->userId(),
            'competition_id' => $id,
            'phone'          => $phone,
            'institution'    => trim((string) $this->request->getPost('institution')),
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