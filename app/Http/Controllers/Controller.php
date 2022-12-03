<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * Social Login
     */
    public function user(Request $request)
    {

        return response()->json([
            'success' => true,
            'user'=>  $request->user()
        ]);
    }
    /**
     * Social Login
     */
    public function socialFBLogin(Request $request)
    {

        $provider = "facebook"; // or $request->input('provider_name') for multiple providers
        $token = $request->input('access_token');
        // get the provider's user. (In the provider server)
       try {
           $providerUser = Socialite::driver($provider)->userFromToken($token);


       } catch (\Exception $e) {


           return response()->json([
               'success' => False,
               'Message' => 'Invalid token',
               'user'=>  ''
           ]);

       }
        // check if access token exists etc.. 5649336978443110
        // search for a user in our server with the specified provider id and provider name
        $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
        // if there is no record with these data, create a new user
        if($user == null){
            $user = User::create([
                'email' => $providerUser ->email?:'',
                'image' => $providerUser ->avatar,
                'name' => $providerUser ->name,
                'provider_name' => $provider,
                'provider_id' => $providerUser->id,
            ]);
        }
        // create a token for the user, so they can login
        $user->tokens()->delete();
        $token = $user->createToken('MyApp')->plainTextToken;

        // return the token for usage
        return response()->json([
            'success' => true,
            'token' => $token,
            'user'=>  $user
        ]);
    }
    public function socialGoogleLogin(Request $request)
    {

        $provider = "google"; // or $request->input('provider_name') for multiple providers
        $token = $request->input('access_token');
        // get the provider's user. (In the provider server)
        try {
            $providerUser = Socialite::driver($provider)->userFromToken($token);


        } catch (\Exception $e) {


            return response()->json([
                'success' => False,
                'Message' => 'Invalid token',
                'user'=>  ''
            ]);

        }
        // check if access token exists etc.. 5649336978443110
        // search for a user in our server with the specified provider id and provider name
        $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
        // if there is no record with these data, create a new user
        if($user == null){
            $user = User::create([
                'email' => $providerUser->email,
                'image' => $providerUser->avatar,
                'name' => $providerUser->name,
                'provider_name' => $provider,
                'provider_id' => $providerUser->id,
            ]);
        }
        // create a token for the user, so they can login
        $user->tokens()->delete();
        $token = $user->createToken('MyApp')->plainTextToken;
        // return the token for usage
        return response()->json([
            'success' => true,
            'token' => $token,
            'user'=>  $user
        ]);
    }
}
