<?php

namespace App\Models;

use App\Enums\BikeStatus;
use App\Mail\BikeAvailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
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

        static::updated(function (Bike $bike) {
            if ($bike->wasChanged('status') && $bike->status === BikeStatus::FOR_SALE && $bike->getOriginal('status') === BikeStatus::RESERVED) {
                foreach ($bike->interests as $interest) {
                    Mail::to($interest->email)->queue(new BikeAvailable($bike));
                    $interest->delete();
                }
            }
        });
    }

    protected function casts(): array
    {
        return ['price' => 'integer', 'featured' => 'boolean', 'published_at' => 'datetime', 'status' => BikeStatus::class];
    }

    /** @return HasMany<BikeImage, $this> */
    public function images()
    {
        return $this->hasMany(BikeImage::class)->orderBy('sort_order');
    }

    /** @return HasMany<BikeInterest, $this> */
    public function interests()
    {
        return $this->hasMany(BikeInterest::class);
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
