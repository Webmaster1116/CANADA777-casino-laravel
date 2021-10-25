@extends('frontend.Default.layouts.app')

@section('content')

<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<title>Canada777 </title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700,900&amp;subset=cyrillic" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&amp;subset=latin,cyrillic" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link rel="shortcut icon" href="resources/favicon/favicon.ico">
    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>
</head>

    <body id="playamo" class="device-desktop locale-en page-home layout-default"><nav id="menu" menu-config="extensions : ['border-none', 'theme-black'], navbar : false" class="mm-menu mm-border-none mm-theme-black mm-offcanvas"><div class="mm-panels"><div class="mobile-menu mm-panel mm-opened mm-current" id="mm-1">
        <div class="mobile-auth ng-isolate-scope" type="mobile">
    <div access-level="accessLevels.anon" class="mobile-auth__anon">
        <a class="mobile-auth__register-link ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showRegistration()" translate="frontend.links.sign_up">Sign Up</a>
        <!--<casino-snippet type="sign-up-left-text" class="mobile-auth__anon-text"></casino-snippet>!-->
        <a class="mobile-auth__login-link ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showLogin()" translate="frontend.links.sign_in">Sign In</a>
    </div>
    <div access-level="accessLevels.user" class="mobile-auth__user" style="display: none;">
        <div class="user__name" dropdown-toggle=""><span class="name ng-binding"></span></div>
        <casino-statuses template="statuses-user-panel" class="mobile-compoints ng-isolate-scope"><div class="statuses-panel">
    <!-- ngIf: $ctrl.statuses.data.current.id -->
    <div class="balance-selector ng-isolate-scope" type="balance-selector">
    <!-- ngRepeat: balance in balances | filter: {code: data.user.currency} | limitTo: 1 -->
    <!-- ngIf: balances.length > 1 -->
</div>

    <p class="statuses-panel__nickname ng-binding"></p>

    <div ng-show="$root.data.current_ip.country_code !== 'SE' &amp;&amp; $root.data.user.country !== 'SE'">
        <!-- ngIf: $ctrl.statuses.data.current.id -->
        <!-- ngIf: !$ctrl.statuses.data.current.id --><a class="statuses-panel__link ng-scope" ng-if="!$ctrl.statuses.data.current.id" ui-sref="app.external({id:'vip', lang: $root.currentLocale})" href="vip.html">
            <span class="statuses-panel__lvl ng-binding">Level :</span>
            <span class="statuses-panel__lvl-name">0</span>
        </a><!-- end ngIf: !$ctrl.statuses.data.current.id -->
        <div class="status-line">
            <div class="status-line__progress" ng-style="{width: $ctrl.statuses.data.progress.percent + '%'}" style="width: 0%;"></div>
        </div>
    </div>
</div></casino-statuses>
        <div class="user__balance"><div class="balance-selector ng-isolate-scope" type="balance_menu_mobile">
    <span class="ng-binding">Your Balance: </span>
    <!-- ngRepeat: balance in balances | filter: {code: data.user.currency} -->
</div></div>
        <div class="btn-group">
            <span type="header_deposit" class="ng-isolate-scope">
    <!-- ngIf: $root.data.user.email -->
</span>
        </div>
    </div>
</div>
    <ul class="header-menu ng-scope ng-isolate-scope mm-listview" type="left-menu-mobile" ng-if="$root.data.current_ip.country_code !== 'SE' &amp;&amp; $root.data.user.country !== 'SE'">

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
            HOME
        </a>
    </li>
      <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
            SLOTS
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           CASINO GAMES
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           JACKPOTS
        </a>
    </li>
      <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           PROMOTIONS
        </a>
    </li>

          <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           MY WALLET
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
      <a ng-if="item.id != 'loot'" class="header-menu__link header-menu__link--about-us" ng-class="{'header-menu__link--current': item.path == state.current.page_name || state.includes('app.cms', {path: item.path})}" ui-sref="app.external({id:'about-us', lang:'en'})" scroll-up="" href="about-us.html">
            About Us
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a ng-if="item.id != 'loot'" class="header-menu__link header-menu__link--vip" ng-class="{'header-menu__link--current': item.path == state.current.page_name || state.includes('app.cms', {path: item.path})}" ui-sref="app.external({id:'vip', lang:'en'})" scroll-up="" href="vip.html">
            VIP
        </a>
    </li>


</ul><!-- end ngIf: $root.data.current_ip.country_code !== 'SE' && $root.data.user.country !== 'SE' -->
        <!-- ngIf: $root.data.current_ip.country_code === 'SE' || $root.data.user.country === 'SE' -->

        <div class="left-menu-mobile text-center">
            <a class="mobile-menu__logo" href="#" ui-sref="home({lang:'en'})">
                <img src="resources/images/logo.png" alt="Playamo">
            </a>
        </div>
    </div></div></nav>



    <div id="mm-0" class="mm-page mm-slideout">

     <div ng-bind-html="content" type="config" class="ng-binding ng-isolate-scope ng-scope">
     <div ng-controller="TournamentCtr" class="ng-scope">

