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

    /**
     * Get the site's WordPress version.
     *
     * @return string
     */
    public function getVersionAttribute()
    {
        return app(Wordpress::class)->version($this->root_uri);
    }
}
