<?php 
namespace VanguardLTE
{
    class Bonus extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'bonus';
        protected $fillable = [
            'deposit_num', 
            'deposit', 
            'bonus'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
