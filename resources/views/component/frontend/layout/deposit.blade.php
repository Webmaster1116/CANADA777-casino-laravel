<?php
$detect = new \Detection\MobileDetect();
?>
<div id="deposit-modal" class="deposit-modal modal  ">
	<!-- <div class="deposit-modal-content">
		<form id="deposit-form" class="modal-form container" action="{{route('frontend.deposit.payment')}}" method="GET" target="_blank">
			<h2 class="text-center mb-5 text-light">Deposit</h2>
            <input type="hidden" name="payment_type" id="payment_type" value="1"/>
			<div class="row">
				<div class="col-sm-4">
					<label for="currency" class="mb-2 text-light">Currency</label>
					<select class="mb-4" id="deposit_currency" name="deposit_currency" placeholder="Currency" onchange="fn_change_currency()">
						@if(isset($currencys) && count($currencys))
                        @foreach($currencys as $currency)
                        <option value="{{$currency->id}}">{{$currency->currency}}</option>
                        @endforeach
                        @endif
					</select>
					<label class="mb-2 text-light">All Payment</label>
					<div class="btn-group-vertical w-100 payment-button-group">
						<button type="button" class="btn btn-outline-success mb-1 rounded-0">
                            <img src="https://static.canada777.com/frontend/Default/img/interac-payment-icon.jpg" alt="">
                        </button>
						<button type="button" class="btn btn-outline-success rounded-0">
                            <img src="https://static.canada777.com/frontend/Default/img/bitcoin-payment-icon.png" alt="">
                        </button>
					</div>
				</div>
				<div class="col-sm-8">
                    <span class="mb-2 text-light">Amount</span>
					<div class="btn-group w-100 mb-4">
						<button type="button" class="btn btn-outline-success m-1 rounded-0" onclick="fn_price(25)">25</button>
						<button type="button" class="btn btn-outline-success m-1 rounded-0" onclick="fn_price(75)">75</button>
						<button type="button" class="btn btn-outline-success m-1 rounded-0" onclick="fn_price(125)">125</button>
						<button type="button" class="btn btn-outline-success m-1 rounded-0" onclick="fn_price(250)">250</button>
						<button type="button" class="btn btn-outline-success m-1 rounded-0" onclick="fn_price(500)">500</button>
					</div>
					<div class="custom-control custom-checkbox mb-4">
						<input type="checkbox" class="custom-control-input" id="customCheck1">
						<label class="custom-control-label text-light p-1" for="customCheck1">I don't want to receive any bonus</label>
					</div>
                    <div class="form-group m-0">
                        <label class="mb-2 text-light" for="deposit_amount">Amount</label>
                        <div class="d-flex align-items-center mb-3 deposit-amount-wrap">
                            <input type="text" id="deposit_amount" name="deposit_amount" class="border-0 m-0 p-0" oninput="fn_amount_input()" />
                            <span id="deposit_currency"></span>
                        </div>
                    </div>
                    <div class="form-group m-0">
                        <label class="form-group text-light p-1 m-0" for="deposit_email">Email</label>
                        <input type="text" id="deposit_email" name="deposit_email" />
                    </div>
                    <div class="form-group">
                        <label class="form-group text-light p-1 m-0" for="deposit_phone">Phone</label>
                        <input type="text" id="deposit_phone" name="deposit_phone" />
                    </div>
					<input type="hidden" name="cur_deposit_currency" id="cur_deposit_currency"/>
					<button type="button" class="btn btn-success btn-block mb-2 rounded-0" onclick="fn_deposit_submit()">Deposit</button>
				</div>
			</div>
		</form>
	</div> -->
</div>

<div id="cashout-modal" class="cashout-modal modal">
    <div class="cashout-modal-content">
        <form id="cashout-form" class="modal-form container" action="{{route('frontend.cashout.payment')}}" method="GET" target="_blank">
            <h2 class="text-center mb-5 text-light">Cash Out</h2>
            <input type="hidden" name="payment_type" id="payment_type" value="2"/>
			<label class="mb-2 text-light" for="cashout_amount">Amount</label>
            <input type="text" id="cashout_amount" name="cashout_amount" />
            <label class="form-group text-light p-1 m-0" for="cashout_email">Email</label>
            <input type="text" id="cashout_email" name="cashout_email" />
            <label class="form-group text-light p-1 m-0" for="cashout_phone">Phone</label>
            <input type="text" id="cashout_phone" name="cashout_phone" data-format="+1 (ddd) ddd-dddd" />
            <button type="button" class="btn btn-success btn-block mb-2 rounded-0" onclick="fn_cashout_submit()">Cash Out</button>
        </form>
    </div>