</div>
</div><div>


        </div>


        <div class="carcass">


            <header class="header" ng-nicescroll="" nice-option="{cursorcolor: '#5e5e5e',cursorwidth: '4px',cursorborderradius: '3px', cursorborder: 'none',background: 'rgba(33,29,48, 0.7)',cursoropacitymin: '0.5'}" tabindex="0" style="overflow: hidden; outline: none;">
    <div class="header__desktop">
        <div class="header__desktop-wrap">
            <a href="#" ui-sref="home({lang: $root.currentLocale})" class="header__logo">
                <img class="header__logo-img" src="https://canada777.com/frontend/assets/resources/images/logo.png" alt="canada777">
            </a>

<div class="header-auth ng-isolate-scope" type="header-desktop">
    <div class="header-auth__anon" access-level="accessLevels.anon">
        <a class="btn-p btn-p-green header-auth__btn ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showRegistration()" translate="frontend.links.sign_up">Sign Up</a>
        <a class="btn-p btn-p-yellow header-auth__btn ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showLogin()" translate="frontend.links.sign_in">Sign In</a>
    </div>
    <div class="header-auth__user" access-level="accessLevels.user" style="display: none;">
        <casino-statuses template="statuses-user-panel" class="ng-isolate-scope">

            <div class="statuses-panel">
    <!-- ngIf: $ctrl.statuses.data.current.id -->
    <div class="balance-selector ng-isolate-scope" type="balance-selector">
    <!-- ngRepeat: balance in balances | filter: {code: data.user.currency} | limitTo: 1 -->
    <!-- ngIf: balances.length > 1 -->
</div>

    <p class="statuses-panel__nickname ng-binding"></p>

    <div ng-show="$root.data.current_ip.country_code !== 'SE' &amp;&amp; $root.data.user.country !== 'SE'">
        <!-- ngIf: $ctrl.statuses.data.current.id -->
        <!-- ngIf: !$ctrl.statuses.data.current.id --><a class="statuses-panel__link ng-scope" ng-if="!$ctrl.statuses.data.current.id" ui-sref="app.external({id:'vip', lang: $root.currentLocale})" href="vip.html">
            <span class="statuses-panel__lvl ng-binding">Level :</span>
            <span class="statuses-panel__lvl-name">0</span>
        </a><!-- end ngIf: !$ctrl.statuses.data.current.id -->
        <div class="status-line">
            <div class="status-line__progress" ng-style="{width: $ctrl.statuses.data.progress.percent + '%'}" style="width: 0%;"></div>
        </div>
    </div>
</div>
         </casino-statuses>

        <a class="btn-p btn-p-green header-auth__btn header-auth__btn--mr ng-scope ng-binding" ng-href="/profile" target="_self" ng-controller="ProfileRoutes" href="users/sign_in.html">My Account</a>
        <a class="btn-p btn-p-yellow header-auth__btn ng-scope ng-binding" ng-href="" access-level="accessLevels.user" ng-controller="PaymentsCtrl as PaymentsCtrl" ng-click="PaymentsCtrl.showPaymentsModal({selectedTab: 'deposit'})" style="display: none;">Deposit</a>
    </div>
</div>

        </div>
        <!-- ngIf: $root.data.current_ip.country_code !== 'SE' && $root.data.user.country !== 'SE' --><ul class="header-menu ng-scope ng-isolate-scope" type="top-menu" ng-if="$root.data.current_ip.country_code !== 'SE' &amp;&amp; $root.data.user.country !== 'SE'">

      <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
            HOME
        </a>
    </li>
      <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
            SLOTS
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           CASINO GAMES
        </a>
    </li>

    <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           JACKPOTS
        </a>
    </li>
      <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           PROMOTIONS
        </a>
    </li>

          <li class="header-menu__item ng-scope" ng-repeat="item in menu">
        <a  class="header-menu__link header-menu__link--promotions" href="#">
           MY WALLET
        </a>
    </li>




