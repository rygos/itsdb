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
}
