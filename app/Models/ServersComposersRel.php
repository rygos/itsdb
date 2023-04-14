<?php

namespace App\Models;

use App\Models\Base\ServersComposersRel as BaseServersComposersRel;

class ServersComposersRel extends BaseServersComposersRel
{
	protected $fillable = [
		'composer_id',
		'server_id'
	];

    public function composer(){
        return $this->hasOne('App\Models\Composer', 'id', 'composer_id');
    }

    public function server(){
        return $this->hasOne('App\Models\Server', 'server_id', 'id');
    }
}
