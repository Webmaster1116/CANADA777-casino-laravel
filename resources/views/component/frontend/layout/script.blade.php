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
<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" integrity="sha512-q583ppKrCRc7N5O0n2nzUiJ+suUv7Et1JGels4bXOaMFQcamPk9HjdUknZuuFjBNs7tsMuadge5k9RzdmO+1GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Slick Slider Steps -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<!-- Crypto Config Json -->
<script type="text/javascript" src="{{asset('frontend/assets/crypto_config.js')}}"></script>
<script type="text/javascript" src="{{asset('frontend/assets/fingerprint_config.js')}}"></script>

<!-- avoid user have different account to get bonus with fingerprintjs -->
<script
    {{-- async --}}
    src="{{asset('frontend/assets/fp.min.js')}}"
    {{-- onload="initFingerprintJS()" --}}
></script>

<!-- integration green popups plugin -->
<script id="lepopup-remote" src="{{asset('/popup/content/plugins/halfdata-green-popups/js/lepopup.js?ver=7.24')}}" data-handler="{{asset('/popup/ajax.php')}}"></script>
<!--  -->
<!-- Google Analytics for tracking traffic -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YV3LJDK5G6"></script>
<!--  -->
<script type="text/javascript" src="{{asset('frontend/Page/js/script.js')}}"></script>

<script id="sbinit" src="{{asset('support/js/main.js')}}"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

<script>

    window.dataLayer = window.dataLayer || [];

    function gtag(){dataLayer.push(arguments);}

    gtag('js', new Date());

    gtag('config', 'UA-185160427-1');

</script>

{{-- <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6jg2Vx3oL5bQAItFPcYZTZJDJLzZIUSE&callback=initAutocomplete&libraries=places&v=weekly"
    async
></script> --}}
