<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContactRequest extends Model { protected $fillable=['name','contact','subject','message','consent','ip_hash']; protected function casts():array{return ['consent'=>'boolean'];} }
