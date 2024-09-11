<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
class Specialization extends Model
{
    use HasFactory;
    public $fillable=[
    'name',
    'image'];


    public function course(){
        return $this->hasMany(Course::class,'specializ_id');
    }
}
