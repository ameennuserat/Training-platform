<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;
use App\Models\Specialization;
use App\Models\User;
use App\Models\Video;
class Course extends Model
{
    use HasFactory;
    public $table = 'courses';
    public $fillable = ['name','price','duration','videos','image','skill','participants','axes','specializ_id'];


    public function discount(){
        return $this->hasOne(Discount::class,'course_id');
    }

    public function specialization(){
        return $this->belongsToMany(Specialization::class,'specializ_id');
    }

    public function users(){

        return $this->belongsToMany(User::class,'user_course','user_id','course_id');
     }

     public function videos(){
        return $this->hasMany(Video::class,'course_id');
    }
}
