<?php

namespace App\Models;

use App\Models\Base\ComposerContainerRel as BaseComposerContainerRel;

class ComposerContainerRel extends BaseComposerContainerRel
{
	protected $fillable = [
		'composer_id',
		'container_id'
	];

    public function composer(){
        return $this->hasOne('App\Models\Composer', 'id', 'composer_id');
    }

    public function container(){
        return $this->hasOne('App\Models\Container', 'id', 'container_id');
    }
}
