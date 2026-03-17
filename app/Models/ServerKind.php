<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerKind extends Model
{
    protected $fillable = [
        'name',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class, 'server_kind_id', 'id');
    }
}
