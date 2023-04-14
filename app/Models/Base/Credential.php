<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Credential
 * 
 * @property int $id
 * @property int $customer_id
 * @property string $username
 * @property string $password
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 *
 * @package App\Models\Base
 */
class Credential extends Model
{
	protected $table = 'credentials';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int'
	];
}
