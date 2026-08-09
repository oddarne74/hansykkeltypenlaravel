<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnavailableWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_start',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date:Y-m-d',
        ];
    }
}
