<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Env
 * 
 * @property int $id
 * @property int $server_id
 * @property string $key
 * @property string $value
 * @property int $needed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Env extends Model
{
	protected $table = 'envs';

	protected $casts = [
		'server_id' => 'int',
		'needed' => 'int'
	];
}
