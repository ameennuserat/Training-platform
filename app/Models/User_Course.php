<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Course extends Model
{
    use HasFactory;
    public $table = 'user__courses';
    public $fillable = ['user_id','views','finish','course_id'];

}
