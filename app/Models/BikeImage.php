<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BikeImage extends Model
{
    protected $fillable = ['path', 'alt', 'stage', 'sort_order'];

    protected static function booted(): void
    {
        // Remove the old file from storage when the image file is replaced.
        static::updating(function (BikeImage $image): void {
            if ($image->isDirty('path') && $image->getOriginal('path')) {
                Storage::disk('public')->delete($image->getOriginal('path'));
            }
        });
        // Remove the file from storage when the image is deleted.
        static::deleting(function (BikeImage $image): void {
            Storage::disk('public')->delete($image->path);
        });
    }

    /** @return BelongsTo<Bike, $this> */
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
}
