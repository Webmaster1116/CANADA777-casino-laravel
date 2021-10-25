/* copyright(c) WEBsiting.co.kr */

var slider;
$(document).ready(function(){
	
	slider = $('.mainVisualImage ul').bxSlider({
		onSliderLoad: function(){
			$(".mvTit01").addClass('on');
			$(".mvTit02").addClass('on');
			$(".mvLink").addClass('on');
		},
		onSlideBefore: function(){
			$(".mvTit01").removeClass('on');
			$(".mvTit02").removeClass('on');
			$(".mvLink").removeClass('on');
		},
		onSlideAfter: function(){
			$(".mvTit01").addClass('on');
			$(".mvTit02").addClass('on');
			$(".mvLink").addClass('on');
		},
		auto: true,
		useCSS: false,
		// adaptiveHeight: true,
		randomStart: false,
		autoControls: false,
		stopAutoOnClick: true,
		touchEnabled: false,
		autoDelay: '100',
		pause: 6000,
		pager: true
	});
});