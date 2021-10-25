/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

;(function($) {
	
	if(!$) {
		
		console.log('core jQuery library not loading correctly');
		return;
		
	}
	
	if(typeof punchgs === 'undefined') {
		
		console.log('punchgs not available');
		return;
		
	}
	
	var win, 
		timer,
		display,
		scrollable;
		
	
	function openPanel() {
			
		clearTimeout(timer);
		
		punchgs.TweenLite.to(jQuery('.rs-sbs-slideout-wrapper').not(display), 0.4, {xPercent: '+100%', autoAlpha: 0, display: 'none', overwrite: 'auto', ease: punchgs.Power3.easeInOut});
		punchgs.TweenLite.to(display, 0.4, {xPercent: '0%', autoAlpha: 1, display: 'block', overwrite: 'auto', ease: punchgs.Power3.easeOut});
		
		scrollable.css('max-height', win.height() - 300);
		timer = setTimeout(updateScroll, 400);
		
	}
	
	function closePanel() {
		
		punchgs.TweenLite.to(display, 0.4, {xPercent: '+100%', autoAlpha: 0, display: 'none', overwrite: 'auto', ease: punchgs.Power3.easeInOut});
		
	}
	
	function onResize() {
		
		scrollable.css('max-height', win.height() - 300).perfectScrollbar('update');
		
	}
	
	function updateScroll() {
		
		scrollable.perfectScrollbar('update');
			
	}
	
	
	$(function() {
		
		display    = jQuery('#rev_addon_bubblemorph_settings_slideout');
		scrollable = display.children('.rs-sbs-slideout-inner');
		win        = $(window).on('resize', onResize);
		
		$('body').on(
		
			'click', 
			'#rs-dash-addons-slide-out-trigger_revslider-bubblemorph-addon', 
			openPanel
			
		).on('click', '#rev_addon_bubblemorph_settings_slideout .rs-sbs-close', closePanel);
		
		punchgs.TweenLite.set(display, {xPercent: '+100%', autoAlpha: 0, display: 'none'});
		scrollable.perfectScrollbar({wheelPropagation: true, suppressScrollX: true});

	});
	

})(typeof jQuery !== 'undefined' ? jQuery : false);




