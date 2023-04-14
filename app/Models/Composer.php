<?php

namespace App\Models;

use App\Models\Base\Composer as BaseComposer;

class Composer extends BaseComposer
{
	protected $fillable = [
		'title',
		'title_alternatives',
		'compose_filename',
		'orig_url',
		'orig_compose',
		'orig_date'
	];

    public function rel(){
        return $this->hasMany('App\Models\ComposerContainerRel', 'composer_id', 'id');
    }

    public function server_rel(){
        return $this->hasMany('App\Models\ServersComposersRel', 'composer_id', 'id');
    }
}
