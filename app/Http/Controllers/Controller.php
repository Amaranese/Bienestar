<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \Firebase\JWT\JWT;
use App\User;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $key = '7kvP3yy3b4SGpVz6uSeSBhBEDtGzPb2n';
    protected function isUserLogged($email, $password)
    {
    	$userSave = User::where('email', $email)->first();
    	if ($userSave == null)
    	{
    		return false;
    	}
    	$emailSave = $userSave->email;
    	$passwordSave = $userSave->password;
    	$passwordSave = decrypt($passwordSave);
    	if ($emailSave == $email and $passwordSave == $password)
    	{
    		return true;
    	}
    	return false;
    }
    protected function getUserLogged()
    {
    	$header = getallheaders();
    	if ($header['Authorization'] == null) {
    		return false;
    	}
    	else
    	{
    		return JWT::decode($header['Authorization'], $this->key, array('HS256'));
    	}
    }
    protected function checkAdminUser($userLogged)
    {
    	if (!empty($userLogged) && $userLogged->role_id == 1)
    	{
    		return true;
    	}else
    	{
    		return false;
    	}
    }
    protected function dateConverter($date)
    {
        $unixDate = strtotime($date);
        $newDateFormat = date('Y-m-d H:i:s', $unixDate);
        return $newDateFormat;
    }
    protected function getGlobalPath($image)
    {
        return "" . $image;
    }
    
    protected function recoverPassword($email)
    {
        $userRecover = User::where('email', $email)->first();
        if ($userRecover == null) {
            return false;
        }
        $emailRecover = $userRecover->email;
        if($emailRecover == $email)
        {
            return true;
        }
        return false;
    }
}
