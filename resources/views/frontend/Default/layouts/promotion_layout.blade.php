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

    <title>{{ settings('free_promotion_name') }}</title>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />

    <!-- include fontawesome for front end part -->
    <link rel="stylesheet" type="text/css" href="/back/bower_components/font-awesome/css/font-awesome.min.css">
    <!--  -->

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

    <link rel="stylesheet" type="text/css" href="https://static.canada777.com/frontend/Page/css/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="https://static.canada777.com/frontend/Page/css/promotion.css" />

    <!-- Intl-Tel-Input-Plugin for phone verify -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <!--  -->
    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>
</head>
<body>
    @yield('content');
    <!-- Remember to include jQuery :) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- jQuery Modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>

    <!-- jQuery Steps -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
</body>
</html>
