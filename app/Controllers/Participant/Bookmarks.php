<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;

class Bookmarks extends BaseController
{
    // ------------------------------------------------- DAFTAR PELUANG TERSIMPAN

        public function index()
    {
        $rows = db_connect()->table('bookmarks b')
            ->select('c.*, cat.name AS category_name, u.name AS organizer_name')
            ->join('competitions c', 'c.id = b.competition_id')
            ->join('categories cat', 'cat.id = c.category_id')
            ->join('users u', 'u.id = c.organizer_id')
            ->where('b.user_id', $this->userId())
            ->where('c.status', 'approved')
            ->orderBy('b.created_at', 'DESC')
            ->get()->getResultArray();

        return view('participant/bookmarks', [
            'title'     => 'Peluang Tersimpan',
            'bookmarks' => $rows,
        ]);
    }

    // ---------------------------------------------- SIMPAN / BATAL SIMPAN (AJAX)

    public function toggle(int $id)
    {
        $db     = db_connect();
        $userId = $this->userId();

        $competition = $db->table('competitions')
            ->select('id')
            ->where('id', $id)
            ->where('status', 'approved')
            ->get()->getRowArray();

        if (! $competition) {
            return $this->json(['message' => 'Peluang tidak ditemukan.'], 404);
        }

        $db->transStart();

        $existing = $db->table('bookmarks')
            ->where('user_id', $userId)
            ->where('competition_id', $id)
            ->get()->getRowArray();

        if ($existing) {
            $db->table('bookmarks')->where('id', $existing['id'])->delete();
            $saved = false;
        } else {
            // ignore: kalau dua klik masuk bersamaan, UNIQUE key mencegah baris ganda
            $db->table('bookmarks')->ignore(true)->insert([
                'user_id'        => $userId,
                'competition_id' => $id,
            ]);
            $saved = true;
        }

        // Angka suka selalu dihitung ulang dari jumlah simpanan, jadi tidak bisa melenceng
        $db->query(
            'UPDATE competitions
             SET likes = (SELECT COUNT(*) FROM bookmarks WHERE competition_id = ?)
             WHERE id = ?',
            [$id, $id]
        );

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->json(['message' => 'Gagal menyimpan. Coba lagi.'], 500);
        }

        $row = $db->table('competitions')->select('likes')->where('id', $id)->get()->getRowArray();

        return $this->json([
            'saved' => $saved,
            'likes' => (int) ($row['likes'] ?? 0),
        ]);
    }

    // ------------------------------------------------------------- HELPERS

    private function userId(): int
    {
        return (int) session()->get('userId');
    }

    /** Selalu sertakan token CSRF terbaru supaya klik berikutnya tetap valid */
    private function json(array $data, int $status = 200)
    {
        $data['csrf'] = csrf_hash();

        return $this->response->setStatusCode($status)->setJSON($data);
    }
}