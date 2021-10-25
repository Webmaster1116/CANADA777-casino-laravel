/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
 * @version   1.0.0
 */

;(function() {

	window.RsRevealerAddOn = function($, slider, spinner) {
		
		if(!$) return;
		
		var opt = slider[0].opt,
			scriptsLoaded,
			delayReady,
			options;
		
		slider.on('scriptsloaded', function() {
			
			scriptsLoaded = true;
			if(delayReady) slider.revstart();
			
		});
		
		if(!window.hasOwnProperty('RsAddonRevealerCustom')) {

			options = opt.revealer;
			
		}
		else {

			options = window.RsAddonRevealerCustom;
			var hash = document.URL.split('?');
			if(hash.length === 2 && window.RsAddonRevealerCustom.hasOwnProperty(hash[1]) && hash[1] !== 'itm_1') {
				
				options = window.RsAddonRevealerCustom[hash[1]];
				if(options.hasOwnProperty('spinner')) spinner = options.spinnerHTML;
				
			}
			else {
				
				options = opt.revealer;
				
			}
			
		}
		
		var direction = options.direction,
			delay = options.delay,
			preloader,
			finished,
			timer;
			
		if(options.spinner !== 'default') {
		
			if(opt.spinner !== 'off') {
				
				window.requestAnimationFrame(checkSpinner);
				
			}
			else {
				
				opt.spinner = 'on';
				setSpinner();
				
			}
		
		}
		
		if(direction === 'none') {
				
			slider.one('revolution.slide.onloaded', function() {
				
				if(preloader && preloader.length) opt.loader = preloader;
				
			});	
			
			return;
			
		}
		
		slider.addClass('rs_addon_reveal').find('li').first().attr('fstransition', 'notransition').data('fstransition', 'notransition');
		
		var wrap = $('<div class="rs_addon_revealer" />'),
			opens = direction.search('open') !== -1,
			corner = direction.search('corner') !== -1,
			ease = options.easing.split('.'),
			special = opt.sliderLayout === 'fullwidth' && direction.search('skew') !== -1,
			optionsOne = {ease: punchgs[ease[0]][ease[1]], onComplete: onFinish},
			optionsTwo = {ease: punchgs[ease[0]][ease[1]]},
			calcNeeded = /skew|shrink/.test(direction),
			duration = options.duration,
			color = options.color,
			callback = onReveal,
			sideOne = '',
			sideTwo = '',
			delayStart,
			overlay,
			hasClip,
			abort,
			tw;
		
		if(isNaN(duration)) duration = '300';
		duration = parseInt(duration, 10) * 0.001;
		
		if(isNaN(delay)) delay = 0;
		delay = parseInt(delay, 10) * 0.001;
		
		if(!corner) {
			sideOne = '<div style="background: ' + color + '; ';
			if(opens) sideTwo = '<div style="background: ' + color + '; ';
		}
		else {
			sideOne = '<svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="none">';
		}
		
		if(!calcNeeded) {
			
			if(delay) {

				delayStart = true;
				opt.waitForInit = true;
				slider.height('100%');
				
			}
			onReady();
			
		} 
		else {
			
			window.addEventListener('resize', onResize);
			
			if(!special) slider.one('revolution.slide.onloaded', onReady);	
			else slider.addClass('rs_addon_revealer_special').one('revolution.slide.onafterswap', onReady);			
			
		}
		
		function onReady() {
			
			if(abort) return;
			switch(direction) {
				
				case 'open_horizontal':
				
					sideOne += 'width: 50%; height: 100%; top: 0; left: 0';
					sideTwo += 'width: 50%; height: 100%; top: 0; left: 50%';
					
					optionsOne.width = '0%';
					optionsTwo.left = '100%';
					
				break;
				
				case 'open_vertical':
				
					sideOne += 'width: 100%; height: 50%; top: 0; left: 0';
					sideTwo += 'width: 100%; height: 50%; top: 50%; left: 0';
					
					optionsOne.height = '0%';
					optionsTwo.top = '100%';
					
				break;
				
				case 'split_left_corner':

					sideOne += '<polygon class="rs_addon_point1" points="0,0 500,0 500,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />' + 
							   '<polygon class="rs_addon_point2" points="0,0 0,500 500,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />';
							   
					isCorner = true;
					callback = onSvg;
					optionsOne.x = 500;
					optionsTwo.x = -500;
				
				break;
				
				case 'split_right_corner':
					
					sideOne += '<polygon class="rs_addon_point1" points="0,0 500,0 0,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />' + 
							   '<polygon class="rs_addon_point2" points="500,0 500,500 0,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />';
							   
					isCorner = true;
					callback = onSvg;
					optionsOne.x = -500;
					optionsTwo.x = 500;
					
				break;
				
				case 'shrink_circle':
					
					var size = (Math.max(slider.width(), slider.height())) * 2;
					sideOne += 'width: ' + size + 'px; height: ' + size + 'px; top: 50%; left: 50%; transform: translate(-50%, -50%); border-radius: 50%';
					
					optionsOne.width = '0';
					optionsOne.height = '0';
					
				break;
				
				case 'expand_circle':
					
					hasClip = true;
					callback = animateClip;
					slider.css('clip-path', 'circle(0% at 50% 50%)');
				
				break;
				
				case 'left_to_right':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.left = '100%';
				
				break;
				
				case 'right_to_left':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.width = '0%';
				
				break;
				
				case 'top_to_bottom':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.top = '100%';
				
				break;
				
				case 'bottom_to_top':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.height = '0%';
				
				break;
				
				case 'tlbr_skew':
					
					var skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; top: 0%; left: -100%; transform: skew(-' + skew + 'rad)';
					optionsOne.left = '100%';
					
				break;
				
				case 'trbl_skew':
				
					var skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; top: 0%; right: -100%; transform: skew(' + skew + 'rad)';
					optionsOne.right = '100%';
				
				break;
				
				case 'bltr_skew':
				
					var skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; bottom: -100%; left: 0%; transform: skew(' + skew + 'rad)';
					optionsOne.bottom = '100%';
				
				break;
				
				case 'brtl_skew':
				
					var skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; bottom: -100%; right: 0; transform: skew(-' + skew + 'rad)';
					optionsOne.bottom = '100%';
				
				break;
				
			}
			
			if(options.overlay_enabled) overlay = $('<div class="rsaddon-revealer-overlay" style="background: ' + options.overlay_color + '" />').appendTo(wrap);
			
			sideOne += !corner ? '" />' : '</svg>';
			sideOne = $(sideOne).appendTo(wrap);
			
			if(hasClip && !slider.css('clip-path')) return;
			if(opens) sideTwo = $(sideTwo + '" />').appendTo(wrap);
			
			wrap.appendTo(slider);
			if(!special) slider.one('revolution.slide.onafterswap', onStart);
			
			if(preloader && preloader.length) opt.loader = preloader;
			if(delayStart) {
				
				timer = setTimeout(function() {
					
					delayReady = true;
					if(scriptsLoaded) slider.revstart();
					
				}, delay);
				
			}
			else if(special) {
				
				slider.removeClass('rs_addon_revealer_special');
				onStart();
				
			}
			
		}
		
		function onStart() {

			
			if(abort) return;
			if(opt.stopLoop === 'off') slider.revpause();
			if(!preloader || !preloader.length) preloader = slider.find('.tp-loader');
			if(preloader.length) {
				
				opt.loader = preloader;
				
				var obj = {opacity: 0, ease: punchgs.Power3.easeOut, onComplete: callback};
				if(calcNeeded && delay) obj.delay = delay;
				
				punchgs.TweenLite.to(preloader, 0.3, obj);
				
			}
			else {
				
				if(calcNeeded && delay) timer = setTimeout(callback, delay);
				else callback();
				
			}
			
		}
			
		function animateClip() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			
			optionsOne.point = 100;
				
			var start = {point: 0};
			tw = new punchgs.TweenLite(start, duration, optionsOne);
			
			tw.eventCallback('onUpdate', function() {	
				slider.css('clip-path', 'circle(' + start.point + '% at 50% 50%)');
			});
			
		}
		
		function onSvg() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			
			punchgs.TweenLite.to(wrap.find('.rs_addon_point1'), duration, optionsOne);
			punchgs.TweenLite.to(wrap.find('.rs_addon_point2'), duration, optionsTwo);
			
		}
		
		function onReveal() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			
			punchgs.TweenLite.to(sideOne, duration, optionsOne);
			if(opens) punchgs.TweenLite.to(sideTwo, duration, optionsTwo);
			
		}
		
		function animateOverlay() {
			
			var dur = options.overlay_duration,
				easing = options.overlay_easing.split('.'),
				del = options.overlay_delay;
			
			if(isNaN(del)) del = 0;
			del = parseInt(del, 10) * 0.001;
			
			if(isNaN(dur)) dur = '300';
			dur = parseInt(dur, 10) * 0.001;
			
			punchgs.TweenLite.to(overlay, dur, {opacity: 0, ease: punchgs[easing[0]][easing[1]], delay: del, onComplete: onFinish});
			
		}
		
		function complete() {
			
			slider.removeClass('rs_addon_reveal rs_addon_revealer_special');
			slider.find('.tp-loader').css('opacity', 1);
			
			if(wrap) wrap.remove();
			if(opt.stopLoop === 'off') slider.revresume();
			
			opt = null;
			slider = null;
			
		}
		
		function onFinish() {
			
			if(!overlay || finished) complete();
			finished = true;
			
		}
		
		function onResize() {
			
			window.removeEventListener('resize', onResize);
			clearTimeout(timer);
			abort = true;
			
			slider.off('revolution.slide.onloaded', onReady).off('revolution.slide.onafterswap', onStart);
			punchgs.TweenLite.killTweensOf($('.rs_addon_revealer').find('*'));
			
			if(tw) {
				tw.eventCallback('onUpdate', null);
				tw.kill();
				tw = null;
			}
			
			complete();
			
		}
		
		function checkSpinner() {
			
			preloader = slider.find('.tp-loader');
			if(preloader.length) setSpinner(preloader);
			else window.requestAnimationFrame(checkSpinner);
			
		}
		
		function setSpinner(preloader) {
			
			if(preloader && preloader.length) preloader[0].className = 'tp-loader';
			else preloader = $('<div class="tp-loader" />').appendTo(slider);
			
			preloader.html(spinner.replace(/{{color}}/g, options.spinnerColor));
			opt.loader = preloader;
			
		}
		
	};
	
})();


