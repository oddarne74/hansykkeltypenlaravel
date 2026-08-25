<?php

namespace App\Models;

use App\Contracts\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BikeImage extends Model
{
    protected $fillable = ['path', 'thumbnail_path', 'large_path', 'alt', 'stage', 'sort_order'];

    protected static function booted(): void
    {
        // The uploaded file itself is kept private; generate the public,
        // optimized thumbnail and large variants whenever it is replaced.
        static::saving(function (BikeImage $image): void {
            if ($image->isDirty('path') && $image->path) {
                try {
                    $directory = Str::replaceFirst('originals/', 'images/', dirname($image->path));
                    $variants = app(GeneratesImageVariants::class)->generate($image->path, $directory);

                    $image->thumbnail_path = $variants['thumbnail'];
                    $image->large_path = $variants['large'];
                } catch (Throwable $e) {
                    report($e);
                }
            }
        });

        // Remove the old files from storage when the image file is replaced.
        static::updating(function (BikeImage $image): void {
            if ($image->isDirty('path') && $image->getOriginal('path')) {
                self::deleteFiles($image->getOriginal('path'), $image->getOriginal('thumbnail_path'), $image->getOriginal('large_path'));
            }
        });

        // Remove the files from storage when the image is deleted.
        static::deleting(function (BikeImage $image): void {
            self::deleteFiles($image->path, $image->thumbnail_path, $image->large_path);
        });
    }

    private static function deleteFiles(?string $originalPath, ?string $thumbnailPath, ?string $largePath): void
    {
        if ($originalPath) {
            Storage::disk('local')->delete($originalPath);
            // Legacy images were uploaded straight to the public disk before variants existed.
            Storage::disk('public')->delete($originalPath);
        }

        foreach (array_filter([$thumbnailPath, $largePath]) as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** @return BelongsTo<Bike, $this> */
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }

    /**
     * URL of the optimized thumbnail, falling back to the legacy path for images uploaded before variants existed.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return asset('storage/'.($this->thumbnail_path ?? $this->path));
    }

    /**
     * URL of the optimized large version, falling back to the legacy path for images uploaded before variants existed.
     */
    public function getLargeUrlAttribute(): string
    {
        return asset('storage/'.($this->large_path ?? $this->path));
    }
}
