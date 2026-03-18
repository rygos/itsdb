<?php

namespace App\Models;

use App\Models\Base\ComposerContainerRel as BaseComposerContainerRel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComposerContainerRel extends BaseComposerContainerRel
{
	protected $fillable = [
		'composer_id',
		'container_id'
	];

    public function composer(): BelongsTo
    {
        return $this->belongsTo(Composer::class, 'composer_id');
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container_id');
    }
}
