<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * 
 * @property int $id
 * @property int $user_id
 * @property string $section
 * @property string $type
 * @property string $msg
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Log extends Model
{
	protected $table = 'Logs';

	protected $casts = [
		'user_id' => 'int'
	];
}
