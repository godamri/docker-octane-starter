<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'url',
        'load_time',
        'request',
        'response',
        'executor_identity',
        'request_id',
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
        'request_id' => 'string',
    ];
}
