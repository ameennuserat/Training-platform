<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User_Course;
use App\Models\User;
use App\Models\Specialization;
use Validator;
use Auth;
class CoursesController extends Controller
{
    public function addcourse(Request $request){

       $a = Auth::id();
       $c = User::find($a);
       if($c->type != 'admin'){

        return response()->json(['sorry you cantnot acsses to this function '],404);
       }

       else{

        $course = Validator::make($request->all(),[

            'image' => 'required',
            'name' => 'required',
            'skill' => 'required',
            'axes' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'specializ_id'=>'required',
            'videos'=>'required'


        ]);

        if($course->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

    $photo = $request->image->getClientOriginalExtension();
    $image_name = time().'.'.$photo;
    $path = 'images/course';
    $request->image->move($path,$image_name);


        $create = Course::create([
            'image' => $image_name,
            'name' => $request->name,
            'skill' => $request->skill,
            'axes' => $request->axes,
            'duration' => $request->duration,
            'price' => $request->price,
            'specializ_id'=>$request->specializ_id,
            'videos'=>$request->videos,
            'participants'=>0
        ]);
        return response()->json('successful',200);
    }
    }

    public function getallcourse(){

        $a = Course::all();
        return response()->json($a);

    }

    public function registcourse(Request $request,$id){
        $a = User_Course::create([
            'user_id'=>Auth::id(),
            'course_id'=>$id,
            'views'=>0
        ]);

        $course = Course::find($id);
        $course->participants = $course->participants+1;
        $course->save();

        return response()->json(['message'=>'true'],200);
    }



    public function searchcourse(Request $request){

        $validate = Validator::make($request->all(),[

            'name'=>'required'
        ]);

        $courses = Course::where('name',$request->name)->get();
        return response()->json($courses);

    }




    public function Archives(Request $request){

        $id_user = Auth::id();
        $user = User::find($id_user);

        $course_user = User_Course::all()->where('user_id',$id_user);
        $array = array();
        $i=0;
        foreach($course_user as $re){
             $watching = $re->views;
             $course = Course::find($re->course_id);
             $videos = $course->videos;
             $div = ($watching*100)/$videos;
             $rate = $div.'%';
             if($div==100){
                $array[$i] = [
                    'message'=>'the course has been successfully completed',
                    'rate'=> $rate,
                     'course'=> $course
                 ];

             }
             else{
            $array[$i] = [
               'rate'=> $rate,
                'course'=> $course
            ];
        }
            $i++;
        }
        return response()->json($array);
    }


    public function getvideo($id){
        $course = Course::find($id);
        $video = $course->videos()->get();
        return response()->json($video);
    }

    public function getcourse($id){

        $a = Specialization::find($id);
        $c = $a->course;
        return response()->json($c);
    }
}
