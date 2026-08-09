<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeWorkItem extends Model
{
    protected $fillable = ['title', 'description', 'sort_order'];

    /** @return BelongsTo<Bike, $this> */
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
}