</ul><!-- end ngIf: $root.data.current_ip.country_code !== 'SE' && $root.data.user.country !== 'SE' -->
        <!-- ngIf: $root.data.current_ip.country_code === 'SE' || $root.data.user.country === 'SE' -->
    </div>
    <div class="header__mobile">
        <div class="header__mobile-wrp">
            <a href="#menu" class="header__mobile-btn">
                <button type="button" class="header__mobile-navbar">
                    <i class="glyphicon glyphicon-menu-hamburger"></i>
                </button>
            </a>

            <div class="header__mobile-language language-mob ng-isolate-scope" ng-show="locales.length > 1" template="mobile">

    <ul role="menu" class="language-mob__dropdown">
        <!-- ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-AU.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-AU.svg" alt="English - Australia" src="https://cdn2.softswiss.net/flags/rectangular/en-AU.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-CA.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-CA.svg" alt="English - Canada" src="https://cdn2.softswiss.net/flags/rectangular/en-CA.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="ru.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/ru.svg" alt="Русский" src="https://cdn2.softswiss.net/flags/rectangular/ru.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="de.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/de.svg" alt="Deutsch" src="https://cdn2.softswiss.net/flags/rectangular/de.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="nn.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/nn.svg" alt="Norsk" src="https://cdn2.softswiss.net/flags/rectangular/nn.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="fi.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/fi.svg" alt="Suomi" src="https://cdn2.softswiss.net/flags/rectangular/fi.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="pl.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/pl.svg" alt="Polski" src="https://cdn2.softswiss.net/flags/rectangular/pl.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="fr.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/fr.svg" alt="Français" src="https://cdn2.softswiss.net/flags/rectangular/fr.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="fr-CA.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/fr-CA.svg" alt="Français - Canada" src="https://cdn2.softswiss.net/flags/rectangular/fr-CA.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="pt.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/pt.svg" alt="Português" src="https://cdn2.softswiss.net/flags/rectangular/pt.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="it.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/it.svg" alt="Italiano" src="https://cdn2.softswiss.net/flags/rectangular/it.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="es.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/es.svg" alt="Español" src="https://cdn2.softswiss.net/flags/rectangular/es.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="hu.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/hu.svg" alt="Hungarian" src="https://cdn2.softswiss.net/flags/rectangular/hu.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="cs.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/cs.svg" alt="Čeština" src="https://cdn2.softswiss.net/flags/rectangular/cs.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-ZA.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-ZA.svg" alt="English - South Africa" src="https://cdn2.softswiss.net/flags/rectangular/en-ZA.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-NZ.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-NZ.svg" alt="English - New Zealand" src="https://cdn2.softswiss.net/flags/rectangular/en-NZ.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="ja.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/ja.svg" alt="日本語" src="https://cdn2.softswiss.net/flags/rectangular/ja.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="zh-CN.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/zh-CN.svg" alt="中文" src="https://cdn2.softswiss.net/flags/rectangular/zh-CN.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-IE.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-IE.svg" alt="English - Ireland" src="https://cdn2.softswiss.net/flags/rectangular/en-IE.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales --><!-- ngIf: num != localeIndex --><li class="language-mob__item ng-scope" ng-repeat="(num, value) in locales" ng-if="num != localeIndex">
            <a class="language-mob__link" href="en-IN.html" ng-click="$event.preventDefault(); changeLocale(value.code);">
                <img class="language-mob__img" ng-src="https://cdn2.softswiss.net/flags/rectangular/en-IN.svg" alt="English - India" src="https://cdn2.softswiss.net/flags/rectangular/en-IN.svg">
            </a>
        </li><!-- end ngIf: num != localeIndex --><!-- end ngRepeat: (num, value) in locales -->
    </ul>
</div>
        </div>
        <a href="#" ui-sref="home({lang: $root.currentLocale})" class="header__mobile-logo">
            <img class="header__mobile-logo-img" src="https://canada777.com/frontend/assets/resources/images/logo.png" alt="canada777">
        </a>
        <div class="header__mobile-auth header-auth-mob ng-isolate-scope" type="header-mobile">
    <div class="header-auth-mob__anon" access-level="accessLevels.anon">
        <a class="btn-p btn-p-yellow header-auth-mob__sign-up ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showRegistration()" translate="frontend.links.sign_up">Sign Up</a>
        <a class="btn-p btn-p-green header-auth-mob__sign-in ng-scope" href="#" ng-controller="AuthModal" ng-click="auth_modal.showLogin()" translate="frontend.links.sign_in">Sign In</a>
    </div>
    <div class="header-auth-mob__user" access-level="accessLevels.user" style="display: none;">
        <div class="profile-mob ng-scope" dropdown="" on-toggle="toggled(open)" ng-controller="ProfileRoutes">
            <button class="profile-mob__btn" type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                <a ng-href="/profile" target="_self" class="profile-mob__name ng-binding" href="users/sign_in.html"></a>
                <span class="profile-mob__caret"></span>
            </button>
            <ul role="menu" class="profile-mob__dropdown">
                <li class="profile-mob__item ">
                    <a ng-href="/profile" target="_self" class="profile-mob__link profile-mob__link--my-account ng-binding" href="users/sign_in.html">My Account</a>
                </li>
                <li class="profile-mob__item">
                    <a ng-href="/profile/bonuses/actual" target="_self" class="profile-mob__link profile-mob__link--bonuses ng-binding" href="users/sign_in.html">Bonuses</a>
                </li>
                <li class="profile-mob__item">
                    <a ng-href="/profile/bets" target="_self" class="profile-mob__link profile-mob__link--my-bets ng-binding" href="users/sign_in.html">My Bets</a>
                </li>
                <li class="profile-mob__item">
                    <a ng-href="/profile/bonuses" target="_self" class="profile-mob__link profile-mob__link--history ng-binding" href="users/sign_in.html">History</a>
                </li>
                <li class="profile-mob__item">
                    <a href="#" ng-click="login.logout()" ng-controller="LoginCtrl" class="profile-mob__link profile-mob__link--sign-out ng-scope ng-binding">Sign Out</a>
                </li>
            </ul>
        </div>
        <div class="balance-selector-table ng-isolate-scope" type="balance_menu_table">
    <!-- ngRepeat: balance in balances | filter: {code: data.user.currency} | limitTo: 1 -->
    <!-- ngIf: balances.length > 1 -->
</div>
        <div class="header-auth-mob__user-wrp">
            <a class="header-auth-mob__deposit-btn ng-scope ng-binding" access-level="accessLevels.user" href="#" ng-controller="PaymentsCtrl as PaymentsCtrl" ng-click="PaymentsCtrl.showPaymentsModal({selectedTab: 'deposit'})" style="display: none;">
                Deposit
            </a>
        </div>
    </div>
</div>
        <span class="header__fake-lang-mob"></span>
    </div>
