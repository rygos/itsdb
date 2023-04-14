<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 * 
 * @property int $id
 * @property int $user_id
 * @property int $short_no
 * @property string $sap_no
 * @property string $dynamics_no
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $city_id
 * @property string|null $intermediate_cert_raw
 * @property string|null $root_cert_raw
 * @property string|null $private_key_raw
 *
 * @package App\Models\Base
 */
class Customer extends Model
{
	protected $table = 'customers';

	protected $casts = [
		'user_id' => 'int',
		'short_no' => 'int',
		'city_id' => 'int'
	];
}
