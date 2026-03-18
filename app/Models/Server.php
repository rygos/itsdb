<?php

namespace App\Models;

use App\Models\Base\Server as BaseServer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends BaseServer
{
	protected $fillable = [
        'type',
        'server_kind_id',
        'operating_system_id',
		'servername',
		'fqdn',
		'ext_ip',
		'int_ip',
		'db_sid',
		'db_server',
        'customer_id',
        'user_id',
	];

    public function customer(): BelongsTo
    {
        // Servers are assigned to exactly one customer through customer_id.
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function composer_rel(): HasMany
    {
        return $this->hasMany(ServersComposersRel::class, 'server_id');
    }

    public function credentials(): BelongsToMany
    {
        return $this->belongsToMany(Credential::class, 'credential_server', 'server_id', 'credential_id');
    }

    public function serverKind(): BelongsTo
    {
        return $this->belongsTo(ServerKind::class, 'server_kind_id');
    }

    public function operatingSystem(): BelongsTo
    {
        return $this->belongsTo(OperatingSystem::class, 'operating_system_id');
    }
}
