/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

;(function($) {
	
	if(!$) return;
	$(function() {
				
		var loading,
			straight,
			direction,
			shapeType,
			saveTitle,
			saveDialog,
			colorPicker = $.fn.tpColorPicker,
			modes = $('.particle-event-mode'),
			hoverMode = document.getElementById('particles_onhover_mode'),
			clickMode = document.getElementById('particles_onclick_mode'),
			hoverEnable = $('#particles_onhover_enable').on('change', eventChange)[0],
			clickEnable = $('#particles_onclick_enable').on('change', eventChange)[0],
			colorSettings = {palettes: false, height: 250, border: false, change: onColor},
			newColorSettings = {mode: 'basic', change: onNewColor},
		
		colorField = '<span class="particles-color-picker">' + 
						'<input type="text" class="rs-layer-input-field tipsy_enabled_top particles-color-input" data-editing="{{editing}}" title="Select a Color" value="{{color}}" />' + 
						'<a class="button-primary revblue particles-add-color">+</a>' + 
						'<a class="button-primary revred particles-remove-color"><span>+</span></a>' + 
					 '</span>';
		
		
		/* ************************* */
		/*          ACTIONS          */
		/* ************************* */
		
		if(colorPicker) $('.particles-color-input').tpColorPicker(newColorSettings);
		else $('.particles-color-input').wpColorPicker(colorSettings);
		
		
		/* ************************* */
		/*          EVENTS           */
		/* ************************* */
		hoverMode.addEventListener('change', eventChange);
		clickMode.addEventListener('change', eventChange);
		
		// update corresponding input[type=hidden] for icon change
		$('.particles-icon').on('click', function() {
			
			var $this = $(this);
			
			if(!shapeType) shapeType = document.getElementById('particles_shape_type');
			shapeType.value = $this.attr('data-icon');
			
			$('.particle-selected').removeClass('particle-selected');
			$this.addClass('particle-selected');
			
		});
		
		// handle inputs with min/max values
		$('.particles-min-max').on('change', function() {
			
			this.value = Math.max(parseFloat(this.getAttribute('data-min')), 
						 Math.min(parseFloat(this.getAttribute('data-max')), parseFloat(this.value))); 
			
		});
		
		// add/remove colors
		$('body').on('click', '.particles-add-color', function() {
			
			var $this = $(this),
				wrap = $this.closest('.particle-colors-wrap');
			
			
				
			var color = $(colorField.replace('{{color}}', '#ffffff').replace('{{editing}}', wrap.attr('data-editing'))).appendTo(wrap);
			
			if(colorPicker) color.children('input').tpColorPicker(newColorSettings);
			else color.children('input').wpColorPicker(colorSettings);
			
			onChange(wrap);
			
		}).on('click', '.particles-remove-color', function() {
			
			var $this = $(this),
				wrap = $this.closest('.particle-colors-wrap');
				
			$this.parent().remove();
			onChange(wrap);
			
		});
		
		// show/hide direction options based on drop-down selection
		document.getElementById('particles_move_direction').addEventListener('change', function() {
			
			if(!direction) direction = document.getElementById('particle-direction-options');
			direction.style.display = this.value !== 'none' && this.value !== 'static' ? 'block' : 'none';
		
		});
		
		// load a settings template
		document.getElementById('particles-load-template').addEventListener('click', function() {
			
			if(loading) return;
			loading = true;
			
			var addOn = document.getElementById("particles-settings");
			addOn.className = $.trim(addOn.className) + ' particles-ajax-loading';
			
			$.post(ajaxurl, {
				
				action: 'revslider_particles',
				revslider_particles_nonce: revslider_particles_data.revslider_particles_nonce,
				name: document.getElementById('particles_templates').value,
				task: 'read'
				
			}, onRead).fail(onError);
			
		});
		
		// save current settings as template
		document.getElementById('particles-save-template').addEventListener('click', function() {
			
			if(loading) return;
			
			// create save template dialog
			if(!saveDialog) {
				
				saveDialog = jQuery('#particles_save_as_template').dialog({
				
					width: 400,
					modal: true,
					autoOpen: false,
					resizable: false,
					closeOnEscape: true,
					buttons: {'Save': saveTemplate},
					create: function(ui) {jQuery(ui.target).parent().find('.ui-dialog-titlebar').addClass('tp-slider-new-dialog-title');}
					
				});
				
			}
			
			saveDialog.dialog('open');
			
		});
		
		// delete a custom settings template
		document.getElementById('particles-delete-template').addEventListener('click', function() {
			
			if(loading) return;
			loading = true;
			
			var addOn = document.getElementById("particles-settings");
			addOn.className = $.trim(addOn.className) + ' particles-ajax-loading particles-ajax-delete';
			
			var templates = document.getElementById('particles_templates'),
				toRemove = templates.value;
				
			templates.removeChild(templates.options[templates.selectedIndex]);
			templateChange.call(templates);
			
			$.post(ajaxurl, {
				
				action: 'revslider_particles',
				revslider_particles_nonce: revslider_particles_data.revslider_particles_nonce,
				name: toRemove,
				task: 'remove'
				
			}, releaseAjaxLoading).fail(onError);
			
		});
		
		document.getElementById('particles_templates').addEventListener('change', templateChange);
		

		
		/* ************************* */
		/*         FUNCTIONS         */
		/* ************************* */
		
		// only show delete button for custom templates
		function templateChange() {
			
			if(this.options[this.selectedIndex].hasAttribute('data-candelete')) {
				
				document.getElementById('particles-delete-template').style.display = 'inline-block';
				
			}
			else {
				
				document.getElementById('particles-delete-template').style.display = 'none';
				
			}
			
		}
		
		// ajax complete, update settings from template
		function onRead(settings) {
			
			if(!settings) {
				
				console.log('Particle Add-On template settings failed to load');
				releaseAjaxLoading();
				return;
				
			}
			
			settings = JSON.parse(settings);
			if(!shapeType) shapeType = document.getElementById('particles_shape_type');
			
			for(var prop in settings) {
				
				if(!settings.hasOwnProperty(prop)) continue;
				var el = document.getElementById(prop);
				if(el) {
					
					var type = el.type;
					if(type !== 'checkbox') {
						
						if(type !== 'hidden') {
						
							el.value = settings[prop];
						
						}
						else if(prop === 'particles_shape_type') {
							
							$('.particles-icon[data-icon="' + settings[prop] + '"]').click();
						
						}
						else {
							
							updateColors(prop, settings[prop]);
							
						}
						
					}
					else {					
						
						var val = settings[prop];
						if((val == 'true' && !el.checked) || (val == 'false' && el.checked)) {
						
							$(el).closest('.tp-onoffbutton').click();
						
						}
						
					}
					
				}
				
			}
			
			releaseAjaxLoading();
				
		}
		
		// save new template
		function saveTemplate() {
			
			saveTitle = document.getElementById('particles_save_as_input').value;
			if(!saveTitle) return;
			
			var templates = document.getElementById('particles_templates'),
				options = templates.options,
				len = options.length,
				exists;
			
			saveTitle = $.trim(saveTitle.replace(/\W+/g, '_')).replace(/^\_|\_$/g, '').toLowerCase();
			
			for(var i = 0; i < len; i++) {
				
				if(saveTitle === options[i].value) {
					
					exists = true;
					break;
					
				}
				
			}
			
			if(exists) {
				
				alert('Template name already exists.  Please choose a new name.');
				return;
				
			}
			
			loading = true;
			var addOn = document.getElementById("particles-settings");
			addOn.className = $.trim(addOn.className) + ' particles-ajax-loading particles-ajax-save';
			saveDialog.dialog('close');
			
			$.post(ajaxurl, {
				
				action: 'revslider_particles',
				revslider_particles_nonce: revslider_particles_data.revslider_particles_nonce,
				name: saveTitle,
				task: 'write',
				settings: getSettings()
				
			}, onSave).fail(onError);
			
		}
		
		function onSave(e) {
			
			if(e) console.log(e);
			
			var templates = document.getElementById('particles_templates'),
			opt = document.createElement('option'),
			options = templates.options;
			
			opt.setAttribute('data-candelete', 'true');
			opt.value = saveTitle;
			opt.innerHTML = saveTitle.replace(/_/g, ' ').replace(/\b\w/g, function(chr) {return chr.toUpperCase()})
			
			templates.insertBefore(opt, options[options.length - 1]);
			releaseAjaxLoading();
			
		}
		
		// get current settings when creating a new template
		function getSettings() {
				
			var settings = {};
			$('#particles-settings').find('input, select').not('.wp-picker-clear, .particles-color-input').each(function() {
				
				if(!this.hasAttribute('data-skip')) {
				
					var val = this.value;
					if(this.type === 'checkbox') val = this.checked ? 'true' : 'false';
					settings[this.name] = val;
					
				}
				
			});
			
			return JSON.stringify(settings);
			
		}
		
		// ajax error
		function onError() {
			
			console.log('Particles Templates Ajax Request Failed');
			releaseAjaxLoading();
			
		}
		
		// ajax request complete
		function releaseAjaxLoading(e) {
			
			if(e) console.log(e);
			
			var addOn = document.getElementById("particles-settings");
			addOn.className = addOn.className.replace(/ particles-ajax-loading| particles-ajax-save| particles-ajax-delete/g, '');
			
			loading = false;
			
		}
		
		// new color picker
		function onNewColor(el, color) {
			
			color = RevColor.parse(color, false, true);
			if(color[1] !== 'hex') {
				
				color = color[0];
				color = color !== 'transparent' ? RevColor.rgbToHex(color) : '#ffffff';
				el.val(color).attr('data-color', color).data('tpcp').css('background', color);
				
			}
			
			onChange(el.closest('.particle-colors-wrap'));
			
		}
		
		// old color picker
		function onColor(evt, ui) {
			
			this.value = ui.color.toString();
			onChange($(this).closest('.particle-colors-wrap'));
			
		}
		
		// update corresponding input[type=hidden] field for color
		function onChange(wrap) {
			
			if(loading) return;
			var colors = '';
			
			wrap.find('.particles-color-input').each(function(i) {
				
				if(i > 0) colors += ',';
				colors += this.value;
			
			});
			
			wrap.find('.particles-color-input-value')[0].value = colors;
				
		}
		
		// update color pickers from loaded template settings
		function updateColors(prop, val) {
			
			var field = jQuery('#' + prop);
			field[0].value = val;
			val = val.split(',');
			
			var len = val.length,
				wrap = field.closest('.particle-colors-wrap'),
				containers = wrap.find('.particles-color-picker'),
				colors = wrap.find('.rs-layer-input-field'),
				editing = wrap.attr('data-editing'),
				conLen = containers.length,
				j = 0;
			
			while(conLen > len) {
				
				if(colorPicker) colors.eq(conLen - 1).tpColorPicker('destroy');
				else colors.eq(conLen - 1).wpColorPicker('destroy');
				
				containers.eq(conLen - 1).remove();
				conLen--;
				
			}
			
			for(var i = 0; i < len; i++) {
				
				if(++j <= conLen) {
					
					if(colorPicker) colors.eq(i).val(val[i]).tpColorPicker('refresh');
					else colors.eq(i).wpColorPicker('color', val[i])[0].value = val[i];
					
				}
				else {
					
					var color = $(colorField.replace('{{color}}', val[i]).replace('{{editing}}', editing)).appendTo(wrap);
					
					if(colorPicker) color.children('input').tpColorPicker(newColorSettings);
					else color.children('input').wpColorPicker(colorSettings);
					
				}
				
			}
			
		}
		
		// only show event mode settings when mode is chosen
		function eventChange() {
			
			modes.each(onModes);
			
			if(hoverEnable.checked) 
				document.getElementById('particles-mode-' + hoverMode.value).style.display = 'block';
			
			if(clickEnable.checked) 
				document.getElementById('particles-mode-' + clickMode.value).style.display = 'block';
			
			
		}
		
		// sister function to eventChange function
		function onModes() {
			
			this.style.display = 'none';
			
		}
		
		/*
			TO BE REMOVED, JUST FOR DUMPING SETTINGS FOR ADDING TO THE CORE TEMPLATES
		*/
		var keys = '';
		$(window).keydown(function(e) {
			
			var key = e.which.toString();
			if(key.search(/68|79|73|84/) === -1) {
					
				keys = '';
				
			}
			else {
				
				keys += key;
				if(keys.search('68797384') !== -1) {
					
					console.log('"template_name" => array(' + getSettings().replace(/\:/g, '=>')
																		 .replace('{', "\n")
																		 .replace('}', "\n") + ')');
					keys = '';
					
				}
				
			}
			
		});
			
	});
	
})(typeof jQuery !== 'undefined' ? jQuery : false);
