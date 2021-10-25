<?php

namespace VanguardLTE;

use Illuminate\Database\Eloquent\Model;

class Freespinround extends Model
{
    //
    protected $table = 'freespinround';
    protected $hidden = [
        'created_at', 
        'updated_at'
    ];
    protected $fillable = [
        'title', 
        'players', 
        'games', 
        'apigames',
        'free_rounds', 
        'bet_type', 
        'valid_from', 
        'valid_to',
        'notify',
        'active'
    ];
    public static function boot()
    {
        parent::boot();
    }
}
