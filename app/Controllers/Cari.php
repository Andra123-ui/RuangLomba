<?php

namespace App\Controllers;

use App\Models\CompetitionModel;
use App\Models\CategoryModel;

class Cari extends BaseController
{
        public function detail($id)
    {
        $id = (int) $id;

        $competition = (new CompetitionModel())
            ->select('competitions.*, categories.name AS category_name, users.name AS organizer_name, users.avatar AS organizer_logo, users.organizer_bio, users.organizer_address')
            ->join('categories', 'categories.id = competitions.category_id')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.id', $id)
            ->where('competitions.status', 'approved')
            ->first();

        if (!$competition) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db         = \Config\Database::connect();
        $session    = session();
        $categoryId = (int) $competition['category_id'];
        $userId     = (int) $session->get('userId');
        $userRole   = $session->get('userRole');

        // ---------------------------------------------------------
        // HITUNG LIHAT: sekali per sesi, pemilik peluang & admin tidak dihitung
        // ---------------------------------------------------------
        $viewed  = $session->get('viewedCompetitions') ?? [];
        $isOwner = $userId > 0 && $userId === (int) $competition['organizer_id'];

        if (!in_array($id, $viewed, true) && !$isOwner && $userRole !== 'admin') {
            $db->table('competitions')
                ->where('id', $id)
                ->set('views', 'views + 1', false)
                ->update();

            $viewed[] = $id;
            $session->set('viewedCompetitions', $viewed);
            $competition['views'] = (int) $competition['views'] + 1;
        }

        // Peluang serupa: utamakan kategori sama, isi sisanya dengan jenis sama (maks. 3)
        $related = (new CompetitionModel())
            ->select('competitions.*, categories.name AS category_name')
            ->join('categories', 'categories.id = competitions.category_id')
            ->where('competitions.status', 'approved')
            ->where('competitions.id !=', $id)
            ->where('competitions.deadline >=', date('Y-m-d H:i:s'))
            ->groupStart()
                ->where('competitions.category_id', $categoryId)
                ->orWhere('competitions.type', $competition['type'])
            ->groupEnd()
            ->orderBy("(competitions.category_id = {$categoryId})", 'DESC', false)
            ->orderBy('competitions.deadline', 'ASC')
            ->findAll(3);

        // Link media sosial / website (tabel competition_links)
        $links = [];
        $rows  = $db->table('competition_links')
            ->where('competition_id', $id)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $links[strtolower($row['platform'])] = $row['url'];
        }

        // Status pendaftaran & simpanan milik peserta yang sedang login
        $myRegistration = null;
        $isBookmarked   = false;

        if ($session->get('isLoggedIn') && $userRole === 'participant') {
            $myRegistration = $db->table('registrations')
                ->where('user_id', $userId)
                ->where('competition_id', $id)
                ->get()
                ->getRowArray();

            $isBookmarked = $db->table('bookmarks')
                ->where('user_id', $userId)
                ->where('competition_id', $id)
                ->countAllResults() > 0;
        }