</header>
            <!-- uiView: --><div class="carcass__body ng-scope" ui-view="" autoscroll="false"><!-- uiView: --><ui-view class="ng-scope"><div class="ng-scope">
    <ul class="categories-mob ng-isolate-scope" name="categories-mobile-menu" filters="{collection: 'all', provider: false}" template="categories_mobile_menu">
    <li class="categories-mob__item categories-mob__item--slots" ng-class="{'categories-mob__item--current': filters.data.collection == 'slots' &amp;&amp; state.current.name != 'home'}">
        <a class="categories-mob__link ng-binding" ui-sref="app.games({category: 'slots', lang: $root.currentLocale, provider: false})" href="games/slots.html">
            Slots
        </a>
    </li>
    <li class="categories-mob__item categories-mob__item--live" ng-class="{'categories-mob__item--current': ['live-casino', 'blackjack', 'roulette-games', 'baccarat', 'poker', 'other_live'].indexOf(filters.data.collection) >= 0 &amp;&amp; state.current.name != 'home'}">
        <a class="categories-mob__link ng-binding" ui-sref="app.games({category: 'live-casino', lang: $root.currentLocale, provider: false})" href="games/live-casino.html">
            Live casino
        </a>
    </li>
</ul>

<div ng-if="$root.data.current_ip.country_code !== 'SE' &amp;&amp; $root.data.user.country !== 'SE'" class="ng-scope">

   <div class="main-slider ng-isolate-scope" category="slider-index-top-v3">

    <div class="main-slider__wrap">
        <iframe style="overflow:hidden !important; height:400px; padding:0px !important; margin:0px !important; border: none !important;" width="100%" src="https://canada777.com/slider" allowfullscreen scrolling="no"></iframe>


    </div>
</div>


<div class="slider-mobile ng-isolate-scope" category="slider-index-top-mobile" template="slider-mobile">
     <iframe style="overflow:hidden !important; padding:0px !important; margin:0px !important; border: none !important;" width="100%" src="https://canada777.com/slider" allowfullscreen scrolling="no"></iframe>
</div>

</div>

    <div class="wrapper-pl">
        <div class="game-menu--hide-menu-mob game-category ng-isolate-scope" name="games_list" filters="{collection: 'all', provider: false}" template="game_category">
    <div class="games-menu-mob" ng-class="{'games-menu-mob--active': $root.customParam.searchMob || $root.customParam.providersMob}">
        <div class="games-search-mob ng-isolate-scope" ng-init="$root.search = '';" ng-class="{'games-search-mob--show-games' : $root.search.length &amp;&amp; (filters.games | gameTitle: $root.search).length, 'games-search-mob--open' : $root.customParam.searchMob}" name="games_list_autocomplete" template="autocomplete_mob" limit="10">
    <div class="games-search-mob__input-wrp">
        <input ng-model="$root.search" type="text" title="search" ng-click="$root.customParam.providersMob = false; $root.customParam.searchMob = true;" placeholder="Find your game" scroll-up="" s-ref="body" class="games-search-mob__input ng-pristine ng-untouched ng-valid ng-empty">
        <i class="games-search-mob__input-icon games-search-mob__input-icon--search icon-pa-search"></i>
        <i class="games-search-mob__input-icon games-search-mob__input-icon--close icon-pa-close" ng-click="$root.search = ''; $root.customParam.searchMob = false"></i>
    </div>
    <ul class="games-search-mob__dropdown">
   <!-- end ngRepeat: game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count --><li class="games-search-mob__item ng-scope" ng-repeat="game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count">
            <a class="games-search-mob__link ng-scope" ng-controller="Game" ng-click="openGame.modalByCurrency(game); $root.customParam.searchMob = false; $root.search = ''" target="_self">
                <img class="games-search-mob__img" ng-src="https://cdn2.softswiss.net/playamo/i/s2/redtiger/AurumCodex.webp" alt="Aurum Codex" src="https://cdn2.softswiss.net/playamo/i/s2/redtiger/AurumCodex.webp">
                <span class="games-search-mob__text ng-binding">Aurum Codex</span>
            </a>
        </li><!-- end ngRepeat: game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count --><li class="games-search-mob__item ng-scope" ng-repeat="game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count">
            <a class="games-search-mob__link ng-scope" ng-controller="Game" ng-click="openGame.modalByCurrency(game); $root.customParam.searchMob = false; $root.search = ''" target="_self">
                <img class="games-search-mob__img" ng-src="https://cdn2.softswiss.net/playamo/i/s2/infin/BeastSaga.webp" alt="Beast Saga" src="https://cdn2.softswiss.net/playamo/i/s2/infin/BeastSaga.webp">
                <span class="games-search-mob__text ng-binding">Beast Saga</span>
            </a>
        </li><!-- end ngRepeat: game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count --><li class="games-search-mob__item ng-scope" ng-repeat="game in filters.games | gamesWithCurrency: $root.data.user.currency | gameCanPlay | gameTitle: $root.search | limitTo: limit_count">
            <a class="games-search-mob__link ng-scope" ng-controller="Game" ng-click="openGame.modalByCurrency(game); $root.customParam.searchMob = false; $root.search = ''" target="_self">
                <img class="games-search-mob__img" ng-src="https://cdn2.softswiss.net/playamo/i/s2/amatic/NicerDice40.webp" alt="Nicer Dice 40" src="https://cdn2.softswiss.net/playamo/i/s2/amatic/NicerDice40.webp">
                <span class="games-search-mob__text ng-binding">Nicer Dice 40</span>
            </a>
        </li>
    </ul>
    <div class="games-search-mob__escape" ng-click="$root.search = ''; $root.customParam.searchMob = false"></div>
