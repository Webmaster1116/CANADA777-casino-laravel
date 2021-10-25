<?php
namespace VanguardLTE\Http\Controllers\Web\Frontend\Auth
{
    use VanguardLTE\User;
    use VanguardLTE\WelcomePackageLog;
    use Twilio\Rest\Client;

    class AuthController extends \VanguardLTE\Http\Controllers\Controller
    {
        private $users = null;
        protected $redirectTo = null;

        /* twilio info */
        private $TWILIO_SID = 'AC051fa70968f876b6a62fd12ca0ac6319';
		private $TWILIO_AUTH_TOKEN = '7c44c26ca33330b920f9ee231392c873';
		private $TWILIO_VERIFY_SID = 'VA33fb133e033fd0563da85815c8962b1a';

        public function __construct(\VanguardLTE\Repositories\User\UserRepository $users)
        {
            $this->middleware('guest', [
                'except' => [
                    'getLogout',
                    'apiLogin',
                    'getIP',
                    'usernameCheck',
                    'emailCheck',
                ]
            ]);
            $this->middleware('auth', [
                'only' => ['getLogout']
            ]);
//            $this->middleware('registration', [
//                'only' => [
//                    'getRegister',
//                    'postRegister'
//                ]
//            ]);
            $this->users = $users;
        }
        public function getBasicTheme()
        {
            $frontend = settings('frontend', 'Default');
            if( \Auth::check() )
            {

            }
            return $frontend;
        }
        public function getLogin()
        {
            $frontend = $this->getBasicTheme();
            $directories = [];
            foreach( glob(resource_path() . '/lang/*', GLOB_ONLYDIR) as $fileinfo )
            {
                $dirname = basename($fileinfo);
                $directories[$dirname] = $dirname;
            }
            return view('frontend.' . $frontend . '.auth.login', compact('directories'));
        }
        public function getIP()
        {
            return $_SERVER['REMOTE_ADDR'];
        }
        public function postLogin(\VanguardLTE\Http\Requests\Auth\LoginRequest $request, \VanguardLTE\Repositories\Session\SessionRepository $sessionRepository)
        {

            $throttles = settings('throttle_enabled');
            $to = ($request->has('to') ? '?to=' . $request->get('to') : '');
            if( $throttles && $this->hasTooManyLoginAttempts($request) )
            {
                return $this->sendLockoutResponse($request);
            }

            $credentials = $request->getCredentials();

            /* avoid user have different account to get bonus with fingerprintjs */

            if( filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) )
            {
                $user = \VanguardLTE\User::where('email', $request->username)->first();
                if($user){
                    if($user->visitor_id == NULL || $user->visitor_id == ""){
                        \VanguardLTE\User::where('email', $request->username)->update(['visitor_id' => $request->login_visitorId]);
                    }
                }
            }
            else
            {
                $user = \VanguardLTE\User::where('username', $request->username)->first();
                if($user){
                    if($user->visitor_id == NULL || $user->visitor_id == ""){
                        \VanguardLTE\User::where('username', $request->username)->update(['visitor_id' => $request->login_visitorId]);
                    }
                }
            }

            /* --- */

