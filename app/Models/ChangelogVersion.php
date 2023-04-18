<?php

namespace App\Models;

use App\Models\Base\ChangelogVersion as BaseChangelogVersion;

class ChangelogVersion extends BaseChangelogVersion
{
	protected $fillable = [
		'version',
		'description',
		'published',
		'published_at'
	];
}
