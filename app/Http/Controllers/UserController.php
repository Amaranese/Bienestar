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
        //
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
        if (empty($request->name) || empty($request->password) || empty($request->email)) {
            return response()->json([
                'MESSAGE' => 'Some fields are null'], 400
            );
        }else{
            $user = new User();
            $user->name = str_replace(' ', '', $request->name);
            $repeatedEmail = User::where('email', $request->email)->first();

            if ($repeatedEmail != true) {
                if (!strpos($request->email, "@") || !strpos($request->email, ".")) 
                {
                    return response()->json([
                        'MESSAGE' => 'The email has not been written correctly'], 406
                    );
                }else{
                    $user->email = $request->email;
                }
            }else{
                return response()->json([
                    'MESSAGE' => 'The email is in use'], 400
                );
            }
            if (strlen($request->password) > 7)
            {
                $user->password = encrypt($request->password);
            }else{
                return response()->json([
                    'MESSAGE' => 'The password must have more than seven characters'], 400
                );
            }
            $user->role_id = 2;
            $user->save();
            $tokenParams = [
                'id' => $user->id,
                'password' => $user->password,
                'email' => $user->email,
            ];
            $token = JWT::encode($tokenParams, $this->key);
            return response()->json([
                'MESSAGE' => $token, 'The user has been created correctly'], 200
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
    public function post_recover(Request $request)
    {
        if (!isset($_POST['email']))
        {
            return response()->json([
                'MESSAGE' => 'Please enter your email address'], 403
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
                Mail::send('emails.welcome', $dataEmail, function($message){
                    $emailRecipient = $_POST['email'];
                    $message->from('bienestardigital@gmail.com', 'Password recovery');
                    $message->to($emailRecipient)->subject('Recover password');
                });
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
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length)
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
       $user->delete();
    }
}