<?php

namespace App\Models;

use App\Models\Base\Calendar as BaseCalendar;

class Calendar extends BaseCalendar
{
	protected $fillable = [
		'date_start',
		'hours',
		'project_id',
		'title',
		'description',
		'date_end'
	];
}
