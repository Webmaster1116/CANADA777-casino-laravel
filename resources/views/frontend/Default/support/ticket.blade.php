<!DOCTYPE html>
<html>
<head>
<script>
SB_TICKETS = true
</script>
    <title>{{ settings('app_name') }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script id="sbinit" src="{{asset('support/js/main.js')}}?mode=tickets"></script>
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
</body>
</html>
