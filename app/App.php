protected $table = 'usages';<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
	protected $table = 'apps';
	protected $fillable = ['user_id', 'app_name', 'max_time'];
	{
        return $this->belongsTo('\App\User');
    }
}
