<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;


class AuthController extends BaseController
{
    /***
     * API: Signup API
     * Description: Validate practitioner signup request.
     * If invalid, Return error message.
     * If valid, save user data and Return success message.
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email',
            'password' => 'required|min:6|max:255',
            'confirm_password' => 'required|same:password',
        ]);


        if($validator->fails()){
            return $this->sendError('There are errors in your input data.', $validator->errors());
        }

        $user = User::whereEmail($request->email)->first();
        if($user)
        {
            return $this->sendError('This email address has already been used.');
        }
        

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user['token'] = $user->createToken($request->email)->plainTextToken;
        return $this->sendResponse($user, 'Registration completed successfully');
    }

    /***
     * API: Login API
     * Description: Validate user login request.
     * If invalid, Return error message.
     * If valid, generate session token and Return success message.
     */
   
     public function login(Request $request)
     {
 
         $validator = Validator::make($request->all(), [
             'email' => 'required|exists:users',
             'password' => 'required',
         ],[
             'email.exists' => 'Invalid Email/Password.'
         ]);
 
 
         if($validator->fails()){
             return $this->sendError('There are errors in your input data.', $validator->errors());
         }
 
 
         $input = $request->all();
         $user = User::whereEmail($request->email)->first();
 
         if(!$user)
         {
             return $this->sendError('Invalid Email/Password.');
         }
 
         if(!Hash::check($request->password, $user->password))
         {
             return $this->sendError('Invalid Email/Password.');
         }
 
         $user['token'] =  $user->createToken($user->name.'-AuthToken')->plainTextToken;

 
         return $this->sendResponse($user, 'Logged in successfully.');
     }
 
     public function logout(){
        auth()->user()->tokens()->delete();
    
        return response()->json([
          "message"=>"logged out"
        ]);
    }
    
}
