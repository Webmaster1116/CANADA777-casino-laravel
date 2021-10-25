'use Strict';
var popupInterval = null;
$(document).ready(function(){

	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'UA-185160427-1');
	
	if(!localStorage.getItem("visitorId") || localStorage.getItem("visitorId") == null || localStorage.getItem("visitorId") == ""){
		initFingerprintJS();
	}
	$.ajax({
		url: '/check_freemodal',
		type: 'GET',
		// headers: {
		// 	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		// },
		dataType:"JSON",
		data:{
			visiterId : localStorage.getItem("visitorId")
		},
		success:(data)=>{
			if (data.status == 1) {
				console.log("GameFun --> popup-play-fun")
				popupInterval = setInterval(() => {
					lepopup_popup_open('popup-user-fun');
				}, 120000);
			}
		},
		error:(data)=>{
		},
		complete:()=>{
		}
	});
});
/* avoid user have different account to get bonus with fingerprintjs */
function initFingerprintJS() {
	FingerprintJS.load({token: fingerprintConfig.browser_token})
	.then(fp => fp.get())
	.then(result => {
		if(!result.visitorId) {
			alert("You can not use this website!!!");
		}
		localStorage.setItem("visitorId", result.visitorId);
	})
	.catch(error => console.log(error));
}
/* --- */

function contiue_user_fun () {
	var email = $("div.lepopup-input input").val();
	if(email == ""){
		alert("Please Enter Email");
	}else{
		$.ajax({
			url: '/check_email',
			type: 'GET',
			// headers: {
			// 	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			// },
			dataType:"JSON",
			data:{
				email : email,
				visiterId : localStorage.getItem("visitorId")
			},
			success:(data)=>{
				if (data.status == 1) {
					alert("Your Email already exist.")
				}else{
					lepopup_close("popup-user-fun");
					if(popupInterval != null){
						clearInterval(popupInterval);
					}
				}
			},
			error:(data)=>{
			},
			complete:()=>{
			}
		});
	}
}

function close_popup () {
	lepopup_close("popup-user-fun");
}