</div>
        <div class="providers-mob ng-isolate-scope" ng-class="{'open': $root.customParam.providersMob}" name="games_providers_mob" filters="{collection: 'all', provider: false}" template="games_provider_mob">
    <button class="providers-mob__btn" ng-click="$root.customParam.providersMob = !$root.customParam.providersMob;">
        <i class="providers-mob__btn-icon icon-pa-filter"></i>
        <!-- ngIf: !filters.data.provider --><span class="providers-mob__btn-text ng-scope" ng-if="!filters.data.provider" translate="games.providers.all">All Providers</span><!-- end ngIf: !filters.data.provider -->
        <!-- ngIf: filters.data.provider -->
    </button>



    <div class="providers-mob__dropdown" role="menu">
        <ul class="providers-mob__list">
            <li class="providers-mob__item providers-mob__item--all">
                <a class="providers-mob__link" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider:false, lang: $root.currentLocale, category:'all'})" href="games.html">
                    <span class="providers-mob__icon">
                        <img class="providers-mob__icon-img" src="resources/images/4-squares.svg" alt="All">
                    </span>
                    <span translate="games.providers.all" class="ng-scope">All Providers</span>
                </a>
            </li>
            <!-- ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/1x2gaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/1x2gaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="1x2gaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/1x2gaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.1x2gaming" translate-default="1x2 Gaming">1x2 Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/2by2.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/2by2.svg" onerror="this.src='resources/images/4-squares.svg'" alt="2by2" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/2by2.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.2by2" translate-default="2By2">2By2</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/4thePlayer.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/4thePlayer.svg" onerror="this.src='resources/images/4-squares.svg'" alt="4thePlayer" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/4thePlayer.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.4thePlayer" translate-default="4the Player">4the Player</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/alg.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/alg.svg" onerror="this.src='resources/images/4-squares.svg'" alt="alg" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/alg.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.alg" translate-default="Absolute Live Gaming">Absolute Live Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/amatic.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/amatic.svg" onerror="this.src='resources/images/4-squares.svg'" alt="amatic" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/amatic.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.amatic" translate-default="Amatic">Amatic</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/authentic.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/authentic.svg" onerror="this.src='resources/images/4-squares.svg'" alt="authentic" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/authentic.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.authentic" translate-default="Authentic Gaming">Authentic Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/belatra.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/belatra.svg" onerror="this.src='resources/images/4-squares.svg'" alt="belatra" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/belatra.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.belatra" translate-default="Belatra">Belatra</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/bgaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="bgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bgaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.bgaming" translate-default="BGaming">BGaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/bigtimegaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bigtimegaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="bigtimegaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bigtimegaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.bigtimegaming" translate-default="BigTimeGaming">BigTimeGaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/booming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="booming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.booming" translate-default="Booming Games">Booming Games</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/booongo.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booongo.svg" onerror="this.src='resources/images/4-squares.svg'" alt="booongo" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booongo.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.booongo" translate-default="Booongo">Booongo</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/bsg.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bsg.svg" onerror="this.src='resources/images/4-squares.svg'" alt="bsg" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bsg.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.bsg" translate-default="Betsoft Gaming">Betsoft Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/caleta.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/caleta.svg" onerror="this.src='resources/images/4-squares.svg'" alt="caleta" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/caleta.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.caleta" translate-default="Caleta">Caleta</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/egt.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/egt.svg" onerror="this.src='resources/images/4-squares.svg'" alt="egt" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/egt.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.egt" translate-default="EGT">EGT</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/elk.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/elk.svg" onerror="this.src='resources/images/4-squares.svg'" alt="elk" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/elk.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.elk" translate-default="ELK">ELK</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/endorphina.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/endorphina.svg" onerror="this.src='resources/images/4-squares.svg'" alt="endorphina" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/endorphina.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.endorphina" translate-default="Endorphina">Endorphina</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/evolution.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evolution.svg" onerror="this.src='resources/images/4-squares.svg'" alt="evolution" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evolution.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.evolution" translate-default="Evolution">Evolution</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/evoplay.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evoplay.svg" onerror="this.src='resources/images/4-squares.svg'" alt="evoplay" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evoplay.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.evoplay" translate-default="Evoplay Entertainment">Evoplay Entertainment</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/ezugi.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/ezugi.svg" onerror="this.src='resources/images/4-squares.svg'" alt="ezugi" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/ezugi.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.ezugi" translate-default="Ezugi">Ezugi</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/fantasma.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/fantasma.svg" onerror="this.src='resources/images/4-squares.svg'" alt="fantasma" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/fantasma.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.fantasma" translate-default="Fantasma">Fantasma</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/felixgaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/felixgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="felixgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/felixgaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.felixgaming" translate-default="Felix Gaming">Felix Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/fugaso.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/fugaso.svg" onerror="this.src='resources/images/4-squares.svg'" alt="fugaso" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/fugaso.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.fugaso" translate-default="Fugaso">Fugaso</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/gameart.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/gameart.svg" onerror="this.src='resources/images/4-squares.svg'" alt="gameart" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/gameart.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.gameart" translate-default="GameArt">GameArt</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/genesisgaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/genesisgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="genesisgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/genesisgaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.genesisgaming" translate-default="Genesis Gaming">Genesis Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/greenjade.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/greenjade.svg" onerror="this.src='resources/images/4-squares.svg'" alt="greenjade" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/greenjade.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.greenjade" translate-default="Greenjade">Greenjade</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/habanero.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/habanero.svg" onerror="this.src='resources/images/4-squares.svg'" alt="habanero" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/habanero.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.habanero" translate-default="Habanero">Habanero</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/hacksaw.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/hacksaw.svg" onerror="this.src='resources/images/4-squares.svg'" alt="hacksaw" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/hacksaw.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.hacksaw" translate-default="Hacksaw">Hacksaw</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/irondogstudio.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/irondogstudio.svg" onerror="this.src='resources/images/4-squares.svg'" alt="irondogstudio" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/irondogstudio.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.irondogstudio" translate-default="IronDogStudio">IronDogStudio</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/isoftbet.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/isoftbet.svg" onerror="this.src='resources/images/4-squares.svg'" alt="isoftbet" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/isoftbet.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.isoftbet" translate-default="iSoftBet">iSoftBet</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/jftw.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/jftw.svg" onerror="this.src='resources/images/4-squares.svg'" alt="jftw" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/jftw.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.jftw" translate-default="JFTW">JFTW</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/kalamba.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/kalamba.svg" onerror="this.src='resources/images/4-squares.svg'" alt="kalamba" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/kalamba.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.kalamba" translate-default="Kalamba">Kalamba</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/luckystreak.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/luckystreak.svg" onerror="this.src='resources/images/4-squares.svg'" alt="luckystreak" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/luckystreak.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.luckystreak" translate-default="Lucky Streak">Lucky Streak</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/mancala.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mancala.svg" onerror="this.src='resources/images/4-squares.svg'" alt="mancala" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mancala.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.mancala" translate-default="Mancala Gaming">Mancala Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/mascot.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mascot.svg" onerror="this.src='resources/images/4-squares.svg'" alt="mascot" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mascot.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.mascot" translate-default="Mascot">Mascot</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/microgaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/microgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="microgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/microgaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.microgaming" translate-default="Microgaming">Microgaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/mrslotty.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mrslotty.svg" onerror="this.src='resources/images/4-squares.svg'" alt="mrslotty" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/mrslotty.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.mrslotty" translate-default="MrSlotty">MrSlotty</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/netent.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/netent.svg" onerror="this.src='resources/images/4-squares.svg'" alt="netent" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/netent.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.netent" translate-default="Netent">Netent</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/nolimit.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/nolimit.svg" onerror="this.src='resources/images/4-squares.svg'" alt="nolimit" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/nolimit.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.nolimit" translate-default="Nolimit">Nolimit</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/northernlights.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/northernlights.svg" onerror="this.src='resources/images/4-squares.svg'" alt="northernlights" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/northernlights.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.northernlights" translate-default="Northernlights">Northernlights</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/nucleus.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/nucleus.svg" onerror="this.src='resources/images/4-squares.svg'" alt="nucleus" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/nucleus.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.nucleus" translate-default="Nucleus Gaming">Nucleus Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/onetouch.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/onetouch.svg" onerror="this.src='resources/images/4-squares.svg'" alt="onetouch" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/onetouch.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.onetouch" translate-default="Onetouch">Onetouch</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/pgsoft.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pgsoft.svg" onerror="this.src='resources/images/4-squares.svg'" alt="pgsoft" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pgsoft.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.pgsoft" translate-default="Pgsoft">Pgsoft</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/platipus.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/platipus.svg" onerror="this.src='resources/images/4-squares.svg'" alt="platipus" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/platipus.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.platipus" translate-default="Platipus">Platipus</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/playngo.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playngo.svg" onerror="this.src='resources/images/4-squares.svg'" alt="playngo" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playngo.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.playngo" translate-default="Play'n GO">Play'n Go</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/playson.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playson.svg" onerror="this.src='resources/images/4-squares.svg'" alt="playson" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playson.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.playson" translate-default="Playson">Playson</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/playtech.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playtech.svg" onerror="this.src='resources/images/4-squares.svg'" alt="playtech" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playtech.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.playtech" translate-default="Playtech">Playtech</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/pragmatic.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmatic.svg" onerror="this.src='resources/images/4-squares.svg'" alt="pragmatic" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmatic.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.pragmatic" translate-default="Pragmatic Play">Pragmatic Play</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/pragmaticplaylive.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmaticplaylive.svg" onerror="this.src='resources/images/4-squares.svg'" alt="pragmaticplaylive" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmaticplaylive.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.pragmaticplaylive" translate-default="Pragmatic Play Live">Pragmatic Play Live</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/pushgaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pushgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="pushgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pushgaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.pushgaming" translate-default="Push Gaming">Push Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/quickfire.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickfire.svg" onerror="this.src='resources/images/4-squares.svg'" alt="quickfire" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickfire.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.quickfire" translate-default="Quickfire">Microgaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/quickspin.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickspin.svg" onerror="this.src='resources/images/4-squares.svg'" alt="quickspin" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickspin.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.quickspin" translate-default="Quickspin">Quickspin</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/rabcat.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/rabcat.svg" onerror="this.src='resources/images/4-squares.svg'" alt="rabcat" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/rabcat.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.rabcat" translate-default="Rabcat">Rabcat</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/redtiger.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/redtiger.svg" onerror="this.src='resources/images/4-squares.svg'" alt="redtiger" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/redtiger.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.redtiger" translate-default="Red Tiger Gaming">Red Tiger Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/relax.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/relax.svg" onerror="this.src='resources/images/4-squares.svg'" alt="relax" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/relax.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.relax" translate-default="Relax Gaming">Relax Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/spinomenal.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/spinomenal.svg" onerror="this.src='resources/images/4-squares.svg'" alt="spinomenal" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/spinomenal.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.spinomenal" translate-default="Spinomenal">Spinomenal</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/swintt.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/swintt.svg" onerror="this.src='resources/images/4-squares.svg'" alt="swintt" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/swintt.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.swintt" translate-default="Swintt">Swintt</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/thunderkick.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/thunderkick.svg" onerror="this.src='resources/images/4-squares.svg'" alt="thunderkick" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/thunderkick.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.thunderkick" translate-default="Thunderkick">Thunderkick</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/tomhorn.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/tomhorn.svg" onerror="this.src='resources/images/4-squares.svg'" alt="tomhorn" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/tomhorn.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.tomhorn" translate-default="Tom Horn Gaming">Tom Horn Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/truelab.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/truelab.svg" onerror="this.src='resources/images/4-squares.svg'" alt="truelab" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/truelab.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.truelab" translate-default="TrueLab">TrueLab</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/vivogaming.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/vivogaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="vivogaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/vivogaming.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.vivogaming" translate-default="Vivogaming">Vivogaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/wazdan.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/wazdan.svg" onerror="this.src='resources/images/4-squares.svg'" alt="wazdan" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/wazdan.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.wazdan" translate-default="Wazdan">Wazdan</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id --><li class="providers-mob__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id">
                <a class="providers-mob__link" ng-class="{'providers-mob__link--current': $root.toParams.provider === filter_provider.id}" ng-click="$root.customParam.providersMob = false" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" href="games/all/yggdrasil.html">
                   <span class="providers-mob__icon">
                      <img class="providers-mob__icon-img" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/yggdrasil.svg" onerror="this.src='resources/images/4-squares.svg'" alt="yggdrasil" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/yggdrasil.svg">
                    </span>
                    <span class="providers-mob__name ng-scope" translate="games.providers.yggdrasil" translate-default="Yggdrasil">Yggdrasil</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | orderBy : 'id' track by filter_provider.id -->
        </ul>
    </div>
