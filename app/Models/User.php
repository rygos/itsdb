<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const PERMISSION_NONE = 0;
    public const PERMISSION_VISIBLE = 1;
    public const PERMISSION_EDITABLE = 2;
    public const PERMISSION_ADMINISTRATION = 3;
    private const PERMISSION_ALIASES = [
        'visible' => self::PERMISSION_VISIBLE,
        'editable' => self::PERMISSION_EDITABLE,
        'administration' => self::PERMISSION_ADMINISTRATION,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'permission_administration',
        'permission_product_matrix',
        'permission_compose',
        'permission_hours',
        'permission_customers',
        'permission_projects',
        'permission_calendar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public static function permissionAreas(): array
    {
        return [
            'administration' => 'Administration',
            'product_matrix' => 'Produktematrix',
            'compose' => 'Compose',
            'hours' => 'Stunden',
            'customers' => 'Customers',
            'projects' => 'Projekte',
            'calendar' => 'Calender',
        ];
    }

    public static function permissionLevels(): array
    {
        return [
            static::PERMISSION_VISIBLE => 'Sichtbar',
            static::PERMISSION_EDITABLE => 'Editierbar',
            static::PERMISSION_ADMINISTRATION => 'Administration',
        ];
    }

    public static function permissionColumn(string $area): string
    {
        return 'permission_' . $area;
    }

    public function permissionLevel(string $area): int
    {
        return (int) ($this->{static::permissionColumn($area)} ?? static::PERMISSION_NONE);
    }

    public function hasPermission(string $area, string|int $level = self::PERMISSION_VISIBLE): bool
    {
        // Accept both numeric permission levels and readable aliases used by middleware/routes.
        $requiredLevel = is_string($level)
            ? static::PERMISSION_ALIASES[$level] ?? static::PERMISSION_VISIBLE
            : (int) $level;

        return $this->permissionLevel($area) >= $requiredLevel;
    }

    public static function resolvePermissionLevel(array|string|null $values): int
    {
        $values = array_filter((array) $values, static fn ($value) => is_numeric($value));

        if (empty($values)) {
            return static::PERMISSION_NONE;
        }

        return max(array_map('intval', $values));
    }
}
