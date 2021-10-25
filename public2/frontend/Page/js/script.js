'use Strict';
function playNow()
{
    showSignup();
}
$(document).ready(function(){
    var deposit_crypto_currency = '';
    var crypto_currency_from = '';
    var deposit_amount;
    var deposit_currency;
    var crypto_deposit_amount;
    function setIntervalX(callback, delay, repetitions) {
        var x = 0;
        var intervalID = window.setInterval(function () {

           callback();

           if (++x === repetitions) {
               window.clearInterval(intervalID);
           }
        }, delay);
    }
    setIntervalX(
        function()
        {
            var timer_count = localStorage.getItem("timer_count");
            if(timer_count == null || timer_count == 300)
                timer_count = 0;
            if(timer_count == 295)
            {
                if($("#auth_status").val() == "1") {
                    if(parseFloat($("#user_balance_amount").val()) <= 0) {
                        $("#nobalance-modal").modal({
                            fadeDuration: 300
                        });
                    }
                }
            }
            // console.log(localStorage.getItem("timer_count"));
            localStorage.setItem("timer_count", parseInt(timer_count) + 5);
        }, 5000, 1200
    );

    if($("#auth_status").val() == "1") {
        $.ajax({
            url: '/check_freespin100',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:"JSON",
            success:(data)=>{
                if(data.status == 1){
                    setTimeout(function(){
                        lepopup_popup_open(data.popupmodal);
                    }, 1000);
                }else{
                    if(parseFloat($("#user_balance_amount").val()) <= 0) {
                        $("#nobalance-modal").modal({
                            fadeDuration: 300
                        });
                    }
                }
            },
            error:()=>{
            },
            complete:()=>{
            }
        });
    }
    /* --- */
    if(performance.navigation.type == 2){
        location.reload(true);
    }
    /* Google Analytics for tracking traffic */
    window.dataLayer = window.dataLayer || [];
    function gtag(){
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'G-YV3LJDK5G6');
    /* --- */
    var deposit_amount;
    var deposit_currency;
    var crypto_deposit_amount;
    if(localStorage.getItem("freespin_signup") && localStorage.getItem("freespin_signup") == "1") {
        /* avoid user have different account to get bonus with fingerprintjs */
        if(!localStorage.getItem("visitorId")){
            initFingerprintJS();
        }

        $("#visitorId").val(localStorage.getItem("visitorId"));
        /* --- */
        $("#freespinuser").val("freespin");

        open_signup_modal();
        localStorage.removeItem("freespin_signup");
    }
    if($("#loginresult").val()){
        /* avoid user have different account to get bonus with fingerprintjs */
        if(!localStorage.getItem("visitorId")){
            initFingerprintJS();
        }
        $("#login_visitorId").val(localStorage.getItem("visitorId"));
        /* --- */
        showSignin();
    }
    var iframe = $('.d-sm-block').contents();
    iframe.find("#choose_pics").click(function(){
        alert("test");
    });
    window.addEventListener('message', function(event){
        if ( event.data.from == 'child'){
            if ( event.data.type == 'playNow') {
                console.log('Play Now');
            }
        }
    });

    if($("#registerresult").val()){
        /* avoid user have different account to get bonus with fingerprintjs */
        if(!localStorage.getItem("visitorId")){
            initFingerprintJS();
        }
        $("#visitorId").val(localStorage.getItem("visitorId"));
        /* --- */
        showSignup();
    }

    if($("#forgotpasswordresult").val()){
        showForgot();
    }

    if($("#resetpasswordresult").val()){
        $("#resetpassword-modal").modal({
            fadeDuration: 300,
            escapeClose: false,
            clickClose: false
        });
    }

    fn_deposit=(auth)=>{
        if(!auth){
            /* avoid user have different account to get bonus with fingerprintjs */
            if(!localStorage.getItem("visitorId")){
                initFingerprintJS();
            }
            $("#login_visitorId").val(localStorage.getItem("visitorId"));
            /* --- */
            showSignin();
        }else{
            $("#deposit-modal").modal({
                fadeDuration: 300,
                escapeClose: false,
                clickClose: false
            });
        }
    };
    /* modify deposit like canada777.com */
    fn_price=(value, e)=>{
        deposit_amount = value;
        $("#deposit_amount").val(deposit_amount);
        $("input[name='deposit_amount']").val(value);
        $("span#deposit_currency").text($("#deposit_currency option:selected").text());

        if (!$(e).hasClass("modal-selected-deposit-currency")) {
            $(e).addClass("modal-selected-deposit-currency") ;

            $('.modal-content-deposit-amount').map((index, elet) => {
                if(elet !== e){
                    if($(elet).hasClass('modal-selected-deposit-currency')){
                        $(elet).removeClass("modal-selected-deposit-currency") ;
                    }
                }
            })
        }else {

        }
    };

    fn_crypto_price = (value, e) => {
        crypto_deposit_amount = value;
        $("#crypto_deposit_amount").val(crypto_deposit_amount);
        $("input[name='crypto_deposit_amount']").val(value);
        $("span#crypto_deposit_currency").text($("#deposit_currency option:selected").text());

        if (!$(e).hasClass("modal-selected-deposit-currency")) {
            $(e).addClass("modal-selected-deposit-currency") ;

            $('.modal-content-deposit-amount').map((index, elet) => {
                if(elet !== e){
                    if($(elet).hasClass('modal-selected-deposit-currency')){
                        $(elet).removeClass("modal-selected-deposit-currency") ;
                    }
                }
            })
            if($('#payment_method').val() == 'crypto' && deposit_crypto_currency != ''){
                crypto_get_address(deposit_crypto_currency, value, crypto_currency_from, "0");
            }
        }else {

        }
    }
    fn_selected_currency=(value, e)=>{
        $("#sel_currency").val(value);
        if (!$(e).hasClass("modal-selected-deposit-currency")) {
            $(e).addClass("modal-selected-deposit-currency") ;

            $('.modal-content-currency-type').map((index, elet) => {
                if(elet !== e){
                    if($(elet).hasClass('modal-selected-deposit-currency')){
                        $(elet).removeClass("modal-selected-deposit-currency") ;
                    }
                }
            });
        }else {
        }
    };

    fn_payment_method_select = (e, type = 'interac', crypto_currency = '') => {
        if (!$(e).hasClass("payment-method-button-element-selected")) {
            if(type == 'crypto'){
                deposit_crypto_currency = crypto_currency;

                var currency_to = $("#deposit_currency option:selected").text();
                crypto_currency_from = $("#deposit_currency option:selected").text();
                $("#currency_to").val(currency_to);
                if($("#crypto_deposit_amount").val() == ""){
                    crypto_deposit_amount = 25;
                    $("#crypto_deposit_amount").val(crypto_deposit_amount);
                    $("input[name='crypto_deposit_amount']").val(crypto_deposit_amount);
                    $("span#crypto_deposit_currency").text($("#deposit_currency option:selected").text());
                }
                $('.deposit-crypto-content').css('display', 'block');
                $('.deposit-interac-content').css('display', 'none');
                if($("#sel_currency").val() == ""){
                    option_for_crypto(true);
                    var get_address_flag = "1";
                    $.ajax({
                        url:"/cryptocurrencies_list",
                        type : "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:"JSON",
                        data: {
                            currency: crypto_currency,
                            crypto_deposit_amount: crypto_deposit_amount,
                            currency_to: currency_to,
                            get_address_flag: get_address_flag
                        },
                        success:(res)=>{
                            if(res.error) {
                                $('.error-body').show("slow", function () {
                                    $('.error-body').css('display', 'block');
                                });
                                $('.error-content').append('<span>'+ res.error +'</span>');
                                return;
                            }else {
                                var currency_rate = res.currency_rate;
                                var address = res.address;
                                var minimum_amount = res.minimum_amount;

                                $('#crypto_address').val(address);
                                if(document.querySelector(".crypto-address-details")){
                                    $('.crypto-address-details').remove();
                                }

                                $('.crypto-address-link').append('<div class="crypto-address-details"></div>');
                                var qr_prefix = '';
                                if(currency_rate['rate_to_currency'] == "BTC"){
                                    qr_prefix = "bitcoin";
                                }else if(currency_rate['rate_to_currency'] == "BCH"){
                                    qr_prefix = "bitcoincash";
                                }else if(currency_rate['rate_to_currency'] == "LTC"){
                                    qr_prefix = "litecoin";
                                }else {
                                    qr_prefix = "ethereum";
                                }
                                var crypto_address_link = "";
                                crypto_address_link += "<p>This is your private depositing address. Any transactions you make to this address will show in your balance after 1 confirmation. Kindly note the minimum deposit limit stated, as deposits below this limit cannot be processed.</p>";
                                crypto_address_link += '<div class="crypto-qr-code" style="display:flex; flex-direction:row;"><a href="'+qr_prefix+':'+ address +'" target="_blank" ><img src="https://chart.googleapis.com/chart?chs=150x150&amp;cht=qr&amp;chl='+qr_prefix+':'+ address +'&amp;choe=UTF-8"></a>';
                                crypto_address_link += '<div class="crypto-description mt-4" style="display:flex; flex-direction:column;"><span>Copy this '+ currency_rate['rate_to_currency'] +' address or follow the link to open your wallet application</span><div class="copy-part" style="display:flex; flex-direction:row"><span >'+address+'</span><a href="javascript:fn_copy_address(`address`)" style="padding-left:10px; font-size: 18px">COPY</a></div><span>Currency Rate : '+currency_rate['rate_from'] + currency_rate['rate_from_currency']+' ~ '+currency_rate['rate_to'] +currency_rate['rate_to_currency']+'</span><span>minimum deposit :'+ minimum_amount +'</span></div>';
                                crypto_address_link += '</div><p>Please be careful to send only '+ currency_rate['rate_to_currency'] +' to this address. Sending any other currency may result in a deposit delay or funds being lost. The deposits made less than the minimal limits may result in the loss of the funds.</p>';
                                crypto_address_link += '<p>Deposit can take up to 10 minutes.</p>';
                                $('.crypto-address-details').append(crypto_address_link);
                            }
                            option_for_crypto(false);
                        },
                        error:(error)=>{
                            console.log(error);
                            option_for_crypto(false);
                        }
                    });
                }
            }else {
                $('.deposit-crypto-content').css('display', 'none');
                $('.deposit-interac-content').css('display', 'block');
            }
            $(e).addClass("payment-method-button-element-selected") ;
            $("#payment_method").val(type);
            $('.payment-method-button-element').map((index, elet) => {
                if(elet !== e){
                    if($(elet).hasClass('payment-method-button-element-selected')){
                        $(elet).removeClass("payment-method-button-element-selected") ;
                    }
                }
            })
        }
    }
    fn_withdraw_payment_method_select = (e, type = 'interac', crypto_currency = '') => {
        if (!$(e).hasClass("payment-method-button-element-selected")) {
            $(e).addClass("payment-method-button-element-selected") ;
            $("#payment_method").val(type);
            $('.payment-method-button-element').map((index, elet) => {
                if(elet !== e){
                    if($(elet).hasClass('payment-method-button-element-selected')){
                        $(elet).removeClass("payment-method-button-element-selected") ;
                    }
                }
            })
        }
		if(type == "crypto"){
            $('#withdraw_crypto_type').val(crypto_currency);
            $("#payment_method").val(type);
			$('#withdrawemail').attr('placeholder', 'CryptoCurrency Address');
			$('.form__description').css('display', 'none');
		}else{
			$('#withdrawemail').attr('placeholder', 'Email');
			$('.form__description').css('display', 'block');
		}
        $("#payment_method").val(type);
    }
    fn_copy_address = (val) => {
        var temp = $("<input>");
        $("body").append(temp);
        temp.val($('#crypto_address').val()).select();
        document.execCommand("copy");
        temp.remove();
    }
    fn_add_currency_type = () => {

    }
    /* --- */

    fn_amount_input=()=>{
        $("span#deposit_currency").text($("#deposit_currency option:selected").text());
    };
    fn_change_currency=()=>{
        $("span#deposit_currency").text($("#deposit_currency option:selected").text());
        $("span#crypto_deposit_currency").text($("#deposit_currency option:selected").text());
        crypto_currency_from = $("#deposit_currency option:selected").text();
        if($('#payment_method').val() == 'crypto' && deposit_crypto_currency != ''){
            crypto_get_address( deposit_crypto_currency, crypto_deposit_amount, crypto_currency_from, "1");
        }
    };
    fn_crypto_amount_input=(amount)=>{
        $("span#crypto_deposit_currency").text($("#deposit_currency option:selected").text());
        crypto_deposit_amount = amount;
        if(crypto_deposit_amount >= 25){
            $('.info-body-min-deposit').fadeOut(1000);
            if($('#payment_method').val() == 'crypto' && deposit_crypto_currency != ''){
                crypto_get_address( deposit_crypto_currency, crypto_deposit_amount, crypto_currency_from, "0");
            }
        }else {
            $('.info-body-min-deposit').fadeIn(1000);
        }
    }

    fn_deposit_submit=()=> {

        deposit_currency = $("#deposit_currency option:selected").text();
        $("#cur_deposit_currency").val(deposit_currency);
        if($("#deposit_amount").val() && $("#deposit_email").val() && $("#deposit_phone").val()){
            $.ajax({
                url: $("#deposit-form").attr('action'),
                type: $("#deposit-form").attr('method'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $("#deposit-form").serialize(),
                success:(data)=>{
                    /*if (data.error) {
                        $('.error-body').show("slow", function () {
                            $('.error-body').css('display', 'block');
                        });
                        $('.error-content').html(data.msg);
                        return;
                    }
                  //show message but not return so window.open() command will be excuted.
                    if(data.multiDeposit == 1){
                        $('.error-body').show("slow", function () {
                            $('.error-body').css('display', 'block');
                        });
                        $('.error-content').html(data.multimsg);
                    }*/
                    /* --- */
                    window.location.href(data.redirectUrl);
                    //window.open(data.redirectUrl);
                },
                error:()=>{
                },
                complete:()=>{
                }
            });
        }
    };

    fn_deposit_close = () => {
        $("#profile-modal").modal('hide');
        location.reload();
    }

    fn_withdraw_submit=()=> {
        if($("#amount").val() && $("#withdrawemail").val() && $("#phone").val()){
            if (/^(1)?\d{10}$/.test($("#phone").val())){
                $.ajax({
                    url: $("#withdraw-form").attr('action'),
                    type: $("#withdraw-form").attr('method'),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $("#withdraw-form").serialize(),
                    success:(data)=>{
                        if (data.error) {
                            $('.error-body').show("slow", function () {
                                $(".error-body").removeClass("alert-success");
                                $(".error-body").addClass("alert-danger");
                                $('.error-body').css('display', 'block');
                                $(".error-body strong").html("Wrong");
                                if (data.msg.indexOf('play through required') > -1){
                                    var content = `<span>${data.msg}</span><div><strong>Your Balance Summary</strong>
									<p>Cash Balance : ${data.realBalance}</p>
									<p>Bonus Balance : ${data.bonusBalance}</p>
									<p>Total Balance : ${data.totalBalance}</p></div>
									<div><strong>Your Bonus Summary</strong>
									<p>Current Playthrough Achieved : ${data.playWager}</p>
									<p>Playthrough Required : ${data.wager}</p></div>`;
                                    $('.error-content').html(content);
                                }
                                else
                                    $('.error-content').html('<span>'+ data.msg +'</span>');
                            });
                            return;
                        }
                        $('.error-body').show("slow", function () {
                            $(".error-body").removeClass("alert-danger");
                            $(".error-body").addClass("alert-success");
                            $('.error-body').css('display', 'block');
                            $(".error-body strong").html("Success");
                        });
                        $('.error-content').html('<span>'+ "Your withdrawal has been requested." +'</span>');
                        $(".form__actions button").prop('disabled', true);
                    },
                    error:()=>{
                    },
                    complete:()=>{
                    }
                });
            }
            else{
                $('.error-body').show("slow", function () {
                    $('.error-body').css('display', 'block');
                });
                $('.error-content').html('<span>'+ "Telephone number is not correct" +'</span>');
                return;
            }
        }
    };

    fn_alert_close = () => {
        $('.error-body').fadeOut("slow", function () {
            $('.error-body').css('display', 'none');

        });
    }

    fn_cashout=()=>{
        $("#cashout-modal").modal({
            fadeDuration: 300,
            escapeClose: false,
            clickClose: false
        });
    }
    fn_cashout_submit=()=>{
        if($("#cashout_amount").val()){
            $.ajax({
                url: $("#cashout-form").attr('action'),
                type: $("#cashout-form").attr('method'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $("#cashout-form").serialize(),
                success:(data)=>{
                    if (data.error) {
                        alert(data.msg);
                        return;
                    }
                    window.location.href(data.redirectUrl);
                },
                error:()=>{
                },
                complete:()=>{
                }
            });
        }
    }
    fn_forgetPassword=()=>{
        if($("#forget_email").val()){
            $.ajax({
                url: $("#forgot-password-form").attr('action'),
                type: $("#forgot-password-form").attr('method'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $("#forgot-password-form").serialize(),
                success:(data)=>{
                    $(".alert-danger").hide();
                    $(".alert-success").hide();
                    if (data.type == 'error') {
                        $(".alert-danger").show();
                        $(".alert-danger span").html(data.msg);
                    }
                    else{
                        $(".alert-success").show();
                        $(".alert-success span").html(data.msg);
                    }
                    return;
                },
                error:()=>{
                },
                complete:()=>{
                }
            });
        }
    }
    $("img").lazyload({
        effect : "fadeIn"
    });

    $("#menu-toggle").on("click", function(){
        if ($("#menu_checkbox").prop("checked"))
        {
            console.log("true");
            $("#menu_checkbox").prop("checked", false);
        }
        else
        {
            console.log("false");
            $("#menu_checkbox").prop("checked", true);
        }

        $("header").toggleClass("active");
        $("main").toggleClass("active");
        $("body").toggleClass("position-fixed");
    });

    $("#menu_label").on("click", function(e){
        e.stopPropagation();
    });

    // $(".search_game").on("keyup", function(e){
    //     fn_search_game();
    // });
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('.search_game').keyup(function() {
        delay(function(){
            fn_search_game();
        }, 1000 );
    });

    /* search Modal functions*/
    $("a[href='#search-modal']").on("click", function(e){
        $("#search_game_keyword").val("");
        $(".search_game_content").html("");
        $("#search-modal").modal({
            fadeDuration: 300
        });
    });

    /* playforFun Modal functions*/
    $("a[href='#playfun-modal']").on("click", function(e){
        $("#playfun-modal").modal({
            fadeDuration: 300
        });
    });

    /* Sign-in Modal functions*/
    $("a[href='#signin-modal']").on("click", function(e){

        /* avoid user have different account to get bonus with fingerprintjs */
        if(!localStorage.getItem("visitorId") || localStorage.getItem("visitorId") == null || localStorage.getItem("visitorId") == ''){
            initFingerprintJS();
        }

        $("#login_visitorId").val(localStorage.getItem("visitorId"));
        /* --- */
        showSignin();
    });

    /* Sign-up Modal functions*/
    $("a[href='#signup-modal']").on("click", function(e){
        /* avoid user have different account to get bonus with fingerprintjs */
        if(!localStorage.getItem("visitorId") || localStorage.getItem("visitorId") == null || localStorage.getItem("visitorId") == ''){
            initFingerprintJS();
        }

        $("#visitorId").val(localStorage.getItem("visitorId"));
        /* --- */
        showSignup();
    });

    /* Forgot Password Modal functions*/
    $("a[href='#forgotpassword-modal']").on("click", function(e){
        showForgot();
    });

    /* validate signup form */
    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "No space please and don't leave it empty");

    jQuery.validator.addMethod("checkEmail", function(value, element) {
        var check_format = /\S+@\S+\.\S+/;
        if( check_format.test(value) == true ){
            return check_format.test(value);
        };
    }, "Please enter a valid email address");

    jQuery.validator.addMethod("validateCity", function(value, element) {
        var patternForCity = /^([a-zA-Z][a-zA-Z ]{0,49})$/;
        if(patternForCity.test(value)) {
            return patternForCity.test(value);
        }
    }, "Please enter a valid city address");

    jQuery.validator.addMethod("validatePostalCode", function(value, element) {
        var patternForPostalCode = /^([a-zA-Z][a-zA-Z ]{0,49})$/;
        if(patternForPostalCode.test(value)) {
            return patternForPostalCode.test(value);
        }
    }, "Please enter a valid Postal Code");
    /* -- */
    var form = $("#sign-up-form").show();

    form.validate({
        rules: {
            username: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
            password: {
                minlength: 8,
                maxlength: 50
            },
            email: {
                required: true,
                checkEmail: true
            },
            first_name: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
            last_name: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
            city: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
            address: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
            postalCode: {
                minlength: 2,
                maxlength: 50,
                noSpace: true
            },
        }
    });

    form.steps({
        headerTag: "h3",
        bodyTag: "fieldset",
        transitionEffect: "slideLeft",
        onStepChanging: function (event, currentIndex, newIndex)
        {
            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex)
            {
                return true;
            }
            // Forbid next action on "Warning" step if the user is to young
            if (newIndex === 3 && Number($("#age-2").val()) < 18)
            {
                return false;
            }

            // Needed in some cases if the user went back (clean up)
            if (currentIndex < newIndex)
            {
                // To remove error styles
                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }

            /* check format of phone number  */
            if( currentIndex == 1){
                if (!phoneInput.isValidNumber()) {
                    var error = document.querySelector(".invalid-phone");
                    error.style.display = "";
                    error.innerHTML = 'Invalid phone number';
                    form.validate().settings.ignore = ":disabled,:hidden";
                    form.valid();
                }else {
                    $(".invalid-phone").css('display', 'none');
                    form.validate().settings.ignore = ":disabled,:hidden";
                    return form.valid();
                }
            }else{
                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            }
            /* --- */
        },
        onStepChanged: function (event, currentIndex, priorIndex)
        {
            /* phone number format (000) 000-0000 */
            $('#phoneNumber').attr('placeholder', "(000) 000-0000");
            /*  */
            // Used to skip the "Warning" step if the user is old enough.
            if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
            {
                form.steps("next");
            }
            // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
            if (currentIndex === 2 && priorIndex === 3)
            {
                // form.steps("previous");
            }
        },
        onFinishing: function (event, currentIndex)
        {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            $('#phoneNumber').val(phoneInput.getNumber());
            if(!localStorage.getItem("visitorId") || localStorage.getItem("visitorId") == null || localStorage.getItem("visitorId") == "" || $("#visitorId").val == ""){
                initFingerprintJS();
                $("#visitorId").val(localStorage.getItem("visitorId"));
            }
            form.submit();
        }
    }).validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            acceptAge: {
                required: true
            }
        }
    });

    // let myCoolCode = document.createElement("script");
    // myCoolCode.setAttribute("src", "https://maps.googleapis.com/maps/api/js?key=AIzaSyD6jg2Vx3oL5bQAItFPcYZTZJDJLzZIUSE&callback=initAutocomplete&libraries=places&v=weekly");
    // document.body.appendChild(myCoolCode);

    /* phone check for sign up */
    if ( $("#phoneNumber").is(":focus") ) {
        $(".invalid-phone").css('display', 'none');
    }

    const phoneField = document.querySelector("#phoneNumber");
    phoneField.addEventListener('focus', (event) => {
        $(".invalid-phone").css('display', 'none');
    });
    phoneField.addEventListener("focusout", (event) => {
        if (!phoneInput.isValidNumber()) {
            var error = document.querySelector(".invalid-phone");
            error.style.display = "";
            error.innerHTML = 'Invalid phone number';
        }else {
            $(".invalid-phone").css('display', 'none');
        }
    });
    const phoneInput = window.intlTelInput(phoneField, {
        preferredCountries: ["ca", "us", "cn"],
        onlyCountries: ["ca", "us", "cn"],
        utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });
    /* --- */

    /* datepicker */
    var date_input=$('input[name="birthday"]'); //our date input has the name "date"
    var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
    date_input.datepicker({
        format: 'mm/dd/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
        startDate: '1910-01-01',
        endDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18))
    });

    // var phones = [{ "mask": "(###) ###-####" }];
    // $('#phoneNumber').inputmask({
    //     mask: phones,
    //     greedy: false,
    //     definitions: { '#': { validator: "[0-9]", cardinality: 1}} });

    $('.top-category-list').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 6,
        slidesToScroll: 6,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    infinite: true,
                }
            },
            {
                breakpoint: 967,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
    $('.mobile-top-category-list').slick({
        dots: false,
        infinite: false,
        variableWidth: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 5,
    });

    $(".dropdown-toggle").on("click", function (e) {
        $(".category-toggle-button").toggleClass("show");
        $(".dropdown-menu").toggleClass("show");
    });

    $(".dropdown-menu").on("click", function (e) {
        e.stopPropagation();
    });

    $('.welcomepackage-games').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
    });

    fn_profile=()=>{
        $("#profile-modal").modal({
            fadeDuration: 300,
            escapeClose: false,
            clickClose: false
        });

        fn_profile_load('balance');
    };
    fn_side_menu=()=>{
        // $("#side-modal").toggleClass("opened");
        $("#side-modal").modal({
            fadeDuration: 300,
            escapeClose: false,
            clickClose: false
        });
    };
    fn_profile_load=(param)=>{
        /* modify deposit UI like canada.com */
        if($('#profile-modal').css("display") === 'none'){
            $("#profile-modal").modal({
                fadeDuration: 300,
                escapeClose: false,
                clickClose: false
            });
        }
        /* ---  */
        $.ajax({
            url: '/profile/' + param,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:(data)=>{
                if (data.success) {
                    $('#profile-modal>div.profile-modal-modal-content').html(data.html);
                    return;
                }
            },
            error:()=>{
            },
            complete:()=>{
            }
        });
    }
    enableButton=()=>{
        $("button[name='updatedetail']").attr('disabled', false);
    };

    changePassword=()=>{
        var oldPass = $("input[name='oldPassword']").val();
        var pass1 = $("input[name='newPassword']").val();
        var pass2 = $("input[name='passwordVerify']").val();
        $(".message.error").css("display", "block");
        if (oldPass == ""){
            $(".message.error").html("Input old password!");
            return;
        }
        if (pass1 == ""){
            $(".message.error").html("Input new password!");
            return;
        }
        if (pass2 == ""){
            $(".message.error").html("Input confirm new password!");
            return;
        }
        if (pass1 != pass2){
            $(".message.error").html("Two password is not matched!");
            return;
        }
        $(".message.error").css("display", "none");
        $.ajax({
            url: '/profile/password/update',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                old_password : oldPass,
                password : pass1
            },
            success:(data)=>{
                if (data.status == 1) {
                    location.reload();
                }
            },
            error:(data)=>{
                if (data.responseJSON) {
                    if (data.responseJSON.status == -1)
                    {
                        $(".message.error").html("Invalid old password");
                        $(".message.error").css("display", "block");
                        return;
                    }
                }
            },
            complete:()=>{
            }
        });
    };
    $('.game-item img').each(function() {
        $(this).attr("src", $(this).data("original"));
    });

    $('.horizontal-slider-section').slick({
        slidesToShow: 5,
        slidesToScroll: 5,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                }
            },
            {
                breakpoint: 990,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});

