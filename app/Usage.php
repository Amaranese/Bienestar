<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    protected $table = 'usages';
    protected $fillable = ['date', 'app', 'event', 'latitude', 'longitude', 'user_id'];
    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
