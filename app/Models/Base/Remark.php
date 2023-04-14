<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Remark
 * 
 * @property int $id
 * @property int $type
 * @property int $relation_id
 * @property string|null $remark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Remark extends Model
{
	protected $table = 'remarks';

	protected $casts = [
		'type' => 'int',
		'relation_id' => 'int'
	];
}
