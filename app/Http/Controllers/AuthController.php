<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;



class AuthController extends Controller
{

    // =================================================================
    // register
    // =================================================================
    public function register(Request $request) {

        $data = $request->validate([
            'name' => 'required|string',
            'tel' => 'required|string|max:10|min:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        // $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'tel' => $data['tel'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'avatar' => 'https://www.google.co.th/url?sa=i&url=https%3A%2F%2Fwww.sermpisit.com%2Fprofile%2F&psig=AOvVaw3AXNsye8CwOeYEW6RowVpf&ust=1641877791054000&source=images&cd=vfe&ved=0CAsQjRxqFwoTCJDo68a1pvUCFQAAAAAdAAAAABAD',
            'role' => 'user',
        ]);

        $token = $user->createToken('my-device')->plainTextToken;

        $reponse = [
            'user' => $user,
            'token' => $token
        ];

        return response($reponse,201);

    }



    // =================================================================
    // login
    // =================================================================
    public function login(Request $request) {

        $data = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        //check email in db
        $email_password = User::where('email', $data['email'])->first();

        
        if(!$email_password || !Hash::check($data['password'], $email_password->password)) {

            $response = [
                'massage' => 'not email ro password'
            ];

            return response($response);
        }else{

            $token = $email_password->createToken('my-device')->plainTextToken;
    
            $reponse = [
                'user' => $email_password,
                'token' => $token
            ];
    
            return response($reponse,201);
            
        }


    }




    // =================================================================
    // logout
    // =================================================================
     public function logout(Request $request) {
   
        // Get bearer token from the request
        $accessToken = $request->bearerToken();
    
        // Get access token from database
        $token = PersonalAccessToken::findToken($accessToken);

       if(!$token->delete()){
            $msg = 'Something went wrong';
       }else{
            $msg = 'Logout';
       } 

        return [
            'msg' => $msg
        ];
    }


}
