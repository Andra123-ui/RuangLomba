<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function tentang()
    {
        return view('pages/tentang', ['title' => 'Tentang Kami']);
    }

    public function syarat()
    {
        return view('pages/syarat', ['title' => 'Syarat & Ketentuan']);
    }

    public function privasi()
    {
        return view('pages/privasi', ['title' => 'Kebijakan Privasi']);
    }

        public function bantuan()
    {
        $faqs = [
            'Umum' => [
                [
                    'q' => 'Apa itu RuangLomba?',
                    'a' => 'RuangLomba adalah platform untuk menemukan lomba, beasiswa, webinar, workshop, dan seminar dalam satu tempat.',
                ],
                [
                    'q' => 'Bagaimana cara mencari peluang yang sesuai?',
                    'a' => 'Buka halaman Jelajahi Peluang, lalu gunakan pencarian dan filter seperti jenis, minat, jenjang, biaya, dan lokasi.',
                ],
                [
                    'q' => 'Apa arti "H-5" pada kartu peluang?',
                    'a' => 'Itu menunjukkan sisa hari menuju deadline. Jika tertulis "Ditutup", pendaftaran sudah berakhir.',
                ],
            ],
            'Akun & Fitur' => [
                [
                    'q' => 'Apa itu LombaMatch AI?',
                    'a' => 'Fitur rekomendasi yang membantu menemukan lomba sesuai minat dan keahlianmu. Fitur ini tersedia setelah kamu login.',
                ],
                [
                    'q' => 'Saya tidak bisa login, apa yang harus dilakukan?',
                    'a' => 'Pastikan data login sudah benar. Jika masih gagal, hubungi kami lewat email di bawah dengan menyebutkan kendala yang kamu alami.',
                ],
            ],
            'Pendaftaran' => [
                [
                    'q' => 'Apakah daftar lomba dilakukan di RuangLomba?',
                    'a' => 'Pendaftaran dilakukan sesuai instruksi dari penyelenggara. RuangLomba membantu kamu menemukan informasi dan detailnya.',
                ],
                [
                    'q' => 'Informasi di suatu peluang tampak salah atau sudah berubah, bagaimana?',
                    'a' => 'Selalu cek informasi resmi dari penyelenggara. Kamu juga bisa melaporkannya ke kami lewat email agar bisa kami tinjau.',
                ],
            ],
            'Untuk Organizer' => [
                [
                    'q' => 'Bagaimana cara mempublikasikan lomba atau event?',
                    'a' => 'Buka halaman Untuk Organizer lalu klik Publikasikan, kemudian ikuti langkah yang tersedia.',
                ],
                [
                    'q' => 'Kenapa publikasi saya belum tampil?',
                    'a' => 'Publikasi bisa perlu ditinjau lebih dulu. Pastikan informasinya lengkap dan jelas. Jika masih belum tampil, hubungi kami lewat email.',
                ],
            ],
        ];

        return view('pages/bantuan', [
            'title' => 'Bantuan',
            'faqs'  => $faqs,
        ]);
    }
}