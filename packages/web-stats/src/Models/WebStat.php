<?php

namespace WebStats\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebStat extends Model
{
    use HasFactory;

    protected $table = 'web_stats';

    protected $guarded = [];

    public function hits(): HasMany
    {
        return $this->hasMany(WebStatHit::class);
    }

    public function getViewsTodayAttribute(): int
    {
        return $this->hits()->whereDate('created_at', today())->count();
    }

    public function getViewsThisWeekAttribute(): int
    {
        return $this->hits()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
    }

    public function getViewsThisMonthAttribute(): int
    {
        return $this->hits()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    }

    public function getTotalViewsAttribute(): int
    {
        return $this->hits()->count();
    }
}
