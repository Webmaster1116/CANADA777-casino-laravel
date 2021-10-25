<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">

	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">

	<link href="/back/dist/css/offline.css" type="text/css" rel="stylesheet">

    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>

	<title>@yield('title')</title>
</head>
<body>
	<div class="offpage">
		<div class="wrap">
			<div class="wrap_in">
               @yield('content')
			</div>
		</div>
		<div class="footer">
			<p class="copyright">
				<a href="http://goldsvet.org/" target="_blank" rel="nofollow"><b>CANADA 777</b> - Multi Shop System</a>
			</p>
		</div>
	</div>
</body>
</html>
