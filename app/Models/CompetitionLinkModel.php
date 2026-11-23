<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionLinkModel extends Model
{
    protected $table         = 'competition_links';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'competition_id',
        'platform',
        'url',
    ];
    protected $useTimestamps = false;
}
