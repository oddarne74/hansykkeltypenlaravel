<?php

namespace WebStats\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebStatHit extends Model
{
    protected $table = 'web_stat_hits';

    protected $guarded = [];

    public function webStat(): BelongsTo
    {
        return $this->belongsTo(WebStat::class);
    }
}
