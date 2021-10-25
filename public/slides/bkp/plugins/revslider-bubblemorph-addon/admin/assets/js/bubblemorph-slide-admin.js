/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */
 
;(function($) {
	
	var title = 'bubblemorph',
		slug = title + '_',
		newBlurStrength,
		newLayerDialog,
		globalSettings,
		newBorderColor,
		newBorderSize,
		layerDefaults,
		settingsInput,
		newBlurColor,
		newBufferX,
		newBufferY,
		newSpeedX,
		newSpeedY,
		newHeight,
		newWidth,
		newBlurX,
		newBlurY,
		newColor,
		options,
		newMax,
		bodies,
		views,
		plug,
		doc;
	
	
	if(!$) {
		
		console.log('core jQuery library not loading correctly');
		return;
		
	}
	
	function read(obj) {
		
		if(!obj) return; 
		
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
			
			if(!Array.isArray(value)) value = [value, 'inherit', 'inherit', 'inherit'];
			newValue = value[masterIndex];
			index = masterIndex;
			
			while(newValue === 'inherit') {
				
				index -= 1;
				if(index > -1) newValue = value[index];
				else newValue = '';
				
			}

			el = plug.find('*[name=' + slug + prop + ']').val(newValue).attr('data-currentval', value.join('|'));
			if(el.hasClass('bubblemorph-layer-color')) el.tpColorPicker('refresh');
			
		}
		
		setSelected(obj);
		checkEnabled();
		
	}
	
	function checkEnabled() {
		
		var hasBubblemorph = $('.slide_layer_type_shape').not('.layer-deleted').find('.tp-bubblemorph').length;
		
		globalSettings.enabled = hasBubblemorph ? 'true' : 'false';
		settingsInput.val(JSON.stringify(globalSettings));
		
		enableDisable(hasBubblemorph);
		
	}
	
	function setSelected(obj) {	
		
		if(obj.subtype !== 'bubblemorph') {
			
			bodies.removeClass('bubblemorph-layer-active');
			
		}
		else {
			
			bodies.addClass('bubblemorph-layer-active');
			
		}
		
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
			
			val = this.getAttribute('data-currentval');
			prop = this.getAttribute('name').split(slug)[1];
			
			if(val) {
				val = val.split('|');
			}
			else {
				val = settings[prop];
				if(!Array.isArray(val)) val = [val, 'inherit', 'inherit', 'inherit'];
			}
			
			val[index] = this.value;
			settings[prop] = val;
			this.setAttribute('data-currentval', val.join('|'));
			
		});
		
		obj[title] = settings;
		checkEnabled();

	}
	
	function newBubblemorph() {
		
		var blurStrength = parseInt(newBlurStrength.val(), 10),
			borderSize = parseInt(newBorderSize.val(), 10),
			bufferX = parseInt(newBufferX.val(), 10),
			bufferY = parseInt(newBufferY.val(), 10),
			max = parseInt(newMax.val(), 10) || 6,
			speedX = parseFloat(newSpeedX.val()),
			speedY = parseFloat(newSpeedY.val()),
			blurX = parseInt(newBlurX.val(), 10),
			blurY = parseInt(newBlurY.val(), 10),
			borderColor = newBorderColor.val(),
			blurColor = newBlurColor.val(),
			bgColor = newColor.val();
		
		var obj = {
		
			static_styles: {},
			text: ' ',
			alias: 'Bubblemorph',
			type: 'shape',
			style: '',
			internal_class: 'tp-shape tp-shapewrapper tp-bubblemorph',
			autolinebreak: false,
			createdOnInit: false,
			resizeme: false,
			
			'deformation-hover': {},
			deformation: {
				
				'background-color': bgColor,
				'background-transparency': 1,
				'border-color': '#000000',
				'border-opacity': '1',
				'border-transparency': '1',
				'border-width': '0',
				'border-style': 'solid',
				'border-radius': ['0', '0', '0', '0']
				
			},
			
			subtype: 'bubblemorph',
			bubblemorph: {
				
				max: max,
				bordersize: borderSize,
				bordercolor: borderColor,
				blurstrength: blurStrength,
				blurcolor: blurColor,
				bufferx: bufferX,
				buffery: bufferY,
				speedx: speedX,
				speedy: speedY,
				blurx: blurX,
				blury: blurY
				
			}
		
		};
		
		var fullWidth = jQuery('input[name="bubbles_width"]')[0].checked,
			fullHeight = jQuery('input[name="bubbles_height"]')[0].checked,
			triggerW,
			triggerH;
		
		if(fullWidth && fullHeight) {
			
			obj.max_width = '100%';
			obj.max_height = '100%';
			obj.cover_mode = 'cover';
			obj.align_base = 'slide';
			obj.left = 0;
			obj.top = 0;
			
		}
		else if(fullWidth) {
			
			obj.max_width = '100%';
			obj.max_height = parseInt(newHeight.val(), 10) || 400;
			obj.cover_mode = 'fullwidth';
			obj.align_base = 'slide';
			triggerH = true;
			
		}
		else if(fullHeight) {
			
			obj.max_height = '100%';
			obj.max_width = parseInt(newWidth.val(), 10) || 400;
			obj.cover_mode = 'fullheight';
			obj.align_base = 'slide';
			triggerW = true;
			
		}
		else {
			
			obj.max_width = parseInt(newWidth.val(), 10) || 400;
			obj.max_height = parseInt(newHeight.val(), 10) || 400;
			
		}

		doc.trigger('addLayer', [{objLayer: obj}]);
		
		if(triggerW) $('#layer_max_width').change();
		if(triggerH) $('#layer_max_height').change();
		jQuery('input[name="css_background-color"]').val(bgColor).change().tpColorPicker('refresh');
		
		bodies.addClass('bubblemorph-addon-enabled');
		newLayerDialog.dialog('close');
		
		$('.rs-addon-tab-button[data-content="#rs-addon-wrapper"]').click();
		$('#rs-addon-trigger-bubblemorph').click();
		
	}
	
	function closeLayerDialog() {
		
		newLayerDialog.dialog('close');
		
	}
	
	function addNewLayer() {
		
		newLayerDialog.find('input').removeClass(' setting-disabled').removeAttr('disabled');
		newLayerDialog.dialog('open');
		
	}
	
	function layerAddedToStage(obj, checked) {
		
		if(checked || obj.subtype === 'bubblemorph') {
			
			var icon = obj.references.quicklayer.find('.rs-icon-layershape_n');	
			if(icon.length) {	
				
				$('<i class="fa-icon-maxcdn"></i>').insertAfter(icon);
				icon.remove();
				
			}
			
			icon = obj.references.sorttable.layer.find('.rs-icon-layershape');	
			if(icon.length) {	
				
				$('<i class="layertypeclass fa-icon-maxcdn"></i>').insertAfter(icon);
				icon.remove();
				
			}
			
		}
		
	}
	
	function enableDisable(enabled) {
		
		if(enabled) {
			
			if(!bodies.hasClass('bubblemorph-addon-enabled')) {
			
				bodies.addClass('bubblemorph-addon-enabled');
				
			}
			
		}
		else {
			
			if(bodies.hasClass('bubblemorph-addon-enabled')) {
			
				bodies.removeClass('bubblemorph-addon-enabled');
			
			}
			
		}
		
	}
	
	$(function() {
		
		if(typeof RsAddonBubbleMorph === 'undefined' || typeof UniteLayersRev === 'undefined' || typeof RevSliderSettings === 'undefined') {
			
			$('body').addClass('bubblemorph-disabled');
			return;
			
		}
		
		var callbacks = UniteLayersRev.addon_callbacks,
			fields = [],
			i = 0;
		
		layerDefaults = RsAddonBubbleMorph.layers;
		globalSettings = RsAddonBubbleMorph.globals;
		views = ['desktop', 'notebook', 'tablet', 'mobile'];
		
		doc = $(document);
		bodies = $('body');
		plug = $('#bubblemorph-addon-wrap');
		
		newWidth = $('#bubblemorph_new_layer_width');
		newHeight = $('#bubblemorph_new_layer_height');
		newColor = $('#bubblemorph_new_layer_color');
		
		newMax = $('#bubblemorph_new_layer_max');
		newBufferX = $('#bubblemorph_new_layer_bufferx');
		newBufferY = $('#bubblemorph_new_layer_buffery');
		newSpeedX = $('#bubblemorph_new_layer_speedx');
		newSpeedY = $('#bubblemorph_new_layer_speedy');
		
		newBlurStrength = $('#bubblemorph_new_layer_blurstrength');
		newBlurColor = $('#bubblemorph_new_layer_blurcolor');
		newBlurX = $('#bubblemorph_new_layer_blurx');
		newBlurY = $('#bubblemorph_new_layer_blury');
		
		newBorderColor = $('#bubblemorph_new_layer_bordercolor');
		newBorderSize = $('#bubblemorph_new_layer_bordersize');
		
		options = $('#bubblemorph-main-settings').find('input, select');
		settingsInput = $("<input name='bubblemorph_globals' type='hidden' value='" + JSON.stringify(globalSettings) + "' />").appendTo($('#form_slide_params'));
		
		$('<a href="javascript:void(0)" id="button_add_layer_bubblemorph" data-isstatic="false" class="add-layer-button">' + 
			'<i class="fa-icon-maxcdn"></i><span class="add-layer-txt">BubbleMorph</span></a>').on('click', addNewLayer).insertBefore($('#button_add_layer_import'));
		
		var dialog = jQuery('#bubblemorph_new_layer_dialog');
		newLayerDialog = dialog.dialog({
			
			width: 500,
			modal: true,
			autoOpen: false,
			resizable: false,
			closeOnEscape: true,
			buttons: {'Add': newBubblemorph, 'Cancel': closeLayerDialog},
			create: function(ui) {jQuery(ui.target).parent().find('.ui-dialog-titlebar').addClass('tp-slider-new-dialog-title');},
			open: function() {
				
				$('.bubbles-addon-checkbox').change();
				dialog.find('input[type=text]').not(':disabled').first().focus().blur();
				
			}
			
		});
		
		$('body').on('change', '.bubbles-addon-checkbox', function() {
			
			var titl = this.name.replace('bubbles_', '');
			$('#bubblemorph_new_layer_' + titl).prop('disabled', this.checked);
			
		});
		
		for(var prop in layerDefaults) {
			
			if(layerDefaults.hasOwnProperty(prop)) {
			
				fields[i++] = 'bubblemorph.' + prop;
				
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

				layerAddedToStage(obj);
				return obj;
				
			}
			
		};
		
		// ADDED LAYER
		callbacks[callbacks.length] = {
			
			environment       : 'unselectHtmlLayers',
			function_position : 'end',
			callback          : function(obj) {

				checkEnabled();
				return obj;
				
			}
			
		};
		
		UniteLayersRev.attributegroups.push({
			
			id        : 'bubblemorph', 
			icon      : 'popup', 
			groupname : 'Bubblemorph', 
			keys      : fields
			
		});
		
		var layrs = UniteLayersRev.arrLayers,
			obj;
			
		for(prop in layrs) {
			
			if(layrs.hasOwnProperty(prop)) {
				
				obj = layrs[prop];
				if(obj.subtype === 'bubblemorph') layerAddedToStage(obj, true);
				
			}
			
		}
		
		enableDisable(globalSettings.enabled == 'true');
		
		var val = newColor.val();
		newColor.tpColorPicker({
			
			wrapper: '<span class="rev-colorpickerspan"></span>',
			init: function() {newColor.val(val);},
			change: function(a, b, c) {if(c) a.val(JSON.stringify(c).replace(/\"/g, "&"));}
			
		});
		
		$('.bubblemorph-layer-color').each(function() {
		
			var titl = this.id === 'bubblemorph_blurcolor' ? 'Shadow Color' : 'Border Color',	
				$this = $(this);
			
			$this.tpColorPicker({init: function() {

				$this.closest('.rev-colorpicker').addClass('tipsy_enabled_top').attr('original-title', titl).tipsy({gravity: 's', delayIn: 70});
				
			}});
			
		});
		
		$('.bubblemorph-new-layer-color').tpColorPicker({mode: 'single', wrapper: '<span class="rev-colorpickerspan"></span>'});
		
	});


})(typeof jQuery !== 'undefined' ? jQuery : false);




