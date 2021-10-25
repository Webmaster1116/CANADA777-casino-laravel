<?php 
namespace VanguardLTE
{
    class BonusLog extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'bonus_log';
        protected $fillable = [
            'user_id',
            'deposit_num',
            'deposit',
            'bonus',
            'wager_time',
            'wager',
            'wager_played'
        ];
        public static function boot()
        {
            parent::boot();
        }
    }

}
