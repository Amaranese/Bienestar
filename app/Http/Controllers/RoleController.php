<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public satatic function getRole()
    {
        $role = new Role();
        $users = User::all();
        foreach ($$users as $key => $user) 
        {
            if ($user->role_id == 1)
            {
                return $role->name = 'administrador';
            }else
            {
                return $role->name = 'guest';
            }
        }
    }
}