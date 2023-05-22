<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;

use App\Http\Traits\HttpResponseTrait;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Google;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{

    //

    use HttpResponseTrait;

        public function redirectToGoogle()
        {
            return Socialite::driver('google')->stateless()->redirect();
        }

        public function handleGoogleCallback($provider)
        {
            $user = Socialite::driver($provider)->stateless()->user();

            // Check if the user already exists in the database
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // Login the existing user
                Auth::login($existingUser);

                // Generate an API token for the user and return it to the client
                $token = $existingUser->createToken('API Token')->plainTextToken;
                return $this->success([
                    'token' => $token,
                    'data'=>$existingUser->email
                           ]);
            } else {
                // Create a new user in the database
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => Hash::make($user->id),
                ]);

                // Save the user's Google data in the database
                Google::create([
                    'user_id' => $newUser->id,
                    'google_id' => $user->id,
                    'access_token' => $user->token,
                    'avatar' => $user->avatar,
                ]);

                // Login the new user
                Auth::login($newUser);

                // Generate an API token for the user and return it to the client
                $token = $newUser->createToken('API Token')->plainTextToken;
                return $this->success([
                    'token' => $token,
                    'data'=>$newUser->email
                ]);
            }
        }



    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if($user){
            $role  = Roles::create([
                'user_id' => $user->id,
                'role' => 'user',
            ]);
        }

        return $this->success([
            'user' => $user,
            'message'=>"user created successfully",
            'token' => $user->createToken('API Token Of' . $user->email)->plainTextToken
        ]);
        // return 'hi';
    }



    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated();

        $credentials = $request->only(['email', 'password']);

        return $this->authenticate($credentials);
    }



    public function logout()
    {
        return  response()->json('logout good');
    }
}
