<?php

namespace App\Models;

use CodeIgniter\Model;

class PreferenceSkillModel extends Model
{
    protected $table         = 'preference_skills';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_preference_id',
        'skill_name',
    ];
    protected $useTimestamps = false;
}
