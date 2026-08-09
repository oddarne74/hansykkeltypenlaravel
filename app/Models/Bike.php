<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Bike extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'brand', 'model', 'type', 'price', 'status', 'size', 'rider_height', 'wheel_size', 'gears', 'frame', 'brakes', 'color', 'year', 'description', 'condition_notes', 'featured', 'published_at'];

    protected static function booted(): void
    {
        // Remove image files from storage before the bike (and its images, via DB cascade) is deleted.
        static::deleting(function (Bike $bike): void {
            foreach ($bike->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }

    protected function casts(): array
    {
        return ['price' => 'integer', 'featured' => 'boolean', 'published_at' => 'datetime'];
    }

    /** @return HasMany<BikeImage, $this> */
    public function images()
    {
        return $this->hasMany(BikeImage::class)->orderBy('sort_order');
    }

    /** @return HasMany<BikeWorkItem, $this> */
    public function workItems()
    {
        return $this->hasMany(BikeWorkItem::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
