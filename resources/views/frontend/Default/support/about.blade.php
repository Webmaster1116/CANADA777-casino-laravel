<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="Find the top online casino, voted best Canadian Casino Sites with bonus, voted number one in Ontario, Alberta, British-Columbia and Quebec.">
    <meta title="title" content="Best Online Casinos Canada 2021- Real Money Gambling" />
    <meta name="google" content="notranslate" />
    <meta name="author" content="JamesJ & Applewood" />
    <!-- <meta name="description" content="HTML template"> -->
    <meta name="viewport" content="width=device-width" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="keywords" content="Canada777+online+casino" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

    <title>{{ settings('app_name') }}</title></title>

    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>

    @include('component.frontend.layout.style')
</head>
<body>
@include('component.frontend.layout.header')
<main>
    <section id="hero-section border-bottom" style="border-bottom-color: white; border-bottom-width: 6px; border-bottom-style: solid; text-align: center">
        <img class="hero-image" src="https://static.canada777.com/frontend/Page/image/logo-1.png" style="width: 50%;"/>
    </section>
    <section id="about-us-section">
        <p>
            Canada777.com wants to become a leading Canadian, we are dedicated to bringing players the best games no matter where you are with the best performance and technology.
            <br/>
            The safe and private environment and the integrity of our products are the fundamental drivers of the Canada777.com online gaming experience. We will have the most advanced security measures available and are continually auditing our games and processes to ensure a totally safe and fair internet gambling experience. We keep all of your information confidential, and we will never share it or sell it to third parties, except in accordance with our Privacy Policy.
            <br/>
            We strive to offer the best prices whilst covering a wide variety of slot games in our casino. At Canada777 we promise you will enjoy the highest class of online gaming entertainment in the world.
            <br/>
            With 24 hour live customer support available 7 days per week, our highly trained and friendly staff will ensure that any queries are dealt with and resolved quickly, politely, and efficiently.
            <br/>
            Our mission is to provide the best online gambling experience for responsible players, please feel free to contact us by phone or email with your comments or suggestions.
            <br/>
            We offer a variety of secure and easy payment methods for your convenience. We adhere to “know your customer (KYC)” and anti-money laundering (AML) policies and cooperate with the third party financial and regulatory authorities to ensure the highest standards of compliance.
        </p>
    </section>
</main>
@include('component.frontend.layout.auth')
@include('component.frontend.layout.deposit')
@include('component.frontend.layout.script')
</body>
</html>
