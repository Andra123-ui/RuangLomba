<?php

namespace App\Controllers;

use App\Models\CompetitionModel;

class Home extends BaseController
{
    public function index(): string
    {
        $isLoggedIn = (bool) session()->get('isLoggedIn');

        $data = [
            'title'                  => 'Beranda',
            'isLoggedIn'             => $isLoggedIn,
            'stats'                  => $this->getStats(),
            'opportunity_categories' => $this->getTypes(),
            'featured_opportunities' => $this->getFeatured(3),
            'interest_categories'    => $this->getInterests(8),
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
}