<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table         = 'registrations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'competition_id',
        'team_id',
        'phone',
        'institution',
        'status',
        'reject_reason',
        'payment_proof',
        'registered_at',
        'verified_at',
    ];
    protected $useTimestamps = false;
}