</div>

<!-- redesign deposit with color like casino.com -->
<div id="profile-modal" class="profile-modal modal">
    <!-- <div class="profile-modal-modal-header"><div class="icon-profile"><i class="fa fa-credit-card-alt"></i></div><span>Cashier</span> -->
    <div class="profile-modal-modal-content"></div>
    @if(!$detect->isMobile() && !$detect->isTablet())
    <div class="profile-modal-modal-footer">
            <div class="profile-modal-footer-first col-md-2"></div>
            <div class="profile-modal-footer-second c0l-md-11">
                <div class="begambleaware"><img src="https://static.canada777.com/frontend/Page/image/other_logo/begambleaware.png" alt=""/></div>
                <div class="plus"><img src="https://static.canada777.com/frontend/Page/image/other_logo/18-plus.png" alt=""/></div>
                <div class="thawte"><img src="https://static.canada777.com/frontend/Page/image/other_logo/thawte.png" alt=""/></div>
                <div class="gt"><img src="https://static.canada777.com/frontend/Page/image/other_logo/gt.png" alt=""/></div>
                <div class="gbga-icon"><img src="https://static.canada777.com/frontend/Page/image/other_logo/gbga_icon@2x.png" alt=""/></div>
                <div class="gbga-icon"><img src="https://static.canada777.com/frontend/Page/image/other_logo/gamanon.png" alt=""/></div>
                <div class="ibas-logo-login"><img src="https://static.canada777.com/frontend/Page/image/other_logo/ibas-logo-login.png" alt=""/></div>
                <div class="gamcare"><img src="https://static.canada777.com/frontend/Page/image/other_logo/gamcare.png" alt=""/></div>
                <div class="gamblingcommisions"><img src="https://static.canada777.com/frontend/Page/image/other_logo/gamblingcommisions.png" alt=""/></div>
            </div>
    </div>
    @endif
</div>
<div id="side-modal" class="captain-up-menu right-menu slide-menu fn-slide-menu-wrapper opened" style="display:none">
    @if (Auth::check())
    <div class="slide-menu__wrap fn-menu-wrap">
        <div class="slide-menu__header"><span class="main-header__menu fn-close-menu" onClick="$('.close-modal').click();"></span><div class="slide-menu-logo-desktop"></div></div>
        <div class="right-menu-balance">
            <div class="balances">
                <div class="top-balance">
                    <div class="balance">Total Balance
                        <div class="total-balance"><span class="val_type_user_balance">$ {{number_format(Auth::user()->balance,2)}}</span></div>
                    </div>
                    <div class="deposit-btn"><a class="btn btn_action_deposit" href="javascript:fn_profile_load('deposit');"> Deposit</a></div>
                </div>
                <div class="balance">Real Balance <span><span class="val_type_user_balance">$ {{number_format(Auth::user()->getRealBalance(),2)}}</span></span></div>
                <div class="balance">Bonus Balance <span><span class="val_type_user_balance">$ {{number_format(Auth::user()->getBonusBalance(),2)}}</span></span></div>
            </div>
        </div>
        <div class="fn-menu-list">
            <ul class="slide-menu__container">
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('balance');" class="slide-menu__point-link">My Balance</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('verify');" class="slide-menu__point-link">Account Verification</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('deposit');" class="slide-menu__point-link">Deposit</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('withdraw');" class="slide-menu__point-link">Withdraw</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('password');" class="slide-menu__point-link">Change Password</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('detail');" class="slide-menu__point-link">Personal details</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('history/payment');" class="slide-menu__point-link">Transaction History</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('bonus');" class="slide-menu__point-link">Bonus History</a></li>
                <li class="slide-menu__point"><a href="javascript:fn_profile_load('freespin');" class="slide-menu__point-link">Free Spins History</a></li>
                <li class="slide-menu__point"><a href="{{route('frontend.support.ticket')}}" class="slide-menu__point-link">Support</a></li>
                <li class="slide-menu__point"><a style="width:100%; background:#FFFF00;" href="{{route('frontend.auth.logout')}}" class="btn btn-md">sign out</a></li>
            </ul>
        </div>
    </div>
    @endif
</div>
<!--  -->
