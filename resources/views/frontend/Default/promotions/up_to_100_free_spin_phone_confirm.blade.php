@extends('frontend.Default.layouts.promotion_layout')
@section('content')
<div class="container free-spin-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card free-spin-content">
                <div class="card-header free-spin-header"><img class="d-md-flex" src="https://static.canada777.com/frontend/Default/img/freespin-header.jpg" /></div>
                
                <div class="card-body free-spin-body">
                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{session('error')}}
                    </div>
                    @endif
                    <div class="free-spin-title">Please Confirm Your Phone</div>
                    <form class="freespin-action-form" action="{{route('phone_confirm')}}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label for="verification_code"
                                class="col-md-6 col-form-label text-md-right">Confirm Code</label>
                            <div class="col-md-6">
                                <input type="hidden" name="phone_number" value="{{$phone_number}}">
                                <input id="verification_code" type="tel"
                                    class="form-control @error('verification_code') is-invalid @enderror"
                                    name="verification_code" value="{{ old('verification_code') }}" placeholder="Confirm Code" required>

                                @error('verification_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-danger col-sm-12">
                                    Veryify Phone!
                                </button>
                            </div>
                        </div>
                    </form>
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