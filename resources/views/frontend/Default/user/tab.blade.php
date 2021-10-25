<ul class="navigation-bar__list">
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/balance') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('balance')" class="navigation-bar__point-link"><span class="micon-my-balance"></span><span class="navigation-bar__point-text">My Balance</span></a>
    </li>
    <li class="navigation-bar__point {{ Request::is('profile/verify') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('verify')" class="navigation-bar__point-link"><span class="micon-"></span><span class="navigation-bar__point-text">Account Verification</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/deposit') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('deposit')" class="navigation-bar__point-link"><span class="micon-my-deposit"></span><span class="navigation-bar__point-text">Deposit</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/withdraw') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('withdraw')" class="navigation-bar__point-link"><span class="micon-my-withdraw"></span><span class="navigation-bar__point-text">Withdraw</span></a>
    </li>
    <li class="navigation-bar__point fn-change-password {{ Request::is('profile/password') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('password')" class="navigation-bar__point-link"><span class="micon-change-password"></span><span class="navigation-bar__point-text">Change Password</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/detail') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('detail')" class="navigation-bar__point-link"><span class="micon-update-details"></span><span class="navigation-bar__point-text">Personal details</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/history/*') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('history/payment')" class="navigation-bar__point-link"><span class="micon-transaction-history"></span><span class="navigation-bar__point-text">Transaction History</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/bonus') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('bonus')" class="navigation-bar__point-link"><span class="micon-bonus-history"></span><span class="navigation-bar__point-text">Bonus History</span></a>
    </li>
    <li class="navigation-bar__point fn-redirect {{ Request::is('profile/freespin') ? 'active' : '' }}">
        <a href="javascript:fn_profile_load('freespin')" class="navigation-bar__point-link"><span class="micon-golden-chips"></span><span class="navigation-bar__point-text">Free Spins History</span></a>
    </li>
</ul>