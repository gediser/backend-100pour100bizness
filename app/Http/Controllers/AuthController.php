<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function updateProfil(Request $request){
        $password = $request->input('password');
        $isPasswordNotFilled = strcmp($password, "") == 0 ? true : false;
        if ($isPasswordNotFilled){ // Pas de mot de passe
            $data = $request->validate([
                'id' => 'required',
                'name' => 'required|string',
                'telephone' => 'required|string',
            ]);
        }else{
            $data = $request->validate([
                'id' => 'required',
                'name' => 'required|string',
                'telephone' => 'required|string',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->mixedCase()->numbers()->symbols()
                ]
            ]);
        }
        

        $user = $request->user();
        if ($user->id !== $data['id']){
            return abort(403, 'Unauthorized action.');
        }


        if ($isPasswordNotFilled){ // On ne met pas a jour le mot de passe
            $user->update([
                'name' => $data['name'],
                'telephone' => $data['telephone'],
            ]);
        }else{
            $user->update([
                'name' => $data['name'],
                'telephone' => $data['telephone'],
                'password' => bcrypt($data['password'])
            ]);
        }
       

        return response([
            'success' => true
        ]);
    }
    public function register(Request $request) {

        $data = $request->validate([
            'name' => 'required|string',
            'telephone' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ]
        ]);
        
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $user->roles()->attach(1); 

        $token = $user->createToken('main')->plainTextToken;
        
        return response([
            'user' => User::with('roles:id,name')->where('id', $user->id)->first(),
            'token' => $token,
        ]);
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => [
                'required',
            ],
            'remember' => 'boolean'
        ]);
        
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember)){
            return response([
                'errors' => [
                    'error' => [
                        'error' => 'The Provided credentials are not correct'
                    ]
                ]
            ], 422);
        }

        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response([
            'user' => User::with('roles:id,name')->where('id', $user->id)->first(),
            'token' => $token
        ]);
    }

    public function logout(){
        /** @var User $user */
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response([
            'success' => true,
        ]);

    }
}
