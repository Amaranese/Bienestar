<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RicardoController extends Controller
{
    public function login()
    {
        $input = $request->all();
        var_dump($input);
        $jsonString = $input[0];
        var_dump($input);
        
  		echo "hoal";
        
        if (empty($request->password) || empty($request->email)) {
            return response()->json([
                'MESSAGE' => 'Some fields are null',
                'e' => $request->input('email')], 400
            );
        }

        $user = User::where('email', $request->email)->first();
        if (empty($request->email) || empty($request->password)) {
            return response()->json([
                'MESSAGE' => 'Some fields are empty'], 400
            );
        }
        if (!is_null($user)) 
        {
            if (decrypt($user->password) != $request->password) {
                return response()->json([
                    'MESSAGE' => 'Wrong password'], 400
                );
            }
            $tokenParams = [
                'id' => $user->id,
                'password' => $user->password,
                'email' => $user->email,
            ];
            $token = JWT::encode($tokenParams, $this->key);
            return response()->json([
                'MESSAGE' => $token,
            ]);
        }else 
        {
            return response()->json([
                'MESSAGE' => 'Wrong email'], 400
            );
        }
    }
}
