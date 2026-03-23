<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    protected $table = 'vacations';

    protected $casts = [
        'user_id' => 'int',
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'days',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
