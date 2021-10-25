<?php 
namespace VanguardLTE
{
    class WelcomePackageLog extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'welcomepackage_log';
        protected $fillable = [
            'user_id',
            'day',
            'freespin',
            'remain_freespin',
            'game_id',
            'max_bonus',
            'win',
            'wager',
            'wager_played',
            'started_at'
        ];
        public static function boot()
        {
            parent::boot();
        }
    }

}
