<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuthLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'auth_logs';

    protected $fillable = [
        'user_id',
        'email',
        'action', // login, logout
        'ip_address',
        'user_agent',
    ];
}
