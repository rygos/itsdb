<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerImportAlias extends Model
{
    protected $fillable = [
        'source_name',
        'container_id',
        'ignore_on_import',
    ];

    protected $casts = [
        'ignore_on_import' => 'bool',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }
}
