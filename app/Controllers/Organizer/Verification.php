<?php

namespace App\Controllers\Organizer;

use App\Controllers\BaseController;
use App\Models\OrganizerProfileModel;
use App\Models\UserModel;

class Verification extends BaseController
{
    private const DOC_DIR = 'assets/uploads/verifications/';

    public function index()
    {
        $userId = (int) session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        $profileModel = new OrganizerProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();

        return view('organizer/verification', [
            'title'   => 'Verifikasi Instansi Penyelenggara',
            'user'    => $user,
            'profile' => $profile,
        ]);
    }

    public function submit()
    {
        $userId = (int) session()->get('userId');
        $profileModel = new OrganizerProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();

        if (! $profile) {
            return redirect()->back()->with('error', 'Profil penyelenggara belum lengkap.');
        }

        if (($profile['verification_status'] ?? '') === 'verified') {
            return redirect()->back()->with('error', 'Akun instansi kamu sudah terverifikasi.');
        }

        $rules = [
            'document' => 'uploaded[document]|mime_in[document,image/jpg,image/jpeg,image/png,image/webp,application/pdf]|max_size[document,4096]',
        ];
        $messages = [
            'document' => [
                'uploaded' => 'File dokumen legalitas/surat tugas wajib diunggah.',
                'mime_in'  => 'Format dokumen harus berupa PDF, JPG, PNG, atau WebP.',
                'max_size' => 'Ukuran dokumen maksimal 4 MB.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('document');
        $dir = FCPATH . self::DOC_DIR;
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = $file->getRandomName();
        $file->move($dir, $filename);
        $docPath = self::DOC_DIR . $filename;

        // Hapus dokumen lama jika ada
        if (! empty($profile['verification_document'])) {
            $oldFile = FCPATH . $profile['verification_document'];
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $profileModel->update($profile['id'], [
            'verification_status'   => 'pending',
            'verification_document' => $docPath,
            'rejection_reason'      => null,
        ]);

        // Kirim notifikasi ke Admin
        $db = \Config\Database::connect();
        $admins = $db->table('users')->where('role', 'admin')->get()->getResultArray();
        foreach ($admins as $admin) {
            $db->table('notifications')->insert([
                'user_id' => (int) $admin['id'],
                'title'   => 'Pengajuan Verifikasi Organizer',
                'message' => session()->get('userName') . ' mengajukan verifikasi legalitas instansi.',
                'type'    => 'organizer_verification',
            ]);
        }

        return redirect()->to(base_url('organizer/verifikasi'))
            ->with('success', 'Dokumen verifikasi berhasil dikirim! Admin akan meninjau berkas keabsahan instansimu.');
    }
}
