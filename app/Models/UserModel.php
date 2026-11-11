<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'email', 'password_hash', 'role', 'avatar'];
    protected $useTimestamps = false; // created_at & updated_at diisi default database
}