/* avoid user have different account to get bonus with fingerprintjs */
function initFingerprintJS() {
    FingerprintJS.load({token: fingerprintConfig.browser_token})
        .then(fp => fp.get())
        .then(result => {
            if(!result.visitorId) {
                console.log("You can not use this website!!!");
            }
            localStorage.setItem("visitorId", result.visitorId);
            console.log(result.visitorId);
        })
        .catch(error => console.log(error));
}
/* --- */

/* show welcome bonus popup when player is not login in 120 s.  */
if($("#auth_status").val() !== "1") {
    lepopup_add_event("onload", {
        item:        "popup-welcome-bonus",
        item_mobile: "popup-welcome-bonus-mobile",
        mode:        "every-time",
        period:      24,
        delay:       120,
        close_delay: 0
    });
}
function option_for_crypto(option = false) {
    $('.modal-content-deposit-button').prop('disabled', option);
    $('.modal-content-deposit_currency').prop('disabled', option);
    $('.modal-content-deposit-amount').prop('disabled', option);
    $('.payment-method-button-element').prop('disabled', option);
    $('#crypto_deposit_amount').prop('disabled', option);
}
function crypto_get_address( currency, amount, convert_to, get_add_flag){
    option_for_crypto(true);
    var get_address_flag = get_add_flag;
    var currency_from = currency;
    $.ajax({
        url:"/cryptocurrencies_list",
        type : "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:"JSON",
        data: {
            currency: currency_from,
            crypto_deposit_amount: amount,
            currency_to: convert_to,
            get_address_flag: get_address_flag
        },
        success:(res)=>{
            if(res.error) {
                $('.error-body').show("slow", function () {
                    $('.error-body').css('display', 'block');
                });
                $('.error-content').append('<span>'+ res.error +'</span>');
                return;
            }else {
                var currency_rate = res.currency_rate;
                var address = res.address;
                var minimum_amount = res.minimum_amount;

                if(document.querySelector(".crypto-address-details")){
                    $('.crypto-address-details').remove();
                }

                $('.crypto-address-link').append('<div class="crypto-address-details"></div>');
                var qr_prefix = '';
                if(get_address_flag == "0"){
                    qr_prefix = currency_from;
                    address = $("#crypto_address").val();
                }else{
                    $('#crypto_address').val(address);
                }
                if(currency_rate['rate_to_currency'] == "BTC"){
                    qr_prefix = "bitcoin";
                }else if(currency_rate['rate_to_currency'] == "BCH"){
                    qr_prefix = "bitcoincash";
                }else if(currency_rate['rate_to_currency'] == "LTC"){
                    qr_prefix = "litecoin";
                }else {
                    qr_prefix = "ethereum";
                }
                var crypto_address_link = "";
                crypto_address_link += "<p>This is your private depositing address. Any transactions you make to this address will show in your balance after 1 confirmation. Kindly note the minimum deposit limit stated, as deposits below this limit cannot be processed.</p>";
                crypto_address_link += '<div class="crypto-qr-code" style="display:flex; flex-direction:row;"><a href="'+qr_prefix+':'+ address +'" target="_blank" ><img src="https://chart.googleapis.com/chart?chs=150x150&amp;cht=qr&amp;chl='+qr_prefix+':'+ address +'&amp;choe=UTF-8"></a>';
                crypto_address_link += '<div class="crypto-description mt-4" style="display:flex; flex-direction:column;"><span>Copy this '+ currency_rate['rate_to_currency'] +' address or follow the link to open your wallet application</span><div class="copy-part" style="display:flex; flex-direction:row"><span >'+address+'</span><a href="javascript:fn_copy_address(`address`)" style="padding-left:10px; font-size: 18px">COPY</a></div><span>Currency Rate : '+currency_rate['rate_from'] + currency_rate['rate_from_currency']+' ~ '+currency_rate['rate_to'] +currency_rate['rate_to_currency']+'</span><span>minimum deposit :'+ minimum_amount +'</span></div>';
                crypto_address_link += '</div><p>Please be careful to send only '+ currency_rate['rate_to_currency'] +' to this address. Sending any other currency may result in a deposit delay or funds being lost. The deposits made less than the minimal limits may result in the loss of the funds.</p>';
                crypto_address_link += '<p>Deposit can take up to 10 minutes.</p>';
                $('.crypto-address-details').append(crypto_address_link);
            }
            option_for_crypto(false);
        },
        error:(error)=>{
            console.log(error);
            option_for_crypto(false);
        }
    });
}
function open_signup_modal () {

    lepopup_close("popup-welcome-bonus*popup-welcome-bonus-mobile");

    /* avoid user have different account to get bonus with fingerprintjs */
    if(!localStorage.getItem("visitorId") || localStorage.getItem("visitorId") == null || localStorage.getItem("visitorId") == ""){
        initFingerprintJS();
    }
    $("#visitorId").val(localStorage.getItem("visitorId"));
    /* --- */
    if($('#signup-modal').css('display') == "inline-block") {
        $('#signup-modal').css('display', 'none');
        showSignup();
    }else {
        showSignup();
    }

}