</div>
    </div>
<div class="menu">
				<div class="menu__wrap">
				@if( settings('use_all_categories') )
					<a href="{{ route('frontend.game.list.category', 'all') }}" class="menu__link @if($currentSliderNum != -1 && $currentSliderNum == 'all') active @endif">@lang('app.all')</a>
				@endif
				@if ($categories && count($categories))
					@foreach($categories AS $index=>$category)
					<a href="{{ route('frontend.game.list.category', $category->href) }}" class="menu__link @if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif"">{{ $category->title }}</a>
					@endforeach
				@endif
				</div>
				<div class="navBurger" role="navigation" id="navToggle"></div>
				<!-- MENU END -->
			</div>

    <div class="providers ng-isolate-scope" name="games_provider" filters="{collection: 'all', provider: false}" template="games_provider">
    <div class="providers__panel">
        <button class="providers__btn-all ng-scope" ui-sref="app.games({category: filter_collection.id, lang: $root.currentLocale, provider: false})" translate="common.all" href="games.html">All</button>
        <ul class="providers__panel-list">
            <!-- ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/netent.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__netent" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/netent.svg" onerror="this.src='resources/images/4-squares.svg'" alt="netent" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/netent.svg">
                    </span>
                    <span class="providers__name ng-binding">Netent</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/playngo.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__playngo" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playngo.svg" onerror="this.src='resources/images/4-squares.svg'" alt="playngo" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/playngo.svg">
                    </span>
                    <span class="providers__name ng-binding">Play'n Go</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/bgaming.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__bgaming" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bgaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="bgaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bgaming.svg">
                    </span>
                    <span class="providers__name ng-binding">BGaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/pragmatic.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__pragmatic" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmatic.svg" onerror="this.src='resources/images/4-squares.svg'" alt="pragmatic" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/pragmatic.svg">
                    </span>
                    <span class="providers__name ng-binding">Pragmatic Play</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/spinomenal.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__spinomenal" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/spinomenal.svg" onerror="this.src='resources/images/4-squares.svg'" alt="spinomenal" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/spinomenal.svg">
                    </span>
                    <span class="providers__name ng-binding">Spinomenal</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/booongo.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__booongo" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booongo.svg" onerror="this.src='resources/images/4-squares.svg'" alt="booongo" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/booongo.svg">
                    </span>
                    <span class="providers__name ng-binding">Booongo</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/evolution.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__evolution" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evolution.svg" onerror="this.src='resources/images/4-squares.svg'" alt="evolution" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/evolution.svg">
                    </span>
                    <span class="providers__name ng-binding">Evolution</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/quickfire.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__quickfire" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickfire.svg" onerror="this.src='resources/images/4-squares.svg'" alt="quickfire" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/quickfire.svg">
                    </span>
                    <span class="providers__name ng-binding">Microgaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/wazdan.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__wazdan" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/wazdan.svg" onerror="this.src='resources/images/4-squares.svg'" alt="wazdan" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/wazdan.svg">
                    </span>
                    <span class="providers__name ng-binding">Wazdan</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/amatic.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__amatic" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/amatic.svg" onerror="this.src='resources/images/4-squares.svg'" alt="amatic" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/amatic.svg">
                    </span>
                    <span class="providers__name ng-binding">Amatic</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/bigtimegaming.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__bigtimegaming" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bigtimegaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="bigtimegaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/bigtimegaming.svg">
                    </span>
                    <span class="providers__name ng-binding">BigTimeGaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/isoftbet.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__isoftbet" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/isoftbet.svg" onerror="this.src='resources/images/4-squares.svg'" alt="isoftbet" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/isoftbet.svg">
                    </span>
                    <span class="providers__name ng-binding">iSoftBet</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/belatra.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__belatra" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/belatra.svg" onerror="this.src='resources/images/4-squares.svg'" alt="belatra" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/belatra.svg">
                    </span>
                    <span class="providers__name ng-binding">Belatra</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/redtiger.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__redtiger" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/redtiger.svg" onerror="this.src='resources/images/4-squares.svg'" alt="redtiger" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/redtiger.svg">
                    </span>
                    <span class="providers__name ng-binding">Red Tiger Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/habanero.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__habanero" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/habanero.svg" onerror="this.src='resources/images/4-squares.svg'" alt="habanero" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/habanero.svg">
                    </span>
                    <span class="providers__name ng-binding">Habanero</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 --><li class="providers__item ng-scope" ng-repeat="filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16">
                <a class="providers__link" ng-class="{'providers__link--current': filters.data.provider == filter_provider.id}" ui-sref="app.games({provider: filter_provider.id, category: 'all', lang: $root.currentLocale})" scroll-up="" href="games/all/1x2gaming.html">
                    <span class="providers__icon">
                        <img class="providers__icon-img providers-icon-img__1x2gaming" ng-src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/1x2gaming.svg" onerror="this.src='resources/images/4-squares.svg'" alt="1x2gaming" src="https://cdn2.softswiss.net/playamo/logos/providers_small/color/1x2gaming.svg">
                    </span>
                    <span class="providers__name ng-binding">1x2 Gaming</span>
                </a>
            </li><!-- end ngRepeat: filter_provider in gamesData.data.providers | customSortArray: 'id': ['netent', 'playngo', 'bgaming', 'pragmatic', 'spinomenal', 'booongo', 'igtech', 'evolution', 'quickfire', 'wazdan', 'amatic', 'bigtimegaming', 'isoftbet', 'belatra', 'redtiger', 'habanero']  | limitTo: 16 -->
        </ul>
        <button class="providers__toggler" ng-click="$root.customParam.providers = !$root.customParam.providers" ng-class="{'providers__toggler--opened' : $root.customParam.providers}"></button>
    </div>
    <!-- ngIf: $root.customParam.providers -->
    <!-- ngIf: $root.customParam.providers -->
