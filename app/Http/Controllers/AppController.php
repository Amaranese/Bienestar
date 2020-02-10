<?php

namespace App\Http\Controllers;

use App\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{

    public function read(Request $request) {
        $user_id = $request["user_id"];
        $app_name = $request["app_name"];
        $controls = App::where('user_id', $user_id)->where('app_name', $app_name)->get();
        return response()->json($controls);
    }

    public function update(Request $request) {
    }
}