function close_popup () {
    lepopup_close("popup-welcome-bonus*popup-welcome-bonus-mobile");
}
/* --- */

function uploadImg(type){
    $("#imageType").val(type);
    $("#imageFile").click();
    $('#imageFile').change(handleFileSelect);
}
function handleFileSelect (e) {
    var files = e.target.files;
    if (files.length < 1) {
        alert('select a file...');
        return;
    }
    var file = files[0];
    var reader = new FileReader();
    reader.onload = onFileLoaded;
    reader.readAsDataURL(file);
}

function onFileLoaded (e) {
    var match = /^data:(.*);base64,(.*)$/.exec(e.target.result);
    if (match == null) {
        return;
    }
    var type = $("#imageType").val();
    var formData = new FormData();
    formData.append("type", type);
    formData.append("file", $("#imageFile")[0].files[0]);
    formData.append("user_id", $("#imageFile")[0].files[0]);
    $.ajax({
        url: '/profile/verify/submit',
        type: 'POST',
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success:(data)=>{
            imageUploadFinish(type);
        },
        error:()=>{
        },
        complete:()=>{
        }
    });
}
function imageUploadFinish(type) {
    var imgTag;
    var backTag;
    if (type == 'id'){
        imgTag = $("#addIdImage");
        backTag = $("#idImage");
    }
    else if (type == 'address'){
        imgTag = $("#addAddressImage");
        backTag = $("#addressImage");
    }
    if (imgTag){
        imgTag.attr('src', '/frontend/Page/image/account-verification-submit.png');
        imgTag[0].onclick = function() {
            return false;
        }
    }
    if (backTag){
        backTag[0].onclick = function() {
            return false;
        }
    }
}

