<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\User;
class Video extends Model
{
    use HasFactory;
    public $table = 'Videos';
    public $fillable = ['description','url','course_id'];


    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }

    public function users(){

        return $this->belongsToMany(User::class,'user_video','user_id','video_id');
     }

}
