<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    public const TYPE_VACATION = 'urlaub';
    public const TYPE_SICKNESS = 'krankheit';
    public const TYPE_COMP_TIME = 'ueberstundenfrei';
    public const PORTION_FULL = 'full';
    public const PORTION_HALF = 'half';

    protected $table = 'vacations';

    protected $casts = [
        'user_id' => 'int',
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'int',
        'day_units' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'start_day_portion',
        'end_day_portion',
        'days',
        'day_units',
    ];

    protected $appends = [
        'display_days',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_VACATION => 'Urlaub',
            self::TYPE_SICKNESS => 'Krankheit',
            self::TYPE_COMP_TIME => 'Ueberstundenfrei',
        ];
    }

    public static function portionOptions(): array
    {
        return [
            self::PORTION_FULL => 'Ganzer Tag',
            self::PORTION_HALF => 'Halber Tag',
        ];
    }

    public function getDisplayDaysAttribute(): string
    {
        $units = (int) ($this->day_units ?? 0);
        if ($units % 2 === 0) {
            return (string) (int) ($units / 2);
        }

        return number_format($units / 2, 1, '.', '');
    }
}
