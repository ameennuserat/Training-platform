<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_video extends Model
{
    use HasFactory;
    public $table = 'user_videos';
    public $fillable = ['video_id','user_id'];
    
}
