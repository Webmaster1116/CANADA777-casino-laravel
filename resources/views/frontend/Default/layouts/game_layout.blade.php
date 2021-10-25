<!DOCTYPE html>
<html lang="ru">

<head>

	<meta charset="utf-8">

	<title>Canada777.com</title>
	<meta name="description" content="HTML template">

	<meta name="viewport" content="width=device-width">

	<link rel="icon" href="/frontend/Default/img/favicon.ico" >

	<link rel="stylesheet" href="/frontend/Default/css/slick.css">
	<link rel="stylesheet" href="/frontend/Default/css/grid.css">
	<link rel="stylesheet" href="/frontend/Default/css/styles.min.css">
	<script src="/frontend/Default/js/jquery-3.4.1.min.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
	@if($play_for_fun == 1)
		<script type="text/javascript" src="{{asset('frontend/assets/fingerprint_config.js')}}"></script>
		<script
			async
			src="{{asset('frontend/assets/fp.min.js')}}"
			onload="initFingerprintJS()"
		></script>

		<!-- integration green popups plugin -->
		<script id="lepopup-remote" src="{{asset('/popup/content/plugins/halfdata-green-popups/js/lepopup.js?ver=7.24')}}" data-handler="{{asset('/popup/ajax.php')}}"></script>
		<!--  -->
		<script type="text/javascript" src="{{asset('frontend/Page/js/common.js')}}"></script>
	@endif
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>
</head>
<body>

		@yield('content')

	<!-- SCRIPTS -->
	<script src="/frontend/Default/js/slick.min.js"></script>
	<script src="/frontend/Default/js/masonry-docs.min.js"></script>
	<script src="/frontend/Default/js/custom.js"></script>

</body>
</html>
