<?php

namespace App\Models;

use App\Models\Base\Server as BaseServer;

class Server extends BaseServer
{
	protected $fillable = [
        'type',
		'servername',
		'fqdn',
		'ext_ip',
		'int_ip',
		'db_sid',
		'db_server',
        'customer_id',
        'user_id',
	];

    public function customer(){
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function composer_rel(){
        return $this->hasMany('App\Models\ServersComposersRel', 'server_id', 'id');
    }

    public function credentials(){
        return $this->belongsToMany('App\Models\Credential', 'credential_server', 'server_id', 'credential_id');
    }
}
