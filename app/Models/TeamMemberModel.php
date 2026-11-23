<?php

namespace App\Models;

use CodeIgniter\Model;

class TeamMemberModel extends Model
{
    protected $table         = 'team_members';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'team_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];
    protected $useTimestamps = false;
}
