<?php

namespace App\Models;

use App\Models\Base\Env as BaseEnv;

class Env extends BaseEnv
{
	protected $fillable = [
		'server_id',
		'key',
		'value',
		'needed'
	];
}
