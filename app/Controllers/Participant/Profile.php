<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PreferenceInterestModel;
use App\Models\PreferenceSkillModel;
use App\Models\UserModel;
use App\Models\UserPreferenceModel;

class Profile extends BaseController
{
    private const AVATAR_DIR = 'assets/images/avatars/';
    private const LEVELS = ['SD', 'SMP', 'SMA/SMK/MA', 'Mahasiswa', 'Umum'];
    private const MODES  = ['Online', 'Offline', 'Hybrid'];
    private const FEES   = ['Gratis', 'Berbayar', 'Tidak Masalah'];

    public function index()
    {
        $userId = (int) session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (! $user) {
            return redirect()->to(base_url('masuk'));
        }

        $prefModel = new UserPreferenceModel();
        $preference = $prefModel->where('user_id', $userId)->first();

        $selectedInterests = [];
        $skills = [];

        if ($preference) {
            $interestModel = new PreferenceInterestModel();
            $interests = $interestModel->where('user_preference_id', $preference['id'])->findAll();
            $selectedInterests = array_column($interests, 'interest_name');

            $skillModel = new PreferenceSkillModel();
            $skillRows = $skillModel->where('user_preference_id', $preference['id'])->findAll();
            $skills = array_column($skillRows, 'skill_name');
        }

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        return view('participant/profile', [
            'title'             => 'Profil & Preferensi Saya',
            'user'              => $user,
            'preference'        => $preference,
            'selectedInterests' => $selectedInterests,
            'skills'            => $skills,
            'categories'        => $categories,
            'levels'            => self::LEVELS,
            'modes'             => self::MODES,
            'fees'              => self::FEES,
        ]);
    }

    public function update()
    {
        $userId = (int) session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (! $user) {
            return redirect()->to(base_url('masuk'));
        }

        $rules = [
            'name'            => 'required|min_length[3]|max_length[100]',
            'preferred_level' => 'permit_empty|in_list[' . implode(',', self::LEVELS) . ']',
            'preferred_mode'  => 'permit_empty|in_list[' . implode(',', self::MODES) . ']',
            'preferred_fee'   => 'permit_empty|in_list[' . implode(',', self::FEES) . ']',
            'avatar'          => 'permit_empty|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 1. Update User basic info
        $userData = [
            'name' => trim((string) $this->request->getPost('name')),
        ];

        // Avatar upload
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && ! $avatarFile->hasMoved()) {
            $dir = FCPATH . self::AVATAR_DIR;
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $avatarName = $avatarFile->getRandomName();
            $avatarFile->move($dir, $avatarName);

            // Delete old avatar if custom
            if (! empty($user['avatar']) && str_starts_with($user['avatar'], self::AVATAR_DIR)) {
                $oldPath = FCPATH . $user['avatar'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $userData['avatar'] = self::AVATAR_DIR . $avatarName;
        }

        // Update session name if changed
        session()->set('userName', $userData['name']);

        // Gamifikasi: Beri poin +20 jika melengkapi profil pertama kali
        $earnedPoints = 0;
        if ((int) ($user['points'] ?? 0) === 0) {
            $userData['points'] = 20;
            $earnedPoints = 20;
        }

        $userModel->update($userId, $userData);

        // 2. Update / Insert user_preferences
        $prefModel = new UserPreferenceModel();
        $preference = $prefModel->where('user_id', $userId)->first();

        $prefData = [
            'user_id'         => $userId,
            'preferred_level' => $this->request->getPost('preferred_level') ?: null,
            'preferred_mode'  => $this->request->getPost('preferred_mode') ?: null,
            'preferred_fee'   => $this->request->getPost('preferred_fee') ?: 'Tidak Masalah',
        ];

        if ($preference) {
            $prefModel->update($preference['id'], $prefData);
            $preferenceId = (int) $preference['id'];
        } else {
            $preferenceId = (int) $prefModel->insert($prefData);
        }

        // 3. Update preference_interests
        $interestModel = new PreferenceInterestModel();
        $interestModel->where('user_preference_id', $preferenceId)->delete();

        $interests = (array) $this->request->getPost('interests');
        foreach ($interests as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $interestModel->insert([
                    'user_preference_id' => $preferenceId,
                    'interest_name'      => $item,
                ]);
            }
        }

        // 4. Update preference_skills
        $skillModel = new PreferenceSkillModel();
        $skillModel->where('user_preference_id', $preferenceId)->delete();

        $skillsRaw = (string) $this->request->getPost('skills');
        $skillsArr = array_filter(array_map('trim', explode(',', $skillsRaw)));
        foreach ($skillsArr as $skill) {
            if ($skill !== '') {
                $skillModel->insert([
                    'user_preference_id' => $preferenceId,
                    'skill_name'         => $skill,
                ]);
            }
        }

        $msg = 'Profil dan preferensi berhasil disimpan!';
        if ($earnedPoints > 0) {
            $msg .= ' Selamat, kamu mendapatkan +' . $earnedPoints . ' poin gamifikasi karena melengkapi profil!';
        }

        return redirect()->to(base_url('profil'))->with('success', $msg);
    }
}
