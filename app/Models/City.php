<?php

namespace App\Models;

use App\Models\Base\City as BaseCity;

class City extends BaseCity
{
	protected $fillable = [
		'name',
		'country_code'
	];
}
