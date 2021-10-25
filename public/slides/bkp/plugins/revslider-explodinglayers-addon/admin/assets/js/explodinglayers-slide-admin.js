/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

;(function($) {

	if(!$) {

		console.log('core jQuery library not loading correctly');
		return;

	}

	var title = 'explodinglayers',
		slug = title + '_',
		layerDefaults,
		curContainer,
		dialogTimer,
		suppress,
		options,
		bodies,
		pauser,
		timer,
		plug;

	var selectionIn,
		durationIn,
		hideableIn,
		settingsIn,
		activateIn,
		shapeIn,
		animeIn;

	var selectionOut,
		durationOut,
		hideableOut,
		settingsOut,
		activateOut,
		shapeOut,
		animeOut;

	function read(obj) {

		if(!obj) return;
		clearTimeout(changeTimer);

		var settings = obj[title] || $.extend({}, layerDefaults),
			newValue,
			index,
			value,
			el;

		var masterIndex = $('.rs-slide-ds-desktop.selected').length ? 0 :
						  $('.rs-slide-ds-notebook.selected').length ? 1 :
						  $('.rs-slide-ds-tablet.selected').length ? 2 :
						  $('.rs-slide-ds-mobile.selected').length ? 3 : 0;

		for(var prop in settings) {

			if(!settings.hasOwnProperty(prop)) continue;

			value = settings[prop];
			el = plug.find('*[name=' + slug + prop + ']');
			if(!el.length) continue;

			if(!Array.isArray(value)) value = [value, 'inherit', 'inherit', 'inherit'];
			newValue = value[masterIndex];
			index = masterIndex;

			while(newValue === 'inherit') {

				index -= 1;
				if(index > -1) newValue = value[index];
				else newValue = '';

			}

			el.val(newValue).attr('data-currentval', value.join('|'));
			if(el[0].type === 'checkbox') {
				el.prop('checked', newValue == 'on');
				RevSliderSettings.onoffStatus(el);
			}

			if(el.hasClass('explodinglayers-layer-color')) el.tpColorPicker('refresh');
			if(el.attr('id').search('explodinglayers_type') !== -1) {

				var container = el.closest('.explodinglayers-main-settings');
				setShape(container, container.find('span[data-icon="' + newValue + '"]'), newValue);

			}

		}

		changeTimer();
		timer = setTimeout(changeTimer, 100);

	}

	function changeTimer() {

		onChange.call(selectionIn[0]);
		onChange.call(selectionOut[0]);

	}

	function write(obj) {

		var val,
			prop,
			settings = obj[title] || $.extend({}, layerDefaults),
			index = $('.rs-slide-ds-desktop.selected').length ? 0 :
					$('.rs-slide-ds-notebook.selected').length ? 1 :
					$('.rs-slide-ds-tablet.selected').length ? 2 :
					$('.rs-slide-ds-mobile.selected').length ? 3 : 0;

		options.each(function() {

			prop = this.getAttribute('name').split(slug)[1];
			val = this.getAttribute('data-currentval');

			if(val) {
				val = val.split('|');
			}
			else {
				val = settings[prop];
				if(!Array.isArray(val)) val = [val, 'inherit', 'inherit', 'inherit'];
			}

			val[index] = this.type !== 'checkbox' ? this.value : this.checked ? 'on' : 'off';
			settings[prop] = val;
			this.setAttribute('data-currentval', val.join('|'));

		});

		obj[title] = settings;

	}

	function swapSettings(anime, settings, hideable, activate) {

		if(this.value !== 'explodinglayers') {

			anime.removeClass('explodinglayers_hideable');
			hideable.removeClass('explodinglayers_hideable');
			settings.addClass('explodinglayers_hideable');
			if(!suppress) activate.prop('checked', false);

		}
		else {

			settings.removeClass('explodinglayers_hideable');
			hideable.addClass('explodinglayers_hideable');
			anime.addClass('explodinglayers_hideable');
			if(!suppress) activate.prop('checked', true);

		}

		if(!suppress) RevSliderSettings.onoffStatus(activate);

	}

	function onChange() {

		if(this.id.search('end') === -1) {

			swapSettings.apply(this, [animeIn, settingsIn, hideableIn, activateIn]);

		}
		else {

			swapSettings.apply(this, [animeOut, settingsOut, hideableOut, activateOut]);

		}

		if(selectionIn[0].value === 'explodinglayers' || selectionOut[0].value === 'explodinglayers') {

			pauser.addClass('explodinglayers_hideable');
			setTimeout(pauseAnimations, 100);

		}
		else {

			pauser.removeClass('explodinglayers_hideable');

		}

	}

	function pauseAnimations() {

		tpLayerTimelinesRev.stopAllLayerAnimation();
		if(!pauser.hasClass('inpause')) pauser.addClass('inpause');

	}

	function setShape(container, el, value) {

		if(!el.length) el = container.find('.explodinglayers-icon-custom');

		container.find('.explodinglayers-icon').removeClass('explodinglayers-selected');
		container.find('.explodinglayers-icon-custom').hide();

		el.addClass('explodinglayers-selected').css('display', 'inline-block');
		if(value && /[A-Z]/.test(value)) el.find('.explodinglayers-path').attr('d', value);

	}

	function onEnter() {

		$('.obj_lib_container_svg').off('click.explodinglayers').one('click.explodinglayers', onSvgSelect);

	}

	function onSvgSelect(e) {

		e.stopImmediatePropagation();
		dialogClose();

		if(!rs_plugin_validated) {

			show_premium_dialog('register-to-acess-object-library');
			$('#dialog_addobj').dialog('close');
			return;

		}

		var $this = $(this),
			selection = $this.closest('.objadd-single-item').find('path').attr('d');

		curContainer.find('.explodinglayers-icon').removeClass('explodinglayers-selected');
		curContainer.find('.explodinglayers-icon-custom').addClass('explodinglayers-selected').css('display', 'inline-block').find('.explodinglayers-path').attr('d', selection);
		curContainer.find('.explodinglayers-type').val(selection).change();

		$('#dialog_addobj').dialog('close');

	}

	function dialogClose() {

		clearTimeout(dialogTimer);
		$('.obj_lib_container_svg').off('.explodinglayers');
		bodies.removeClass('explodinglayers_custom_particle').off('.explodinglayers');

	}

	function setupDialog() {

		if(!UniteLayersRev.object_library_loaded) {

			dialogTimer = setTimeout(setupDialog, 100);
			return;

		}

		$('.objectlibrary_dialog').find('.ui-dialog-title').text('Select SVG');
		$('#obj_lib_main_cat_filt_svg').click();

	}

	function checkDependencies() {

		return typeof RsAddonExplodingLayers === 'undefined' ||
		       typeof UniteLayersRev === 'undefined' ||
			   typeof RevSliderSettings === 'undefined' ||
			   typeof tpLayerTimelinesRev === 'undefined' ||
			   typeof show_premium_dialog === 'undefined' ||
			   typeof rs_plugin_validated === 'undefined' ||
			   typeof setExampleButtons === 'undefined' ||
			   !$.fn.tpColorPicker || false;

	}

	$(function() {

		if(checkDependencies()) return;

		var callbacks = UniteLayersRev.addon_callbacks,
			fields = [],
			i = 0;

		bodies = $('body');
		plug = $('#explodinglayers-addon-wrap');
		layerDefaults = RsAddonExplodingLayers.layers;
		options = $('.explodinglayers-main-settings').find('input, select');

		for(var prop in layerDefaults) {

			if(layerDefaults.hasOwnProperty(prop)) {

				fields[i++] = 'explodinglayers.' + prop;

			}

		}

		// READ
		callbacks[callbacks.length] = {

			environment       : 'updateLayerFormFields',
			function_position : 'start',
			callback          : function(obj) {

				read(obj);
				return obj;

			}

		};

		// WRITE
		callbacks[callbacks.length] = {

			environment       : 'updateLayerFromFields_Core',
			function_position : 'start',
			callback          : function(obj) {

				write(obj);
				return obj;

			}

		};

		// ADDED LAYER
		callbacks[callbacks.length] = {

			environment       : 'add_layer_to_stage',
			function_position : 'end',
			callback          : function(obj) {

				pauseAnimations();
				return obj;

			}

		};

		UniteLayersRev.attributegroups.push({

			id        : 'explodinglayers',
			icon      : 'popup',
			groupname : 'Explodinglayers',
			keys      : fields

		});

		$('.explodinglayers-layer-color').each(function() {

			var color = $(this);
			color.tpColorPicker({

				init: function() {

					color.closest('.rev-colorpicker').addClass('tipsy_enabled_top').attr('original-title', 'Particle Color').tipsy({gravity: 's', delayIn: 70});

				},
				change: function(a, b, c) {

					if(c) a.val(JSON.stringify(c).replace(/\"/g, "&"));

				}

			});

		});

		var addons = $('#rs-addon-wrapper-button-row > .rs-layer-toolbar-box');
		if(addons.length <= 3) $('.rs-addon-tab-button').remove();
		else $('#rs-addon-trigger-explodinglayers').remove();

		var start = '<option disabled>-----------------------------------</option>',
			end = '<option disabled>-----------------------------------</option>';

		animeIn = $('#extra_start_animation_settings .inner-settings-wrapper, #animin-template-handling-dd');
		settingsIn = $('#explodinglayers-main-settings-in').insertAfter($('#extra_start_animation_settings .inner-settings-wrapper'));
		selectionIn = $('#layer_animation').prepend(start + '<option value="explodinglayers">Exploding Layers</option>' + end).on('change', onChange);
		durationIn = $('#layer_speed');

		animeOut = $('#extra_end_animation_settings .inner-settings-wrapper, #animout-template-handling-dd');
		settingsOut = $('#explodinglayers-main-settings-out').insertAfter($('#extra_end_animation_settings .inner-settings-wrapper'));
		selectionOut = $('#layer_endanimation').prepend(start + '<option value="explodinglayers">Exploding Layers</option>' + end).on('change', onChange);
		durationOut = $('#layer_endspeed');

		hideableIn = $('#extra_start_animation_settings').prev('p').find('.hide-on-sfx_in, .hide-on-sfx_out');
		hideableOut = $('#extra_end_animation_settings').prev('p').find('.hide-on-sfx_in, .hide-on-sfx_out');

		plug = $('.explodinglayers-main-settings');
		pauser = $('#layeranimation-playpause');

		shapeIn = $('#explodinglayers_type_in');
		shapeOut = $('#explodinglayers_type_in');

		activateIn = $('#explodinglayers-activate-in');
		$('#explodinglayers-activate-wrap-in').insertAfter($('#add_customanimation_in'));

		activateOut = $('#explodinglayers-activate-out');
		$('#explodinglayers-activate-wrap-out').insertAfter($('#add_customanimation_out'));

		$('.explodinglayers-activate-toolbox').each(function() {

			var $this = $(this);
			$this.closest('.tp-onoffbutton').attr('original-title', $this.attr('original-title')).addClass('tipsy_enabled_top').tipsy({gravity: 's', delayIn: 70});

		});

		$('.explodinglayers-activate').on('change', function() {

			var isIn = this.id.search('-in') !== -1,
				transition,
				selection,
				duration,
				time;

			if(isIn) {

				selection = selectionIn;
				duration = durationIn;

			}
			else {

				selection = selectionOut;
				duration = durationOut;

			}

			if(this.checked) {

				transition = 'explodinglayers';
				time = isIn ? 1000 : 500;

				selection.data({

					revexplasttransition: selection.val(),
					revexplastduration: duration.val()

				});

			}
			else {

				transition = selection.data('revexplasttransition');
				if(!transition) transition = 'tp-fade';

				time = selection.data('revexplastduration');
				if(!time) time = 300;

			}

			suppress = true;
			duration.val(time);
			selection.val(transition).change();
			suppress = false;

		}).each(function() {

			var $this = $(this);
			$this.closest('.tp-onoffbutton').attr('original-title', $this.attr('original-title')).tipsy({gravity: 's', delayIn: 70});

		});

		$('.explodinglayers-icon').on('click', function() {

			var $this = $(this);
			curContainer = $this.closest('.explodinglayers-main-settings');
			curContainer.find('.explodinglayers-type').val($this.attr('data-icon')).change();
			curContainer.find('.explodinglayers-icon-custom').hide();
			setShape(curContainer, $this);

		});

		$('.explodinglayers-main-settings .rs-layer-animation-settings-tabs li').off().on('click', function() {

			var $this = $(this),
				container = $this.closest('.explodinglayers-main-settings');

			$this.closest('.rs-layer-animation-settings-tabs').find('li').removeClass('selected');
			$this.addClass('selected');

			container.find('.explodinglayers-container').hide();
			container.find('.explodinglayers-' + $this.attr('data-content')).show();

		});

		document.getElementById('rs-animation-tab-button').addEventListener('click', pauseAnimations);
		document.getElementById('layeranimation-playpause').addEventListener('click', function(event) {

			event.stopImmediatePropagation();

		});

		$('.explodinglayers_custom_shape').click(function() {

			setExampleButtons();
			UniteLayersRev.callObjectLibraryDialog('object');

			bodies.addClass('explodinglayers_custom_particle')
				  .off('mouseover.explodinglayers').one('mouseover.explodinglayers', '.obj_lib_container_svg', onEnter)
				  .off('click.explodinglayers').one('click.explodinglayers', '.ui-dialog-titlebar-close', dialogClose);

			setupDialog();
			curContainer = $(this).closest('.explodinglayers-main-settings');

		});

		$(document).ready(function() {
			$("#extra_start_animation_settings .explodinglayers-main-settings input, #extra_end_animation_settings .explodinglayers-main-settings input").off('change');
		});

	});

})(typeof jQuery !== 'undefined' ? jQuery : false);




