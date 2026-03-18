<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 *
 * @package App\Models
 */
class Project extends Model
{
	protected $table = 'projects';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int',
		'hours' => 'int',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
	];

	protected $fillable = [
		'dynamics_id',
		'name',
		'customer_id',
		'user_id',
		'status_id',
		'start_date',
		'end_date',
		'hours',
	];

    public function scopeOwnedBy(Builder $query, ?int $userId): Builder
    {
        // Ownership is queried in multiple dashboards; a scope keeps that filter consistent.
        return $query->where('user_id', $userId);
    }

    public function customer(): BelongsTo
    {
        // The project table stores customer_id, so the inverse side is belongsTo.
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
