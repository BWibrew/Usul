<?php

namespace App;

use App\ApiConnections\Wordpress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'url',
    ];

    protected $casts = [
        'namespaces' => 'array',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
