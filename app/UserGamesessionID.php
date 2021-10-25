<?php 
namespace VanguardLTE
{
    class UserGamesessionID extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'user_gamesession_id';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'user_id', 
            'session_id', 
            'game_id'
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
