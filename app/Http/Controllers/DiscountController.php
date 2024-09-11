<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Auth;
use Validator;

class DiscountController extends Controller
{
    public function adddiscount(Request $request,$id){

        $a = Auth::id();
        $user = User::find($a);
        if($user->type != 'admin'){

            return response()->json(['sorry you cantnot acsses to this function '],404);
        }

        else {

            $validat = Validator::make($request->all(),[

                'rate'=>'required',
            ]);

            if($validat->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            else{
                $x = Course::find($id);
                $price = $x->price;
                $new_price = $price - ($price*$request->rate);
                $x->Update([
                    $x->price = $new_price,
                ]);
                $disc  = Discount::create([
                    'rate' => $request->rate,
                    'oldprice' => $price,
                    'newprice' => $new_price,
                    'course_id' =>$id
                ]);
                return response()->json('true');
            }
        }

    }

    public function retreatdiscount(Request $request,$id){

        $course = Course::find($id);
         $dis = $course->discount->oldprice;
        $idd = $course->discount->id;
         $discount = Discount::find($idd)->delete();
        $course->price = $dis;
        $course->save();
        return response()->json('successfully');
    }

    public function coursesdiscount(){
        $courses = DB::table('courses')
            ->join('discounts', 'courses.id', '=', 'discounts.course_id')
            ->select('courses.*','discounts.newprice','discounts.oldprice')
            ->get();
            return response()->json($courses);
    }
}
