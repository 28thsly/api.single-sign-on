<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class LoginController extends Controller
{

    /**
     * This class is responsible for authenticating Login requests from client
     *
     */


    /**
     * 
    * This function authenticates user's login credentials in 6 steps
    *
    * Request: Username :string  
    *          Password :string
    *
    * Response: Authorization :json
    */

    public function login(Request $request)
    {

        try {
            
            //Step 1: validate requests from user
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);
            
            if($validator->fails())
            {
                return response()->json([ 'errors' => $validator->errors()->all() ], 422);
            }

            //Step 2: find the user using email or phone in the DB
            $user = User::where('email', $request->username)
                            ->orWhere('phone', $request->username)
                            ->first();
                            
            //Step 3: confirm the user exists                
            if( isset($user) )
            {

                //Step 4: check for Admin permission/restricted on the account
                if( $user->permission != 'GRANTED')

                    return response()->json([
                        'message' => 'There\'s a restriction on this account. Please contact Admin.',
                    ], 401);


                //Step 5: check for password match and authorize the user  
                if( Hash::check($request->password, $user->password) ) 
                {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                    return response()->json([
                        'message' => 'Authentication successful',
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'token' => $token,
                    ], 200);

                }

                
            }
            
            //Step 6: return default unauthorized response
            return response()->json([
                'message' => 'Incorrect username/password'
            ], 401);
            

        } catch (\Exception $e) {

            return response()->json([ 'exception' => 'Something went wrong' ], 422);
            
        }

    }


}
