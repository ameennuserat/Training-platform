<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Contracts\Encryption\DecryptException;
use App\Models\User;
use App\Models\Video;
use App\Models\Course;
use App\Models\user_video;
use App\Models\User_Course;
use App\Models\ResetCodePassword;
use App\Mail\SendCodeConfirmation;
use App\Mail\SendCodeResetPassword;
use App\Mail\SendFinishCourse;
use Illuminate\Support\Facades\Mail;
use Validator;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','fnishcourse','__invoke','register','forgetpassword','checkcode','resetpassword','updatephon','UpdateCertificateHolder']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'certificate_holder' => 'required',
            'phone_number' => 'required',
        ]);



        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create([
            'email'=> $request->email,
            'code'=> $data['code']
        ]);

        // Send email to user
        //Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));
        Mail::to($request->email)->send(new SendCodeConfirmation($codeData->code));
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'certificate_holder' => $request->certificate_holder,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'type'=>$request->type,
        ]);
        //return response(null, trans('passwords.sent'), 200);
        return response(['message' => trans('code.sent')], 200);

        // $token = Auth::login($user);
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'User created successfully',
        //     'user' => $user,
        //     'authorisation' => [
        //         'token' => $token,
        //         'type' => 'bearer',
        //     ]
        // ]);
    }



    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function me()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function oldepassword(Request $request){

        $validator = Validator::make($request->all(), [
            'password' =>'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = Auth::id();
        $pass = User::find($id);
        // $oldpass = Crypt::decrypt($pass->password);
        // $newpass = bcrypt($request->password);


        if (Hash::check($request->password,$pass->password)) {
            return response()->json([
                'message'=>'password is true'
            ]);
        } else {
            return response()->json([
                'message'=>'password is false'
            ],400);

        }

    }

    public function updatepassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' =>'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::find(Auth::id());
        $newpas = bcrypt($request->password);

        $user->password = $newpas;
        $user->save();
        return response()->json([
            'Password changed successfully'
        ],200);

    }

    public function forgetpassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return response(['message' => trans('passwords.sent')], 200);
    }




    public function checkcode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        
        if ($passwordReset->created_at > now()->addMinute()) {
            $passwordReset->delete();
            return response(['message' => trans('Confirmation.code_is_expire')], 422);
        }

        $passwordReset->delete();

        return response(200);
    }


    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => trans('passwords.code_is_valid')
        ], 200);
    }



    public function resetpassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        // find user's email
        $user = User::firstWhere('email', $passwordReset->email);

        // update user password
        $newpass = bcrypt($request->password);
        $user->password = $newpass;
        $user->save();

        // delete current code
        $passwordReset->delete();

        return response(['message' =>'password has been successfully reset'], 200);
    }

    public function updatephon(Request $request)
{
    $request->validate([
        'phone_number' => 'required',
    ]);
    $user = User::find(Auth::id());
    $user->phone_number = $request->phone_number;
    $user->save();
    return response()->json([
        'The number has been changed successfully'
    ],200);
}

public function UpdateCertificateHolder(Request $request){

    $request->validate([
        'certificate_holder' => 'required|string',
    ]);

    $user = User::find(Auth::id());
    $user->certificate_holder = $request->certificate_holder;
    $user->save();
    return response()->json([
        'The certificate_holder has been changed successfully'
    ],200);

}

public function addvideo(Request $request,$id){


        $idu = Auth::id();
        $user = User::find($idu);
        if($user->type != 'admin'){

            return response()->json(['sorry you cantnot acsses to this function '],404);
        }

        else {
           // $a = Course::find($id);
            foreach($request->url as $key=>$val){

                $row =[
                    'url'=>$request->url[$key],
                    'description'=>$request->description[$key],
                    'course_id'=>$id
                ];

                Video::insert($row);


                    }
                    return response()->json('successfully');
        }


}


public function watching(Request $request,$id){

    $watch = user_video::all();
    $user_id = Auth::id();
foreach($watch as $re){


    if($re->user_id==$user_id&&$re->video_id==$request->video_id){
        return;
    }

}

    $video = Video::find($id);
    $course_id = $video->course->id;
    $course = Course::find($course_id);
    $user_couser = User_Course::all()->where('user_id',$user_id)->where('course_id',$course_id)->first();
    $user_couser->views = $user_couser->views+1;
    if($user_couser->views==$course->videos){
        $user_couser->finish = true;
        $user = User::find(Auth::id());
        $admin = User::find(1);
        $message ='The subscriber:'.' '.$user->name.', '.'completed the:'.' '. $course->name .' '.'course,'.'   his email:  '.$user->email.',      his phone:  '.$user->phone_number;


          Mail::to($admin->email)->send(new SendFinishCourse($message));
        // $message = 'The subscriber Ahmed completed the programming course'
        // Mail::to($request->email)->send(new SendCodeConfirmation($codeData->code));
    }
    $user_couser->save();
    $a = user_video::create([
        'video_id'=>$id,
        'user_id'=>Auth::id()
    ]);

    return response()->json('successfully');
}




}