            if( filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) )
            {
                $credentials = [
                    'email' => $credentials['username'],
                    'password' => $credentials['password']
                ];
            }
            else
            {
                $credentials = [
                    'username' => $credentials['username'],
                    'password' => $credentials['password']
                ];
            }
            if( !\Auth::validate($credentials) )
            {
                if( $throttles )
                {
                    $this->incrementLoginAttempts($request);
                }
                // return redirect()->to('login' . $to)->withErrors(trans('auth.failed'));
                return redirect('categories/all?login=fail')->withErrors(trans('auth.failed'));
            }
            $user = \Auth::getProvider()->retrieveByCredentials($credentials);
            if( $user->hasRole([
                1,
                2,
                3
            ]))
            {

            }
            if( settings('use_email') && $user->isUnconfirmed() )
            {
                //return redirect()->to('login' . $to)->withErrors(trans('app.please_confirm_your_email_first'));
                return redirect('categories/all?login=fail')->withErrors(trans('app.please_confirm_your_email_first'));
            }
            if( $user->isBanned() )
            {
                //return redirect()->to('login' . $to)->withErrors(trans('app.your_account_is_banned'));
                return redirect('categories/all?login=fail')->withErrors(trans('app.your_account_is_banned'));
            }
            if( $request->lang )
            {
                $user->update(['language' => $request->lang]);
            }
            \Auth::login($user, settings('remember_me') && $request->get('remember'));
            if( settings('reset_authentication') && count($sessionRepository->getUserSessions(\Auth::id())) )
            {
                foreach( $sessionRepository->getUserSessions($user->id) as $session )
                {
                    if( $session->id != session()->getId() )
                    {
                        $sessionRepository->invalidateSession($session->id);
                    }
                }
            }
            return $this->handleUserWasAuthenticated($request, $throttles, $user);
        }
        public function apiLogin($game, $token, $mode)
        {
            if( \Auth::check() )
            {
                event(new \VanguardLTE\Events\User\LoggedOut());
                \Auth::logout();
            }
            $us = \VanguardLTE\User::where('api_token', '=', $token)->get();
            if( isset($us[0]->id) )
            {
                \Auth::loginUsingId($us[0]->id, true);
                $ref = request()->server('HTTP_REFERER');
                if( $mode == 'desktop' )
                {
                    $gameUrl = 'game/' . $game . '?lobby_url=frame';
                }
                else
                {
                    $gameUrl = 'game/' . $game . '?lobby_url=' . $ref;
                }
                return redirect()->to($gameUrl);
            }
            else
            {
                return redirect()->to('');
            }
        }
        protected function handleUserWasAuthenticated(\Illuminate\Http\Request $request, $throttles, $user)
        {
            if( $throttles )
            {
                $this->clearLoginAttempts($request);
            }
            event(new \VanguardLTE\Events\User\LoggedIn());
            if( $request->has('to') )
            {
                return redirect()->to($request->get('to'));
            }
            if( !$user->hasRole('user') )
            {
                if( !\Auth::user()->hasPermission('dashboard') )
                {
                    return redirect()->route('backend.user.list');
                }
                return redirect()->route('backend.dashboard');
            }
            return redirect()->intended();
        }
        public function getLogout()
        {
            event(new \VanguardLTE\Events\User\LoggedOut());
            \Auth::logout();
            return redirect('/');
        }
        public function loginUsername()
        {
            return 'username';
        }
        protected function hasTooManyLoginAttempts(\Illuminate\Http\Request $request)
        {
            return app('Illuminate\Cache\RateLimiter')->tooManyAttempts($request->input($this->loginUsername()) . $request->ip(), $this->maxLoginAttempts());
        }
        protected function incrementLoginAttempts(\Illuminate\Http\Request $request)
        {
            app('Illuminate\Cache\RateLimiter')->hit($request->input($this->loginUsername()) . $request->ip(), $this->lockoutTime() / 60);
        }
        protected function retriesLeft(\Illuminate\Http\Request $request)
        {
            $attempts = app('Illuminate\Cache\RateLimiter')->attempts($request->input($this->loginUsername()) . $request->ip());
            return $this->maxLoginAttempts() - $attempts + 1;
        }
        protected function sendLockoutResponse(\Illuminate\Http\Request $request)
        {
            $seconds = app('Illuminate\Cache\RateLimiter')->availableIn($request->input($this->loginUsername()) . $request->ip());
            return redirect('/')->withInput($request->only($this->loginUsername(), 'remember'))->withErrors([$this->loginUsername() => $this->getLockoutErrorMessage($seconds)]);
        }
        protected function getLockoutErrorMessage($seconds)
        {
            return trans('auth.throttle', ['seconds' => $seconds]);
        }
        protected function clearLoginAttempts(\Illuminate\Http\Request $request)
        {
            app('Illuminate\Cache\RateLimiter')->clear($request->input($this->loginUsername()) . $request->ip());
        }
        protected function maxLoginAttempts()
        {
            return settings('throttle_attempts', 5);
        }
        protected function lockoutTime()
        {
            $lockout = (int)settings('throttle_lockout_time');
            if( $lockout <= 1 )
            {
                $lockout = 1;
            }
            return 60 * $lockout;
        }
        public function getRegister()
        {
            $frontend = $this->getBasicTheme();
            $countrys =  \VanguardLTE\Country::orderBy('ranking','ASC')->get();
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            return view('frontend.' . $frontend . '.auth.register', compact('countrys', 'currencys'));
        }
