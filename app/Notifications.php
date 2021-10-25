<?php

namespace VanguardLTE;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    //
    protected $table = 'notifications';
    protected $hidden = [
        'created_at', 
        'updated_at'
    ];
    protected $fillable = [
        'message', 
        'image', 
        'campaign', 
        'notify_date',
        'notify_time', 
        'timezone', 
        'frequency', 
        'active'
    ];
    public static function boot()
    {
        parent::boot();
    }
}
