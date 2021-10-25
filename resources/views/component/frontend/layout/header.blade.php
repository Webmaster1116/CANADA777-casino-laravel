<header>
    <div id="menu-toggle">
        <div id="menu_button">
            <input type="checkbox" id="menu_checkbox">
            <label for="menu_checkbox" id="menu_label">
                <div id="menu_text_bar"></div>
            </label>
        </div>

        <ul id="header-menu">
            <li><a href="{{url('/')}}">Home</a></li>
            @if(Auth::check())
            <li><a href="javascript:fn_profile();">My Balance</a></li>
            <li><a href="javascript:fn_profile_load('verify');">Account Verification</a></li>
            <li><a href="javascript:fn_profile_load('deposit');">Deposit</a></li>
            <li><a href="javascript:fn_profile_load('withdraw');">Withdraw</a></li>
            <li><a href="javascript:fn_profile_load('password');">Change Password</a></li>
            <li><a href="javascript:fn_profile_load('detail');">Personal details</a></li>
            <li><a href="javascript:fn_profile_load('history/payment');">Transaction History</a></li>
            <li><a href="javascript:fn_profile_load('bonus');">Bonus History</a></li>
            <li><a href="javascript:fn_profile_load('freespin');">Free Spins History</a></li>
            @endif
            <li><a href="{{url('bonus')}}">Bonus</a></li>
            <li><a href="{{url('about')}}">About Us</a></li>
            <li><a href="{{route('frontend.support.ticket')}}">Support</a></li>
            @if(Auth::check())
            <li><a href="{{route('frontend.auth.logout')}}">Sign Out</a></li>
            @else
            <li class="d-md-block d-lg-none"><a href="#signin-modal">Sign In</a></li>
            <li class="d-md-block d-lg-none"><a href="#signup-modal">Sign Up</a></li>
            @endif
        </ul>
        <span class="d-md-flex d-none">Menu</span>
    </div>
    <div class="header-content">
        <div class="logo">
            <a href="{{ url('/') }}" class="d-md-flex d-none">
                <img src="https://static.canada777.com/frontend/Page/image/logo.png" />
            </a>
            <a href="{{ url('/') }}" class="d-md-none d-flex">
                <img src="https://static.canada777.com/frontend/Page/image/mobile-logo.png" />
            </a>
        </div>
        <div class="account-header-menu d-flex">
            @if(!Auth::check())
            <div class="account-header-menu-item">
                <a href="#signin-modal">
                    <img class="d-md-flex d-none" src="https://static.canada777.com/frontend/Page/image/signin-icon.png" />
                    <span>sign in</span>
                </a>
            </div>
            <div class="account-header-menu-item">
                <a href="#signup-modal">
                    <img class="d-md-flex d-none" src="https://static.canada777.com/frontend/Page/image/signup-icon.png" />
                    <span class="singup">sign up</span>
                </a>
            </div>
            @else
            <div class="account-header-menu-item d-lg-block">
                <a href="javascript:fn_profile_load('freespin');">
                    <span><img src="https://static.canada777.com/frontend/Page/image/notification.png" /></span>
                </a>
            </div>    
            <div class="account-header-menu-item d-lg-block">
                <!-- <a href="javascript:fn_deposit('{{Auth::check()}}')"> -->
                
                <!-- redesign deposit with color like casino.com  -->
                <a href="javascript:fn_profile_load('deposit')">
                    <img src="https://static.canada777.com/frontend/Page/image/deposit-icon.png" />
                    <span>deposit</span>
                </a>
            </div>
            <div class="account-header-menu-item">
                <!-- <a href="{{route('frontend.profile.balance')}}" data-ol-has-click-handler>
                    <img src="https://static.canada777.com/frontend/Page/image/signin-icon.png" />
                    <span>
                        <div>{{Auth::user()->username}}</div>
                        <div style="line-height: 1">$ <b>{{Auth::user()->balance}}</b></div>
                    </span>
                </a> -->
                <input type="hidden" id="user_balance_amount" value="{{number_format((float)Auth::user()->balance, 2, '.', '')}}">
                <a href="javascript:fn_side_menu()" data-ol-has-click-handler>
                    <img src="https://static.canada777.com/frontend/Page/image/signin-icon.png" />
                    <span>
{{--                        <div>{{Auth::user()->username}}</div>--}}
                        <div>balance</div>
                        <div style="line-height: 1">$ <b>{{number_format((float)Auth::user()->balance, 2, '.', '')}}</b></div>
                    </span>
                </a>
            </div>
            @endif
        </div>
    </div>
</header>
