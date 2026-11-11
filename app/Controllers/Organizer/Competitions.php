<?php

namespace App\Controllers\Organizer;

use App\Controllers\BaseController;
use App\Models\CompetitionModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Competitions extends BaseController
{
    /** true = peluang yang diedit kembali ke 'pending' dan ditinjau ulang admin */
    private const REVIEW_AFTER_EDIT = true;

    private const POSTER_DIR = 'assets/images/opportunities/';

    private const TYPES  = ['Lomba', 'Beasiswa', 'Webinar', 'Workshop', 'Seminar', 'Program & Event'];
    private const LEVELS = ['SD', 'SMP', 'SMA/SMK/MA', 'Mahasiswa', 'Umum'];
    private const SCOPES = ['Nasional', 'Internasional'];
    private const MODES  = ['Online', 'Offline', 'Hybrid'];
    private const REG_MODES = ['external', 'internal'];

    // ---------------------------------------------------------------- READ

        public function index()
    {
        $competitions = (new CompetitionModel())
            ->where('organizer_id', $this->userId())
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Jumlah pendaftar per peluang (total dan yang masih menunggu)
        $rows = db_connect()->table('registrations r')
            ->select("r.competition_id, COUNT(*) AS total, SUM(r.status = 'pending') AS pending", false)
            ->join('competitions c', 'c.id = r.competition_id')
            ->where('c.organizer_id', $this->userId())
            ->groupBy('r.competition_id')
            ->get()->getResultArray();

        $regCounts = [];
        foreach ($rows as $row) {
            $regCounts[(int) $row['competition_id']] = [
                'total'   => (int) $row['total'],
                'pending' => (int) $row['pending'],
            ];
        }

        return view('organizer/competitions/index', [
            'title'        => 'Peluang Saya',
            'competitions' => $competitions,
            'regCounts'    => $regCounts,
        ]);
    }

    // -------------------------------------------------------------- CREATE

    public function create()
    {
        return view('organizer/competitions/form', $this->formData(null));
    }

    public function store()
    {
        [$data, $errors] = $this->validateInput(true);

        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $data['organizer_id'] = $this->userId();
        $data['poster']       = $this->savePoster();
        $data['status']       = 'pending';

        (new CompetitionModel())->insert($data);

        return redirect()->to(base_url('organizer/lomba'))
            ->with('success', 'Peluang dikirim. Menunggu persetujuan admin sebelum tayang.');
    }

    // ---------------------------------------------------------------- EDIT

    public function edit(int $id)
    {
        return view('organizer/competitions/form', $this->formData($this->findOwned($id)));
    }

    public function update(int $id)
    {
        $competition = $this->findOwned($id);

        [$data, $errors] = $this->validateInput(false, $competition);

        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $newPoster = $this->savePoster();
        if ($newPoster !== null) {
            $this->deletePoster($competition['poster']);
            $data['poster'] = $newPoster;
        }

        $reviewed = self::REVIEW_AFTER_EDIT && $competition['status'] !== 'pending';
        if ($reviewed) {
            $data['status'] = 'pending';
        }

        (new CompetitionModel())->update($id, $data);

        return redirect()->to(base_url('organizer/lomba'))
            ->with('success', $reviewed
                ? 'Perubahan disimpan. Peluang akan tayang lagi setelah ditinjau admin.'
                : 'Perubahan disimpan.');
    }

    // -------------------------------------------------------------- DELETE

    public function delete(int $id)
    {
        $competition = $this->findOwned($id);

        (new CompetitionModel())->delete($id);
        $this->deletePoster($competition['poster']);

        return redirect()->to(base_url('organizer/lomba'))
            ->with('success', 'Peluang "' . $competition['title'] . '" dihapus.');
    }

    // ------------------------------------------------------------- HELPERS

    private function userId(): int
    {
        return (int) session()->get('userId');
    }

    /** Ambil peluang milik penyelenggara yang sedang login, selain itu 404 */
    private function findOwned(int $id): array
    {
        $row = (new CompetitionModel())
            ->where('id', $id)
            ->where('organizer_id', $this->userId())
            ->first();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Mode dikunci kalau peluang internal dan sudah ada pendaftar */
    private function isModeLocked(array $competition): bool
    {
        if (($competition['registration_mode'] ?? 'external') !== 'internal') {
            return false;
        }

        return db_connect()->table('registrations')
            ->where('competition_id', $competition['id'])
            ->countAllResults() > 0;
    }

    private function formData(?array $competition): array
    {
        return [
            'title'       => $competition ? 'Ubah Peluang' : 'Tambah Peluang',
            'competition' => $competition,
            'categories'  => db_connect()->table('categories')
                                ->select('id, name')->orderBy('name')->get()->getResultArray(),
            'types'       => self::TYPES,
            'levels'      => self::LEVELS,
            'scopes'      => self::SCOPES,
            'modes'       => self::MODES,
            'regLocked'   => $competition ? $this->isModeLocked($competition) : false,
        ];
    }

    /**
     * @return array{0: array, 1: array} [data bersih, daftar error]
     */
    private function validateInput(bool $isNew, ?array $competition = null): array
    {
        $mode          = $this->request->getPost('implementation_type');
        $needsLocation = in_array($mode, ['Offline', 'Hybrid'], true);

        // Mode pendaftaran: dikunci ke 'internal' kalau sudah ada pendaftar
        $regMode = $this->request->getPost('registration_mode');
        if ($competition && $this->isModeLocked($competition)) {
            $regMode = 'internal';
        }
        $isExternal = ($regMode === 'external');

        $rules = [
            'title'               => 'required|min_length[5]|max_length[200]',
            'category_id'         => 'required|is_not_unique[categories.id]',
            'type'                => 'required|in_list[' . implode(',', self::TYPES) . ']',
            'description'         => 'required|min_length[20]|max_length[5000]',
            'education_level'     => 'required|in_list[' . implode(',', self::LEVELS) . ']',
            'competition_level'   => 'required|in_list[' . implode(',', self::SCOPES) . ']',
            'implementation_type' => 'required|in_list[' . implode(',', self::MODES) . ']',
            'location'            => ($needsLocation ? 'required' : 'permit_empty') . '|max_length[150]',
            'prize'               => 'permit_empty|is_natural',
            'registration_fee'    => 'permit_empty|is_natural',
            'requirements'        => 'permit_empty|max_length[2000]',
            'registration_mode'   => 'required|in_list[' . implode(',', self::REG_MODES) . ']',
            'registration_link'   => ($isExternal ? 'required|valid_url_strict' : 'permit_empty') . '|max_length[500]',
            'deadline'            => 'required|valid_date[Y-m-d]',
            'poster'              => ($isNew ? 'uploaded[poster]|' : '')
                                     . 'is_image[poster]|mime_in[poster,image/jpg,image/jpeg,image/png,image/webp]|max_size[poster,2048]',
        ];

        $messages = [
            'title' => [
                'required'   => 'Judul wajib diisi.',
                'min_length' => 'Judul minimal 5 karakter.',
                'max_length' => 'Judul maksimal 200 karakter.',
            ],
            'category_id' => [
                'required'      => 'Pilih kategori.',
                'is_not_unique' => 'Kategori tidak valid.',
            ],
            'type' => [
                'required' => 'Pilih jenis peluang.',
                'in_list'  => 'Jenis peluang tidak valid.',
            ],
            'description' => [
                'required'   => 'Deskripsi wajib diisi.',
                'min_length' => 'Deskripsi minimal 20 karakter.',
                'max_length' => 'Deskripsi maksimal 5000 karakter.',
            ],
            'education_level' => [
                'required' => 'Pilih jenjang.',
                'in_list'  => 'Jenjang tidak valid.',
            ],
            'competition_level' => [
                'required' => 'Pilih tingkat peluang.',
                'in_list'  => 'Tingkat tidak valid.',
            ],
            'implementation_type' => [
                'required' => 'Pilih mode pelaksanaan.',
                'in_list'  => 'Mode pelaksanaan tidak valid.',
            ],
            'location' => [
                'required'   => 'Lokasi wajib diisi untuk peluang offline atau hybrid.',
                'max_length' => 'Lokasi maksimal 150 karakter.',
            ],
            'prize' => ['is_natural' => 'Hadiah harus berupa angka bulat tanpa titik atau koma.'],
            'registration_fee' => ['is_natural' => 'Biaya harus berupa angka bulat tanpa titik atau koma.'],
            'requirements' => ['max_length' => 'Persyaratan maksimal 2000 karakter.'],
            'registration_mode' => [
                'required' => 'Pilih cara pendaftaran.',
                'in_list'  => 'Cara pendaftaran tidak valid.',
            ],
            'registration_link' => [
                'required'         => 'Link pendaftaran wajib diisi.',
                'valid_url_strict' => 'Link harus diawali http:// atau https://.',
            ],
            'deadline' => [
                'required'   => 'Deadline wajib diisi.',
                'valid_date' => 'Format deadline tidak valid.',
            ],
            'poster' => [
                'uploaded' => 'Poster wajib diunggah.',
                'is_image' => 'File poster harus berupa gambar.',
                'mime_in'  => 'Format poster harus JPG, PNG, atau WebP.',
                'max_size' => 'Ukuran poster maksimal 2 MB.',
            ],
        ];

        $errors = $this->validate($rules, $messages) ? [] : $this->validator->getErrors();

        // Deadline tidak boleh sebelum hari ini
        $deadline = (string) $this->request->getPost('deadline');
        if (! isset($errors['deadline']) && $deadline < date('Y-m-d')) {
            $errors['deadline'] = 'Deadline tidak boleh sebelum hari ini.';
        }

        // Tanggal pelaksanaan (opsional, datetime-local)
        $eventRaw  = trim((string) $this->request->getPost('event_date'));
        $eventDate = null;
        if ($eventRaw !== '') {
            $dt = \DateTime::createFromFormat('Y-m-d\TH:i', $eventRaw);
            if ($dt === false) {
                $errors['event_date'] = 'Format tanggal pelaksanaan tidak valid.';
            } else {
                $eventDate = $dt->format('Y-m-d H:i:s');
            }
        }

        $post = $this->request->getPost();

        $data = [
            'category_id'         => (int) ($post['category_id'] ?? 0),
            'title'               => trim((string) ($post['title'] ?? '')),
            'description'         => trim((string) ($post['description'] ?? '')),
            'type'                => $post['type'] ?? '',
            'prize'               => (int) ($post['prize'] ?? 0),
            'registration_fee'    => (int) ($post['registration_fee'] ?? 0),
            'education_level'     => $post['education_level'] ?? '',
            'competition_level'   => $post['competition_level'] ?? '',
            'implementation_type' => $mode,
            'location'            => $needsLocation ? trim((string) ($post['location'] ?? '')) : 'Online',
            'requirements'        => trim((string) ($post['requirements'] ?? '')) ?: null,
            'registration_mode'   => $regMode,
            'registration_link'   => $isExternal ? trim((string) ($post['registration_link'] ?? '')) : null,
            'deadline'            => $deadline . ' 23:59:59',
            'event_date'          => $eventDate,
        ];

        return [$data, $errors];
    }

    /** Simpan poster yang diunggah, kembalikan path relatif (atau null kalau tidak ada file) */
    private function savePoster(): ?string
    {
        $file = $this->request->getFile('poster');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $dir = FCPATH . self::POSTER_DIR;
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $name = $file->getRandomName();
        $file->move($dir, $name);

        return self::POSTER_DIR . $name;
    }

    private function deletePoster(?string $path): void
    {
        if (! $path || ! str_starts_with($path, self::POSTER_DIR)) {
            return;
        }

        $file = FCPATH . self::POSTER_DIR . basename($path);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}