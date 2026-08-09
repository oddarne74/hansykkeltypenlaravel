<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BikeWorkItem extends Model { protected $fillable=['title','description','sort_order']; public function bike(){ return $this->belongsTo(Bike::class); } }