</div>
</div>
        <section class="games-list ng-isolate-scope" ng-init="query = ''" name="home_top_games" template="games_list" filters="{collection: 'top_games', provider: false}" limit="20">
    <div class="title-block">
        <div class="title-block__wrap">
            <!-- ngIf: filters.data.provider == false && (filters.data.collection != 'slots' || $root.currentLocale != 'en-AU') --><h1 ng-if="filters.data.provider == false &amp;&amp; (filters.data.collection != 'slots' || $root.currentLocale != 'en-AU')" translate="games.categories.top_games" class="title-block__title ng-scope">Top games</h1><!-- end ngIf: filters.data.provider == false && (filters.data.collection != 'slots' || $root.currentLocale != 'en-AU') -->
            <!-- ngIf: filters.data.provider == false && (filters.data.collection == 'slots' && $root.currentLocale === 'en-AU') -->

            <!-- ngIf: filters.data.provider !== false -->
        </div>
        <div class="title-block__search">
            <button class="games-list__btn-search ng-scope" ng-controller="GameAutocomplete" ng-click="showAutocompleteModal()">
                <i class="games-list__btn-search-icon icon-pa-search"></i>
                <span class="games-list__btn-search-text ng-scope" translate="frontend.filters.find_your_game">Find your game</span>
            </button>
        </div>
    </div>


