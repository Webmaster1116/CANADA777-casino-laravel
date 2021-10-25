<?php

Route::namespace('Frontend')->middleware(['siteisclosed'])->group(function () {

    Route::get('/{url}', 'GamesController@SEO')->where('url', '(home|alberta|british-columbia|manitoba|new-brunswick|northwest-territories|nova-scotia|ontario|prince-edward-island|saskatchewan|quebec|vegascasinoonline|casinoniagarafalls|betway|888casino)');

    ## Auth verify

	Route::get('login', [
		'as' => 'frontend.auth.login',
		'uses' => 'Auth\AuthController@getLogin'
	]);
    Route::post('login', [
        'as' => 'frontend.auth.login.post',
        'uses' => 'Auth\AuthController@postLogin'
    ]);
    Route::get('logout', [
        'as' => 'frontend.auth.logout',
        'uses' => 'Auth\AuthController@getLogout'
    ]);
    Route::get('ip', [
        'as' => 'frontend.auth.ip',
        'uses' => 'Auth\AuthController@getIP'
    ]);
    Route::get('usernameCheck', [
        'as' => 'frontend.auth.usernameCheck',
        'uses' => 'Auth\AuthController@usernameCheck'
    ]);
    Route::get('emailCheck', [
        'as' => 'frontend.auth.emailCheck',
        'uses' => 'Auth\AuthController@emailCheck'
    ]);
    Route::get('launcher/{game}/{token}/{mode}','Auth\AuthController@apiLogin' );

	Route::get('register', [
        'as' => 'frontend.register',
        'uses' => 'Auth\AuthController@getRegister'
    ]);
    Route::post('register', [
        'as' => 'frontend.register.post',
        'uses' => 'Auth\AuthController@postRegister'
    ]);
      Route::post('register2', [
        'as' => 'frontend.register2.post',
        'uses' => 'Auth\AuthController@postRegister2'
    ]);



    Route::post('register/page', [
        'as' => 'frontend.register.postpage',
        'uses' => 'Auth\AuthController@postRegisterPage'
    ]);
    Route::post('check_freespin100', [
        'as' => 'frontend.check_freespin100',
        'uses' => 'PagesController@check_freespin100'
    ]);
    Route::get('register/confirmation/{token}', [
        'as' => 'frontend.register.confirm-email',
        'uses' => 'Auth\AuthController@confirmEmail'
    ]);

    Route::get('forgotpassword', [
        'as' => 'frontend.forgotpassword',
        'uses' => 'Auth\AuthController@forgotPassword'
    ]);
    Route::get('password/reset/{token}', [
        'as' => 'frontend.password.reset',
        'uses' => 'Auth\AuthController@getPasswordReset'
    ]);
    Route::post('password/reset', [
        'as' => 'frontend.password.reset.post',
        'uses' => 'Auth\AuthController@postPasswordReset'
    ]);
    Route::get('support/ticket', [
        'as' => 'frontend.support.ticket',
        'uses' => 'SupportController@ticket'
    ]);
    ## Deposit

    Route::prefix('deposit')->group(function () {
        Route::get('payment', [
            'as' => 'frontend.deposit.payment',
            'uses' => 'PaymentController@gigadat'
        ]);
    });

    ## Cashout

    Route::prefix('cashout')->group(function () {
        Route::get('payment', [
            'as' => 'frontend.cashout.payment',
            'uses' => 'PaymentController@gigadat'
        ]);
    });

    ## Withdraw

    Route::prefix('withdraw')->group(function () {
        Route::get('payment', [
            'as' => 'frontend.withdraw.payment',
            'uses' => 'PaymentController@withdraw'
        ]);
    });



    Route::prefix('payment')->group(function () {
        Route::match(['get', 'post'], 'gigadat/success', 'PaymentController@gigadatSuccess');
        Route::match(['get', 'post'], 'gigadat/failure', 'PaymentController@gigadatFail');
        Route::match(['get', 'post'], 'gigadat/listener', 'PaymentController@gigadatListener');
    });

    Route::match(['get', 'post'], '/about', 'SupportController@about');

    Route::match(['get', 'post'], '/bonus', 'BonusController@index');
    Route::match(['get', 'post'], '/bonus/term', 'BonusController@term');

    Route::match(['get', 'post'], '/promotions', 'PromotionsController@index');

    /* cryptopayment api */

    Route::post('cryptocurrencies_list', [
        'as' => 'frontend.cryptopayment.cryptocurrencies_list',
        'uses' => 'PaymentController@cryptocurrencies_list'
    ]);
    Route::get('check_freemodal', [
        'as' => 'frontend.check_freemodal',
        'uses' => 'GamesController@check_freemodal'
    ]);
    Route::get('check_email', [
        'as' => 'frontend.check_email',
        'uses' => 'GamesController@check_email'
    ]);
    // Route::match(['get', 'post'], '/callback_cryptopayment', 'PaymentController@callback_cryptopayment');
    Route::post('/callback_cryptopayment', [
        'as' => 'frontend.games.callback_cryptopayment',
        'uses' => 'PaymentController@callback_cryptopayment'
    ]);
    /* --- */
	/*
    ## license config

	Route::get('new-license', [
        'as' => 'frontend.new_license',
        'uses' => 'PagesController@new_license'
    ]);
	Route::post('new-license', [
        'as' => 'frontend.new_license.post',
        'uses' => 'PagesController@new_license_post'
    ]);
	Route::get('license-error', [
        'as' => 'frontend.page.error_license',
        'uses' => 'PagesController@error_license'
    ]);
    Route::get('jpstv/{id?}', [
        'as' => 'frontend.jpstv',
        'uses' => 'PagesController@jpstv'
    ]);
    Route::get('jpstv.json', [
        'as' => 'frontend.jpstv_json',
        'uses' => 'PagesController@jpstv_json'
    ]);
    */

	/**
     * Dashboard
     */

    /*
	Route::get('statistics', [
        'as' => 'frontend.statistics',
        'uses' => 'DashboardController@statistics'
    ]);
    */
    Route::get('subsession', [
        'as' => 'frontend.subsession',
        'uses' => 'GamesController@subsession'
    ]);

	/**
     * User Profile
     */
    Route::get('profile/info', [
        'as' => 'frontend.profile.info',
        'uses' => 'ProfileController@index'
    ]);
    Route::get('profile/history/payment', [
        'as' => 'frontend.profile.history.payment',
        'uses' => 'ProfileController@payment_history'
    ]);
    Route::get('profile/history/bet', [
        'as' => 'frontend.profile.history.bet',
        'uses' => 'ProfileController@bet_history'
    ]);
    Route::get('profile/bonus', [
        'as' => 'frontend.profile.bonus',
        'uses' => 'ProfileController@bonus'
    ]);
    Route::get('profile/freespin', [
        'as' => 'frontend.profile.freespin',
        'uses' => 'ProfileController@freespin'
    ]);
    Route::get('profile/activity', [
        'as' => 'frontend.profile.activity',
        'uses' => 'ProfileController@activity'
    ]);
	Route::get('profile/balance', [
        'as' => 'frontend.profile.balance',
        'uses' => 'ProfileController@balance'
    ]);
	Route::post('profile/balance', [
        'as' => 'frontend.profile.balance.post',
        'uses' => 'ProfileController@balanceAdd'
    ]);
	Route::get('profile/balance/success', [
        'as' => 'frontend.profile.balance.success',
        'uses' => 'ProfileController@success'
    ]);
	Route::get('profile/balance/fail', [
        'as' => 'frontend.profile.balance.fail',
        'uses' => 'ProfileController@fail'
    ]);
    Route::post('profile/details/update', [
        'as' => 'frontend.profile.update.details',
        'uses' => 'ProfileController@updateDetails'
    ]);
	Route::post('profile/password/update', [
        'as' => 'frontend.profile.update.password',
        'uses' => 'ProfileController@updatePassword'
    ]);
    Route::post('profile/avatar/update', [
        'as' => 'frontend.profile.update.avatar',
        'uses' => 'ProfileController@updateAvatar'
    ]);
    Route::post('profile/avatar/update/external', [
        'as' => 'frontend.profile.update.avatar-external',
        'uses' => 'ProfileController@updateAvatarExternal'
    ]);

	Route::post('profile/exchange', [
        'as' => 'frontend.profile.exchange',
        'uses' => 'ProfileController@exchange'
    ]);

    Route::put('profile/login-details/update', [
        'as' => 'frontend.profile.update.login-details',
        'uses' => 'ProfileController@updateLoginDetails'
    ]);
    Route::post('profile/two-factor/enable', [
        'as' => 'frontend.profile.two-factor.enable',
        'uses' => 'ProfileController@enableTwoFactorAuth'
    ]);
    Route::post('profile/two-factor/disable', [
        'as' => 'frontend.profile.two-factor.disable',
        'uses' => 'ProfileController@disableTwoFactorAuth'
    ]);
    Route::get('profile/sessions', [
        'as' => 'frontend.profile.sessions',
        'uses' => 'ProfileController@sessions'
    ]);
    Route::delete('profile/sessions/{session}/invalidate', [
        'as' => 'frontend.profile.sessions.invalidate',
        'uses' => 'ProfileController@invalidateSession'
    ]);

	Route::get('profile/returns', [
        'as' => 'frontend.profile.returns',
        'uses' => 'ProfileController@returns'
    ]);

    Route::get('profile/jackpots', [
        'as' => 'frontend.profile.jackpots',
        'uses' => 'ProfileController@jackpots'
    ]);

    Route::get('profile/pincode', [
        'as' => 'frontend.profile.pincode',
        'uses' => 'ProfileController@pincode'
    ]);
    Route::get('profile/verify', [
        'as' => 'frontend.profile.verify',
        'uses' => 'ProfileController@verify'
    ]);
    Route::post('profile/verify/submit', [
        'as' => 'frontend.profile.submit',
        'uses' => 'ProfileController@submitImage'
    ]);
    Route::get('profile/password', [
        'as' => 'frontend.profile.password',
        'uses' => 'ProfileController@password'
    ]);
    Route::get('profile/detail', [
        'as' => 'frontend.profile.detail',
        'uses' => 'ProfileController@detail'
    ]);
    Route::get('profile/transaction', [
        'as' => 'frontend.profile.transaction',
        'uses' => 'ProfileController@transaction'
    ]);
    Route::get('setlang/{lang}', [
        'as' => 'frontend.setlang',
        'uses' => 'ProfileController@setlang'
    ]);

	//redesign deposit with color like casino.com
    Route::get('profile/deposit', [
        'as' => 'frontend.profile.deposit',
        'uses' => 'ProfileController@deposit'
    ]);
    Route::get('profile/withdraw', [
        'as' => 'frontend.profile.withdraw',
        'uses' => 'ProfileController@withdraw'
    ]);
	/**
     * Games routes
    */

	Route::get('/', [
        'as' => 'frontend.game.list',
        'uses' => 'GamesController@index'
    ]);
	Route::get('/search', [
        'as' => 'frontend.game.search',
        'uses' => 'GamesController@search'
    ]);
	/*
	Route::get('games', [
        'as' => 'frontend.game.list',
        'uses' => 'GamesController@index'
    ]);
	*/

	Route::get('categories/{category1}', [
        'as' => 'frontend.game.list.category',
        'uses' => 'GamesController@index'
    ]);

	Route::get('categories/{category1}/{category2}', [
        'as' => 'frontend.game.list.category_level2',
        'uses' => 'GamesController@index'
    ]);

    Route::get('setpage.json', [
        'as' => 'frontend.category.setpage',
        'uses' => 'GamesController@setpage'
    ]);

    // changed by game developer
    Route::get('game/{game}/{prego}', [
        'as' => 'frontend.game.go.prego',
        'uses' => 'GamesController@go'
    ]);
    Route::get('/game_init', [
        'as' => 'frontend.game.init',
        'uses' => 'GamesController@init'
    ]);
	Route::post('game/{game}/server', [
        'as' => 'frontend.game.server',
        'uses' => 'GamesController@server'
    ]);
	Route::get('/game_stat', [
        'as' => 'frontend.game_stat',
        'uses' => 'GamesController@game_stat',
    ]);

    Route::post('/search', [
        'as' => 'frontend.search.game',
        'uses' => 'GamesController@searchgame',
    ]);

    Route::group(['prefix' => 'ajax'], function () {
        Route::get('/loadmore/game', [
            'as' => 'frontend.loadmore.game',
            'uses' => 'GamesController@loadmore',
        ]);
    });

    /* games from api */
    Route::get('/apigame/{game}/{type}', [
        'as' => 'frontend.game.apigame',
        'uses' => 'GamesController@apigame'
    ]);

    Route::get('/callback_gamehub', [
        'as' => 'frontend.games.callback_gamehub',
        'uses' => 'GamesController@callback_gamehub'
    ]);
    /* --- */

    /* api for free spin */
	Route::get('/canada777-up-to-100-free-spin', [
        'as' => 'frontend.promotions.up_to_100_free_spin',
        'uses' => 'PromotionsController@up_to_100_free_spin',
    ]);

    Route::post('/phone_verify', 'Auth\AuthController@phone_verify')->name('phone_verify');

   //Route::post('/phone_verify2', 'Auth\AuthController@phone_verify2')->name('phone_verify2');

Route::post('phone_verify2', [
        'as' => 'frontend.phone_verify2.post',
        'uses' => 'Auth\AuthController@phone_verify2'
    ]);


    Route::get('/canada777-up-to-100-free-spin-phone-confirm', [
        'as' => 'frontend.promotions.up_to_100_free_spin_phone_confirm',
        'uses' => 'PromotionsController@up_to_100_free_spin_phone_confirm',
    ]);

    Route::post('/phone_confirm', 'Auth\AuthController@phone_confirm')->name('phone_confirm');

    Route::get('/canada777-welcome-up-to-100-free-spin', [
        'as' => 'frontend.promotions.welcome_promotion_up_to_100_free_spin',
        'uses' => 'PromotionsController@welcome_up_to_100_free_spin',
    ]);
	/* --- */


/* api2 for free spin */
	Route::get('/canada777-up-to-100-free-spin2', [
        'as' => 'frontend.promotions.up_to_100_free_spin2',
        'uses' => 'PromotionsController@up_to_100_free_spin2',
    ]);

    Route::get('/canada777-up-to-100-free-spin-phone-confirm2', [
        'as' => 'frontend.promotions.up_to_100_free_spin_phone_confirm2',
        'uses' => 'PromotionsController@up_to_100_free_spin_phone_confirm2',
    ]);


    Route::get('/canada777-welcome-up-to-100-free-spin2', [
        'as' => 'frontend.promotions.welcome_promotion_up_to_100_free_spin2',
        'uses' => 'PromotionsController@welcome_up_to_100_free_spin2',
    ]);
	/* --- */


});

