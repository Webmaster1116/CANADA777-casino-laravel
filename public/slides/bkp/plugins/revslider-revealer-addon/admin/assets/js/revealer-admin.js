/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

;(function($) {
	
	$(function() {
		
		var spinnerColor,
			spinners = JSON.parse(rsAddOnPreloaders);
		
		if(jQuery.fn.tpColorPicker) {
			
			jQuery('#revealer_color').tpColorPicker({
				wrapClasses: 'revealer-cp', 
				init: function() {jQuery('.revealer-cp').css({position: 'relative', top: 8})},
			});
			
			jQuery('#revealer_overlay_color').tpColorPicker({
				wrapClasses: 'revealer-cp', 
				init: function() {jQuery('.revealer-cp').css({position: 'relative', top: 8})},
			});
			
			spinnerColor = jQuery('#revealer_spinner_color').tpColorPicker({
				
				wrapClasses: 'revealer-cp', 
				init: function() {jQuery('.revealer-cp').css({position: 'relative', top: 8})},
				cancel: function() {updateSpinnerColor(spinnerColor.attr('data-color'));},
				change: onColor,
				onEdit: onColor
				
			});
			
		}
		else {
			
			jQuery('.revealer-color-picker').wpColorPicker({palettes: false, height: 250, border: false, change: function(evt, ui) {
				this.value = ui.color.toString();
				if(this.id === 'revealer_spinner_color') updateSpinnerColor(this.value);
			}});
			
		}
		
		jQuery('#revealer_direction').on('change', function() {
			
			var val = this.value;
			if(val !== 'none') {
				
				var display = val !== 'expand_circle' ? 'block' : 'none',
					mode = val.search('corner') === -1 ? 'full' : 'single';
				
				document.getElementById('revealer_color').setAttribute('data-mode', mode);
				document.getElementById('revealer-color-wrap').style.display = display;
				document.getElementById('revealer-opening-settings').style.display = 'block';
				
			}
			else {
				
				document.getElementById('revealer-opening-settings').style.display = 'none';
				
			}
			
		}).change();
		
		function onColor(el, color) {
			
			updateSpinnerColor(color);
			
		}
		
		function updateSpinnerColor(color) {
			
			var val = document.getElementById('revealer_spinner').value;
			
			if(val === 'default') return;
			if(val == '2') color = RevColor.processRgba(color).replace(/rgb/g, 'rgba').replace(/\)/g, ',');
			
			document.getElementById('revealer_spinner_preview').innerHTML = spinners[val].replace(/{{color}}/g, color);
		
		}
		
		function updateSpinner(val) {
			
			if(val !== 'default') {
				
				document.getElementById('revealer-spinner-wrap').style.display = 'block';
				updateSpinnerColor(document.getElementById('revealer_spinner_color').value);

			}
			else {
				
				document.getElementById('revealer-spinner-wrap').style.display = 'none';
				
			}
			
		}
		
		jQuery('#revealer_spinner').on('change', function() {

			updateSpinner(this.value);
			
		}).change();
		
	});

})(typeof jQuery !== 'undefined' ? jQuery : false);




