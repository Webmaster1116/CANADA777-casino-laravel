@extends('frontend.Default.layouts.promotion_layout')
@section('content')
<div class="container free-spin-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card free-spin-content">
                <div class="card-header free-spin-header"><img class="d-md-flex" src="https://static.canada777.com/frontend/Default/img/freespin-header.jpg" /></div>
                
                <div class="card-body free-spin-body">
                    
                    <div class="free-spin-title">You have got 100 free spin.</div>
                    
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    <a href="{{route('frontend.game.list')}}" class="go-to-home" >canada777.com</a>
                </div>
            </div>
        </div>
        
    </div>
    
</div>
<div class="free-spin-footer">
    <div class="footer__wrap-int col-md-8">
        <div class="footer__payment-icons">
            <img class="footer__payment-icons-img-int lazy-loaded" data-lazy-type="image" alt="payment-methods" data-src="https://static.canada777.com/frontend/Default/img/payment_logo.png')}}" src="{{asset('frontend/Default/img/payment_logo.png">
        </div>
        <p class="footer__para">Canada777 is a brand name of <span data-text="EmSpcmEvcyOCbmxpbmHgH2IydmljMKZgGTltaKElMN==" class="crypt">Fairdos Online Services Limited</span>, Reg No. C387234. It is a wholly owned subsidiary of <span data-text="EmSpcmEvcyOZdTDu" class="crypt">Fairdos Ltd.</span> Licensed to conduct online gaming operations by the Government of Curacao under license 365/JAZ.</p>
        <p class="footer__para">Canada777 –  Achilleos, 21 Flat/Office B, Agios Dometios 2370, Nicosia, Cyprus</p>
        <p class="footer__para"><a class="footer__link" href="https://canada777.com/" target="_blank">Licence Number: GLH-OCCHKTW0707192018 </a></p>
        <div class="footer__para modal__popup-js tandc__popup-js">Terms and conditions</div>
        <div class="footer__para modal__popup-js footer__link privacy-link">Privacy Policy</div>
    </div>
</div>
@endsection