<?php 
namespace VanguardLTE
{
    class WelcomePackage extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'welcomepackages';
        protected $fillable = [
            'day', 
            'freespin', 
            'game_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
