<?php

namespace App\Models;

use App\Models\Base\Credential as BaseCredential;

class Credential extends BaseCredential
{
	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'customer_id',
		'username',
		'password',
		'type'
	];
}
