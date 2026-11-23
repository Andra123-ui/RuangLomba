<?php

namespace App\Controllers;

use App\Models\CompetitionModel;
use App\Models\PreferenceInterestModel;
use App\Models\PreferenceSkillModel;
use App\Models\UserPreferenceModel;

class Home extends BaseController
{
    public function index(): string
    {
        $isLoggedIn = (bool) session()->get('isLoggedIn');

        $userId     = (int) session()->get('userId');

        $data = [
            'title'                  => 'Beranda',
            'isLoggedIn'             => $isLoggedIn,
            'stats'                  => $this->getStats(),
            'opportunity_categories' => $this->getTypes(),
            'featured_opportunities' => $this->getFeatured(3),
            'interest_categories'    => $this->getInterests(8),
            'lomba_match'            => $isLoggedIn ? $this->getLombaMatch($userId) : null,
        ];

        return view('home', $data);
    }

    /** Statistik hero dari database */
    private function getStats(): array
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $fmt = fn ($n) => number_format((int) $n, 0, ',', '.');

        return [
            'organizers' => $fmt($db->table('users')->where('role', 'organizer')->countAllResults()),
            'users'      => $fmt($db->table('users')->where('role', 'participant')->countAllResults()),
            'total'      => $fmt($db->table('competitions')->where('status', 'approved')->countAllResults()),
            'active'     => $fmt($db->table('competitions')
                                ->where('status', 'approved')
                                ->where('deadline >=', $now)
                                ->countAllResults()),
            'verified_registrations' => $fmt($db->table('registrations')
                                ->where('status', 'verified')
                                ->countAllResults()),
        ];
    }

    /** Kartu jenis peluang: deskripsi statis, jumlah dari database */
    private function getTypes(): array
    {
        $db = \Config\Database::connect();

        $counts = [];
        $rows   = $db->table('competitions')
            ->select('type, COUNT(*) AS total')
            ->where('status', 'approved')
            ->groupBy('type')
            ->get()
            ->getResultArray();

        foreach ($rows as $r) {
            $counts[$r['type']] = (int) $r['total'];
        }

        $types = [
            ['Lomba',           'lomba',    'Ikuti kompetisi dan tunjukkan kemampuanmu.'],
            ['Beasiswa',        'beasiswa', 'Temukan peluang untuk mendukung pendidikanmu.'],
            ['Webinar',         'webinar',  'Belajar dari berbagai topik dan pembicara.'],
            ['Workshop',        'workshop', 'Tingkatkan kemampuan melalui pengalaman langsung.'],
            ['Seminar',         'seminar',  'Perluas wawasan dan pengetahuanmu.'],
            ['Program & Event', 'event',    'Temukan berbagai kesempatan pengembangan diri.'],
        ];

        $result = [];
        foreach ($types as $i => [$name, $file, $desc]) {
            $result[] = [
                'num'   => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'name'  => $name,
                'desc'  => $desc,
                'total' => $counts[$name] ?? 0,
                'image' => media_url('assets/images/types/' . $file . '.jpg'),
            ];
        }

        return $result;
    }

    /** Lomba pilihan: suka + dilihat tertinggi */
    private function getFeatured(int $limit = 3): array
    {
        $rows = (new CompetitionModel())
            ->select('competitions.*, users.name AS organizer_name')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.status', 'approved')
            ->orderBy('(competitions.likes + competitions.views)', 'DESC', false)
            ->orderBy('competitions.likes', 'DESC')
            ->findAll($limit);

        $featured = [];

        foreach ($rows as $r) {
            $deadlineTs = strtotime($r['deadline']);
            $daysLeft   = (int) floor(($deadlineTs - strtotime('today')) / 86400);
            $orgName    = $r['organizer_name'] ?? '-';

            $featured[] = [
                'id'                => $r['id'],
                'title'             => $r['title'],
                'type'              => $r['type'],
                'jenjang'           => $r['education_level'],
                'harga'             => (int) $r['registration_fee'] === 0
                                        ? 'Gratis'
                                        : 'Rp ' . number_format($r['registration_fee'], 0, ',', '.'),
                'lokasi'            => $r['implementation_type'] . ' (' . $r['competition_level'] . ')',
                'tanggal'           => 'Deadline ' . date('d M Y', $deadlineTs),
                'hdays'             => $daysLeft,
                'likes'             => number_format((int) $r['likes'], 0, ',', '.'),
                'views'             => number_format((int) $r['views'], 0, ',', '.'),
                'organizer'         => $orgName,
                'organizer_initial' => strtoupper(mb_substr($orgName, 0, 1)),
                'image'             => media_url($r['poster']),
            ];
        }

        return $featured;
    }

    /** Kategori minat dari tabel categories (yang paling banyak peluangnya) */
    private function getInterests(int $limit = 8): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('categories c')
            ->select('c.id, c.name, c.image, COUNT(comp.id) AS total')
            ->join('competitions comp', "comp.category_id = c.id AND comp.status = 'approved'", 'left')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC')
            ->orderBy('c.name', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($rows as $i => $r) {
            $result[] = [
                'num'   => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'id'    => $r['id'],
                'name'  => $r['name'],
                'image' => media_url($r['image']),
            ];
        }

        return $result;
    }

    /**
     * LombaMatch AI: Rekomendasi dinamis berbasis skor kecocokan minat, jenjang, mode, dan keahlian
     */
    private function getLombaMatch(int $userId): array
    {
        $prefModel = new UserPreferenceModel();
        $pref = $prefModel->where('user_id', $userId)->first();

        $interestNames = [];
        $skillNames    = [];

        if ($pref) {
            $interestModel = new PreferenceInterestModel();
            $interestRows  = $interestModel->where('user_preference_id', $pref['id'])->findAll();
            $interestNames = array_column($interestRows, 'interest_name');

            $skillModel    = new PreferenceSkillModel();
            $skillRows     = $skillModel->where('user_preference_id', $pref['id'])->findAll();
            $skillNames    = array_column($skillRows, 'skill_name');
        }

        $now = date('Y-m-d H:i:s');
        $competitions = (new CompetitionModel())
            ->select('competitions.*, categories.name AS category_name, users.name AS organizer_name')
            ->join('categories', 'categories.id = competitions.category_id')
            ->join('users', 'users.id = competitions.organizer_id')
            ->where('competitions.status', 'approved')
            ->where('competitions.deadline >=', $now)
            ->findAll();

        $userLevel = $pref['preferred_level'] ?? null;
        $userMode  = $pref['preferred_mode'] ?? null;
        $userFee   = $pref['preferred_fee'] ?? 'Tidak Masalah';

        $hasPrefs = ! empty($interestNames) || ! empty($skillNames) || ! empty($userLevel);

        $maxBase = 7 + (2 * max(1, count($skillNames)));
        $scored = [];

        foreach ($competitions as $c) {
            $score = 0;

            // 1. Minat / Kategori (+3 poin)
            if (! empty($interestNames) && in_array($c['category_name'], $interestNames, true)) {
                $score += 3;
            }

            // 2. Jenjang (+2 poin)
            if ($userLevel) {
                if ($c['education_level'] === $userLevel || $c['education_level'] === 'Umum') {
                    $score += 2;
                }
            } else {
                $score += 1;
            }

            // 3. Mode Pelaksanaan (+1 poin)
            if (! empty($userMode) && $userMode !== 'Tidak Masalah') {
                if ($c['implementation_type'] === $userMode || $c['implementation_type'] === 'Hybrid') {
                    $score += 1;
                }
            } else {
                $score += 1;
            }

            // 4. Biaya Pendaftaran (+1 poin)
            if ($userFee === 'Gratis' && (int) $c['registration_fee'] === 0) {
                $score += 1;
            } elseif ($userFee === 'Berbayar' && (int) $c['registration_fee'] > 0) {
                $score += 1;
            } elseif ($userFee === 'Tidak Masalah') {
                $score += 1;
            }

            // 5. Keahlian (+2 poin per skill yang muncul di judul atau deskripsi)
            $matchedSkills = [];
            foreach ($skillNames as $skill) {
                if ($skill === '') continue;
                if (
                    stripos($c['title'], $skill) !== false ||
                    stripos($c['description'], $skill) !== false ||
                    (! empty($c['requirements']) && stripos($c['requirements'], $skill) !== false)
                ) {
                    $score += 2;
                    $matchedSkills[] = $skill;
                }
            }

            // Hitung persentase kecocokan (antara 65% - 98%)
            $pct = $hasPrefs
                ? min(98, max(65, (int) round(($score / $maxBase) * 100)))
                : 85;

            $deadlineTs = strtotime($c['deadline']);
            $daysLeft   = (int) floor(($deadlineTs - strtotime('today')) / 86400);

            $scored[] = [
                'id'                 => $c['id'],
                'title'              => $c['title'],
                'type'               => $c['type'],
                'category_name'      => $c['category_name'],
                'implementation_type'=> $c['implementation_type'],
                'registration_fee'   => (int) $c['registration_fee'],
                'fee_label'          => (int) $c['registration_fee'] === 0 ? 'Gratis' : 'Rp ' . number_format($c['registration_fee'], 0, ',', '.'),
                'education_level'    => $c['education_level'],
                'organizer_name'     => $c['organizer_name'] ?? '-',
                'poster'             => media_url($c['poster']),
                'days_left'          => $daysLeft,
                'score'              => $score,
                'pct'                => $pct,
                'matched_skills'     => $matchedSkills,
            ];
        }

        // Urutkan berdasarkan score tertinggi
        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);
        $top = array_slice($scored, 0, 3);

        return [
            'has_preferences' => $hasPrefs,
            'interests_text'  => ! empty($interestNames) ? implode(' · ', $interestNames) : 'Semua Bidang',
            'skills_text'     => ! empty($skillNames) ? implode(' · ', $skillNames) : 'Belum diisi',
            'fee_text'        => $userFee,
            'mode_text'       => $userMode ?: 'Semua Mode',
            'level_text'      => $userLevel ?: 'Semua Jenjang',
            'matches'         => $top,
        ];
    }
}