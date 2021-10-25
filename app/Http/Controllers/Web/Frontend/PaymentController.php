<?php
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Http\Client\Response;
    use Illuminate\Http\Client\RequestException;
    use VanguardLTE\Transaction;

    class PaymentController extends \VanguardLTE\Http\Controllers\Controller
    {
        /* cryptoprocessing real mode */
        private $crypto = "https://app.alphapo.net/api/v2";
        private $api_key = "F96EmCRfFv0v2zaAb8mrr08bUNUd12PC";
        private $secret_key = "8p5GHD6m4vQQMb3G7J9b4o7k0rK7CiDZl6lNsfeouICf1oyvrr3lctA3CGWSFZJQ";
        /* --- */
        /* cryptoprocessing sandbox mode */
        // private $crypto = "https://app.sandbox.cryptoprocessing.com/api/v2";
        // private $api_key = "DB3m25gUiXORTkunbLC2mcdWB4bIEf83";
        // private $secret_key = "q7EC2lxUf4HIHkb3fbcApbY6eHxC4TsX25r4H3EQ8Vp8ZJE9RrpEsvE0wJynm0xj";
        /* --- */
        private function createRequestHeaders($params = [])
        {
            $signature = hash_hmac( "sha512", json_encode($params), $this->secret_key );
            return $signature;
        }

        public function cryptocurrencies_list(\Illuminate\Http\Request $request) {

            $users = \Auth::user();
            $visitorId = $users->visitor_id;
            $multiAccounts = [];
            $multiDeposit = 0;
            $get_address_flag = $request->post('get_address_flag');
            if(\VanguardLTE\User::where(['visitor_id' => $visitorId])->count() > 1 ) {
                $multiAccounts = \VanguardLTE\User::where(['visitor_id' => $visitorId])->get();
                foreach($multiAccounts as $multiAccount) {

                    if($users->id != $multiAccount->id){
                        if(\VanguardLTE\Transaction::where(['user_id' => $multiAccount->id])->count() > 0){
                            $multiDeposit = 1;
                        }else{
                            $multiDeposit = 0;
                        }
                    }
                }
            }

            $amount = $request->post('crypto_deposit_amount');
            $currency_from = $request->post('currency');
            $currency_to = $request->post('currency_to');
            $params = [
                'currency_from' => $currency_to,
                'currency_to' => strtoupper($currency_from)
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->crypto.'/currencies/rates',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                    'X-Processing-Key:'.$this->api_key,
                    'X-Processing-Signature:'.$this->createRequestHeaders($params),
                    'Content-Type: application/json',
                )
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            
            $currency_rate = [];
            $response = json_decode($response, true);
            if (isset($response['erros'])){
                return response(json_encode([
                    'error' => false,
                    'msg' => $response['errors'],
                ]));
            }

            foreach($response['data'] as $val) {
                $currency_rate = [
                    'rate_from' => $amount,
                    'rate_from_currency' => $currency_to,
                    'rate_to' => $val['rate_to'] * $amount,
                    'rate_to_currency' => $currency_from,
                ];
            }

            $params = [
                'visible' => true
            ];

            $curl_list = curl_init();

            curl_setopt_array($curl_list, array(
                CURLOPT_URL => $this->crypto.'/currencies/list',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                    'X-Processing-Key:'.$this->api_key,
                    'X-Processing-Signature:'.$this->createRequestHeaders($params),
                    'Content-Type: application/json',
                )
            ));

            $response_list = curl_exec($curl_list);
            curl_close($curl_list);
            
            $response_list = json_decode($response_list, true);
            if (isset($response_list['erros'])){
                return response(json_encode([
                    'error' => false,
                    'msg' => $response_list['errors'],
                ]));
            }

            foreach($response_list['data'] as $val) {
                if( $val['currency'] == strtoupper($currency_from)){
                    $minimum_amount = $val['minimum_amount'];
                }
            }

            $payment_user = \VanguardLTE\CoinpaymentTransactions::where(['address' => $users->id, 'received'=> $currency_from])->first();
            $address = "";
            if($payment_user){
                $address = $payment_user->payment_address;
            }else {
                if($get_address_flag == "1"){ 
                    $userId = $users->id;
                    $params = [
                        'foreign_id' => $userId,
                        'currency' => $currency_from,
                        'convert_to' => $currency_to
                    ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $this->crypto.'/addresses/take',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($params),
                        CURLOPT_HTTPHEADER => array(
                            'X-Processing-Key:'.$this->api_key,
                            'X-Processing-Signature:'.$this->createRequestHeaders($params),
                            'Content-Type: application/json',
                        )
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);

                    // var_dump($currency_to);exit;
                    $response = json_decode($response, true);

                    if (isset($response['erros'])){
                        return response(json_encode([
                            'error' => false,
                            'msg' => $response['errors'],
                        ]));
                    }

                    foreach($response as $val){
                        $address = $val['address'];
                    }
                }
            }

            return response(json_encode([
                'error' => false,
                'address' => $address,
                'minimum_amount' => $minimum_amount,
                'currency_rate' => $currency_rate,
            ]));
        }
        public function callback_cryptopayment(\Illuminate\Http\Request $request){

            $type = $request->post('type');
            $status = $request->post('status');
            if($status == 'confirmed'){
                if($type == 'deposit_exchange') {

                    $id = $request->post('id');
                    $crypto_address = $request->post('crypto_address');
                    $currency_sent = $request->post('currency_sent');
                    $currency_received = $request->post('currency_received');
                    $transactions = $request->post('transactions');
                    $fees = $request->post('fees');

                    $amount = $currency_received['amount'];
                    $userId = $crypto_address['foreign_id'];

                    $user = \VanguardLTE\User::where('id', $userId)->first();
                    if(!$user){
                        return response()->json(['error' => true, 'msg' => trans('app.wrong_user')], 200);
                    }

                    $txn_exist = \VanguardLTE\CoinpaymentTransactions::where('txn_id', $id)->where('address', $userId)->first();
                    if($txn_exist){
                        if($txn_exist['status'] == "not_confirmed"){
                            $txn_exist->update(['status' => $status, 'status_text' => 'completed']);
                        }else {
                            return response()->json(['error' => false, 'msg' => trans('app.completed_transaction')], 200);
                        }
                    }else {
                        $crytp_address = \VanguardLTE\CoinpaymentTransactions::create([
                            'txn_id'=> $id,
                            'address'=> $userId,
                            'amount'=> $currency_received['amount'],
                            'amountf'=> isset($fees[0]['amount']) ? $fees[0]['amount'] : 0,
                            'coin'=> $currency_sent['amount'],
                            'payment_address'=> $crypto_address['address'],
                            'received'=> isset($currency_sent['currency']) ? $currency_sent['currency'] : 'BTC',
                            'receivedf'=> $currency_received['currency'],
                            'type'=> $type,
                            'status'=> $status,
                            'status_text'=> "completed"
                        ]);
                    }

                    $deposit_count = \VanguardLTE\Transaction::where(['user_id'=>$user->id, 'type'=>'in'])->count();
                    $user->increment('balance', $amount);
                    $user->increment('count_balance', $amount);
                    switch ($deposit_count) {
                        case 1:
                            $welcomepackages = \VanguardLTE\WelcomePackage::leftJoin('games', function ($join)
                                                                {
                                                                    $join->on('games.original_id','=','welcomepackages.game_id');
                                                                    $join->on('games.id','=','games.original_id');
                                                                })->select('welcomepackages.*', 'games.name')->get();
                            foreach ($welcomepackages as $welcomepackage) {
                                $welcomepackagelog = \VanguardLTE\WelcomePackageLog::create([
                                    'user_id' => $userId,
                                    'day' => $welcomepackage->day,
                                    'freespin' => $welcomepackage->freespin,
                                    'remain_freespin' => $welcomepackage->freespin,
                                    'game_id' => $welcomepackage->game_id,
                                    'max_bonus' => 20,
                                    'started_at' => date('Y-m-d', strtotime('+'.($welcomepackage->day-1).' days'))
                                ]);
                            }
                            app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch(new \VanguardLTE\Jobs\GetFreespinJob($user));
                        case 2:
                        case 3:
                            $bonus = \VanguardLTE\Bonus::where('deposit_num', $deposit_count + 1)->first();
                            if ($bonus) {
                                $bonus_amount = $bonus->bonus;
                                if ($amount < $bonus_amount)
                                    $bonus_amount = $amount;
                                if ($amount < 10)
                                    break;
                                $user->increment('balance', $bonus_amount);
                                $user->increment('bonus', $bonus_amount);
                                $user->increment('count_bonus', $bonus_amount);
                                $user->increment('wager', $bonus_amount * 70);

                                \VanguardLTE\BonusLog::create([
                                    'user_id' => $userId,
                                    'deposit_num' => $deposit_count + 1,
                                    'deposit' => $amount,
                                    'bonus' => $bonus_amount,
                                    'wager' => $bonus_amount * 70,
                                    'wager_played' => 0
                                ]);
                            }
                            break;
                    }
                    return response()->json(['error' => false, 'msg' => trans('app.success')], 200);

                }else if($type == 'withdrawal_exchange') {
                    $id = $request->post('id');
                    $crypto_address = $request->post('crypto_address');
                    $currency_sent = $request->post('currency_sent');
                    $currency_received = $request->post('currency_received');
                    $transactions = $request->post('transactions');
                    $fees = $request->post('fees');

                    $amount = $currency_received['amount'];
                    $trans_exist = \VanguardLTE\Transaction::where('transaction', (string)$id)->first();
                    if(!$trans_exist){
                        return response()->json(['error' => true, 'msg' => trans('app.wrong_transaction')], 200);
                    }

                    $txn_exist = \VanguardLTE\CoinpaymentTransactions::where('txn_id', $id)->first();

                    if($txn_exist){
                        if($txn_exist['status'] == "not_confirmed"){
                            $txn_exist->update(['status' => $status, 'status_text' => 'completed']);
                            $trans_exist->update(['status' => '1']);
                            return response()->json(['error' => false, 'msg' => trans('app.success')], 200);
                        }else {
                            
                            return response()->json(['error' => false, 'msg' => trans('app.completed_transaction')], 200);
                        }
                    }else {

                        $crytp_address = \VanguardLTE\CoinpaymentTransactions::create([
                            'txn_id'=> $id,
                            'address'=> $trans_exist->user_id,
                            'amount'=> $currency_sent['amount'],
                            'amountf'=> isset($fees[0]['amount']) ? $fees[0]['amount'] : 0,
                            'coin'=> $currency_received['amount'],
                            'payment_address'=> $crypto_address['address'],
                            'received'=> $currency_sent['currency'],
                            'receivedf'=> isset($currency_received['currency']) ? $currency_received['currency'] : $trans_exist->value,
                            'type'=> $type,
                            'status'=> $status,
                            'status_text'=> "completed"
                        ]);
                        $trans_exist->update(['status' => '1']);
                        return response()->json(['error' => false, 'msg' => trans('app.success')], 200);
                    }
                }else {
                    return response()->json(['error' => ture, 'msg' => trans('app.wrong_transaction')], 200);
                }
            }else if($status == 'not_confirmed') {
                if($type == 'deposit_exchange') {
                    $id = $request->post('id');
                    $crypto_address = $request->post('crypto_address');
                    $currency_sent = $request->post('currency_sent');
                    $currency_received = $request->post('currency_received');
                    $transactions = $request->post('transactions');
                    $fees = $request->post('fees');

                    $amount = $currency_received['amount'];
                    $userId = $crypto_address['foreign_id'];

                    $user = \VanguardLTE\User::where('id', $userId)->first();
                    if(!$user){
                        return response()->json(['error' => true, 'msg' => trans('app.wrong_user')], 200);
                    }

                    $txn_exist = \VanguardLTE\CoinpaymentTransactions::where('txn_id', $id)->where('address', $userId)->first();
                    if($txn_exist){
                        return response()->json(['error' => false, 'msg' => trans('app.completed_transaction')], 200);
                    }else {
                        $crytp_address = \VanguardLTE\CoinpaymentTransactions::create([
                            'txn_id'=> $id,
                            'address'=> $userId,
                            'amount'=> $currency_sent['amount'],
                            'amountf'=> isset($fees[0]['amount']) ? $fees[0]['amount'] : 0,
                            'coin'=> $currency_received['amount'],
                            'payment_address'=> $crypto_address['address'],
                            'received'=> isset($currency_sent['currency']) ? $currency_sent['currency'] : 'BTC',
                            'receivedf'=> $currency_received['currency'],
                            'type'=> $type,
                            'status'=> $status,
                            'status_text'=> "pending" 
                        ]);
                        return response()->json(['error' => false, 'msg' => trans('app.success')], 200);
                    }
                }else if($type == 'withdrawal_exchange'){
                    $id = $request->post('id');
                    $crypto_address = $request->post('crypto_address');
                    $currency_sent = $request->post('currency_sent');
                    $currency_received = $request->post('currency_received');
                    $transactions = $request->post('transactions');
                    $fees = $request->post('fees');

                    $amount = $currency_received['amount'];

                    $trans_exist = \VanguardLTE\Transaction::where('transaction', $id)->first();
                    if(!$trans_exist){
                        return response()->json(['error' => true, 'msg' => trans('app.wrong_transaction')], 200);
                    }

                    $txn_exist = \VanguardLTE\CoinpaymentTransactions::where('txn_id', $id)->where('address', $trans_exist->user_id)->first();
                    if($txn_exist){
                        return response()->json(
                            [
                                "status" => 500,
                                "msg" => "Transaction is not completed"
                            ],
                            200
                        );
                    }else {
                        $crytp_address = \VanguardLTE\CoinpaymentTransactions::create([
                            'txn_id'=> $id,
                            'address'=> $trans_exist->user_id,
                            'amount'=> $currency_sent['amount'],
                            'amountf'=> isset($fees[0]['amount']) ? $fees[0]['amount'] : 0,
                            'coin'=> $currency_received['amount'],
                            'payment_address'=> $crypto_address['address'],
                            'received'=> $currency_sent['currency'],
                            'receivedf'=> isset($currency_received['currency']) ? $currency_received['currency'] : $trans_exist->value,
                            'type'=> $type,
                            'status'=> $status,
                            'status_text'=> "pending"
                        ]);
                        return response()->json(['error' => false, 'msg' => trans('app.success')], 200);
                    }
                }else {
                    return response()->json(['error' => ture, 'msg' => trans('app.wrong_transaction')], 200);
                }
            }
        }

        /* --- */
        public function gigadat(\Illuminate\Http\Request $request)
        {
            $transaction = new \VanguardLTE\Transaction();
            $user = \Auth::user();
            $todayDeposit = $transaction->getTodayDeposit($user->id);
            if($todayDeposit + $request->deposit_amount > $user->max_deposit)
                return response()->json(['error' => true, 'msg' => 'You have reached your daily limit of $' . $user->max_deposit . ', The maximum at the moment you can deposit is $' . ($user->max_deposit - $todayDeposit) . '. Contact support to increase your limit. '], 200);

            set_time_limit(300);

            $visitorId = $user->visitor_id;
            $multiAccounts = [];
            $multiDeposit = 0;

            if(\VanguardLTE\User::where(['visitor_id' => $visitorId])->count() > 1 ) {
                $multiAccounts = \VanguardLTE\User::where(['visitor_id' => $visitorId])->get();
                foreach($multiAccounts as $multiAccount) {

                    if($user->id != $multiAccount->id){
                        if(\VanguardLTE\Transaction::where(['user_id' => $multiAccount->id])->count() > 0){
                            $multiDeposit = 1;
                        }else{
                            $multiDeposit = 0;
                        }
                    }
                }
            }
            $userId = $user->id;
            $userName = $user->username;
            $userEmail = $user->email;
            $transactionId = hash('crc32b', rand());
            $type = 'CPI';
            $amount = $request->deposit_amount;
            $email = $request->deposit_email;
            $mobile = $request->deposit_phone;
            $currency = $request->cur_deposit_currency;

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $curl = curl_init();
            $ip = $_SERVER['REMOTE_ADDR'];
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://interac.express-connect.com/api/payment-token/5578ad563e2da4ccf5da8ab02ecf18c1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(array(
                    "userId"=> $userId,
                    "transactionId"=> $transactionId, // merchant defined value
                    "name"=> $userName,
                    "email"=> $email,
                    "site"=> "https://www.canada777.com",
                    "userIp"=> $ip,
                    "mobile"=> $mobile,
                    "currency"=> $currency,
                    "language"=> "en",
                    "amount"=> $amount,
                    "type"=> $type,
                    "hosted"=> true,
                    "sandbox" => false,
                )),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic '.base64_encode(sprintf('%s:%s', 'eed58d71-35a5-4071-8860-21b9fba29f7b', 'c986ca3b-0436-46e8-9f68-4844a8bff349')),
                    'Content-Type: application/json'
                )
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($response, true);
            if (isset($response['err']))
                return response()->json(['error' => true, 'msg' => $response['err']], 200);
            $newItem = new \VanguardLTE\Transaction;
            $newItem->user_id = $userId;
            $newItem->system = 'interac';
            $newItem->type = 'in';
            $newItem->summ = $amount;
            $newItem->email = $email;
            $newItem->phone = $mobile;
            $newItem->ip = $ip;
            $newItem->status = -1;
            $newItem->transaction = $transactionId;
            $newItem->save();

            $data = ['email' => $request->email];
            $automizy_api = new \VanguardLTE\Lib\automizy_Api;
            $lists = $automizy_api->getAllLists();
            $smart_lists = $lists['smartLists'];
            if(count($lists['smartLists']) > 0){
                foreach($smart_lists as $val) {
                    if(str_contains(strtolower($val['name']), 'all')){
                        $list_id = $val['id'];
                        $automizy_api->addContactsByList($data, $list_id);
                    }
                }
            }
            
            return response()->json(['error' => false, 'multiDeposit'=> $multiDeposit, 'multimsg'=> trans('app.notdeposit_to_multiaccount'), 'redirectUrl' => 'https://interac.express-connect.com/webflow?token='.$response['token'].'&transaction='.$transactionId], 200);
        }

        public function gigadatSuccess(\Illuminate\Http\Request $request)
        {
            Log::channel('payment')->info('***Gigadat Success***', ['request' => $request]);
        }

        public function gigadatFail(\Illuminate\Http\Request $request)
        {
            Log::channel('payment')->info('***Gigadat Failed***', ['request' => $request]);
        }

        public function gigadatListener(\Illuminate\Http\Request $request)
        {
            Log::channel('payment')->info('***Gigadat Listener***', ['request' => $request]);
            $transaction = $request->post('transactionId');
            $status = $request->get('status');
            $userId = $request->post('userId');
            $amount = $request->post('amount');
            $type = $request->post('type');
            $email = $request->post('email');
            $phone = $request->post('mobile');
            $ip = $request->post('userIp');

           /* $user = \VanguardLTE\User::where('id', $userId)->first();
            if (!$user) {
                return response()->json(['error' => true, 'msg' => trans('app.wrong_user')], 200);
            }
            $curTransaction = \VanguardLTE\Transaction::where(['user_id'=> $userId, 'transaction' => $transaction, 'status' => -1])->first();
            if (!$curTransaction) {
                return response()->json(['error' => true, 'msg' => trans('app.wrong_transaction')], 200);
            }*/

            if ($status == 'STATUS_INITED') {
            }
            else if ($status == 'STATUS_PENDING') {
            }
            else if ($status == 'STATUS_SUCCESS') {
                if ($type == 'CPI') {
                    // $deposit_count = \VanguardLTE\Transaction::leftjoin('users', 'transactions.user_id', '=', 'users.id')->where(['users.visitor_id'=>$user->visitor_id, 'type'=>'in', 'transactions.status' => '1'])->count();
                    $deposit_count = \VanguardLTE\Transaction::where(['user_id'=>$user->id, 'type'=>'in'])->count();
                    $user->increment('balance', $amount);
                    $user->increment('count_balance', $amount);
                    switch ($deposit_count) {
                        case 1:
                            $welcomepackages = \VanguardLTE\WelcomePackage::leftJoin('games', function ($join)
                                                                {
                                                                    $join->on('games.original_id','=','welcomepackages.game_id');
                                                                    $join->on('games.id','=','games.original_id');
                                                                })->select('welcomepackages.*', 'games.name')->get();
                            foreach ($welcomepackages as $welcomepackage) {
                                $welcomepackagelog = \VanguardLTE\WelcomePackageLog::create([
                                    'user_id' => $userId,
                                    'day' => $welcomepackage->day,
                                    'freespin' => $welcomepackage->freespin,
                                    'remain_freespin' => $welcomepackage->freespin,
                                    'game_id' => $welcomepackage->game_id,
                                    'max_bonus' => 20,
                                    'started_at' => date('Y-m-d', strtotime('+'.($welcomepackage->day-1).' days'))
                                ]);
                            }
                            app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch(new \VanguardLTE\Jobs\GetFreespinJob($user));
                        case 2:
                        case 3:
                            $bonus = \VanguardLTE\Bonus::where('deposit_num', $deposit_count + 1)->first();
                            if ($bonus) {
                                $bonus_amount = $bonus->bonus;
                                if ($amount < $bonus_amount)
                                    $bonus_amount = $amount;
                                if ($amount < 10)
                                    break;
                                $user->increment('balance', $bonus_amount);
                                $user->increment('bonus', $bonus_amount);
                                $user->increment('count_bonus', $bonus_amount);
                                $user->increment('wager', $bonus_amount * 70);

                                \VanguardLTE\BonusLog::create([
                                    'user_id' => $userId,
                                    'deposit_num' => $deposit_count + 1,
                                    'deposit' => $amount,
                                    'bonus' => $bonus_amount,
                                    'wager' => $bonus_amount * 70,
                                    'wager_played' => 0
                                ]);
                            }
                            break;
                    }
                    app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch(new \VanguardLTE\Jobs\DepositSuccessedJob($curTransaction));
                }
                else if ($type == 'ETO') {

                }
                $curTransaction->status = 1;
                $curTransaction->save();
            }
            else if ($status == 'STATUS_REJECTED' || $status == 'STATUS_ABORTED'){
                $statNum = $status == 'STATUS_REJECTED' ? -2 : -4;
                if ($type == 'CPI') {
                    app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch(new \VanguardLTE\Jobs\DepositFailedJob($user));
                }
                else if ($type == 'ETO') {

                }
                $curTransaction->status = $statNum;
                $curTransaction->save();
                /* if transaction is not confirmed, send message to user.  */

                /* --- */
            }
            else if ($status == 'STATUS_ERROR') {
                app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch(new \VanguardLTE\Jobs\DepositFailedJob($user));
                // $user->notify(new \VanguardLTE\Notifications\DepositFailed($user));
                $curTransaction->status = -5;
                $curTransaction->save();
            }

            return response()->json(['error' => false, 'msg' => trans('app.success')], 200);
        }

        public function withdraw(\Illuminate\Http\Request $request){
            if ($request->amount < 50){
                return response()->json(['error' => true, 'msg' => 'You cannot withdraw less than 50.'], 200);
            }
            $user = \Auth::user();
            if ($user->balance < $user->bonus)
                $user->update(['bonus' => $user->balance]);

            if ($user->balance == 0) {
                \VanguardLTE\BonusLog::where([
                    ['user_id', '=', $user->id],
                    ['wager', '>', '0']
                ])->update(['wager' => 0, 'wager_played'=> 0]);
                \VanguardLTE\WelcomePackageLog::where([
                    ['user_id', '=', $user->id],
                    ['wager', '>', '0']
                ])->update(['wager' => 0, 'wager_played'=> 0]);
            }
            else {
                $wager_played = $user->bonus * 70 - $user->wager;
                if ($wager_played > 0) {
                    $bonus_logs = \VanguardLTE\BonusLog::where([
                        ['user_id', '=', $user->id],
                        ['wager', '>', '0']
                    ])->get();
                    foreach ($bonus_logs as $bonus_log) {
                        $wager_remaining = $bonus_log->wager - $bonus_log->wager_played;
                        if ($wager_remaining <= 0)
                            break;
                        if ($wager_played > $wager_remaining) {
                            $bonus_log->update(['wager_played'=> $bonus_log->wager]);
                            $wager_played = $wager_played - $wager_remaining;
                            $user->incrument('bonus', -1 * min($bonus_log->bonus, $user->bonus));
                        }
                        else {
                            $bonus_log->update(['wager_played' => $wager_played]);
                            $wager_played = 0;
                        }
                    }

                    $welcomepackage_logs = \VanguardLTE\WelcomePackageLog::where([
                        ['user_id', '=', $user->id],
                        ['wager', '>', '0']
                    ])->get();
                    foreach ($welcomepackage_logs as $welcomepackage_log) {
                        $wager_remaining = $welcomepackage_log->wager - $welcomepackage_log->wager_played;
                        if ($wager_remaining <= 0)
                            break;
                        if ($wager_played > $wager_remaining) {
                            $wager = $welcomepackage_log->wager;
                            $welcomepackage_log->update([
                                'wager' => 0,
                                'wager_played'=> 0
                            ]);
                            $wager_played = $wager_played - $wager_remaining;
                            $user->incrument('bonus', -1 * min($wager / 70, $user->bonus));
                        }
                        else {
                            $welcomepackage_log->update(['wager_played' => $wager_played]);
                            $wager_played = 0;
                        }
                    }
                }
            }

            $withdrawable = $user->balance - $user->wager / 70;
            if ($user->wager > 0){
                $playWager = $user->bonus * 70 > $user->wager ? $user->bonus * 70 - $user->wager : 0;
                return response()->json(['error' => true, 'msg' => 'You cannot withdraw as you have bonus funds in your account and you have not met the minimun play through required.', 'totalBalance' => number_format((float)$user->balance, 2, '.', ''), 'realBalance' => number_format((float)$user->getRealBalance(), 2, '.', ''), 'bonusBalance' => number_format((float)$user->getBonusBalance(), 2, '.', ''), 'wager' => number_format((float)$user->wager, 2, '.', ''), 'playWager' => number_format((float)$playWager, 2, '.', '')], 200);
            }
            if ($withdrawable < $request->amount){
                return response()->json(['error' => true, 'msg' => 'Maximun withdrawable balance is '.$withdrawable], 200);
            }
            $newWithdraw = new \VanguardLTE\Transaction;
            $newWithdraw->user_id = $user->id;
            $newWithdraw->system = $request->payment_method;
            $newWithdraw->type = 'out';
            $newWithdraw->summ = -1 * $request->amount;
            $newWithdraw->email = $request->email;
            $newWithdraw->phone = $request->phone;
            $newWithdraw->transaction = hash('crc32b', rand());
            if($request->payment_method == 'crypto'){
                $newWithdraw->value = $request->withdraw_crypto_type;
            } 
            $newWithdraw->ip = $_SERVER['REMOTE_ADDR'];
            $newWithdraw->save();
            $user->balance -= $request->amount;
            if ($user->balance < $user->bonus){
                $user->bonus = $user->balance;
            }
            $user->save();

            $data = ['email' => $request->email];
            $automizy_api = new \VanguardLTE\Lib\automizy_Api;
            $lists = $automizy_api->getAllLists();
            $smart_lists = $lists['smartLists'];
            if(count($lists['smartLists']) > 0){
                foreach($smart_lists as $val) {
                    if(str_contains(strtolower($val['name']), 'all')){
                        $list_id = $val['id'];
                        $automizy_api->addContactsByList($data, $list_id);
                    }
                }
            }
            
            return response()->json(['error' => false], 200);
        }
    }
}

namespace
{
    function onkXppk3PRSZPackRnkDOJaZ9()
    {
        return 'OkBM2iHjbd6FHZjtvLpNHOc3lslbxTJP6cqXsMdE4evvckFTgS';
    }

}
