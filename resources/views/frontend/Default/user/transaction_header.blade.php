<div class="sub-menu mb-4">
    <ul class="d-flex flex-md-row flex-column">
        <li>
            <a href="javascript:fn_profile_load('history/payment')" class="{{ Request::is('profile/history/payment') ? 'active' : '' }} py-2 px-4 d-block">Payment History</a>
        </li>
        <li>
            <a href="javascript:fn_profile_load('history/bet')" class="{{ Request::is('profile/history/bet') ? 'active' : '' }} py-2 px-4 d-block">Bet History</a>
        </li>
    </ul>
</div>