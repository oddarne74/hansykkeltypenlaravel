<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type',
        'name',
        'email',
        'phone',
        'message',
        'week_start',
        'wants_pickup',
        'address',
        'status',
        'images',
    ];

    protected static function booted(): void
    {
        // Remove any uploaded images from storage when the request is deleted.
        static::deleting(function (ServiceRequest $serviceRequest): void {
            foreach ($serviceRequest->images ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service_type' => ServiceType::class,
            'week_start' => 'date',
            'wants_pickup' => 'boolean',
            'status' => ServiceStatus::class,
            'images' => 'array',
        ];
    }
}
