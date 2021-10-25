<?php 
namespace VanguardLTE
{
    class UserGamesLog extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'user_games_log';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'user_id', 
            'session_id', 
            'game_id',
            'transaction_id', 
            'amount', 
            'no_money_left', 
            'there_was_money', 
            'remote_id', 
            'provider', 
            'original_session_id', 
            'action',
            'status'
        ];
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
    }

}
