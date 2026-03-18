<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $table = 'customer_documents';

    protected $casts = [
        'customer_id' => 'int',
        'user_id' => 'int',
        'file_size' => 'int',
    ];

    protected $fillable = [
        'customer_id',
        'user_id',
        'original_name',
        'stored_name',
        'disk',
        'path',
        'description',
        'file_size',
        'mime_type',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFormattedSizeAttribute(): string
    {
        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, $unitIndex === 0 ? 0 : 2, ',', '.').' '.$units[$unitIndex];
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function getFormattedUploadedAtAttribute(): string
    {
        if (!$this->created_at) {
            return '-';
        }

        return $this->created_at->format('d.m.Y H:i');
    }
}
