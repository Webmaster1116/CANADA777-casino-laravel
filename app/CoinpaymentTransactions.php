<?php 
namespace VanguardLTE
{
    class CoinpaymentTransactions extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'coinpayment_transactions';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'txn_id', 
            'address', 
            'amount',
            'amountf',
            'coin',
            'confirms_needed',
            'payment_address',
            'qrcode_url',
            'received',
            'receivedf',
            'recv_confirms',
            'status',
            'status_text',
            'status_url',
            'timeout',
            'type',
            'payload',
            'deleted_at'
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
