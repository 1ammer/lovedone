<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Routing\Controller as BaseController;
use App\Rules\MatchOldPassword;
use DB;
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user = User::find( $user->id);
        $user->tokens()->delete();
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $user->tokens()->delete();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['user'] =  $user;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required' ],
            'new_password' => ['required'],
            'match_password' => ['required','same:new_password'],
        ]);

         if(!Hash::check($request->current_password,$request->user()->password)){
             return $this->sendError('error.', ['error'=>'The Old password not match']);
            }


        DB::beginTransaction();

        try{

           $user= User::find($request->user()->id);
            $user->update(['password'=> \Hash::make($request->new_password)]);
            $user->save();
            DB::commit();
            return $this->sendResponse('', 'Password Updated successfully.');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError('error.', ['error'=>'There is something went wrong, please try again.']);
        }

    }
    public function forgotPassword(Request $request){

    }
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}
