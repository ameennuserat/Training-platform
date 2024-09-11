<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';
    public $fillable = ['rate','oldprice','newprice','course_id'];

    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }
}
