<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Competitions extends BaseController
{
    private const STATUSES = ['pending', 'approved', 'rejected', 'expired'];

    // ---------------------------------------------------------------- READ

    public function index()
    {
        $status = $this->request->getGet('status');
        if (! in_array($status, self::STATUSES, true)) {
            $status = 'pending';
        }

        $db = db_connect();

        $rows = $db->table('competitions c')
            ->select('c.id, c.title, c.type, c.poster, c.deadline, c.status, c.created_at, u.name AS organizer_name')
            ->join('users u', 'u.id = c.organizer_id')
            ->where('c.status', $status)
            ->orderBy('c.created_at', $status === 'pending' ? 'ASC' : 'DESC') // yang menunggu paling lama di atas
            ->get()->getResultArray();

        $counts = array_fill_keys(self::STATUSES, 0);
        $grouped = $db->table('competitions')
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()->getResultArray();
        foreach ($grouped as $g) {
            $counts[$g['status']] = (int) $g['total'];
        }

        return view('admin/competitions/index', [
            'title'        => 'Moderasi Peluang',
            'competitions' => $rows,
            'status'       => $status,
            'counts'       => $counts,
        ]);
    }

    public function show(int $id)
    {
        return view('admin/competitions/show', [
            'title'       => 'Tinjau Peluang',
            'competition' => $this->findOrFail($id),
        ]);
    }

    // ------------------------------------------------------------- ACTIONS

    public function approve(int $id)
    {
        $competition = $this->findOrFail($id);
        $back        = base_url('admin/peluang');

        if ($competition['status'] === 'approved') {
            return redirect()->to($back)->with('error', 'Peluang ini sudah tayang.');
        }

        // Peluang yang deadline-nya sudah lewat tidak ada gunanya ditayangkan
        if (strtotime($competition['deadline']) < time()) {
            return redirect()->to($back)
                ->with('error', 'Deadline peluang ini sudah lewat, tidak bisa disetujui.');
        }

        $this->changeStatus(
            $competition,
            'approved',
            'Peluang disetujui',
            '"' . $competition['title'] . '" sudah disetujui dan kini tayang di RuangLomba.'
        );

        return redirect()->to($back)
            ->with('success', '"' . $competition['title'] . '" disetujui dan sudah tayang.');
    }

    public function reject(int $id)
    {
        $competition = $this->findOrFail($id);
        $back        = base_url('admin/peluang');

        if ($competition['status'] === 'rejected') {
            return redirect()->to($back)->with('error', 'Peluang ini sudah ditolak.');
        }

        $reason = trim((string) $this->request->getPost('reason'));
        if (mb_strlen($reason) > 255) {
            return redirect()->back()->with('error', 'Alasan penolakan maksimal 255 karakter.');
        }

        $message = '"' . $competition['title'] . '" ditolak oleh admin.'
            . ($reason !== '' ? ' Alasan: ' . $reason : '')
            . ' Kamu bisa mengubahnya lalu mengirim ulang.';

        $this->changeStatus($competition, 'rejected', 'Peluang ditolak', $message);

        return redirect()->to($back)
            ->with('success', '"' . $competition['title'] . '" ditolak.');
    }

    // ------------------------------------------------------------- HELPERS

    private function findOrFail(int $id): array
    {
        $row = db_connect()->table('competitions c')
            ->select('c.*, cat.name AS category_name, u.name AS organizer_name, u.email AS organizer_email')
            ->join('categories cat', 'cat.id = c.category_id')
            ->join('users u', 'u.id = c.organizer_id')
            ->where('c.id', $id)
            ->get()->getRowArray();

        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Ubah status dan kirim notifikasi ke penyelenggara dalam satu transaksi */
    private function changeStatus(array $competition, string $status, string $title, string $message): void
    {
        $db = db_connect();
        $db->transStart();

        $db->table('competitions')
            ->where('id', $competition['id'])
            ->update(['status' => $status]);

        $db->table('notifications')->insert([
            'user_id' => (int) $competition['organizer_id'],
            'title'   => $title,
            'message' => $message,
            'type'    => 'competition',
        ]);

        $db->transComplete();
    }
}