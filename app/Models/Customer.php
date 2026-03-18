<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
		'name',
        'city_id',
	];

    public function city(): BelongsTo
    {
        // Customer holds the foreign key, so this must be a belongsTo relation.
        return $this->belongsTo(City::class, 'city_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'customer_id');
    }

    public function latestProject(): HasOne
    {
        // latestOfMany expresses the actual intent better than a manual latest() chain.
        return $this->hasOne(Project::class, 'customer_id')->latestOfMany('updated_at');
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class, 'customer_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'customer_id');
    }

    public function remark(): HasOne
    {
        return $this->hasOne(Remark::class, 'customer_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class, 'customer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class, 'customer_id');
    }
}