//        public function postRegister(\VanguardLTE\Http\Requests\Auth\RegisterRequest $request)
        public function postRegister(\Illuminate\Http\Request $request)
        {
            if (empty($request->username)) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.invalid', ['attribute' => trans('app.username')]));
            }
            if (\VanguardLTE\User::where('username', '=', $request->username)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.username')]));
            }
            if (\VanguardLTE\User::where('phone', '=', $request->phone)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.phone')]));
            }
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.email', ['attribute' => trans('app.email')]));
            }
            if (\VanguardLTE\User::where('email', '=', $request->email)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.email')]));
            }
            /* avoid user have different account to get bonus with fingerprintjs */
            // if(\VanguardLTE\User::where('visitor_id', $request->visitorId)->exists()) {
            //     return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.visitorId')]));
            // }
            /* --- */
            // if($request->visitorId == null || $request->visitorId == ""){
            //     return redirect('categories/all?register=fail')->withErrors(trans('validation.invalid', ['attribute' => trans('app.finger_error')]));
            // }
            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->currency = $request->currency;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->birthday = $request->birthday_year . "-". $request->birthday_month . "-" . $request->birthday_day;
            $user->phone = $request->phone;
            $user->country = $request->country;
            $user->city = $request->user_address_city;
            $user->address = $request->user_address;
            $user->province = $request->user_address_state;
            $user->postalCode = $request->user_address_postcode;
            $user->role_id = 1;
            $user->shop_id = 0;
            $user->visitor_id = $request->visitorId ? $request->visitorId : "";
            $user->status = settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE;
            $user->save();

            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login');

            /* if user is new player, it gives 100 free spin to him/her. */
            if($request->freespinuser == "freespin"){
                // if($request->visitorId){
                    // $multi_accounts = \VanguardLTE\User::where('visitor_id', $request->visitorId)->count();
                    // if($multi_accounts == 1){
                        $user = \VanguardLTE\User::where('username', $request->username)->first();
                        if($user){
                            $promotion_user = new WelcomePackageLog;
                            $promotion_user->user_id = $user->id;
                            $promotion_user->day = 7;
                            $promotion_user->freespin = 100;
                            $promotion_user->remain_freespin = 100;
                            $promotion_user->game_id = 975;
                            $promotion_user->save();

                            $message .= trans('app.free_spin_bonus');
                        }
                    // }else {
                        // $message .= trans('app.notdeposit_to_multiaccount');
                    // }
                // }
            }
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
            /* --- */

            // if (!settings('use_email'))
            // {
            //     \Auth::login($user, true);
            // }

            /*
            $data = $request->only('email',  'username');
            $user = $this->users->create(array_merge($data, [
                'role_id' => 1,
                'status' => (settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE)
            ]));
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = (settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login'));
            if( !settings('use_email') )
            {
                \Auth::login($user, true);
            }
            */
            return redirect()->route('frontend.auth.login')->with('success', $message);
        }

 public function postRegister2(\Illuminate\Http\Request $request)
        {
            if (empty($request->username)) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.invalid', ['attribute' => trans('app.username')]));
            }
            if (\VanguardLTE\User::where('username', '=', $request->username)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.username')]));
            }
            if (\VanguardLTE\User::where('phone', '=', $request->phone)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.phone')]));
            }
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.email', ['attribute' => trans('app.email')]));
            }
            if (\VanguardLTE\User::where('email', '=', $request->email)->exists()) {
                return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.email')]));
            }
            /* avoid user have different account to get bonus with fingerprintjs */
            // if(\VanguardLTE\User::where('visitor_id', $request->visitorId)->exists()) {
            //     return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.visitorId')]));
            // }
            /* --- */
            // if($request->visitorId == null || $request->visitorId == ""){
            //     return redirect('categories/all?register=fail')->withErrors(trans('validation.invalid', ['attribute' => trans('app.finger_error')]));
            // }
            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->currency = $request->currency;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->birthday = $request->birthday_year . "-". $request->birthday_month . "-" . $request->birthday_day;
            $user->phone = $request->phone;
            $user->country = $request->country;
            $user->city = $request->user_address_city;
            $user->address = $request->user_address;
            $user->province = $request->user_address_state;
            $user->postalCode = $request->user_address_postcode;
            $user->role_id = 1;
            $user->shop_id = 0;
            $user->visitor_id = $request->visitorId ? $request->visitorId : "";
            $user->status = settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE;
            $user->save();

            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login');

            /* if user is new player, it gives 100 free spin to him/her. */
            if($request->freespinuser == "freespin"){
                // if($request->visitorId){
                    // $multi_accounts = \VanguardLTE\User::where('visitor_id', $request->visitorId)->count();
                    // if($multi_accounts == 1){
                        $user = \VanguardLTE\User::where('username', $request->username)->first();
                        if($user){
                            $promotion_user = new WelcomePackageLog;
                            $promotion_user->user_id = $user->id;
                            $promotion_user->day = 7;
                            $promotion_user->freespin = 100;
                            $promotion_user->remain_freespin = 100;
                            $promotion_user->game_id = 975;
                            $promotion_user->save();

                            $message .= trans('app.free_spin_bonus');
                        }
                    // }else {
                        // $message .= trans('app.notdeposit_to_multiaccount');
                    // }
                // }
            }
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
            /* --- */

            // if (!settings('use_email'))
            // {
            //     \Auth::login($user, true);
            // }

            /*
            $data = $request->only('email',  'username');
            $user = $this->users->create(array_merge($data, [
                'role_id' => 1,
                'status' => (settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE)
            ]));
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = (settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login'));
            if( !settings('use_email') )
            {
                \Auth::login($user, true);
            }
            */
            return redirect()->route('frontend.auth.login')->with('success', $message);
        }


        public function postRegisterPage(\Illuminate\Http\Request $request)
        {
            if (empty($request->username)) {
                return redirect('register')->withErrors(trans('validation.invalid', ['attribute' => trans('app.username')]));
            }
            if (\VanguardLTE\User::where('username', '=', $request->username)->exists()) {
                return redirect('register?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.username')]));
            }
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return redirect('register?register=fail')->withErrors(trans('validation.email', ['attribute' => trans('app.email')]));
            }
            if (\VanguardLTE\User::where('email', '=', $request->email)->exists()) {
                return redirect('register?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.email')]));
            }
            /* avoid user have different account to get bonus with fingerprintjs */
            // if(\VanguardLTE\User::where('visitor_id', $request->visitorId)->exists()) {
            //     return redirect('categories/all?register=fail')->withErrors(trans('validation.exists', ['attribute' => trans('app.visitorId')]));
            // }
            /* --- */

            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->currency = $request->currency;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->birthday = $request->birthday_month . "/" . $request->birthday_day . "/" . $request->birthday_year;
            $user->phone = $request->phone;
            $user->country = $request->country;
            $user->city = $request->user_address_city;
            $user->address = $request->user_address;
            $user->province = $request->user_address_state;
            $user->postalCode = $request->user_address_postcode;
            $user->role_id = 1;
            $user->shop_id = 0;
            $user->visitor_id = $request->visitorId ? $request->visitorId : "";
            $user->status = settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE;
            $user->save();

            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login');

            if (!settings('use_email'))
            {
                \Auth::login($user, true);
            }

            /*
            $data = $request->only('email',  'username');
            $user = $this->users->create(array_merge($data, [
                'role_id' => 1,
                'status' => (settings('use_email') ? \VanguardLTE\Support\Enum\UserStatus::UNCONFIRMED : \VanguardLTE\Support\Enum\UserStatus::ACTIVE)
            ]));
            $role = \jeremykenedy\LaravelRoles\Models\Role::where('name', '=', 'User')->first();
            $user->attachRole($role);
            event(new \VanguardLTE\Events\User\Registered($user));
            $message = (settings('use_email') ? trans('app.account_create_confirm_email') : trans('app.account_created_login'));
            if( !settings('use_email') )
            {
                \Auth::login($user, true);
            }
            */
            return redirect()->route('frontend.auth.login')->with('success', $message);
        }

        public function checkUsername($username)
        {
            $generated = false;
            $key = 1;
            $logins = [];
            $generate = $username;
            $tmp = explode(',', settings('bots_login'));
            foreach( $tmp as $item )
            {
                $item = trim($item);
                if( $item )
                {
                    $logins[] = $item;
                }
            }
            while( !$generated )
            {
                $count = \VanguardLTE\User::where('username', $generate)->count();
                if( $count || in_array($generate, $logins) )
                {
                    $generate = $username . '_' . $key;
                }
                else
                {
                    $generated = true;
                }
                $key++;
            }
            return $generate;
        }
        public function confirmEmail($token)
        {
            if( $user = $this->users->findByConfirmationToken($token) )
            {
                $this->users->update($user->id, [
                    'status' => \VanguardLTE\Support\Enum\UserStatus::ACTIVE,
                    'confirmation_token' => null
                ]);
                return redirect()->to('/')->withSuccess(trans('app.email_confirmed_can_login'));
            }
            return redirect()->to('/')->withErrors(trans('app.wrong_confirmation_token'));
        }

        public function forgotPassword(\Illuminate\Http\Request $request)
        {
            $user = \VanguardLTE\User::where('email', $request->email)->first();
            if ($user == null) {
                return response()->json(['type' => 'error', 'msg' => "Email is not registered!"], 200);
            }
            event(new \VanguardLTE\Events\User\RequestedPasswordResetEmail($user));
            return response()->json(['type' => 'success', 'msg' => 'Password reset email sent. Check your inbox (and spam) folder.'], 200);
        }

        public function getPasswordReset(\Illuminate\Http\Request $request, $token)
        {
            $passwordReset = \VanguardLTE\PasswordReset::where(['token' => $token])->first();
            if(!$passwordReset)
            {
                return redirect('categories/all?forgotpassword=fail')->withErrors(trans('auth.failed'));
            }
            $elapsedMins = (time() - strtotime($passwordReset->created_at)) / 60;
            if($elapsedMins > 10)
            {
                return redirect('categories/all?forgotpassword=fail')->withErrors(trans('app.password_reset_expired'));
            }
            return redirect('categories/all?resetpassword=show')->with(['username' => $passwordReset->username, 'email' => $passwordReset->email, 'token' => $passwordReset->token]);
        }

        public function postPasswordReset(\Illuminate\Http\Request $request)
        {
            $passwordReset = \VanguardLTE\PasswordReset::where(['username' => $request->username, 'email' => $request->email, 'token' => $request->token])->first();
            if(!$passwordReset)
            {
                return redirect('categories/all?forgotpassword=fail')->withErrors(trans('auth.failed'));
            }
            $elapsedMins = (time() - strtotime($passwordReset->created_at)) / 60;
            if($elapsedMins > 10)
            {
                return redirect('categories/all?forgotpassword=fail')->withErrors(trans('app.password_reset_expired'));
            }
            $user = \VanguardLTE\User::where(['username' => $request->username, 'email' => $request->email])->first();
            if (!$user)
            {
                return redirect('categories/all?forgotpassword=fail')->withErrors(trans('auth.failed'));
            }
            if ($request->password != $request->password_confirmation)
            {
                return redirect('categories/all?resetpassword=fail')->with(['username' => $request->username, 'email' => $request->email, 'token' => $request->token])->withErrors(trans('validation.confirmed', ['attribute' => trans('app.password')]));
            }

            $user->password = $request->password;
            $user->save();

            event(new \VanguardLTE\Events\User\ResetedPasswordViaEmail($user));
            \Auth::login($user);

            return redirect('');
        }

        /* phone verify for free spin */

        public function phone_verify2(\Illuminate\Http\Request $request) {
               return response(json_encode([
                    'type' => 'exist_error',
                    'message' => "Please wait while you are redirected to get the 100 Free spins",
                    'url' => route('frontend.game.list.category', ['category1' => 'hot'])
                ]));
        }

        public function phone_verify(\Illuminate\Http\Request $request) {

            $existing_phone_check = \VanguardLTE\User::where('phone', $request['phone'])->count();

            if($existing_phone_check == 0) {
                return response(json_encode([
                    'type' => 'exist_error',
                    'message' => "Please wait while you are redirected to get the 100 Free spins",
                    'url' => route('frontend.game.list.category', ['category1' => 'hot'])
                ]));
            }
            if($existing_phone_check > 1) {
                return response(json_encode([
                    'type' => 'error',
                    'message' => "The 100 Free spin bonus is only available to new customers.. ",
                    'url' => route('frontend.game.list')
                ]));
            }
            $user = \VanguardLTE\User::where('phone', $request['phone'])->first();

            // $multi_accounts_check = \VanguardLTE\User::where('visitor_id', $user->visitor_id)->count();
            // if($multi_accounts_check > 1) {
            //     return response(json_encode([
            //         'type' => 'error',
            //         'message' => "You have multi accounts. If you have multi accounts, you can't get promotion for 100 free spin. ",
            //         'url' => route('frontend.game.list')
            //     ]));
            // }

            $existing_promotion_check = \VanguardLTE\WelcomePackageLog::where('user_id', $user->id)->count();
            if($existing_promotion_check > 0){
                return response(json_encode([
                    'type' => 'error',
                    'message' => "The 100 Free spin bonus is only available to new customers.",
                    'url' => route('frontend.game.list')
                ]));
            }

            $token = $this->TWILIO_AUTH_TOKEN;
            $twilio_sid = $this->TWILIO_SID;
            $twilio_verify_sid = $this->TWILIO_VERIFY_SID;

            $twilio = new Client($twilio_sid, $token);

            $verifications = $twilio->verify->v2->services($twilio_verify_sid)
                ->verifications
                ->create($request['phone'], "sms");

            return response(json_encode([
                'type' => 'success',
                'url' => route('frontend.promotions.up_to_100_free_spin_phone_confirm', ['p_num' => $request['phone']]),
            ]));
        }

        public function phone_confirm(\Illuminate\Http\Request $request) {

            $data = $request->validate([
                'verification_code' => ['required', 'numeric'],
                'phone_number' => ['required', 'string'],
            ]);

            $user = \VanguardLTE\User::where('phone', $data['phone_number'])->first();

            $token = $this->TWILIO_AUTH_TOKEN;
            $twilio_sid = $this->TWILIO_SID;
            $twilio_verify_sid = $this->TWILIO_VERIFY_SID;

            $twilio = new Client($twilio_sid, $token);

            $verification = $twilio->verify->v2->services($twilio_verify_sid)
                ->verificationChecks
                ->create($data['verification_code'], array('to' => $data['phone_number']));


            if ($verification->valid) {

                $promotion_user = new WelcomePackageLog;
                $promotion_user->user_id = $user->id;
                $promotion_user->day = 7;
                $promotion_user->freespin = 100;
                $promotion_user->remain_freespin = 100;
                $promotion_user->game_id = 975;
                $promotion_user->save();
                return redirect()->route('frontend.promotions.welcome_promotion_up_to_100_free_spin')->with(['message' => 'Phone number verified']);
            }
            return back()->with(['phone_number' => $data['phone_number'], 'error' => 'Invalid verification code entered!']);
        }
        public function usernameCheck(\Illuminate\Http\Request $request) {

            if (!$request->username){
                return response()->json(['type' => 'error', 'msg' => "input username!"], 200);
            }
            if (\VanguardLTE\User::where('username', $request->username)->count() == 0){
                return response()->json(['type' => 'success', 'msg' => "valid username!"], 200);
            }else{
                return response()->json(['type' => 'error', 'msg' => "username is already exist!"], 200);
            }
        }
        public function emailCheck(\Illuminate\Http\Request $request) {

            if (!$request->email){
                return response()->json(['type' => 'error', 'msg' => "input email!"], 200);
            }
            if (\VanguardLTE\User::where('email', $request->email)->count() == 0){
                return response()->json(['type' => 'success', 'msg' => "valid email!"], 200);
            }else{
                return response()->json(['type' => 'error', 'msg' => "email is already exist!"], 200);
            }
        }
        /* --- */
    }
}
