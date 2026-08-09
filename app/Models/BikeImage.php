<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BikeImage extends Model { protected $fillable=['path','alt','stage','sort_order']; public function bike(){ return $this->belongsTo(Bike::class); } }
