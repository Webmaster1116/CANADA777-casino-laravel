@extends('frontend.Default.layouts.promotion_layout')
@section('content')
<div class="container free-spin-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card free-spin-content">
                <div class="card-header free-spin-header"><img class="d-md-flex" src="https://static.canada777.com/frontend/Default/img/freespin-header.jpg" /></div>
                
                <div class="card-body free-spin-body">
                    
                    <div class="free-spin-title">Fill In Your Phone Number To Get Started</div>

                    <div class="alert alert-error" style="display: none"></div>
                    <form id="freespin-action-form" class="freespin-action-form" action="javascript:process(event)" method="post">
                        @csrf
                        <div class="form-group row">
                            <label for="phoneforspinn" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>
                            <div class="col-md-8">
                                <input type="tel" id="phoneforspinn" name="phoneforspinn" placeholder="(000) 000-0000">
                            </div>
                            <div class="alert alert-info" style="display: none"></div>
                            
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" id="submit_button" class="btn btn-danger col-sm-12">
                                    YES! Give Me FREE Spins!
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

<script>
    const phoneInputField = document.querySelector("#phoneforspinn");
    const phoneInput = window.intlTelInput(phoneInputField, {
        preferredCountries: ["ca", "us", "cn"], 
        onlyCountries: ["ca", "us", "cn"],
        utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });

    const info = document.querySelector(".alert-info");
    const error = document.querySelector(".alert-error");

    info.style.display = "none";
    error.style.display = "none";

 function process(event) {
    $('#submit_button').prop('disabled', true);
    const phoneNumber = phoneInput.getNumber();
    var form = $('#freespin-action-form');
    if (phoneInput.isValidNumber()) {
        $.ajax({
            type : "POST",  
            url  : "phone_verify", 
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data : { phone : phoneNumber },
            dataType:"JSON",
            success: function(res){  
                if(res.type == 'exist_error'){
                    error.style.display = "";
                    error.innerHTML = res.message;
                    $(".alert-error").delay(10000).fadeOut(1000);
                    localStorage.setItem("freespin_signup", "1");
                    setTimeout(function() {            
                        window.location.replace(res.url);
                    }, 2000);
                }else if(res.type == 'error') {
                    error.style.display = "";
                    error.innerHTML = res.message;
                    $(".alert-error").delay(10000).fadeOut(1000);
                    $('#submit_button').prop('disabled', false);
                }else {
                    // window.open(res.url);
                    window.location.replace(res.url);
                    $('#submit_button').prop('disabled', false);
                }
            },
            error: function(xhr, textStatus, error){
                $(".alert-error").css('display', '');
                error.innerHTML = "request error";
                $(".alert-error").delay(10000).fadeOut(1000);
                $('#submit_button').prop('disabled', false);
            }
        });
    } else {
        error.style.display = "";
        error.innerHTML = 'Please wait while you are redirected to get the 100 Free spins.';
        $(".alert-error").delay(10000).fadeOut(1000);
        $('#submit_button').prop('disabled', false);
    }
 }
 </script>
@endsection