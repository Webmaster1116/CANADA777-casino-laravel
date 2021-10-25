<?php 
namespace VanguardLTE
{
    class PasswordReset extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'password_resets';
        protected $fillable = [
            'username',
            'email',
            'token'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
