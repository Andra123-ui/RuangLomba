<?php

namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table         = 'teams';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'leader_id',
        'name',
        'join_code',
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
}
