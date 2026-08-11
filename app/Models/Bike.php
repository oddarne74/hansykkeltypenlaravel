<?php

namespace App\Models;

use App\Enums\BikeStatus;
use App\Mail\BikeAvailable;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Bike extends Model
{
    use HasFactory;

    public const FEATURED_BIKES_CACHE_KEY = 'home.featured_bikes';

    private bool $wasEligibleForPromotion = false;

    protected $fillable = ['name', 'slug', 'brand', 'model', 'type', 'price', 'status', 'size', 'rider_height', 'wheel_size', 'gears', 'frame', 'brakes', 'color', 'year', 'description', 'condition_notes', 'featured', 'published_at'];

    protected static function booted(): void
    {
        // Remove image files from storage before the bike (and its images, via DB cascade) is deleted.
        static::deleting(function (Bike $bike): void {
            foreach ($bike->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        });

        static::created(function (Bike $bike): void {
            if ($bike->qualifiesForPromotionFromAttributes($bike->getAttributes())) {
                Cache::forget(self::FEATURED_BIKES_CACHE_KEY);
            }
        });

        static::updating(function (Bike $bike): void {
            $bike->wasEligibleForPromotion = $bike->qualifiesForPromotionFromAttributes($bike->getOriginal());
        });

        static::updated(function (Bike $bike): void {
            $wasEligible = $bike->wasEligibleForPromotion;
            $isEligible = $bike->qualifiesForPromotionFromAttributes($bike->getAttributes());

            if ($wasEligible !== $isEligible) {
                Cache::forget(self::FEATURED_BIKES_CACHE_KEY);
            }

            if ($bike->wasChanged('status') && $bike->status === BikeStatus::FOR_SALE && $bike->getOriginal('status') === BikeStatus::RESERVED) {
                foreach ($bike->interests as $interest) {
                    Mail::to($interest->email)->queue(new BikeAvailable($bike));
                    $interest->delete();
                }
            }
        });

        static::deleted(function (Bike $bike): void {
            if ($bike->qualifiesForPromotionFromAttributes($bike->getAttributes())) {
                Cache::forget(self::FEATURED_BIKES_CACHE_KEY);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function qualifiesForPromotionFromAttributes(array $attributes): bool
    {
        $isFeatured = (bool) ($attributes['featured'] ?? false);
        $status = $attributes['status'] ?? null;
        $publishedAt = $attributes['published_at'] ?? null;

        if ($status instanceof BikeStatus) {
            $status = $status->value;
        }

        if (! $isFeatured) {
            return false;
        }

        if ($status !== BikeStatus::FOR_SALE->value) {
            return false;
        }

        if ($publishedAt === null) {
            return false;
        }

        $publishedAtDate = match (true) {
            $publishedAt instanceof DateTimeInterface => CarbonImmutable::instance($publishedAt),
            default => CarbonImmutable::parse((string) $publishedAt),
        };

        return $publishedAtDate->lte(now());
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
