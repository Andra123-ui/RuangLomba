<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionModel extends Model
{
    protected $table = 'competitions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'organizer_id',
        'category_id',
        'title',
        'description',
        'type',
        'poster',
        'prize',
        'registration_fee',
        'education_level',
        'competition_level',
        'implementation_type',
        'location',
        'requirements',
        'registration_link',
        'registration_mode',
        'deadline',
        'event_date',
        'status',
        'views',
        'likes',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}