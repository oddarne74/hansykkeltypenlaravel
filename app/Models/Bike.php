<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Bike extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','brand','model','type','price','status','size','rider_height','wheel_size','gears','frame','brakes','color','year','description','condition_notes','featured','published_at'];
    protected function casts(): array { return ['price'=>'integer','featured'=>'boolean','published_at'=>'datetime']; }
    public function images() { return $this->hasMany(BikeImage::class)->orderBy('sort_order'); }
    public function workItems() { return $this->hasMany(BikeWorkItem::class)->orderBy('sort_order'); }
    public function scopePublished($query) { return $query->whereNotNull('published_at')->where('published_at','<=',now()); }
}
