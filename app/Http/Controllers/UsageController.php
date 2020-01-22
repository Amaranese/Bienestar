<?php

namespace App\Http\Controllers;

use App\Usage;
use Illuminate\Http\Request;

class UsageController extends Controller
{

    public function registration(Request $request) {
        $user_id = $request["user_id"];
        $rows = $request["usages"];
        foreach ($rows as $row) {
            $usage = new Usage();
            $usage->user_id = $user_id;
            $usage->date = $row["date"];
            $usage->app = $row["app"];
            $usage->event = $row["event"];
            $usage->latitude = $row["latitude"];
            $usage->longitude = $row["longitude"];
            $usage->save();
        }

        return response()->json([
                'MESSAGE' => 'The usages has been created correctly'
            ]);
    }
   
}
