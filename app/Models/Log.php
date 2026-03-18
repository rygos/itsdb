<?php

namespace App\Models;

use App\Models\Base\Log as BaseLog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends BaseLog
{
	protected $fillable = [
		'user_id',
		'section',
		'type',
		'msg',
        'content_id',
	];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer(): BelongsTo
    {
        // Customer-related logs store the customer id in content_id.
        return $this->belongsTo(Customer::class, 'content_id');
    }
}
