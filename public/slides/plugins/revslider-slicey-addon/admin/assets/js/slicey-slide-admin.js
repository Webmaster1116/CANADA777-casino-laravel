/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */
 
;(function($) {
	
	var title = 'slicey',
		slug = title + '_',
		newLayerDialog,
		globalSettings,
		layerDefaults,
		settingsInput,
		layerClasses,
		newBlurStart,
		kbBlurStart,
		newBlurEnd,
		kbDuration,
		kbScaleEnd,
		kbBlurEnd,
		newHeight,
		kbEasing,
		newWidth,
		newDepth,
		options,
		globals,
		bodies,
		plug,
		doc;
	
	
	if(!$) {
		
		console.log('core jQuery library not loading correctly');
		return;
		
	}
	
	function read(obj) {
		
		if(!obj) return; 
		
		var settings = obj[title] || $.extend({}, layerDefaults),
			value;
		
		for(var prop in settings) {
			
			if(!settings.hasOwnProperty(prop)) continue;
			
			value = settings[prop];
			plug.find('*[name=' + slug + prop + ']').val(value);
			
		}
		
		globals.each(resetValues);
		setSelected(obj);
		checkEnabled();
		
	}
	
	function checkEnabled() {
		
		var hasSlicey = $('.slide_layer_type_shape').not('.layer-deleted').find('.tp-slicey').length;
		
		globalSettings.enabled = hasSlicey ? 'true' : 'false';
		settingsInput.val(JSON.stringify(globalSettings));
		
		enableDisable(hasSlicey);
		
	}
	
	function setSelected(obj) {	
		
		if(obj.subtype !== 'slicey') {
			
			bodies.removeClass('slicey-layer-active');
			
		}
		else {
			
			bodies.addClass('slicey-layer-active');
			
		}
		
	}
	
	function write(obj) {
		
		var settings = obj[title] || $.extend({}, layerDefaults);

		options.each(function() {
			
			settings[this.getAttribute('name').split(slug)[1]] = this.value;
			
		});
		
		obj[title] = settings;
		checkEnabled();

	}
	
	function resetValues() {
		
		this.value = this.getAttribute('data-slicey-value');
		
	}
	
	function readGlobals() {
		
		var val = this.value || this.getAttribute('data-color');
		globalSettings[this.getAttribute('name').replace('slicey_', '')] = val;
		
	}
	
	function globalChange() {
	
		this.setAttribute('data-slicey-value', this.value);
		globals.each(readGlobals);
		settingsInput.val(JSON.stringify(globalSettings));
		updateKenBurns();
	
	}
	
	function newSlicey() {
		
		doc.trigger('addLayer', [{objLayer: {
		
			static_styles: {},
			text: ' ',
			alias: 'Slicey',
			type: 'shape',
			style: '',
			internal_class: 'tp-shape tp-shapewrapper tp-slicey',
			autolinebreak: false,
			createdOnInit: false,
			max_width: newWidth.val(),
			max_height: newHeight.val(),
			'deformation-hover': {},
			deformation: {
				
				'background-color': '#000000',
				'background-transparency': 0.5,
				'border-color': '#000000',
				'border-opacity': '1',
				'border-transparency': '1',
				'border-width': '0',
				'border-style': 'solid',
				'border-radius': ['0', '0', '0', '0']
				
			},
			
			subtype: 'slicey',
			slicey: {
				
				scale_offset: newDepth.val(), 
				blurlstart: newBlurStart.val(), 
				blurlend: newBlurEnd.val()
				
			}
		
		}}]);
		
		bodies.addClass('slicey-addon-enabled');
		newLayerDialog.dialog('close');
		
	}
	
	function closeLayerDialog() {
		
		newLayerDialog.dialog('close');
		
	}
	
	function addNewLayer() {
		
		newLayerDialog.find('input').removeClass(' setting-disabled').removeAttr('disabled');
		newLayerDialog.dialog('open');
		
	}
	
	function layerAddedToStage(obj, checked) {
		
		if(checked || obj.subtype === 'slicey') {
			
			var icon = obj.references.quicklayer.find('.rs-icon-layershape_n');	
			if(icon.length) {	
				
				$('<i class="fa-icon-object-ungroup"></i>').insertAfter(icon);
				icon.remove();
				
			}
			
			icon = obj.references.sorttable.layer.find('.rs-icon-layershape');	
			if(icon.length) {	
				
				$('<i class="layertypeclass fa-icon-object-ungroup"></i>').insertAfter(icon);
				icon.remove();
				
			}
			
		}
		
	}
	
	function enableDisable(enabled) {
		
		var kbEnabled;
		if(enabled) {
			
			if(!bodies.hasClass('slicey-addon-enabled')) {
			
				bodies.addClass('slicey-addon-enabled');
				
				kbEnabled = $('#kenburn_effect').attr('checked', 'checked').change();
				RevSliderSettings.onoffStatus(kbEnabled);
				
				$('#slide_bg_position').val('center center').change();
				$('#kb_start_fit').val('100');
				$('#kb_start_offset_x').val('0');
				$('#kb_start_offset_y').val('0');
				$('#kb_end_offset_x').val('0');
				$('#kb_end_offset_y').val('0');
				$('#kb_start_rotate').val('0');
				$('#kb_start_rotate').val('0');
				
				updateKenBurns();
				
			}
			
		}
		else {
			
			if(bodies.hasClass('slicey-addon-enabled')) {
			
				bodies.removeClass('slicey-addon-enabled');
				kbEnabled = $('#kenburn_effect').removeAttr('checked').change();
				RevSliderSettings.onoffStatus(kbEnabled);
			
			}
			
		}
		
	}
	
	function updateKenBurns() {
		
		kbEasing.val(globalSettings.easing);
		kbScaleEnd.val(globalSettings.scale);
		kbDuration.val(globalSettings.time);
		kbBlurStart.val(globalSettings.blurgstart);
		kbBlurEnd.val(globalSettings.blurgend);
		
	}
	
	$(function() {
		
		if(typeof RsAddonSlicey === 'undefined' || typeof UniteLayersRev === 'undefined' || typeof RevSliderSettings === 'undefined') {
			
			$('body').addClass('slicey-disabled');
			return;
			
		}
		
		var callbacks = UniteLayersRev.addon_callbacks,
			fields = [],
			i = 0;
		
		layerDefaults = RsAddonSlicey.layers;
		globalSettings = RsAddonSlicey.globals;
		
		doc = $(document);
		bodies = $('body');
		kbEasing = $('#kb_easing');
		kbScaleEnd = $('#kb_end_fit');
		plug = $('#slicey-addon-wrap');
		kbDuration = $('#kb_duration');
		kbBlurEnd = $('#kb_blur_end');
		kbBlurStart = $('#kb_blur_start');
		layerClasses = $('#layer_classes');
		newDepth = $('#slicey_new_layer_depth');
		newWidth = $('#slicey_new_layer_width');
		newHeight = $('#slicey_new_layer_height');
		newBlurEnd = $('#slicey_new_layer_blurlend');
		newBlurStart = $('#slicey_new_layer_blurlstart');
		options = $('#slicey-layer-settings').find('input, select');
		globals = $('.slicey-global-settings').find('input, select').on('change', globalChange);
		settingsInput = $("<input name='slicey_globals' type='hidden' value='" + JSON.stringify(globalSettings) + "' />").appendTo($('#form_slide_params'));
		
		$('<a href="javascript:void(0)" id="button_add_layer_slicey" data-isstatic="false" class="add-layer-button">' + 
			'<i class="fa-icon-object-ungroup"></i><span class="add-layer-txt">Slicey</span></a>').on('click', addNewLayer).appendTo($('#add-new-layer-container'));
		
		newLayerDialog = jQuery('#slicey_new_layer_dialog').dialog({
			
			width: 400,
			modal: true,
			autoOpen: false,
			resizable: false,
			closeOnEscape: true,
			buttons: {'Add': newSlicey, 'Cancel': closeLayerDialog},
			create: function(ui) {jQuery(ui.target).parent().find('.ui-dialog-titlebar').addClass('tp-slider-new-dialog-title');}
			
		});
		
		for(var prop in layerDefaults) {
			
			if(layerDefaults.hasOwnProperty(prop)) {
			
				fields[i++] = 'slicey.' + prop;
				
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
			
			id        : 'slicey', 
			icon      : 'popup', 
			groupname : 'Slicey', 
			keys      : fields
			
		});
		
		var layrs = UniteLayersRev.arrLayers,
			obj;
			
		for(prop in layrs) {
			
			if(layrs.hasOwnProperty(prop)) {
				
				obj = layrs[prop];
				if(obj.subtype === 'slicey') layerAddedToStage(obj, true);
				
			}
			
		}
		
		enableDisable(globalSettings.enabled == 'true');
		$('#slicey_color').tpColorPicker({mode: 'single', editing: 'Shadow Color'});
		
	});


})(typeof jQuery !== 'undefined' ? jQuery : false);