        return view('detail', [
            'title'          => $competition['title'],
            'competition'    => $competition,
            'related'        => $related,
            'links'          => $links,
            'myRegistration' => $myRegistration,
            'isBookmarked'   => $isBookmarked,
        ]);
    }

    public function index()
    {
        $competitionModel = new CompetitionModel();
        $categoryModel = new CategoryModel();

        // Ambil parameter dari URL
        $keyword = trim($this->request->getGet('q') ?? '');
        $type = $this->request->getGet('type');
        $category = $this->request->getGet('category');
        $educationLevel = $this->request->getGet('education_level');
        $competitionLevel = $this->request->getGet('competition_level');
        $implementationType = $this->request->getGet('implementation_type');
        $fee = $this->request->getGet('fee');
        $deadlineFilter = $this->request->getGet('deadline');
        $location = trim($this->request->getGet('location') ?? '');

        // Query utama
        $competitionModel
            ->select('competitions.*, categories.name AS category_name, users.name AS organizer_name')
            ->join('categories', 'categories.id = competitions.category_id')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.status', 'approved');

        // =========================
        // SEARCH
        // =========================
        if ($keyword !== '') {
            $competitionModel->groupStart()
                ->like('competitions.title', $keyword)
                ->orLike('competitions.description', $keyword)
                ->orLike('categories.name', $keyword)
                ->orLike('users.name', $keyword)
                ->groupEnd();
        }

        // =========================
        // FILTER JENIS
        // =========================
        if (!empty($type)) {
            $competitionModel->where('competitions.type', $type);
        }

        // =========================
        // FILTER KATEGORI
        // =========================
        if (!empty($category)) {
            $competitionModel->where('competitions.category_id', $category);
        }

        // =========================
        // FILTER JENJANG
        // =========================
        if (!empty($educationLevel)) {
            $competitionModel->where(
                'competitions.education_level',
                $educationLevel
            );
        }

        // =========================
        // FILTER TINGKAT
        // =========================
        if (!empty($competitionLevel)) {
            $competitionModel->where(
                'competitions.competition_level',
                $competitionLevel
            );
        }

        // =========================
        // FILTER MODE
        // =========================
        if (!empty($implementationType)) {
            $competitionModel->where(
                'competitions.implementation_type',
                $implementationType
            );
        }

        // =========================
        // FILTER BIAYA
        // =========================
        if ($fee === 'gratis') {
            $competitionModel->where('competitions.registration_fee', 0);
        } elseif ($fee === 'berbayar') {
            $competitionModel->where('competitions.registration_fee >', 0);
        }

        // =========================
// FILTER LOKASI (hanya berlaku untuk Offline / Hybrid)
// =========================
if (!in_array($implementationType, ['Offline', 'Hybrid'], true)) {
    $location = '';
}

if ($location !== '') {
    $competitionModel->where('competitions.location', $location);
}
        // =========================
        // FILTER DEADLINE
        // =========================
        if ($deadlineFilter === 'terdekat') {

            $competitionModel
                ->where('competitions.deadline >=', date('Y-m-d H:i:s'))
                ->orderBy('competitions.deadline', 'ASC');

        } elseif ($deadlineFilter === 'hari_ini') {

            $competitionModel
                ->where(
                    'DATE(competitions.deadline)',
                    date('Y-m-d')
                )
                ->orderBy('competitions.deadline', 'ASC');

        } elseif ($deadlineFilter === 'minggu_ini') {

            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

            $competitionModel
                ->where(
                    'DATE(competitions.deadline) >=',
                    $startOfWeek
                )
                ->where(
                    'DATE(competitions.deadline) <=',
                    $endOfWeek
                )
                ->orderBy('competitions.deadline', 'ASC');

        } elseif ($deadlineFilter === 'bulan_ini') {

            $startOfMonth = date('Y-m-01');
            $endOfMonth = date('Y-m-t');

            $competitionModel
                ->where(
                    'DATE(competitions.deadline) >=',
                    $startOfMonth
                )
                ->where(
                    'DATE(competitions.deadline) <=',
                    $endOfMonth
                )
                ->orderBy('competitions.deadline', 'ASC');

        } else {

            $competitionModel->orderBy(
                'competitions.created_at',
                'DESC'
            );
        }

 // =========================
// DAFTAR LOKASI PER MODE (Offline / Hybrid yang masih tersedia)
// =========================
$locationRows = \Config\Database::connect()
    ->table('competitions')
    ->select('implementation_type, location')
    ->distinct()
    ->where('status', 'approved')
    ->whereIn('implementation_type', ['Offline', 'Hybrid'])
    ->where('location IS NOT NULL', null, false)
    ->where('location !=', '')
    ->where('location != implementation_type', null, false)
    ->where('deadline >=', date('Y-m-d H:i:s'))
    ->orderBy('location', 'ASC')
    ->get()
    ->getResultArray();

$locationsByMode = ['Offline' => [], 'Hybrid' => []];
foreach ($locationRows as $row) {
    $locationsByMode[$row['implementation_type']][] = $row['location'];
}

        // =========================
        // PAGINATION
        // =========================
        $perPage = 9;

        $data = [
            'title' => 'Jelajahi Peluang - RuangLomba',

            'competitions' => $competitionModel
                ->paginate($perPage),

            'pager' => $competitionModel->pager,

            'categories' => $categoryModel
                ->orderBy('name', 'ASC')
                ->findAll(),

            // nilai filter yang sedang aktif
            'keyword' => $keyword,
            'selectedType' => $type,
            'selectedCategory' => $category,
            'selectedEducationLevel' => $educationLevel,
            'selectedCompetitionLevel' => $competitionLevel,
            'selectedImplementationType' => $implementationType,
            'selectedFee' => $fee,
            'selectedDeadline' => $deadlineFilter,
            'selectedLocation' => $location,
            'locationsByMode' => $locationsByMode,
        ];

        return view('cari', $data);
    }
}