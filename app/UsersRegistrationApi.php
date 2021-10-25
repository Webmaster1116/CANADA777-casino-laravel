<?php 
namespace VanguardLTE
{
    class UsersRegistrationApi extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'user_registration_api';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'user_id', 
            'password', 
            'username', 
            'currency'
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
