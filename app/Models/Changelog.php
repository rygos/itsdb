<?php

namespace App\Models;

use App\Models\Base\Changelog as BaseChangelog;

class Changelog extends BaseChangelog
{
	protected $fillable = [
		'version_id',
		'type',
		'description'
	];
}
