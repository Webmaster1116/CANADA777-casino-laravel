<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="Find the top online casino, voted best Canadian Casino Sites with bonus, voted number one in Ontario, Alberta, British-Columbia and Quebec.">
    <meta title="title" content="Best Online Casinos Canada 2021- Real Money Gambling" />
    <meta name="google" content="notranslate" />
    <meta name="author" content="Adonis" />
    <!-- <meta name="description" content="HTML template"> -->
    <meta name="viewport" content="width=device-width" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="keywords" content="Canada777+online+casino" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

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
</head>
<body>
    @include('component.frontend.layout.header')
    <main>
        <section id="hero-section border-bottom" style="border-bottom-color: white; border-bottom-width: 6px; border-bottom-style: solid;">
            <img class="hero-image" src="https://static.canada777.com/frontend/Page/image/bonus-banner.jpg" />
        </section>
        <section id="bonus-section">
            <div class="position-relative bonus-content py-5 px-5 d-flex justify-content-center">
                @if(Auth::check())
                    <img class="section-image" src="https://static.canada777.com/frontend/Page/image/bonus-content-image.jpg" />
                @else
                    <a href="#signin-modal" class="d-flex justify-content-center">
                        <img class="section-image" src="https://static.canada777.com/frontend/Page/image/bonus-content-image.jpg" />
                    </a>
                @endif
                <a href="{{ url('/bonus/term') }}" class="position-absolute d-block text-light" style="bottom: 10px; right: 100px;">Terms Apply</a>
            </div>
            @if ($welcomepackages && count($welcomepackages))
            <div class="position-relative bonus-content py-5 px-5 d-flex justify-content-center">
                <div class="welcomepackage-games w-100">
                    @foreach ($welcomepackages as $index=>$welcomepackage)
                    <div class="game-item m-1">
                        @if ($welcomepackage->day == 1)
                        <div class="text-white">1st Day {{$welcomepackage->freespin}} Free Spins</div>
                        @elseif ($welcomepackage->day == 2)
                        <div class="text-white">2nd Day {{$welcomepackage->freespin}} Free Spins</div>
                        @elseif ($welcomepackage->day == 3)
                        <div class="text-white">3rd Day {{$welcomepackage->freespin}} Free Spins</div>
                        @else
                        <div class="text-white">{{$welcomepackage->day}}th Day {{$welcomepackage->freespin}} Free Spins</div>
                        @endif
                        <img class="section-image mw-100" src="https://static.canada777.com/frontend/Default//ico/{{$welcomepackage->name}}.jpg/">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <p class="text-center text-light py-2 px-2 border-top border-default mb-0">*1st Deposit - Match Bonus up to C$ 400 • 2nd / 3rd Deposit - Match Bonus up to C$ 300 • New customers only • Min deposit C$ 10 • 70x wagering</p>
        </section>
    </main>
    @include('component.frontend.layout.auth')
    @include('component.frontend.layout.deposit')
    @include('component.frontend.layout.script')
</body>
</html>