/**
*
*
*
******************* BACKEND
*
*
*
*/

Route::prefix('backend')->group(function () {
    Route::namespace('Backend')->group(function () {
        Route::get('login', [
            'as' => 'backend.auth.login',
            'uses' => 'Auth\AuthController@getLogin'
        ]);
        Route::post('login', [
            'as' => 'backend.auth.login.post',
            'uses' => 'Auth\AuthController@postLogin'
        ]);
    });
});

Route::prefix('backend')->middleware(['auth'])->group(function () {
	Route::namespace('Backend')->group(function () {

	Route::get('logout', [
		'as' => 'backend.auth.logout',
		'uses' => 'Auth\AuthController@getLogout'
	]);

    /**
     * Dashboard
     */

    Route::get('/search', [
        'as' => 'backend.search',
        'uses' => 'DashboardController@search',
        'middleware' => 'permission:full.search',
    ]);

    Route::get('/', [
        'as' => 'backend.dashboard',
        'uses' => 'DashboardController@index',
    ]);
	Route::get('/game_stat', [
        'as' => 'backend.game_stat',
        'uses' => 'DashboardController@game_stat',
        'middleware' => 'permission:stats.game',
    ]);
	Route::delete('/game_stat/clear', [
        'as' => 'backend.game_stat.clear',
        'uses' => 'DashboardController@game_stat_clear'
    ]);
	Route::get('/bank_stat', [
        'as' => 'backend.bank_stat',
        'uses' => 'DashboardController@bank_stat',
        'middleware' => 'permission:stats.bank',
    ]);
	Route::get('/shop_stat', [
        'as' => 'backend.shop_stat',
        'uses' => 'DashboardController@shop_stat',
        'middleware' => 'permission:stats.shop',
    ]);
	Route::get('/shift_stat', [
        'as' => 'backend.shift_stat',
        'uses' => 'DashboardController@shift_stat',
        'middleware' => 'permission:stats.shift',
    ]);
	Route::get('/live', [
        'as' => 'backend.live_stat',
        'uses' => 'DashboardController@live_stat',
        'middleware' => 'permission:stats.live',
    ]);

	Route::get('/start_shift', [
        'as' => 'backend.start_shift',
        'uses' => 'DashboardController@start_shift'
    ]);

    ##Country Manage

    Route::get('/country', [
        'as' => 'backend.country',
        'uses' => 'UsersController@country',
        'middleware' => 'permission:country.manage'
    ]);


    Route::get('/currency', [
        'as' => 'backend.currency',
        'uses' => 'UsersController@country',
        'middleware' => 'permission:currency.manage'
    ]);

    /* withdraw section */
    Route::get('/withdraw', [
        'as' => 'backend.withdraw.list',
        'uses' => 'WithDrawController@index',
        'middleware' => 'permission:withdraw.manage'
    ]);

    Route::match(['get','post'], '/withdraw/approve/{id}', [
        'as' => 'backend.withdraw.approve',
        'uses' => 'WithDrawController@approve',
        'middleware' => 'permission:withdraw.manage'
    ]);
    Route::match(['get','post'], '/withdraw/reject/{id}', [
        'as' => 'backend.withdraw.reject',
        'uses' => 'WithDrawController@reject',
        'middleware' => 'permission:withdraw.manage'
    ]);

    Route::match(['get','post'], '/crypto_withdraw/approve/{id}', [
        'as' => 'backend.crypto_withdraw.approve',
        'uses' => 'WithDrawController@crypto_approve',
        'middleware' => 'permission:withdraw.manage'
    ]);

    Route::match(['get','post'], '/crypto_withdraw/reject/{id}', [
        'as' => 'backend.crypto_withdraw.reject',
        'uses' => 'WithDrawController@crypto_reject',
        'middleware' => 'permission:withdraw.manage'
    ]);


    /* --- */

    /**
    * User Profile
    **/

    Route::get('profile', [
        'as' => 'backend.profile',
        'uses' => 'ProfileController@index'
    ]);
    Route::get('profile/activity', [
        'as' => 'backend.profile.activity',
        'uses' => 'ProfileController@activity'
    ]);
    Route::put('profile/details/update', [
        'as' => 'backend.profile.update.details',
        'uses' => 'ProfileController@updateDetails'
    ]);
    Route::post('profile/avatar/update', [
        'as' => 'backend.profile.update.avatar',
        'uses' => 'ProfileController@updateAvatar'
    ]);
    Route::post('profile/avatar/update/external', [
        'as' => 'backend.profile.update.avatar-external',
        'uses' => 'ProfileController@updateAvatarExternal'
    ]);
    Route::put('profile/login-details/update', [
        'as' => 'backend.profile.update.login-details',
        'uses' => 'ProfileController@updateLoginDetails'
    ]);
    Route::post('profile/two-factor/enable', [
        'as' => 'backend.profile.two-factor.enable',
        'uses' => 'ProfileController@enableTwoFactorAuth'
    ]);
    Route::post('profile/two-factor/disable', [
        'as' => 'backend.profile.two-factor.disable',
        'uses' => 'ProfileController@disableTwoFactorAuth'
    ]);
    Route::get('profile/sessions', [
        'as' => 'backend.profile.sessions',
        'uses' => 'ProfileController@sessions'
    ]);
    Route::delete('profile/sessions/{session}/invalidate', [
        'as' => 'backend.profile.sessions.invalidate',
        'uses' => 'ProfileController@invalidateSession'
    ]);
	Route::match(['get','post'], 'profile/setshop', [
        'as' => 'backend.profile.setshop',
        'uses' => 'ProfileController@setshop'
    ]);

    /**
     * User Management
    */

    Route::get('user', [
        'as' => 'backend.user.list',
        'uses' => 'UsersController@index',
        'middleware' => 'permission:users.manage'
    ]);
    Route::get('tree', [
        'as' => 'backend.user.tree',
        'uses' => 'UsersController@tree',
        'middleware' => 'permission:users.tree'
    ]);
    Route::get('statistics', [
        'as' => 'backend.statistics',
        'uses' => 'DashboardController@statistics',
        'middleware' => 'permission:stats.pay',
    ]);
	Route::post('profile/balance/update', [
        'uses' => 'UsersController@updateBalance',
		'as' => 'backend.user.balance.update',
		'middleware' => 'permission:users.balance.manage'
    ]);
    Route::post('profile/balance/update/manually', [
        'uses' => 'UsersController@updateBalanceManually',
        'as' => 'backend.user.balance.update.manually',
        'middleware' => 'permission:users.balance.manage'
    ]);
    Route::get('user/create', [
        'as' => 'backend.user.create',
        'uses' => 'UsersController@create',
        'middleware' => 'permission:users.add'
    ]);
    Route::post('user/create', [
        'as' => 'backend.user.store',
        'uses' => 'UsersController@store',
        'middleware' => 'permission:users.add'
    ]);
	Route::get('user/{user}/stat', [
        'as' => 'backend.user.stat',
        'uses' => 'UsersController@statistics'
    ]);
    Route::post('user/mass', [
        'as' => 'backend.user.massadd',
        'uses' => 'UsersController@massadd',
        'middleware' => 'permission:users.add'
    ]);
    Route::get('user/{user}/show', [
        'as' => 'backend.user.show',
        'uses' => 'UsersController@view'
    ]);
    Route::get('user/{user}/profile', [
        'as' => 'backend.user.edit',
        'uses' => 'UsersController@edit'
    ]);
    Route::put('user/{user}/update/details', [
        'as' => 'backend.user.update.details',
        'uses' => 'UsersController@updateDetails'
    ]);
    Route::put('user/{user}/update/verify', [
        'as' => 'backend.user.update.verify',
        'uses' => 'UsersController@updateVerify'
    ]);
    Route::put('user/{user}/update/login-details', [
        'as' => 'backend.user.update.login-details',
        'uses' => 'UsersController@updateLoginDetails'
    ]);
    Route::delete('user/{user}/delete', [
        'as' => 'backend.user.delete',
        'uses' => 'UsersController@delete',
		'middleware' => 'permission:users.delete'
    ]);
    Route::delete('user/{user}/hard_delete', [
        'as' => 'backend.user.hard_delete',
        'uses' => 'UsersController@hard_delete',
        'middleware' => 'permission:users.delete'
    ]);
    Route::post('user/{user}/update/avatar', [
        'as' => 'backend.user.update.avatar',
        'uses' => 'UsersController@updateAvatar'
    ]);
    Route::post('user/{user}/update/avatar/external', [
        'as' => 'backend.user.update.avatar.external',
        'uses' => 'UsersController@updateAvatarExternal'
    ]);
    Route::post('user/{user}/update/deposit-amount', [
        'as' => 'backend.user.update.deposit-amount',
        'uses' => 'UsersController@updateDepositAmount'
    ]);
    Route::get('user/{user}/sessions', [
        'as' => 'backend.user.sessions',
        'uses' => 'UsersController@sessions'
    ]);
    Route::delete('user/{user}/sessions/{session}/invalidate', [
        'as' => 'backend.user.sessions.invalidate',
        'uses' => 'UsersController@invalidateSession'
    ]);
    Route::post('user/{user}/two-factor/enable', [
        'as' => 'backend.user.two-factor.enable',
        'uses' => 'UsersController@enableTwoFactorAuth'
    ]);
    Route::post('user/{user}/two-factor/disable', [
        'as' => 'backend.user.two-factor.disable',
        'uses' => 'UsersController@disableTwoFactorAuth'
    ]);

        Route::delete('user/action/{action}', [
            'as' => 'backend.user.action',
            'uses' => 'UsersController@action',
        ]);

    /* Notifications */
    Route::get('notifications', [
        'as' => 'backend.notifications.list',
        'uses' => 'NotificationsController@index',
        'middleware' => 'permission:notifications.manage'
    ]);
    Route::match(['get', 'post'], 'notifications/add', [
        'as' => 'backend.notifications.add',
        'uses' => 'NotificationsController@add',
        'middleware' => 'permission:notifications.manage'
    ]);
    Route::match(['get', 'post'], 'notifications/edit/{id}', [
        'as' => 'backend.notifications.edit',
        'uses' => 'NotificationsController@edit',
        'middleware' => 'permission:notifications.manage'
    ]);
    Route::get('notifications/delete/{id}', [
        'as' => 'backend.notifications.delete',
        'uses' => 'NotificationsController@delete',
        'middleware' => 'permission:notifications.manage'
    ]);
    /* --- */

    /* automizy service */
    Route::get('/automizy/create_list', [
        'as' => 'backend.automizy.create_list',
        'uses' => 'AutomizyController@create_list',
        'middleware' => 'permission:automizy.manage'
    ]);

    Route::get('/automizy/list', [
        'as' => 'backend.automizy.list',
        'uses' => 'AutomizyController@index',
        'middleware' => 'permission:automizy.manage'
    ]);
    Route::match(['get', 'post'], '/automizy/add_list', [
        'as' => 'backend.automizy.add_list',
        'uses' => 'AutomizyController@add_list',
        'middleware' => 'permission:automizy.manage'
    ]);
    Route::match(['get', 'post'], '/automizy/edit_list/{id}', [
        'as' => 'backend.automizy.edit_list',
        'uses' => 'AutomizyController@edit_list',
        'middleware' => 'permission:automizy.manage'
    ]);
    Route::get('/automizy/delete_list/{id}', [
        'as' => 'backend.automizy.delete_list',
        'uses' => 'AutomizyController@delete_list',
        'middleware' => 'permission:automizy.manage'
    ]);
    Route::get('/automizy/add_contacts/{id}/{email}', [
        'as' => 'backend.automizy.add_contacts',
        'uses' => 'AutomizyController@add_contacts',
        'middleware' => 'permission:automizy.manage'
    ]);
    // Route::match(['get', 'post'], '/automizy/delete_contacts/{id}/{contact}', [
    //     'as' => 'backend.automizy.delete_contacts',
    //     'uses' => 'AutomizyController@delete_contacts',
    //     'middleware' => 'permission:automizy.manage'
    // ]);
    /* --- */

	/**
     * Games routes
    */

	Route::get('game', [
        'as' => 'backend.game.list',
        'uses' => 'GamesController@index',
        'middleware' => 'permission:games.manage'
    ]);
	Route::get('games.json', [
        'as' => 'backend.game.list.json',
        'uses' => 'GamesController@index_json'
    ]);
    Route::get('game/create', [
        'as' => 'backend.game.create',
        'uses' => 'GamesController@create',
        'middleware' => 'permission:games.add'
    ]);
    Route::post('game/create', [
        'as' => 'backend.game.store',
        'uses' => 'GamesController@store',
        'middleware' => 'permission:games.add'
    ]);
    Route::get('game/{game}/show', [
        'as' => 'backend.game.show',
        'uses' => 'GamesController@view',
    ]);
	Route::get('game/{game}', [
        'as' => 'backend.game.go',
        'uses' => 'GamesController@go'
    ]);
	Route::post('/game/{game}/server', [
        'as' => 'backend.game.server',
        'uses' => 'GamesController@server'
    ]);
    Route::get('game/{game}/edit', [
        'as' => 'backend.game.edit',
        'uses' => 'GamesController@edit',
        'middleware' => 'permission:games.edit'
    ]);
    Route::get('game/{apigame}/apiedit', [
        'as' => 'backend.game.apiedit',
        'uses' => 'GamesController@apiedit',
        'middleware' => 'permission:games.edit'
    ]);
	Route::post('game/{game}/update', [
        'as' => 'backend.game.update',
        'uses' => 'GamesController@update',
    ]);
	Route::post('game/{apigame}/apiupdate', [
        'as' => 'backend.game.apiupdate',
        'uses' => 'GamesController@apiupdate',
    ]);
    Route::delete('game/{game}/delete', [
        'as' => 'backend.game.delete',
        'uses' => 'GamesController@delete',
        'middleware' => 'permission:games.delete'
    ]);
	Route::post('game/categories', [
        'as' => 'backend.game.categories',
        'uses' => 'GamesController@categories',
    ]);
    Route::post('game/update/mass', [
        'as' => 'backend.game.mass',
        'uses' => 'GamesController@mass',
        'middleware' => 'permission:games.edit'
    ]);
    Route::post('game/orderupdate', [
        'as' => 'backend.game.orderupdate',
        'uses' => 'GamesController@orderupdate'
    ]);


    Route::post('gamebanks_add', [
       'as' => 'backend.game.gamebanks_add',
        'uses' => 'GamesController@gamebanks_add',
    ]);
    Route::get('gamebanks_clear', [
        'as' => 'backend.game.gamebanks_clear',
        'uses' => 'GamesController@gamebanks_clear',
    ]);

/**
     * Bonus routes
    */

	Route::get('bonus', [
        'as' => 'backend.bonus.list',
        'uses' => 'BonusController@index',
        'middleware' => 'permission:bonus.manage'
    ]);
    Route::match(['get', 'post'], 'bonus/add', [
        'as' => 'backend.bonus.add',
        'uses' => 'BonusController@add',
        'middleware' => 'permission:bonus.manage'
    ]);
    Route::match(['get', 'post'], 'bonus/edit/{id}', [
        'as' => 'backend.bonus.edit',
        'uses' => 'BonusController@edit',
        'middleware' => 'permission:bonus.manage'
    ]);
    Route::get('bonus/delete/{id}', [
        'as' => 'backend.bonus.delete',
        'uses' => 'BonusController@delete',
        'middleware' => 'permission:bonus.manage'
    ]);

    /* freespin round */
    Route::get('freespinround', [
        'as' => 'backend.freespinround.list',
        'uses' => 'FreespinroundController@index',
        'middleware' => 'permission:freespinround.manage'
    ]);
    Route::match(['get', 'post'], 'freespinround/add', [
        'as' => 'backend.freespinround.add',
        'uses' => 'FreespinroundController@add',
        'middleware' => 'permission:freespinround.manage'
    ]);
    Route::match(['get', 'post'], 'freespinround/edit/{id}', [
        'as' => 'backend.freespinround.edit',
        'uses' => 'FreespinroundController@edit',
        'middleware' => 'permission:freespinround.manage'
    ]);
    Route::get('freespinround/delete/{id}', [
        'as' => 'backend.freespinround.delete',
        'uses' => 'FreespinroundController@delete',
        'middleware' => 'permission:freespinround.manage'
    ]);
    /* --- */
    /* free play user */
    Route::get('freeplay', [
        'as' => 'backend.freeplay.list',
        'uses' => 'FreeplayController@index',
        'middleware' => 'permission:freespinround.manage'
    ]);
    /* --- */
    /**
     * Categories routes
     */

	Route::get('category', [
        'as' => 'backend.category.list',
        'uses' => 'CategoriesController@index',
        'middleware' => 'permission:categories.manage'
    ]);
    Route::get('category/create', [
        'as' => 'backend.category.create',
        'uses' => 'CategoriesController@create',
        'middleware' => 'permission:categories.add'
    ]);
    Route::post('category/create', [
        'as' => 'backend.category.store',
        'uses' => 'CategoriesController@store',
        'middleware' => 'permission:categories.add'
    ]);
    Route::get('category/{category}/edit', [
        'as' => 'backend.category.edit',
        'uses' => 'CategoriesController@edit',
    ]);
	Route::post('category/{category}/update', [
        'as' => 'backend.category.update',
        'uses' => 'CategoriesController@update',
    ]);
	Route::delete('category/{category}/delete', [
        'as' => 'backend.category.delete',
        'uses' => 'CategoriesController@delete',
        'middleware' => 'permission:categories.delete'
    ]);

	/**
     * Categories routes
     */

	Route::get('shops', [
        'as' => 'backend.shop.list',
        'uses' => 'ShopsController@index',
        'middleware' => 'permission:shops.manage'
    ]);
    Route::get('shops/create', [
        'as' => 'backend.shop.create',
        'uses' => 'ShopsController@create',
        'middleware' => 'permission:shops.manage'
    ]);
    Route::post('shops/create', [
        'as' => 'backend.shop.store',
        'uses' => 'ShopsController@store',
        'middleware' => 'permission:shops.manage'
    ]);

    Route::get('shops/admin/create', [
        'as' => 'backend.shop.admin_create',
        'uses' => 'ShopsController@admin_create',
        'middleware' => 'permission:shops.manage'
    ]);
    Route::post('shops/admin/create', [
        'as' => 'backend.shop.admin_store',
        'uses' => 'ShopsController@admin_store',
        'middleware' => 'permission:shops.manage'
    ]);

    Route::get('shops/{shop}/edit', [
        'as' => 'backend.shop.edit',
        'uses' => 'ShopsController@edit',
        'middleware' => 'permission:shops.manage'
    ]);
	Route::post('shops/{shop}/update', [
        'as' => 'backend.shop.update',
        'uses' => 'ShopsController@update',
        'middleware' => 'permission:shops.manage'
    ]);
	Route::post('shops/balance', [
        'as' => 'backend.shop.balance',
        'uses' => 'ShopsController@balance',
        'middleware' => 'permission:shops.manage'
    ]);
	Route::delete('shops/{shop}/delete', [
        'as' => 'backend.shop.delete',
        'uses' => 'ShopsController@delete',
        'middleware' => 'permission:shops.manage'
    ]);
	Route::delete('shops/{shop}/hard_delete', [
        'as' => 'backend.shop.hard_delete',
        'uses' => 'ShopsController@hard_delete',
        'middleware' => 'permission:shops.manage'
    ]);
    Route::delete('shops/{shop}/action/{action}', [
        'as' => 'backend.shop.action',
        'uses' => 'ShopsController@action',
        'middleware' => 'permission:shops.manage'
    ]);

        /**
         * Pincodes routes
         */

        Route::get('pincodes', [
            'as' => 'backend.pincode.list',
            'uses' => 'PincodeController@index',
            'middleware' => 'permission:pincodes.manage'
        ]);
        Route::get('pincodes/create', [
            'as' => 'backend.pincode.create',
            'uses' => 'PincodeController@create',
            'middleware' => 'permission:pincodes.add'
        ]);
        Route::post('pincodes/create', [
            'as' => 'backend.pincode.store',
            'uses' => 'PincodeController@store',
            'middleware' => 'permission:pincodes.add'
        ]);
        Route::post('pincodes/mass/create', [
            'as' => 'backend.pincode.massadd',
            'uses' => 'PincodeController@massadd',
            'middleware' => 'permission:pincodes.add'
        ]);
        Route::get('pincodes/{pincode}/edit', [
            'as' => 'backend.pincode.edit',
            'uses' => 'PincodeController@edit',
        ]);
        Route::post('pincodes/{pincode}/update', [
            'as' => 'backend.pincode.update',
            'uses' => 'PincodeController@update',
        ]);
        Route::post('pincodes/balance', [
            'as' => 'backend.pincode.balance',
            'uses' => 'PincodeController@balance',
        ]);
        Route::delete('pincodes/{pincode}/delete', [
            'as' => 'backend.pincode.delete',
            'uses' => 'PincodeController@delete',
            'middleware' => 'permission:pincodes.delete'
        ]);

        /**
         * Happyhours routes
         */

        Route::get('happyhours', [
            'as' => 'backend.happyhour.list',
            'uses' => 'HappyHourController@index',
            'middleware' => 'permission:happyhours.manage'
        ]);
        Route::get('happyhours/create', [
            'as' => 'backend.happyhour.create',
            'uses' => 'HappyHourController@create',
            'middleware' => 'permission:happyhours.add'
        ]);
        Route::post('happyhours/create', [
            'as' => 'backend.happyhour.store',
            'uses' => 'HappyHourController@store',
            'middleware' => 'permission:happyhours.add'
        ]);
        Route::get('happyhours/{happyhour}/edit', [
            'as' => 'backend.happyhour.edit',
            'uses' => 'HappyHourController@edit',
        ]);
        Route::post('happyhours/{happyhour}/update', [
            'as' => 'backend.happyhour.update',
            'uses' => 'HappyHourController@update',
        ]);
        Route::delete('happyhours/{happyhour}/delete', [
            'as' => 'backend.happyhour.delete',
            'uses' => 'HappyHourController@delete',
            'middleware' => 'permission:happyhours.delete'
        ]);

        /**
         * Info routes
         */

        Route::get('info', [
            'as' => 'backend.info.list',
            'uses' => 'InfoController@index',
            'middleware' => 'permission:helpers.manage'
        ]);
        Route::get('info/create', [
            'as' => 'backend.info.create',
            'uses' => 'InfoController@create',
            'middleware' => 'permission:helpers.add'
        ]);
        Route::post('info/create', [
            'as' => 'backend.info.store',
            'uses' => 'InfoController@store',
            'middleware' => 'permission:helpers.add'
        ]);
        Route::get('info/{info}/edit', [
            'as' => 'backend.info.edit',
            'uses' => 'InfoController@edit',
        ]);
        Route::post('info/{info}/update', [
            'as' => 'backend.info.update',
            'uses' => 'InfoController@update',
        ]);
        Route::post('info/balance', [
            'as' => 'backend.info.balance',
            'uses' => 'InfoController@balance',
        ]);
        Route::delete('info/{info}/delete', [
            'as' => 'backend.info.delete',
            'uses' => 'InfoController@delete',
            'middleware' => 'permission:helpers.delete'
        ]);

        /**
         * Info routes
         */

        Route::get('api', [
            'as' => 'backend.api.list',
            'uses' => 'ApiController@index',
            'middleware' => 'permission:api.manage'
        ]);
        Route::get('api/create', [
            'as' => 'backend.api.create',
            'uses' => 'ApiController@create',
            'middleware' => 'permission:api.add',
        ]);
        Route::post('api/create', [
            'as' => 'backend.api.store',
            'uses' => 'ApiController@store',
            'middleware' => 'permission:api.add',
        ]);
        Route::get('api/generate', [
            'as' => 'backend.api.generate',
            'uses' => 'ApiController@generate',
        ]);
        Route::get('api/json', [
            'as' => 'backend.api.json',
            'uses' => 'ApiController@json',
        ]);
        Route::get('api/{api}/edit', [
            'as' => 'backend.api.edit',
            'uses' => 'ApiController@edit',
        ]);
        Route::post('api/{api}/update', [
            'as' => 'backend.api.update',
            'uses' => 'ApiController@update',
        ]);
        Route::post('api/balance', [
            'as' => 'backend.api.balance',
            'uses' => 'ApiController@balance',
        ]);
        Route::delete('api/{api}/delete', [
            'as' => 'backend.api.delete',
            'uses' => 'ApiController@delete',
            'middleware' => 'permission:api.delete',
        ]);


        /**
         * Return routes
         */

    	Route::get('returns', [
            'as' => 'backend.returns.list',
            'uses' => 'ReturnsController@index',
            'middleware' => 'permission:returns.manage',
        ]);
        Route::get('returns/create', [
            'as' => 'backend.returns.create',
            'uses' => 'ReturnsController@create',
            'middleware' => 'permission:returns.add',
        ]);
        Route::post('returns/create', [
            'as' => 'backend.returns.store',
            'uses' => 'ReturnsController@store',
            'middleware' => 'permission:returns.add',
        ]);
        Route::get('returns/{return}/edit', [
            'as' => 'backend.returns.edit',
            'uses' => 'ReturnsController@edit',
        ]);
    	Route::post('returns/{return}/update', [
            'as' => 'backend.returns.update',
            'uses' => 'ReturnsController@update',
        ]);
    	Route::delete('returns/{return}/delete', [
            'as' => 'backend.returns.delete',
            'uses' => 'ReturnsController@delete',
            'middleware' => 'permission:returns.delete',
        ]);

        /**
         * Roles & Permissions
         */

        Route::get('jpgame', [
            'as' => 'backend.jpgame.list',
            'uses' => 'JPGController@index',
            //'middleware' => 'permission:jackpots.manage',
        ]);
        Route::get('jpgame/create', [
            'as' => 'backend.jpgame.create',
            'uses' => 'JPGController@create',
            //'middleware' => 'permission:jackpots.add'
        ]);
        Route::post('jpgame/create', [
            'as' => 'backend.jpgame.store',
            'uses' => 'JPGController@store',
            //'middleware' => 'permission:jackpots.add'
        ]);
        Route::get('jpgame/{jackpot}/edit', [
            'as' => 'backend.jpgame.edit',
            'uses' => 'JPGController@edit',
        ]);
        Route::post('jpgame/{jackpot}/update', [
            'as' => 'backend.jpgame.update',
            'uses' => 'JPGController@update',
        ]);
        Route::post('jpgame/balance', [
            'as' => 'backend.jpgame.balance',
            'uses' => 'JPGController@balance',
        ]);

        /**
        * Roles & Permissions
        */

        Route::get('role', [
            'as' => 'backend.role.index',
            'uses' => 'RolesController@index',
            'middleware' => 'permission:roles.manage'
        ]);
        Route::get('role/create', [
            'as' => 'backend.role.create',
            'uses' => 'RolesController@create'
        ]);
        Route::post('role/store', [
            'as' => 'backend.role.store',
            'uses' => 'RolesController@store'
        ]);
        Route::get('role/{role}/edit', [
            'as' => 'backend.role.edit',
            'uses' => 'RolesController@edit'
        ]);
        Route::put('role/{role}/update', [
            'as' => 'backend.role.update',
            'uses' => 'RolesController@update'
        ]);
        Route::delete('role/{role}/delete', [
            'as' => 'backend.role.delete',
            'uses' => 'RolesController@delete'
        ]);

        Route::post('permission/save', [
            'as' => 'backend.permission.save',
            'uses' => 'PermissionsController@saveRolePermissions'
        ]);

    	/**
         * Permissions
         */

    	Route::get('permission', [
            'as' => 'backend.permission.index',
            'uses' => 'PermissionsController@index',
            'middleware' => 'permission:permissions.manage'
        ]);
        Route::get('permission/create', [
            'as' => 'backend.permission.create',
            'uses' => 'PermissionsController@create',
            'middleware' => 'permission:permissions.add'
        ]);
        Route::post('permission/store', [
            'as' => 'backend.permission.store',
            'uses' => 'PermissionsController@store',
            'middleware' => 'permission:permissions.add'
        ]);
        Route::get('permission/{permission}/edit', [
            'as' => 'backend.permission.edit',
            'uses' => 'PermissionsController@edit'
        ]);
        Route::put('permission/{permission}/update', [
            'as' => 'backend.permission.update',
            'uses' => 'PermissionsController@update'
        ]);
        Route::delete('permission/{permission}/delete', [
            'as' => 'backend.permission.delete',
            'uses' => 'PermissionsController@delete'
        ]);


        /**
         * Settings
         */

        Route::get('settings', [
            'as' => 'backend.settings.general',
            'uses' => 'SettingsController@general',
            'middleware' => 'permission:settings.general',
        ]);
        Route::post('settings/general', [
            'as' => 'backend.settings.general.update',
            'uses' => 'SettingsController@update',
            'middleware' => 'permission:settings.general'
        ]);
        Route::get('settings/auth', [
            'as' => 'backend.settings.auth',
            'uses' => 'SettingsController@auth',
            'middleware' => 'permission:settings.auth'
        ]);
        Route::post('settings/auth', [
            'as' => 'backend.settings.auth.update',
            'uses' => 'SettingsController@update',
            'middleware' => 'permission:settings.auth'
        ]);

    	Route::get('generator', [
            'as' => 'backend.settings.generator',
            'uses' => 'SettingsController@generator',
            'middleware' => 'permission:settings.generator'
        ]);

    	Route::post('generator', [
            'as' => 'backend.settings.generator.post',
            'uses' => 'SettingsController@generator',
            'middleware' => 'permission:settings.generator'
        ]);

        Route::put('shops/block', [
            'as' => 'backend.settings.shop_block',
            'uses' => 'SettingsController@shop_block',
            'middleware' => 'permission:shops.block'
        ]);

        Route::put('shops/unblock', [
            'as' => 'backend.settings.shop_unblock',
            'uses' => 'SettingsController@shop_unblock',
            'middleware' => 'permission:shops.unblock'
        ]);

        Route::put('settings/sync', [
            'as' => 'backend.settings.sync',
            'uses' => 'SettingsController@sync'
        ]);


        /**
         * Activity Log
         */

        Route::get('activity', [
            'as' => 'backend.activity.index',
            'uses' => 'ActivityController@index',
            'middleware' => 'permission:users.activity',
        ]);
        Route::get('activity/user/{user}/log', [
            'as' => 'backend.activity.user',
            'uses' => 'ActivityController@userActivity'
        ]);

        Route::delete('activity/clear', [
            'as' => 'backend.activity.clear',
            'uses' => 'ActivityController@clear',
        ]);

	});
});

