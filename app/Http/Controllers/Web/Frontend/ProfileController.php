<?php
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    use Illuminate\Support\Facades\Storage;
    class ProfileController extends \VanguardLTE\Http\Controllers\Controller
    {
        protected $theUser = null;
        private $users = null;
        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users)
        {
            $this->middleware('auth');
            $this->middleware('session.database', [
                'only' => [
                    'sessions',
                    'invalidateSession'
                ]
            ]);
            $this->users = $users;
            $this->middleware(function($request, $next)
            {
                $this->theUser = \Auth::user();

                if ($this->theUser->balance < $this->theUser->bonus){
                    $this->theUser->update(['bonus' => $this->theUser->balance]);
                }
                if ($this->theUser->bonus < 0){
                    $this->theUser->update(['bonus' => 0, 'wager' => 0]);
                }
                if ($this->theUser->balance == 0 || $this->theUser->bonus == 0 && $this->theUser->wager == 0) {
                    \VanguardLTE\BonusLog::where([
                        ['user_id', '=', $this->theUser->id],
                        ['wager', '>', '0']
                    ])->update(['wager' => 0, 'wager_played'=> 0]);
                    \VanguardLTE\WelcomePackageLog::where([
                        ['user_id', '=', $this->theUser->id],
                        ['wager', '>', '0']
                    ])->update(['wager' => 0, 'wager_played'=> 0]);
                }
                else {
                    $wager_played = $this->theUser->bonus * 70 - $this->theUser->wager;
                    if ($wager_played > 0) {
                        $bonus_logs = \VanguardLTE\BonusLog::where([
                            ['user_id', '=', $this->theUser->id],
                            ['wager', '>', '0']
                        ])->get();
                        foreach ($bonus_logs as $bonus_log) {
                            $wager_remaining = $bonus_log->wager - $bonus_log->wager_played;
                            if ($wager_remaining > 0) {
                                if ($wager_played >= $bonus_log->wager) {
                                    $wager_played = $wager_played - $bonus_log->wager;
                                    $bonus_log->update(['wager' => 0, 'wager_played'=> 0]);
                                    $this->theUser->increment('bonus', -1 * min($bonus_log->bonus, $this->theUser->bonus));
                                }
                                else {
                                    $bonus_log->update(['wager_played' => $wager_played]);
                                    $wager_played = 0;
                                }
                            }
                        }
                    }
                    else{
                        if ($this->theUser->wager == 0 && $this->theUser->bonus == 0){
                            \VanguardLTE\BonusLog::where([
                                ['user_id', '=', $this->theUser->id],
                                ['wager', '>', '0']
                            ])->update(['wager' => 0, 'wager_played'=> 0]);
                        }
                        else{
                            $this->theUser->update(['wager' => $this->theUser->bonus * 70 ]);
                        }
                    }
                    $bonus_wager = \VanguardLTE\BonusLog::where('user_id', $this->theUser->id)->sum('wager');
                    if ($wager_played > 0){
                        $wager_played = $this->theUser->bonus * 70 - $this->theUser->wager;
                        if ($wager_played > 0) {
                            $welcomepackage_logs = \VanguardLTE\WelcomePackageLog::where([
                                ['user_id', '=', $this->theUser->id],
                                ['wager', '>', '0']
                            ])->get();
                            foreach ($welcomepackage_logs as $welcomepackage_log) {
                                $wager_remaining = $welcomepackage_log->wager - $welcomepackage_log->wager_played;
                                if ($wager_remaining > 0) {
                                    $wager = $welcomepackage_log->wager;
                                    if ($wager_played > $wager) {
                                        $welcomepackage_log->update([
                                            'wager' => 0,
                                            'wager_played'=> 0
                                        ]);
                                        $wager_played = $wager_played - $wager;
                                        $this->theUser->increment('bonus', -1 * min($wager / 70, $this->theUser->bonus));
                                    }
                                    else {
                                        $welcomepackage_log->update(['wager_played' => $wager_played]);
                                        $wager_played = 0;
                                    }
                                }
                            }
                        }
                        else{
                            if ($this->theUser->wager == 0 && $this->theUser->bonus == 0){
                                \VanguardLTE\WelcomePackageLog::where([
                                    ['user_id', '=', $this->theUser->id],
                                    ['wager', '>', '0']
                                ])->update(['wager' => 0, 'wager_played'=> 0]);
                            }
                            else{
                                $this->theUser->update(['wager' => $this->theUser->bonus * 70 ]);
                            }
                        }
                    }
                }
                return $next($request);
            });
        }

        public function index(\VanguardLTE\Repositories\Role\RoleRepository $rolesRepo)
        {
            $tab = 'info';
            $user = $this->theUser;
            $edit = true;
            $roles = $rolesRepo->lists();
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            $country = \VanguardLTE\Country::find($user->country);

            if(isset($country)) {
                $country = $country->country;
            }
            else {
                $country = "";
            }
            $currencys =  \VanguardLTE\Currency::orderBy('ranking', 'ASC')->get();
            $html = view('frontend.Default.user.info')->with(compact('user', 'edit', 'roles', 'statuses','tab','country','currencys'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function payment_history(\Illuminate\Http\Request $request)
        {
            $tab = "history";
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $payment_history = \VanguardLTE\Transaction::where('user_id', \Auth::user()->id)->orderBy('created_at', 'DESC')->get();
            $html = view('frontend.Default.user.payment_history')->with(compact('tab','currencys','payment_history'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function bet_history(\Illuminate\Http\Request $request)
        {
            $tab = "history";
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $bet_history = \VanguardLTE\StatGame::where('user_id', \Auth::user()->id)->orderBy('id','DESC')->get();
            $html = view('frontend.Default.user.bet_history')->with(compact('tab','currencys','bet_history'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function bonus(\Illuminate\Http\Request $request)
        {
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $bonus_history = \VanguardLTE\BonusLog::where('user_id', \Auth::user()->id)->get();
            $html = view('frontend.Default.user.bonus')->with(compact('currencys','bonus_history'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function freespin(\Illuminate\Http\Request $request)
        {
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $welcomepackage_history = \VanguardLTE\WelcomePackageLog::leftJoin('games', function ($join)
            {
                $join->on('games.original_id','=','welcomepackage_log.game_id');
                $join->on('games.id','=','games.original_id');
            })->where('user_id', \Auth::user()->id)->select('welcomepackage_log.*', 'games.name')->get();
            $html = view('frontend.Default.user.freespin')->with(compact('currencys','welcomepackage_history'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function balance(\Illuminate\Http\Request $request)
        {
            $tab = "balance";
            $currency =  \VanguardLTE\Currency::where('id', \Auth::user()->currency)->first();
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $html = view('frontend.Default.user.balance')->with(compact('tab','currency','currencys'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        public function updateDetails(\VanguardLTE\Http\Requests\User\UpdateProfileDetailsRequest $request)
        {
            $this->users->update($this->theUser->id, $request->except('role_id', 'status'));
            event(new \VanguardLTE\Events\User\UpdatedProfileDetails());
            // return response()->json(['success' => trans('app.profile_updated_successfully')], 200);
            return redirect()->back()->withSuccess(trans('app.profile_updated_successfully'));
        }
        public function updatePassword(\VanguardLTE\Http\Requests\User\UpdateProfilePasswordRequest $request)
        {
            $old_password = $request->old_password;
            if( !\Illuminate\Support\Facades\Hash::check($old_password, \Auth::user()->password) )
            {
                return response()->json(['error' => 'old password error', 'status' => -1], 422);
            }
            $this->users->update($this->theUser->id, $request->only('password'));
            event(new \VanguardLTE\Events\User\UpdatedProfileDetails());
            return response()->json(['success' => 'password change success', 'status' => 1], 200);
        }
        public function updateAvatar(\Illuminate\Http\Request $request, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager)
        {
            $this->validate($request, ['avatar' => 'image']);
            $name = $avatarManager->uploadAndCropAvatar($this->theUser, $request->file('avatar'), $request->get('points'));
            if( $name )
            {
                return $this->handleAvatarUpdate($name);
            }
            return redirect()->route('frontend.profile')->withErrors(trans('app.avatar_not_changed'));
        }
        private function handleAvatarUpdate($avatar)
        {
            $this->users->update($this->theUser->id, ['avatar' => $avatar]);
            event(new \VanguardLTE\Events\User\ChangedAvatar());
            return redirect()->route('frontend.profile')->withSuccess(trans('app.avatar_changed'));
        }
        public function updateAvatarExternal(\Illuminate\Http\Request $request, \VanguardLTE\Services\Upload\UserAvatarManager $avatarManager)
        {
            $avatarManager->deleteAvatarIfUploaded($this->theUser);
            return $this->handleAvatarUpdate($request->get('url'));
        }
        public function updateLoginDetails(\VanguardLTE\Http\Requests\User\UpdateProfileLoginDetailsRequest $request)
        {
            $data = $request->except('role', 'status');
            if( trim($data['password']) == '' )
            {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            $this->users->update($this->theUser->id, $data);
            return redirect()->route('frontend.profile')->withSuccess(trans('app.login_updated'));
        }
        public function exchange(\Illuminate\Http\Request $request)
        {
            $user = \Auth::user();
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            $exchange_rate = $user->point()->exchange_rate(true);
            $add = $request->sumpoints * $exchange_rate;
            $wager = $add * $user->point()->exchange_wager();
            if( !$shop )
            {
                return response()->json(['error' => trans('app.wrong_shop')], 422);
            }
            if( !$request->sumpoints )
            {
                return response()->json(['error' => trans('app.zero_points')], 422);
            }
            if( $user->points < $request->sumpoints )
            {
                return response()->json(['error' => trans('app.available_points', ['points' => $user->points])], 422);
            }
            if( $shop->balance < $add )
            {
                return response()->json(['error' => 'Not Money "' . $shop->name . '"'], 422);
            }
            $open_shift = \VanguardLTE\OpenShift::where([
                'shop_id' => \Auth::user()->shop_id,
                'end_date' => null
            ])->first();
            if( !$open_shift )
            {
                return response()->json(['error' => trans('app.shift_not_opened')], 422);
            }
            $user->decrement('points', $request->sumpoints);
            $user->increment('balance', $add);
            $user->increment('wager', $wager);
            $user->increment('bonus', $wager);
            $shop->decrement('balance', $add);
            $open_shift->increment('balance_out', $add);
            \VanguardLTE\Transaction::create([
                'user_id' => $user->id,
                'summ' => abs($add),
                'system' => 'Exchange points',
                'shop_id' => $user->shop_id
            ]);
            return response()->json(['success' => true], 200);
        }
        public function activity(\VanguardLTE\Repositories\Activity\ActivityRepository $activitiesRepo, \Illuminate\Http\Request $request)
        {
            $user = $this->theUser;
            $activities = $activitiesRepo->paginateActivitiesForUser($user->id, $perPage = 20, $request->get('search'));
            return view('frontend.activity.index', compact('activities', 'user'));
        }
        public function sessions(\VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $profile = true;
            $user = $this->theUser;
            $sessions = $sessionRepository->getUserSessions($user->id);
            return view('frontend.user.sessions', compact('sessions', 'user', 'profile'));
        }
        public function invalidateSession($session, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {
            $sessionRepository->invalidateSession($session->id);
            return redirect()->route('frontend.profile.sessions')->withSuccess(trans('app.session_invalidated'));
        }
        public function balanceAdd(\Illuminate\Http\Request $request)
        {
            $amount = str_replace(',', '.', trim($request->summ));
            $amount = number_format(floatval($amount), 2, '.', '');
            if( $request->system == 'piastrix' )
            {
                $payment = \VanguardLTE\Payment::create([
                    'user_id' => \Auth::user()->id,
                    'summ' => $amount,
                    'system' => $request->system
                ]);
                $currency = 840;
                $shop_id = Config::get('payments.piastrix.id');
                $shop_order_id = $payment->id;
                $description = base64_encode('Пополнение счета для клиента #' . \Auth::user()->id);
                $arHash = [
                    $amount,
                    $currency,
                    $shop_id,
                    $shop_order_id
                ];
                $sign = hash('sha256', implode(':', $arHash));
                $data = [
                    'method' => 'POST',
                    'action' => 'https://pay.piastrix.com/ru/pay',
                    'charset' => 'UTF-8',
                    'fields' => [
                        'shop_id' => $shop_id,
                        'shop_order_id' => $shop_order_id,
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                        'sign' => $sign
                    ]
                ];
                return view('frontend.user.payment_form', compact('data'));
            }
            if( $request->system == 'coinpayment' )
            {
                if( $amount < config('coinpayment.add_min') )
                {
                    return response()->json(['error' => trans('app.min_amount', ['amount' => config('coinpayment.add_min')])], 422);
                }
                if( config('coinpayment.add_max') < $amount )
                {
                    return response()->json(['error' => trans('app.max_amount', ['amount' => config('coinpayment.add_max')])], 422);
                }
                $payment = \VanguardLTE\Transaction::create([
                    'user_id' => \Auth::user()->id,
                    'summ' => abs($amount),
                    'system' => $request->system,
                    'shop_id' => \Auth::user()->shop_id,
                    'status' => 0
                ]);
                $trx['amountTotal'] = $amount;
                $trx['note'] = 'Adding money to a balance';
                $trx['items'][0] = [
                    'descriptionItem' => 'Balance',
                    'priceItem' => $amount,
                    'qtyItem' => 1,
                    'subtotalItem' => $amount
                ];
                $trx['payload'] = [
                    'user_id' => \Auth::user()->id,
                    'payment_id' => $payment->id
                ];
                $link_transaction = CoinPayment::url_payload($trx);
                return response()->json([
                    'success' => 'success',
                    'link' => $link_transaction
                ], 200);
            }
        }
        public function pincode(\Illuminate\Http\Request $request)
        {
            $user = \VanguardLTE\User::find(\Auth::id());
            if( !$request->pincode )
            {
                return response()->json([
                    'fail' => 'fail',
                    'error' => 'Please enter pincode'
                ], 200);
            }
            $pincode = \VanguardLTE\Pincode::where([
                'code' => $request->pincode,
                'shop_id' => \Auth::user()->shop_id
            ])->first();
            if( !$pincode )
            {
                return response()->json([
                    'fail' => 'fail',
                    'error' => 'Pincode not exist'
                ], 200);
            }
            if( !$pincode->status )
            {
                return response()->json([
                    'fail' => 'fail',
                    'error' => 'Wrong Pincode'
                ], 200);
            }
            $transaction = new \VanguardLTE\Transaction();
            $transaction->user_id = \Auth::id();
            $transaction->system = 'PIN';
            $transaction->value = $pincode->code;
            $transaction->type = 'add';
            $transaction->summ = abs($pincode->nominal);
            $transaction->shop_id = $user->shop_id;
            $transaction->save();
            $user->update([
                'balance' => $user->balance + $pincode->nominal,
                'count_balance' => $user->count_balance + $pincode->nominal,
                'count_return' => $user->count_return + \VanguardLTE\Lib\Functions::count_return($pincode->nominal, $user->shop_id),
                'total_in' => $user->total_in + $pincode->nominal
            ]);
            $pincode->delete();
            return response()->json([
                'success' => 'success',
                'text' => 'Pincode activated'
            ], 200);
        }
        public function returns(\Illuminate\Http\Request $request)
        {
            $user = \Auth::user();
            $shop = \VanguardLTE\Shop::find($user->shop_id);
            $sum = floatval($user->count_return);
            $return = \VanguardLTE\Returns::where('shop_id', $user->shop_id)->first();
            if( $sum )
            {
                if( $return && $return->min_balance < $user->balance )
                {
                    return response()->json([
                        'fail' => 'fail',
                        'value' => 0,
                        'balance' => $user->balance,
                        'text' => 'Min Balance "' . $return->min_balance . '"'
                    ], 200);
                }
                $open_shift = \VanguardLTE\OpenShift::where([
                    'shop_id' => \Auth::user()->shop_id,
                    'end_date' => null
                ])->first();
                if( !$open_shift )
                {
                    return response()->json([
                        'fail' => 'fail',
                        'value' => 0,
                        'balance' => $user->balance,
                        'text' => trans('app.shift_not_opened')
                    ], 200);
                }
                $user->increment('balance', $sum);
                $user->increment('count_bonus', $sum);
                $user->update(['count_return' => 0]);
                \VanguardLTE\Transaction::create([
                    'user_id' => $user->id,
                    'summ' => abs($sum),
                    'system' => 'Refund',
                    'shop_id' => $user->shop_id
                ]);
                $open_shift->increment('balance_out', $sum);
                return response()->json([
                    'success' => 'success',
                    'value' => number_format($sum, 2, '.', ''),
                    'balance' => number_format($user->balance, 2, '.', ''),
                    'count_return' => number_format($user->count_return, 2, '.', ''),
                    'currency' => $shop->currency
                ], 200);
            }
            return response()->json([
                'success' => 'success',
                'value' => 0,
                'balance' => number_format($user->balance, 2, '.', ''),
                'currency' => $shop->currency
            ], 200);
        }
        public function jackpots(\Illuminate\Http\Request $request)
        {
            $jackpots = \VanguardLTE\JPG::select([
                'id',
                'balance',
                'shop_id'
            ])->where('shop_id', auth()->user()->shop_id)->get();
            return response()->json($jackpots->toArray());
        }
        public function setlang($lang)
        {
            auth()->user()->update(['language' => $lang]);
            return redirect()->back();
        }
        public function success(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.profile.balance')->withSuccess(trans('app.payment_success'));
        }
        public function fail(\Illuminate\Http\Request $request)
        {
            return redirect()->route('frontend.profile.balance')->withSuccess(trans('app.payment_fail'));
        }
        public function verify(\Illuminate\Http\Request $request)
        {
            $adminVerified = -1;
            $idVerified = false;
            $addressVerified = false;
            $curVerify = \VanguardLTE\Verify::where('user_id', \Auth::user()->id)->first();
            if ($curVerify != null){
                $idVerified = !empty($curVerify->id_img);
                $addressVerified = !empty($curVerify->address_img);
                $adminVerified = $curVerify->verified;
            }
            $html = view('frontend.Default.user.verify')->with(compact('adminVerified','idVerified','addressVerified'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function password(\Illuminate\Http\Request $request)
        {
            $html = view('frontend.Default.user.password')->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function detail(\Illuminate\Http\Request $request)
        {
            $tab = 'info';
            $user = $this->theUser;
            $edit = true;
            $statuses = \VanguardLTE\Support\Enum\UserStatus::lists();
            $country = \VanguardLTE\Country::find($user->country);

            if(isset($country)) {
                $country = $country->country;
            }
            else {
                $country = "";
            }
            $currencys =  \VanguardLTE\Currency::orderBy('ranking', 'ASC')->get();
            $html = view('frontend.Default.user.detail')->with(compact('user', 'edit', 'statuses','tab','country','currencys'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function transaction(\Illuminate\Http\Request $request)
        {
            $html = view('frontend.Default.user.transaction')->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        // show deposit in user profile
        public function deposit(\Illuminate\Http\Request $request)
        {
            $login_ = \Auth::user();
            $no_bonus = 0;
            // if($login_->visitor_id != '' || $login_->visitor_id != NULL)
            // if(\VanguardLTE\User::where(['visitor_id' => $login_->visitor_id])->count() > 1 ) {
            //     $no_bonus = "1";
            // }else {
            //     $no_bonus = "0";
            // }

            $user = $this->theUser;
            $tab = "deposit";
            $currency =  \VanguardLTE\Currency::where('id', \Auth::user()->currency)->first();
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            // $html = view('frontend.Default.user.deposit')->with(compact('user', 'tab','currency','currencys', 'no_bonus'))->render();
            $html = view('frontend.Default.user.deposit')->with(compact('user', 'tab','currency','currencys'))->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        public function withdraw(\Illuminate\Http\Request $request)
        {
            $tab = "withdraw";
            $realBalance = $this->theUser->balance - $this->theUser->bonus;
            $bonusBalance = ($this->theUser->bonus * 70 - $this->theUser->wager) / 70;
            $html = view('frontend.Default.user.withdraw')->with(compact('realBalance', 'bonusBalance'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        public function submitImage(\Illuminate\Http\Request $request)
        {
            $path = Storage::putFile('public/verifies', $request->file('file'));
            $link = '/storage/verifies/'.basename($path);
            $type = $request->type;
            $curUser = \VanguardLTE\Verify::where('user_id', \Auth::user()->id)->first();
            if ($curUser == null){
                $curUser = new \VanguardLTE\Verify;
                $curUser->user_id = \Auth::user()->id;
            }
            if ($type == 'id'){
                $curUser->id_img = $link;
            }
            else if ($type == 'address'){
                $curUser->address_img = $link;
            }
            $curUser->save();
            return response()->json(['success' => true, 'user_id' => \Auth::user()->id, 'type' => $type, 'url' => $link]);
        }
    }
}
