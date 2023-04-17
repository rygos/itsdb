<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Calendar
 * 
 * @property int $id
 * @property Carbon $date_start
 * @property int $hours
 * @property int|null $project_id
 * @property string|null $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $date_end
 *
 * @package App\Models\Base
 */
class Calendar extends Model
{
	protected $table = 'calendar';

	protected $casts = [
		'date_start' => 'datetime',
		'hours' => 'int',
		'project_id' => 'int',
		'date_end' => 'datetime'
	];
}
