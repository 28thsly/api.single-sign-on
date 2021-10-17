<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\User;

class RegisterController extends Controller
{

    /**
    * This class is responsible for creating new authorized users for the system
    *
    */


    /**
     * 
    * This function creates a new user's login credential in 6 steps
    *
    * Request: Name :string  
    *          Email: string
    *          Phone Number: string
    *          Password :string
    *          Role: string
    *
    * Response: User :json
    */

    public function create_user(Request $request)
    {

        try {
            
            //Step 1: validate requests from user
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:6',
                'role' => 'required|string|max:15'
            ]);
            
            if($validator->fails())
            {
                return response()->json([ 'errors' => $validator->errors()->all() ], 422);
            }

            //Step 2: hash user's password and create a remember token
            $request['password'] = Hash::make($request['password']);
            $request['remember_token'] = Str::random(10);

            //Step 3: create user record in the DB, and authorize the user
            $user = User::create($request->toArray());
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            //Step 4: return success reponse 
            return response()->json([
                'message' => 'User created successfully',
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([ 'exception' => 'Something went wrong' ], 422);
                   
        }

    }
 
}
