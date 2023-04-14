<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerContact
 * 
 * @property int $id
 * @property int $customer_id
 * @property string|null $prefix
 * @property string|null $name
 * @property string $familyname
 * @property string|null $phone_mobile
 * @property string|null $phone_office
 * @property string|null $email
 * @property string|null $comments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CustomerContact extends Model
{
	protected $table = 'customer_contacts';

	protected $casts = [
		'customer_id' => 'int'
	];
}
