<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialization;
use App\Models\User;
use Validator;
use Auth;
class SpecializationController extends Controller
{


    public function addspecial(Request $request){


        $a = Auth::id();
        $d = User::find($a);
        if($d->type != 'admin'){
         return response()->json(['sorry you cantnot acsses to this function '],404);
        }



        else {

        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'name' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

    $photo = $request->image->getClientOriginalExtension();
    $image_name = time().'.'.$photo;
    $path = 'images/special';
    $request->image->move($path,$image_name);

   $c = Specialization::create([

            'name'=>$request->name,
            'image'=>$image_name,

        ]);

        return response()->json('succssful',200);

    }
    // $photo = $request->photo->getClientOriginalExtension();
    // $photo_name = time().'.'.$photo;
    // $path = 'images/expert';
    // $request->photo->move($path,$photo_name);
}

public function getallspecil(){

    $a = Specialization::all();

    return response()->json($a);

}
}
