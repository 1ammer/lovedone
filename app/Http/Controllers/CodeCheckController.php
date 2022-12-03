<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Routing\Controller ;
use App\Rules\MatchOldPassword;
use DB;
use App\Models\ResetCodePassword;
class CodeCheckController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6',
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
        $user->update(['password'=>\Hash::make( $request->password )]);

        // delete current code
        $passwordReset->delete();

        return response(['message' =>'password has been successfully reset'], 200);
    }
}
