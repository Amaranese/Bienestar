<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class CreateApp extends Model
{
	protected $table = "createapp";
	protected $fillable = ['date', 'app', 'opens','closes', 'latitude', 'longitude'];
	public function users()
    {
    	return $this->belongsTo('\App\User');
    }
}
