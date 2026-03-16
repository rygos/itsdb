<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMatrix extends Model
{
    protected $fillable = [
        'import_key',
        'position',
        'category',
        'function_name',
        'product',
        'short_description',
        'synonyms',
        'description',
    ];

    public function containers()
    {
        return $this->belongsToMany(Container::class, 'container_product_matrix')
            ->withTimestamps()
            ->orderBy('title');
    }
}
