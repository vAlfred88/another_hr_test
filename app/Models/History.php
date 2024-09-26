<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @mixin Builder
 */
class History extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false; // Disable auto-incrementing

    protected $keyType = 'string'; // Set the key type to string (for UUID)

    protected $fillable = ['model_id', 'model_name', 'before', 'after', 'action'];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }
}
