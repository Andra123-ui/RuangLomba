<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizerProfileModel extends Model
{
    protected $table         = 'organizer_profiles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'org_type',
        'phone',
        'description',
        'link',
        'verification_status',
        'verification_document',
        'verified_at',
        'rejection_reason',
    ];
    protected $useTimestamps = false;
}