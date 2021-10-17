<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{

    /**
    * This class is responsible for destroying users login token
    *
    */


    /**
     * 
    * This function revokes a user's token
    *
    * Request: token:user  
    *          
    * Response: json
    */

    public function logout(Request $request)
    {

        try {

            //retrieve user's token and destroy it
            $token = $request->user();//->token();
            $token->revoke();
    
            //return a success maessage to the client
            return response()->json([
                'message' => 'Bye for now.',
            ], 200);

        } catch (\Exception $e) {

            return response()->json([ 'exception' => 'Something went wrong' ], 422);            
            
        }

    }
 
}
