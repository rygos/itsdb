<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingSystem extends Model
{
    protected $fillable = [
        'name',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class, 'operating_system_id', 'id');
    }
}
