<?php

namespace App\Models;

use App\Models\Base\ServersComposersRel as BaseServersComposersRel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServersComposersRel extends BaseServersComposersRel
{
	protected $fillable = [
		'composer_id',
		'server_id'
	];

    public function composer(): BelongsTo
    {
        return $this->belongsTo(Composer::class, 'composer_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }
}
