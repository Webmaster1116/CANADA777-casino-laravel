<!DOCTYPE html>
<html lang="en">
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
    <link rel="stylesheet" type="text/css" href="https://static.canada777.com/frontend/Page/css/register.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   <style>
   .wizard > .steps {
	position: absolute;
	display: block;
	top: 300px;
	left: 150px !important;
	width: 50%;
}
   </style>
    @include('component.frontend.layout.style')
</head>
<body>
    
 
<div class="sign-up-modal-content" style="width:90%; margin:50px auto; border:ridge 15px purple;">
        <div class="modal-left-side modal-side">
            <div class="sign-up-header">
                <h2>Sign Up</h2>
                <p>Already have an account? <a href="{{url('login')}}">Sign In</a></p>
            </div>
            <div class="sign-up-banner">
                <img src="{{asset('frontend/Page/image/sign-up-banner.png')}}" />
            </div>
        </div>
        <div class="modal-right-side modal-side">
            <form id="sign-up-form" class="modal-form" action="{{url('register')}}" method="POST">
                @csrf
                <h3 class="fs-subtitle">Login Detail</h3>

                <fieldset style="overflow-y: scroll; overflow-x: hidden;">
                    @if(isset($register_result))
                        <input type="hidden" id="registerresult" value="{{$register_result}}">
                        @if($error = $errors->first())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Wrong!</strong> {{$error}}
                            </div>
                    @endif
                @endif
                <!-- avoid user have different account to get bonus with fingerprintjs -->
                    <input type="hidden" id="visitorId" name="visitorId" value="">
                    <input type="hidden" id="freespinuser" name="freespinuser" value="">
                   
                   
                    <!--  -->
                    <div class="row">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" placeholder="Username" class="required" />
               <div id="uname_response2" ></div>
                    </div>
                    <div class="row">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="Email" class="required" />
                          <div id="uname_response" ></div>
                    </div>
                   
                    
                    <div class="row">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Password" class="required" />
                    </div>
                    <div class="row">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" placeholder="Currency">
                            @if(isset($currencys) && count($currencys))
                                @foreach($currencys as $currency)
                                    <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </fieldset>

                <h3 class="fs-subtitle">Personal Info</h3>
                <fieldset style="overflow-y: scroll; overflow-x: hidden;">
                    <div class="row">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="first_name" placeholder="First Name" class="required" />
                    </div>
                    <div class="row">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="last_name" placeholder="Last Name" class="required" />
                    </div>
                    
                    <br/>
                    <div class="mb-3">
                        <label for="birthday">Date of Birth (YYYY/MM/DD) *</label>
                     </div>  
                         <div class="mb-3" style="display: flex">
                         
                    <div class="col-4" style="padding-left: 0;">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_year" name="birthday_year" aria-label="Floating label select example">
                                @for($i = 1920; $i <= 2021; $i++)
                                    @if($i == 1980)
                                        <option value="{{$i}}" selected>{{$i}}</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Year</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_month" name="birthday_month" aria-label="Floating label select example">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Month</label>
                        </div>
                    </div>
                    <div class="col-4" style="padding-right: 0;">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_day" name="birthday_day" aria-label="Floating label select example">
                                @for($i = 1; $i <= 31; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Date</label>
                        </div>
                    </div>
                </div>
                  
                    
                    
                    
                    
                    
                    
                    <div class="row phone-for-scroll">
                        <label for="phoneNumber">Mobile Phone *</label>
                        <!-- <input type="tel" id="phoneNumber" name="phone" placeholder="Mobile Phone" class="required" /> -->
                        <input id="phoneNumber" type="tel" name="phone" placeholder="(000) 000-0000" />
                        <div class="invalid-phone" role="alert" style="display: none"></div>
                    </div>
                </fieldset>

                <h3 class="fs-subtitle">Confirm Your Detail</h3>
                <fieldset style="overflow-y: scroll; overflow-x: hidden;">
                    <div class="row">
                        <label for="user_address">Address *</label>
                        <input
                            id="user_address"
                            name="user_address"
                            required
                            autocomplete="off"
                        />
                    </div>
{{--                    <div class="row">--}}
{{--                        <label for="user_address_country">Country *</label>--}}
{{--                        <input id="user_address_country" name="user_address_country" required />--}}
{{--                    </div>--}}
                    <div class="row">
                        <label for="country">Country *</label>
                        <select id="country" name="country" class="selectpicker" data-live-search="true" onchange="onCountryChange()">
                            @if(isset($countrys) && count($countrys))
                                @foreach($countrys as $country)
                                    @if($country->country == "Canada")
                                        <option value="{{$country->id}}" selected>{{$country->country}}</option>
                                    @else
                                        <option value="{{$country->id}}">{{$country->country}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
{{--                    <div class="row">--}}
{{--                        <label for="user-second-address">Address *</label>--}}
{{--                        <input id="user-second-address" name="user-second-address" />--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <label for="address">Address *</label>--}}
{{--                        <input type="text" id="address" name="address" placeholder="Address" class="required" />--}}
{{--                    </div>--}}
                    <div class="row">
                        <label for="user_address_city">City *</label>
                        <input id="user_address_city" name="user_address_city" required />
                    </div>
{{--                    <div class="row">--}}
{{--                        <label for="city">City *</label>--}}
{{--                        <input type="text" id="city" name="city" placeholder="City" class="required" />--}}
{{--                    </div>--}}
                    <div class="row" id="province-row">
                        <label for="user_address_state">State/Province *</label>
                        <input id="user_address_state" name="user_address_state" required />
                    </div>
{{--                    <div class="row" id="province-row">--}}
{{--                        <label for="country">Province *</label>--}}
{{--                        <select id="province" name="province" class="selectpicker" data-live-search="true">--}}
{{--                            @if(isset($provinces) && count($provinces))--}}
{{--                                @foreach($provinces as $province)--}}
{{--                                    <option value="{{$province->id}}">{{$province->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="row" id="state-row" style="display: none;">--}}
{{--                        <label for="state">State *</label>--}}
{{--                        <select id="state" name="state" class="selectpicker" data-live-search="true">--}}
{{--                            @if(isset($states) && count($states))--}}
{{--                                @foreach($states as $state)--}}
{{--                                    <option value="{{$state->id}}">{{$state->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="row" id="other-state-row" style="display: none;">--}}
{{--                        <label for="other-state">Province or States *</label>--}}
{{--                        <input type="text" id="other-state" name="other-state" placeholder="Province or States" />--}}
{{--                    </div>--}}
                    <div class="row">
                        <label for="user_address_postcode">Postal Code *</label>
                        <input id="user_address_postcode" name="user_address_postcode" required />
                    </div>
{{--                    <div class="row">--}}
{{--                        <label for="postalCode">Postal Code *</label>--}}
{{--                        <input type="text" id="postalCode" name="postalCode" class="col-sm-6" placeholder="Postal Code" class="required" required />--}}
{{--                    </div>--}}
                    <div class="row">
                        <label class="checkbox-container">Receive promotions by email and SMS
                            <input type="checkbox" id="receiveEmailSMS" name="receiveEmailSMS" checked />
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </fieldset>

                <h3 class="fs-subtitle">Welcome Package</h3>
                <fieldset style="overflow-y: scroll; overflow-x: hidden;">
                    <legend>Terms and Conditions</legend>
                    <!-- <legend>Terms and Conditions</legend>

                    <input id="acceptTerms-2" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms-2">I agree with the Terms and Conditions.</label> -->

                    <label class="checkbox-container" for="acceptTerms-2">I agree with the Terms and Conditions.
                        <input type="checkbox" id="acceptTerms-2" name="acceptTerms"class="required" checked/>
                        <span class="checkmark"></span>
                    </label>
                    <br>
                    <br>
                    <label class="checkbox-container">I am 18 years old and I accept the Terms and Conditions and Privacy Policy
                        <input type="checkbox" id="acceptAge" name="acceptAge" checked/>
                        <span class="checkmark"></span>
                    </label>
                </fieldset>
            </form>
        </div>
    </div>
   
    
    <div class="page-register" style="display:none;">
        <div class="signup-logo">
            <img src="https://static.canada777.com/frontend/Page/image/sign-up-banner.png" />
        </div>
        <div class="login-button">
            <a href="{{url('login')}}">
                Login if you already have an account
            </a>
        </div>
        <div class="page-register-content">
            <form action="{{url('register/page')}}" method="POST">
                @csrf
                @if($error = $errors->first())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Wrong!</strong> {{$error}}
                    </div>
                @endif
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email@example.com">
                    <label for="floatingInput">Email</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    <label for="floatingInput">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" id="currency" name="currency" aria-label="Floating label select example">
                        @if(isset($currencys) && count($currencys))
                            @foreach($currencys as $currency)
                                <option value="{{$currency->id}}">{{$currency->currency}}</option>
                            @endforeach
                        @endif
                    </select>
                    <label for="floatingSelect">Currency</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="FirstName">
                    <label for="floatingInput">First Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Username">
                    <label for="floatingInput">Last Name</label>
                </div>
                <div class="mb-3" style="display: flex">
                    <div class="col-4" style="padding-left: 0;">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_year" name="birthday_year" aria-label="Floating label select example">
                                @for($i = 1920; $i <= 2021; $i++)
                                    @if($i == 1980)
                                        <option value="{{$i}}" selected>{{$i}}</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Year</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_month" name="birthday_month" aria-label="Floating label select example">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Month</label>
                        </div>
                    </div>
                    <div class="col-4" style="padding-right: 0;">
                        <div class="form-floating">
                            <select class="form-select" id="birthday_day" name="birthday_day" aria-label="Floating label select example">
                                @for($i = 1; $i <= 31; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                            <label for="floatingSelectGrid">Date</label>
                        </div>
                    </div>
                </div>
               
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="MobilePhone">
                    <label for="floatingInput">Mobile Phone</label>
                    <div class="invalid-phone" role="alert" style="display: none"></div>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="user_address" name="user_address" placeholder="Address" autocomplete="off">
                    <label for="floatingInput">Address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="user_address_country" name="user_address_country" placeholder="Country">
                    <label for="floatingInput">Country</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="user_address_city" name="user_address_city" placeholder="City">
                    <label for="floatingInput">City</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="user_address_state" name="user_address_state" placeholder="State/Province">
                    <label for="floatingInput">State/Province</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="user_address_postcode" name="user_address_postcode" placeholder="Postal Code">
                    <label for="floatingInput">Postal Code</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="" id="receiveEmailSMS" name="receiveEmailSMS" checked>
                    <label class="form-check-label" for="flexCheckDefault">
                        Receive promotions by email and SMS
                    </label>
                </div>
                <legend>Terms and Conditions</legend>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="" id="acceptTerms-2" name="acceptTerms-2" checked>
                    <label class="form-check-label" for="flexCheckDefault">
                        I agree with the Terms and Conditions.
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="" id="acceptAge" name="acceptAge" checked>
                    <label class="form-check-label" for="flexCheckDefault">
                        I am 18 years old and I accept the Terms and Conditions and Privacy Policy
                    </label>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">Register</button>
                </div>


{{--                <div class="row">--}}
{{--                    <label for="username">Username *</label>--}}
{{--                    <input type="text" id="username" name="username" placeholder="Username" class="required" />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="email">Email *</label>--}}
{{--                    <input type="email" id="email" name="email" placeholder="Email" class="required" />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="password">Password *</label>--}}
{{--                    <input type="password" id="password" name="password" placeholder="Password" class="required" />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="currency">Currency</label>--}}
{{--                    <select id="currency" name="currency" placeholder="Currency">--}}
{{--                        @if(isset($currencys) && count($currencys))--}}
{{--                            @foreach($currencys as $currency)--}}
{{--                                <option value="{{$currency->id}}">{{$currency->currency}}</option>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="firstName">First Name *</label>--}}
{{--                    <input type="text" id="firstName" name="first_name" placeholder="First Name" class="required" />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="lastName">Last Name *</label>--}}
{{--                    <input type="text" id="lastName" name="last_name" placeholder="Last Name" class="required" />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="birthday">Date of Birth (YYYY/MM/DD) *</label>--}}
{{--                    --}}{{--                        <input type="text" id="birthday" name="birthday" placeholder="Date of Birth" class="required" />--}}
{{--                    <div class="birthday">--}}
{{--                        <select id="birthday_year" name="birthday_year" class="selectpicker" data-live-search="true">--}}
{{--                            @for($i = 1920; $i <= 2021; $i++)--}}
{{--                                @if($i == 1980)--}}
{{--                                    <option value="{{$i}}" selected>{{$i}}</option>--}}
{{--                                @else--}}
{{--                                    <option value="{{$i}}">{{$i}}</option>--}}
{{--                                @endif--}}
{{--                            @endfor--}}
{{--                        </select>--}}
{{--                        <select id="birthday_month" name="birthday_month" class="selectpicker" data-live-search="true">--}}
{{--                            @for($i = 1; $i <= 12; $i++)--}}
{{--                                <option value="{{$i}}">{{$i}}</option>--}}
{{--                            @endfor--}}
{{--                        </select>--}}
{{--                        <select id="birthday_day" name="birthday_day" class="selectpicker" data-live-search="true">--}}
{{--                            @for($i = 1; $i <= 31; $i++)--}}
{{--                                <option value="{{$i}}">{{$i}}</option>--}}
{{--                            @endfor--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="phoneNumber">Mobile Phone *</label>--}}
{{--                    <!-- <input type="tel" id="phoneNumber" name="phone" placeholder="Mobile Phone" class="required" /> -->--}}
{{--                    <input id="phoneNumber" type="tel" name="phone" />--}}
{{--                    <div class="invalid-phone" role="alert" style="display: none"></div>--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="user_address">Address *</label>--}}
{{--                    <input--}}
{{--                        id="user_address"--}}
{{--                        name="user_address"--}}
{{--                        required--}}
{{--                        autocomplete="off"--}}
{{--                    />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="user_address_country">Country *</label>--}}
{{--                    <input id="user_address_country" name="user_address_country" required />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="user_address_city">City *</label>--}}
{{--                    <input id="user_address_city" name="user_address_city" required />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="user_address_state">State/Province *</label>--}}
{{--                    <input id="user_address_state" name="user_address_state" required />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label for="user_address_postcode">Postal Code *</label>--}}
{{--                    <input id="user_address_postcode" name="user_address_postcode" required />--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <label class="checkbox-container">Receive promotions by email and SMS--}}
{{--                        <input type="checkbox" id="receiveEmailSMS" name="receiveEmailSMS" checked />--}}
{{--                        <span class="checkmark"></span>--}}
{{--                    </label>--}}
{{--                </div>--}}
{{--                <legend>Terms and Conditions</legend>--}}
{{--                <!-- <legend>Terms and Conditions</legend>--}}

{{--                <input id="acceptTerms-2" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms-2">I agree with the Terms and Conditions.</label> -->--}}

{{--                <label class="checkbox-container" for="acceptTerms-2">I agree with the Terms and Conditions.--}}
{{--                    <input type="checkbox" id="acceptTerms-2" name="acceptTerms"class="required" checked/>--}}
{{--                    <span class="checkmark"></span>--}}
{{--                </label>--}}
{{--                <br>--}}
{{--                <br>--}}
{{--                <label class="checkbox-container">I am 18 years old and I accept the Terms and Conditions and Privacy Policy--}}
{{--                    <input type="checkbox" id="acceptAge" name="acceptAge" checked/>--}}
{{--                    <span class="checkmark"></span>--}}
{{--                </label>--}}
{{--                <div class="row" style="margin-top: 50px;">--}}
{{--                    <button type="submit" class="button-submit">Regist</button>--}}
{{--                </div>--}}
            </form>
        </div>
    </div>
    @include('component.frontend.layout.script')
</body>
</html>

<script>
    const textFields = document.querySelectorAll('.mdc-text-field');
    for (const textField of textFields) {
        mdc.textField.MDCTextField.attachTo(textField);
    }
</script>
