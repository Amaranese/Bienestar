<?php
namespace App\Http\Controllers;
use App\App;
use Illuminate\Http\Request;

class AppController extends Controller
{

    public function read(Request $request) {
    	
        $user_id = $request["user_id"];
        $app_name = $request["app_name"];
        $controls = App::where('user_id', $user_id)->where('app_name', $app_name)->get();        
        return response()->json($controls);
    }

    public function update(Request $request) {
    	$user_id = $request["user_id"];
        $app_name = $request["app_name"];
        $max_time = $request["max_time"];
        $control = App::where('user_id', $user_id)->where('app_name', $app_name)->first();   
        if(empty($control)){
        	$nuevoControl = new App();
        	$nuevoControl->user_id = $user_id;
        	$nuevoControl->app_name = $app_name;
        	$nuevoControl->max_time = $max_time;
        	$nuevoControl->save();
        	return response()->json([
                        'MESSAGE' => 'The control has been created correctly'], 200
                    );
        } 
        else {
        	$control->max_time = $max_time;
        	$control->save();
        	return response()->json([
                        'MESSAGE' => 'The control has been updated correctly'], 200
                    );
        }
    }
}
