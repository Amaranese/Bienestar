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
    public function show(User $user, Request $request)
    {
        //
    }
    public function post_recover(Request $request)
    {
        if(!isset($_POST['email']))
        {
            return response()->json([
                'MESSAGE' => 'Please enter your email adress'], 403
            );
        }
        $email = $_POST['email'];
        if (self::recoverPassword($email)) {
            $userRecover = User::where('email', $email)->first();
            $id = $userRecover->id;
            //$pwdDB = User::where('email', $userRecover->email)->first()->password;
            $dataEmail = array(
                'newPwd' => $this->randomString(),
            );
            if ($_POST["email"] == $userRecover->email) {
                $userRecover->password = encrypt($dataEmail["newPwd"]);
                $userRecover->save();
                Mail::send('email', $dataEmail, function($message){
                    $emailRecipient = $_POST['email'];
                    $message->from('bienestardigital2019@gmail.com', 'Password recovery');
                    $message->to($emailRecipient)->subject('Recover password');
                });
                mail(to, subject, message)
                return response()->json([
                    "MESSAGE" => "The new password has been sent to your email address " . $email], 200
                );
            }
        }
        else
        {
            return response()->json([
                'MESSAGE' => 'The specified email does not exist'], 403
            );
        }
    }
    private function randomString($length = 30)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
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
    public function update(Request $request, User $user)
    {
        if (empty($request->header('Authorization'))) 
        {
            return response()->json([
                'MESSAGE' => 'Dont have enough permission'
            ]);
        }else
        {
            $userLogged = JWT::decode($request->header('Authorization'), $this->key, array('HS256'));
            if (empty($request->name) && empty($request->email) && empty($request->newPassword))
            {
                return response()->json([
                    'MESSAGE' => 'You have to change at least one field'], 400
                );
            }
            $user->name = $userLogged->name;
            if(empty($request->email))
            {
                $user->email = $userLogged->email;
            }else
            {
                if (!strpos($request->email, "@") || !strpos($request->email, ".")) 
                {
                    return response()->json([
                        'MESSAGE' => 'Wrong email syntax'], 400
                    );
                }
                else
                {
                    $user->email = $request->email;
                    if ($request->email == $userLogged->email) {
                        return response()->json([
                            'MESSAGE' => 'The email must be different from the previous one'], 400
                        );
                    }
                    $user->save();
                    return response()->json([
                        'MESSAGE' => 'The user has been updated correctly'], 200
                    );
                }
            }
            if (empty($request->oldPassword) || empty($request->confirmNewPassword) || empty($request->newPassword)) 
            {
                $user->password = $userLogged->password;
            }else
            {
                if ($userLogged->password != $request->oldPassword) {
                    return response()->json([
                        'MESSAGE' => 'The old password does not match with the new one'], 400
                    );
                }
                if ($request->newPassword != $request->confirmNewPassword)
                {
                    return response()->json([
                        'MESSAGE' => 'The new password does not match with the confirm password'], 400
                    );
                }
                if ($request->newPassword == $userLogged->password) {
                    return response()->json([
                        'MESSAGE' => 'The password must be different from the previous one'], 400
                    );
                }
                if (strlen($request->newPassword) > 7)
                {
                    $user->password = encrypt($request->newPassword);
                    $user->save();
                    return response()->json([
                        'MESSAGE' => 'The user has been updated correctly'], 200
                    );
                } 
                else 
                {
                    return response()->json([   
                        'MESSAGE' => 'The password must have more than seven characters'], 411
                    );
                }
            }            
        }
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