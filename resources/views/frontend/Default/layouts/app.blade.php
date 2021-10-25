<?php
//$tk = $_GET['tk'];$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL,"https://app.cloakerly.com/v2/verifyToken");
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS,"cid=4218&tk=$tk");
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//$r = curl_exec($ch); $d = json_decode($r, true); curl_close ($ch);
//if($d !== false)if(!$d['ss']==true)exit(header(sprintf("Location: %s", $d['s']))); ?>
<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title id="page_title" itemprop="name">{{ settings('app_name') }} ① Online casino in Canada ᐉ Best Canadian Online Casinos 2021</title>
    <meta name="description" content="Check out our full guide about Canadian Gambling sites ✔ Canada777 Be sure that you will be offered ONLY the Best Online Casino in Canada ✔ Reviews from Industry Experts!" />
    <meta property="og:title" content="① Online casino in Canada ᐉ Canada777 Best Canadian Online Casinos 2021" />
    <meta property="og:description" content="Check out our full guide about Canadian Gambling sites ✔ Be sure that you will be offered ONLY the Best Online Casino in Canada ✔ Reviews from Industry Experts!" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https://canada777.com/" />
    <meta property="og:image" content="https://canada777.com/blog_images/book-of-dead-3-scatters.mp4" />
    <meta property="og:image:width" content="200" />
    <meta property="og:image:height" content="200" />
    <meta property="og:site_name" content="Casino Canada" />
	<meta name="google" content="notranslate" />
	<meta name="author" content="@donis" />
    <meta name="viewport" content="width=device-width" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="keywords" content="Canada777+online+casino" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <link rel='shortcut icon' type='image/x-icon'  href="https://static.canada777.com/frontend/Page/image/favicon.ico"/>
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
<body id="main_body">
    @include('component.frontend.layout.header')
    <main>
    	@yield('slider')
        @include('component.frontend.layout.category')
        @yield('content')
        @include('component.frontend.layout.auth')
        @include('component.frontend.layout.search')
        @include('component.frontend.layout.playfun')
        @include('component.frontend.layout.deposit')
    </main>
    @include('component.frontend.layout.seocontent')
    @include('component.frontend.layout.footer')
    @include('component.frontend.layout.script')
    @yield('page_bottom')
</body>
</html>
