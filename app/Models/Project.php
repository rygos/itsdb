<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Project extends Model
{
	protected $table = 'projects';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'dynamics_id',
		'name',
		'customer_id',
		'user_id',
		'status_id'
	];

    public function customer(){
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function status(){
        return $this->hasOne('App\Models\Status', 'id', 'status_id');
    }

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

}
