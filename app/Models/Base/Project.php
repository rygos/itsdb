<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Project
 * 
 * @property int $id
 * @property string $dynamics_id
 * @property string $name
 * @property int $customer_id
 * @property int $user_id
 * @property int $status_id
 * @property int|null $hours
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 *
 * @package App\Models\Base
 */
class Project extends Model
{
	protected $table = 'projects';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'hours' => 'int'
	];
}
