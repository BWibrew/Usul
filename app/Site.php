<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $casts = [
        'namespaces' => 'array',
    ];
}
