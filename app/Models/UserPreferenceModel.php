<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPreferenceModel extends Model
{
    protected $table         = 'user_preferences';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'preferred_level', 'preferred_mode', 'preferred_fee'];
    protected $useTimestamps = false;
}