fn_search_game=()=>{
    var page_game = parseInt($("#search_game_load_count").val()) + 1;
    if($("#search_game_keyword").val() == "")
        return;
    $.ajax({
        url:"/search",
        type:"POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            pagegame:page_game,
            keyword: $("#search_game_keyword").val()
        },
        dataType:"JSON",
        success:(data)=>{
            var games = data.games;
            var apigames = data.apigames;
            var section_game = "";

            if( $("#auth_status").val() != "1" ){
                if(games.length > 0){
                    for(var i=0;i<games.length;i++) {
                        section_game+=  '<div class="game-item">\
                                                    <img src="/frontend/Default/ico/'+games[i].name+'.jpg" data-original="/frontend/Default/ico/'+games[i].name+'.jpg" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="javascript:fn_playreal_auth()">Play For Real</a>\
                                                        <a href="/game/'+games[i].name+'/prego">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                    }
                }
                if(Object.keys(apigames).length > 0){
                    for( val in apigames) {
                        if(apigames[val].play_for_fun_supported == 1){
                            section_game+=  '<div class="game-item api-game-item">\
												<img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
												<div class="game-overlay">\
													<a href="javascript:fn_playreal_auth()">Play For Real</a>\
													<a href="javascript:fn_playreal_auth()">Play For Fun</a>\
												</div>\
											</div>';
                        }else{
                            section_game+=  '<div class="game-item api-game-item">\
											<img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
											<div class="game-overlay">\
												<a href="javascript:fn_playreal_auth()">Play For Real</a>\
											</div>\
										</div>';
                        }
                    }
                }
            }else {
                if(games.length > 0){
                    for(var i=0;i<games.length;i++) {
                        section_game+=  '<div class="game-item">\
                                                    <img src="/frontend/Default/ico/'+games[i].name+'.jpg" data-original="/frontend/Default/ico/'+games[i].name+'.jpg" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="/game/'+games[i].name+'/realgo">Play For Real</a>\
                                                        <a href="/game/'+games[i].name+'/prego">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                    }
                }
                if(Object.keys(apigames).length > 0){
                    for( val in apigames) {
                        if(apigames[val].play_for_fun_supported == 1){
                            section_game+=  '<div class="game-item api-game-item">\
												<img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
												<div class="game-overlay">\
													<a href="/apigame/'+apigames[val].game_id+'/api_go">Play For Real</a>\
													<a href="/apigame/'+apigames[val].game_id+'/demo_go">Play For Fun</a>\
												</div>\
											</div>';
                        }else{
                            section_game+=  '<div class="game-item api-game-item">\
											<img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
											<div class="game-overlay">\
												<a href="/apigame/'+apigames[val].game_id+'/api_go">Play For Real</a>\
											</div>\
										</div>';
                        }
                    }
                }
            }
            $(".search_game_content").html(section_game);
            // $(".search_game_content").append(section_game);
        },
        error:(error)=>{
            console.log(error);
        }
    });
}

function onCountryChange()
{
    if($("#country").find("option:selected").text() == "Canada")
    {
        $("#province-row").show();
        $("#state-row").hide();
        $("#other-state-row").hide();
    }
    else if($("#country").find("option:selected").text() == "United States of America")
    {
        $("#province-row").hide();
        $("#state-row").show();
        $("#other-state-row").hide();
    }
    else
    {
        $("#province-row").hide();
        $("#state-row").hide();
        $("#other-state-row").show();
    }
}
showSignin=()=>{
    $("#signin-modal input[type='text']").val("");
    $("#signin-modal").modal({
        fadeDuration: 300,
        escapeClose: false,
        clickClose: false
    });
}
showSignup=()=>{
    $("#signup-modal input[type='text']").val("");
    $('#signup-modal').modal({fadeDuration: 300, escapeClose: false, clickClose: false});
}
showForgot=()=>{
    $("#forgotpassword-modal input[type='text']").val("");
    $("#forgotpassword-modal").modal({
        fadeDuration: 300,
        escapeClose: false,
        clickClose: false
    });
}
/* --- */
// This sample uses the Places Autocomplete widget to:
// 1. Help the user select a place
// 2. Retrieve the address components associated with that place
// 3. Populate the form fields with those address components.
// This sample requires the Places library, Maps JavaScript API.
// Include the libraries=places parameter when you first load the API.
// For example: <script
// src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
let autocomplete;
let address1Field;
let address2Field;
let postalField;

function initAutocomplete() {
    address1Field = document.querySelector("#user_address");
    address2Field = document.querySelector("#user-second-address");
    postalField = document.querySelector("#user_address_postcode");
    // Create the autocomplete object, restricting the search predictions to
    // addresses in the US and Canada.
    autocomplete = new google.maps.places.Autocomplete(address1Field, {
        componentRestrictions: { country: ["us", "ca"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    address1Field.focus();
    // When the user selects an address from the drop-down, populate the
    // address fields in the form.
    autocomplete.addListener("place_changed", fillInAddress);
}

function play_freespin100(){
    lepopup_close("popup-freespin-100*popup-freespin-100-mobile");
    location.href = '/game/BookOfTombGM/realgo';
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    const place = autocomplete.getPlace();
    let address1 = "";
    let postcode = "";

    // Get each component of the address from the place details,
    // and then fill-in the corresponding field on the form.
    // place.address_components are google.maps.GeocoderAddressComponent objects
    // which are documented at http://goo.gle/3l5i5Mr
    for (const component of place.address_components) {
        const componentType = component.types[0];

        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }

            case "route": {
                address1 += component.short_name;
                break;
            }

            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
            }

            case "postal_code_suffix": {
                postcode = `${postcode}-${component.long_name}`;
                break;
            }
            case "locality":
                document.querySelector("#user_address_city").value = component.long_name;
                break;

            case "administrative_area_level_1": {
                document.querySelector("#user_address_state").value = component.short_name;
                break;
            }
            case "country":
                document.querySelector("#user_address_country").value = component.long_name;
                break;
        }
    }
    address1Field.value = address1 + " " + document.querySelector("#user_address_city").value + ", " + document.querySelector("#user_address_state").value + " " + postcode + ", " + document.querySelector("#user_address_country").value;
    postalField.value = postcode;
    // After filling the form with address components from the Autocomplete
    // prediction, set cursor focus on the second address line to encourage
    // entry of subpremise information such as apartment, unit, or floor number.
}
