<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = getallheaders();
        $userParams = JWT::decode($header['Authorization'],$this->key, array('HS256'));
        if ($userParams->id == 1) {
            return User::where('role_id', 2)->get();
        }else{
            return reponse()->json([
                'MESSAGE' => 'Dont have enough permission'], 403
            );
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $key = $this->key;
        $user = new User();
        if (empty($request->name) or empty($request->email) or empty($request->password) or empty($request->confirm_password)) 
        {
            return response()->json([
                'MESSAGE' => 'You should fill all the fields'], 400

            );
        }
        $user->name = str_replace(' ', '', $request->name);
        $user->email = $request->email;
        $users = User::where('email', $request->email)->get();
        foreach ($users as $key => $value) 
        {
            if ($request->email == $value->email) 
            {
                return response()->json([
                    'MESSAGE' => 'The email is in use'], 401
                );
            }
        }
        $user->name = str_replace(' ', '', $request->name);
        if (!strpos($request->email, "@") || !strpos($request->email, ".")) 
        {
            return response()->json([
                'MESSAGE' => 'The email has not been written correctly'], 406
            );
        } else
        {
            $user->email = $request->email;
        }
        if (strlen($request->password) > 7)
        {
            $user->password = encrypt($request->password);
        } else
        {
            return response()->json([
                'MESSAGE' => 'The password must have at least eight characters'], 411
            );
        }
        $user->role_id = 2;
        if ($request->confirm_password == $request->password) 
        {   
            $user->save();
            $userSave = User::where('email', $request->email)->first();
            return response()->json([
                'MESSAGE' => 'The user has been created correctly'
            ]);
        } else
        {
            return response()->json([
                'MESSAGE' => 'The password confirmation must be the same as the password'], 406
            );
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\
     * @return \Illuminate\Http\Response
     */
    public function show(UserController $user)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
       //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)     //$user es el usuario que pasamos por parametro en POSTMAN, {en las rutas} entre corchetes pasamos el id del usuario que queremos borrar
    {
        $header = getallheaders();
        $decoded = JWT::decode($header['Authorization'], $this->key, array('HS256'));
        if ($decoded->role_id == 1) {
            $user->delete();
            return reponse()->json([
                'MESSAGE' => 'The user has been deleted correctly'], 200
            );
        }
        else{
            return response()->json([
                'MESSAGE' => 'Dont have enough permission',], 403
            );
        }
    }
} 