<div class="games-list__wrap ng-scope" ng-controller="Game">

  	@if ($games && count($games))
					@foreach ($games as $key=>$game)
						<div class="grid-item grid-item--height2 grid-item--width2">
							<div class="grid__content games">
								<div class="games__item">
									<div class="games__content">
										<img src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" alt="{{ $game->title }}" />
										<a href="{{ route('frontend.game.go', $game->name) }}" class="play-btn play-btn-real btn">Play for Real</a>
										<a href="{{ route('frontend.game.pre_go', $game->name) }}" class="play-btn play-btn-fun btn">Play for Fun</a>
										<!--<span class="game-name">{{ $game->title }}</span>-->
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@endif

</div>



    <div class="games-list__btn-wrp" ng-show="limit_count < filters.games.length">
        <a class="games-list__btn ng-scope" ng-click="$root.state.current.page_name === 'home'? loadMore(10): loadMore(30)" translate="frontend.links.load_more_games">Load More Games</a>
    </div>
</section>


</div>
</div>
</ui-view>
</div>

<footer class="footer footer--desktop">

</footer>
</div>



</div>

<link rel="stylesheet" href="https://canada777.com/frontend/assets/css/appbf03.css">
<!--<link rel="stylesheet" href="https://canada777.com/frontend/assets/css/app.css">--->

</body>
</html>

@stop
