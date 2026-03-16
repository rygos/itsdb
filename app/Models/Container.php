<?php

namespace App\Models;

use App\Models\Base\Container as BaseContainer;

class Container extends BaseContainer
{
	protected $fillable = [
		'title',
		'content',
		'content_orig',
		'content_orig_date'
	];

    public function rel(){
        return $this->hasMany('App\Models\ComposerContainerRel', 'container_id', 'id');
    }

    public function productMatrices()
    {
        return $this->belongsToMany(ProductMatrix::class, 'container_product_matrix')
            ->withTimestamps();
    }
}
