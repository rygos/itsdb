<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
 *
 * @package App\Models
 */
class Customer extends Model
{
	protected $table = 'customers';

	protected $casts = [
		'user_id' => 'int',
		'short_no' => 'int'
	];

	protected $fillable = [
		'user_id',
		'short_no',
		'sap_no',
		'dynamics_no',
		'name'
	];

    public function city(){
        return $this->hasOne('App\Models\City','id', 'city_id');
    }

    public function projects(){
        return $this->hasMany('App\Models\Project', 'customer_id', 'id');
    }

    public function servers(){
        return $this->hasMany('App\Models\Server', 'customer_id', 'id');
    }

    public function credentials(){
        return $this->hasMany('App\Models\Credential', 'customer_id', 'id');
    }

    public function remark(){
        return $this->hasOne('App\Models\Remark', 'customer_id', 'id');
    }

    public function contacts(){
        return $this->hasMany('App\Models\CustomerContact', 'customer_id', 'id');
    }
}
