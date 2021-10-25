<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
	<meta name="google" content="notranslate">
	<meta name="author" content="JamesJ & Applewood">
	<meta name="description" content="HTML template">
	<meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="keywords" content="Canada777+online+casino">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Canada777 online casino makes gambling simpler. Easy and fast payouts and account verification, promotions and VIP loyalty program.">

    <title>{{ settings('app_name') }}</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>

    @include('component.frontend.layout.style')

    @yield('page_top')

</head>
<body>
	<header>
		<div class="header-content">
            <div class="logo">
                <a href="/" class="d-flex">
                    <img src="https://static.canada777.com/frontend/Page/image/logo.png" />
                </a>
            </div>
            <div class="account-header-menu d-none d-sm-none d-md-none d-lg-flex">
                <div class="account-header-menu-item">
                    <a href="javascript:fn_deposit('{{Auth::check()}}')">
                        <img src="https://static.canada777.com/frontend/Page/image/deposit-icon.png" />
                        <span>deposit</span>
                    </a>
                </div>
                <div class="account-header-menu-item">
                    <a href="{{route('frontend.auth.logout')}}" data-ol-has-click-handler>
                        <img src="https://static.canada777.com/frontend/Page/image/signin-icon.png" />
                        <span>sign out</span>
                    </a>
                </div>
                <div class="account-header-menu-item">
                    <a href="{{ url('/categories/all') }}">
                        <span>games</span>
                    </a>
                </div>
                <div class="account-header-menu-item">
                    <a href="{{route('frontend.profile.info')}}">
                        <span>{{Auth::user()->username}}</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    @include('component.frontend.layout.deposit')
	@yield('profile')
	@include('component.frontend.layout.script')

    @yield('page_bottom')
</body>
</html>
