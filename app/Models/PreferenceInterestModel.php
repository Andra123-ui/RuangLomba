<?php

namespace App\Models;

use CodeIgniter\Model;

class PreferenceInterestModel extends Model
{
    protected $table         = 'preference_interests';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_preference_id',
        'interest_name',
    ];
    protected $useTimestamps = false;
}
