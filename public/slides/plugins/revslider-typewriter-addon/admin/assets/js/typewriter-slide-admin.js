/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

;(function($) {
	
	if(!$) {
		
		console.log('core jQuery library not loading correctly');
		return;
		
	}
	
	var id,
		slug,
		plug,
		lines,
		opens,
		title,
		layrs,
		delays,
		cursor,
		toggle,
		enabled,
		options, 
		wrapper,
		toggles,
		hasTipsy,
		patterns,
		defaults,
		layerTxt,
		advanced,
		advanced2,
		skeleton1,
		skeleton2,
		skeleton3,
		skeleton4,
		skeleton5,
		skeleton6,
		sequenced,
		editLayer,
		multiline,
		wordDelay,
		activeLayer;

	function writeVars() {
		
		id        = '#typewriter-addon-settings';
		plug      = $(id);
		
		defaults  = RsTypeWriter;
		slug      = 'typewriter_';
		title     = 'typewriter';
		layrs     = $('#divLayers');
		toggle    = $('#toggle_allow');
		patterns  = $('#typewriter-word-patterns');
		advanced  = $('#typewriter-addon-settings-advanced');
		
		layerTxt  = $('#layer_text');
		wrapper   = $('.form_layers');
		hasTipsy  = $.fn.tipsy !== 'undefined';
		
		toggles   = plug.find('.cj-addon-advanced');
		lines     = plug.find('input[name=typewriter_lines]');
		delays    = plug.find('input[name=typewriter_delays]');
		enabled   = plug.find('input[name=typewriter_enabled]');
		sequenced = plug.find('input[name=typewriter_sequenced]');
		wordDelay = plug.find('input[name=typewriter_word_delay]');
		options   = plug.find('input, select').not('input[type=button]');
		
		editLayer = $('#button_edit_layer');
		advanced2 = $('#typewriter-addon-settings-advanced-2').on('click', '.button-primary', handleWordDelays)
															  .on('mouseleave', '.inp-deep-wrapper', inputDeepLeave);
		
		plug.on('change', '.tp-moderncheckbox', handleCheckboxes);
		plug.on('change focusout', '.typewriter-delays', checkChange);
		sequenced.closest('.tp-onoffbutton').on('click', toggleSequencing);
		
		skeleton1 = '<div class="typerwriter-line-wrap layer_text_wrapper_inner">' + 
						'<input type="text" class="typewriter-line" placeholder="enter text..." value="';			
		skeleton2 = '" />' + 
						'<span class="typewriter-add-remove layer-short-tool revgreen"><i class="eg-icon-plus"></i></span>' + 
						'<span class="typewriter-add-remove layer-short-tool revred"><i class="rs-lighttrash"></i></span>' + 
					'</div>';
					
		skeleton3 = '<div class="typewriter-word-pattern-wrap rs-layer-toolbar-box" data-index="';
		skeleton4 = '"><span class="inp-deep-wrapper">' + 
					'<input type="text" class="textbox-caption typewriter-delays input-deepselects rs-layer-input-field tipsy_enabled_top" original-title="For every (x) words..." value="';
					
		skeleton5 = '" />' + 
					'<div class="inp-deep-list">' + 
						'<span class="inp-deep-listitems">' + 
							'<span class="inp-deep-prebutton" data-val="6"><i class="eg-icon-wrench"></i>Custom</span>' + 
							'<span class="inp-deep-prebutton" data-val="1"><i class="eg-icon-filter"></i>1 word</span>' + 
							'<span class="inp-deep-prebutton" data-val="2"><i class="eg-icon-filter"></i>2 words</span>' + 
							'<span class="inp-deep-prebutton" data-val="3"><i class="eg-icon-filter"></i>3 words</span>' + 
							'<span class="inp-deep-prebutton" data-val="4"><i class="eg-icon-filter"></i>4 words</span>' + 
							'<span class="inp-deep-prebutton" data-val="5"><i class="eg-icon-filter"></i>5 words</span>' + 
						'</span>' + 
					'</div></span>' + 
					'<i class="fa-icon-arrow-right"></i>' + 
					'<span class="inp-deep-wrapper">' + 
					'<input type="text" class="textbox-caption typewriter-delays input-deepselects rs-layer-input-field tipsy_enabled_top" original-title="...delay by (x) milliseconds" value="';
					
		skeleton6 = '" />' + 
					'<div class="inp-deep-list">' + 
						'<span class="inp-deep-listitems">' + 
							'<span class="inp-deep-prebutton" data-val="150"><i class="eg-icon-wrench"></i>Custom</span>' + 
							'<span class="inp-deep-prebutton" data-val="100"><i class="eg-icon-filter"></i>100ms</span>' + 
							'<span class="inp-deep-prebutton" data-val="250"><i class="eg-icon-filter"></i>250ms</span>' + 
							'<span class="inp-deep-prebutton" data-val="500"><i class="eg-icon-filter"></i>500ms</span>' + 
							'<span class="inp-deep-prebutton" data-val="750"><i class="eg-icon-filter"></i>750ms</span>' + 
							'<span class="inp-deep-prebutton" data-val="1000"><i class="eg-icon-filter"></i>1000ms</span>' + 
						'</span>' + 
					'</div></span>' + 
					'<span class="button-primary revred tipsy_enabled_top" original-title="Delete Word Delay Pattern"><i class="eg-icon-trash"></i></span>' + 
					'</div>';
		
		multiline = $('<div id="typewriter-lines"></div>').appendTo($('#layer_text_wrapper').on(
		
			'click', '.typewriter-add-remove', handleMultilines)
				
		).css('height', $('#thelayer-editor-wrapper').outerHeight() - 237).perfectScrollbar({
			
			wheelPropagation: true, 
			suppressScrollX: true
			
		});
		
		if(hasTipsy) {
		
			plug.find('.tipsy_enabled_top').each(function() {
				
				var $this = $(this),
					btn   = $this.closest('.tp-onoffbutton');
				
				if(btn.length && !btn.data('tipsy')) {
					
					btn.attr('original-title', $this.attr('original-title')).tipsy({gravity: 's', delayIn: 70});
					
				}
				
			});
			
		}
		
		if(RsTypeWriterSliderType === 'gallery') {
		
			cursor = plug.find('select[name=typewriter_cursor_type]').on('change', handleCursor);
			$('#hide_layer_content_editor').on('click', handleCursor);
			editLayer.on('click', handleCursor);
			
		}
		
	}
	
	function updateWordDelayIndexes() {
		
		var $this = $(this);
		$this.attr('data-index', $this.index() + 1);
		
	}
	
	function addWordDelay(words, delay, index) {
		
		var delay = $(skeleton3 + index + '.' + skeleton4 + words + skeleton5 + delay + skeleton6);
		
		if(hasTipsy) delay.find('.tipsy_enabled_top').tipsy({gravity: 's', delayIn: 70});
		
		delay.insertBefore(patterns);
		
	}
	
	function handleWordDelays() {
		
		var $this = $(this);
		
		if($this.hasClass('revgreen')) {
			
			addWordDelay('1', '250', $('.typewriter-word-pattern-wrap').length + 1);
			
		}
		else {
			
			$this = $this.closest('div');
			if(hasTipsy) $this.find('.tipsy_enabled_top').each(destroyTipsy);
			$this.remove();
			
			$('.typewriter-word-pattern-wrap').each(updateWordDelayIndexes);
			
		}
		
		writeDelays();
		delays.change();
		
	}
	
	function destroyTipsy() {
		
		var tip = jQuery(this).data('tipsy');
		
		if(tip) {
			
			tip = tip.$tip;
			if(tip) tip.remove();
			
		}
		
		$.removeData(this, 'tipsy');
		
	}
	
	function handleMultilines() {
		
		var $this = $(this),
			changed = true;
		
		if($this.hasClass('revgreen')) {
			
			$(skeleton1 + skeleton2).insertAfter($this.closest('div'));
			
		}
		else {
			
			if(multiline.children('.typerwriter-line-wrap').length > 1) {
			
				$this.closest('div').remove();
				
			}
			else {
				
				$this.closest('div').children('input').val('');
				changed = false;
				
			}
			
			lines.change();
			
		}
		
		if(changed) multiline.perfectScrollbar('update');
		
	}
	
	function toggleSequencing() {
		
		editLayer.click();
		
	}
	
	function inputDeepLeave() {
		
		jQuery(this).removeClass('selected-deep-wrapper').children('.inp-deep-list').removeClass('visible');
		
	}
	
	function checkChange(e) {
		
		if(e.type === 'focusout') return false;
		
		var $this = $(this).closest('.typewriter-word-pattern-wrap').find('input');
		if($this.eq(0).val() && $this.eq(1).val()) delays.change();
		
		return false;
		
	}
	
	function handleCursor(evt) {
		
		if(!activeLayer || !activeLayer.typewriter || activeLayer.typewriter.enabled !== 'on') return;
		
		var txt = layerTxt.val().replace(/(_|\|)$/, '');
		evt = evt.type === 'click';
		
		if(evt && $(this).attr('id') === 'button_edit_layer') {
			
			layerTxt.val(txt);
			
		}
		else {
			
			var cur = evt ? activeLayer.typewriter['cursor_type'] : cursor.val();
			txt += cur === 'one' ? '_' : '|';
			
			layerTxt.val(txt);
			$('.layer_selected .innerslide_layer').html(txt);
			
		}
		
	}
	
	function handleCheckboxes() {
		
		var $this = $(this);
		if(!$this.parents(id).length) return;
		
		var isChecked = $this.is(':checked');
		$this.val(isChecked ? 'on' : 'off');
		
		if($this[0] === enabled[0]) {
			
			checkEnabled(isChecked);
			
		}
		else if($this[0] === sequenced[0]) {
			
			multiline[isChecked ? 'show' : 'hide']();

		}
		
		if($this.hasClass('cj-addon-advanced')) {
			
			checkAdvanced();
			
		}
		else if($this.hasClass('cj-addon-advanced-2')) {
			
			advanced2[wordDelay.val() === 'on' ? 'show' : 'hide']();
			
		}
		
	}
	
	function checkEnabled(checked) {
		
		if(checked) {
			
			plug.addClass('cj-addon-enabled');
			wrapper.addClass('typewriter-active');
			
			if(toggle.is(':checked')) toggle.removeAttr('checked').change();
			multiline[sequenced.val() === 'on' ? 'show' : 'hide']();
			
			checkAdvanced();
			
		}
		else {
			
			plug.removeClass('cj-addon-enabled');
			wrapper.removeClass('typewriter-active');
			
			opens = false;
			multiline.hide();
			toggleAdvanced();
			
		}
		
	}
	
	function checkAdvanced() {
		
		opens = false;
		toggles.each(checkToggle);
		toggleAdvanced();
		
	}
	
	function checkToggle() {
		
		var $this = $(this),
		value = $this.val() === 'on';
		
		if(value) opens = true;
		checkDisable(value, $this.attr('data-toggle'));
		
	}
	
	function checkDisable(optioned, checks) {
		
		checks = checks.split(' ');
		
		var len = checks.length,
			action = optioned ? 'removeClass' : 'addClass';
		
		while(len--) {
			
			plug.find('*[name=' + slug + checks[len] + ']')
				.closest('.rs-layer-toolbar-box')[action]('cj-advanced-disabled');
			
		}
		
	}
	
	function toggleAdvanced() {
		
		advanced[opens ? 'show' : 'hide']();
		advanced2[opens && wordDelay.val() === 'on' ? 'show' : 'hide']();
		
	}
	
	function readDelays() {
		
		$('.typewriter-word-pattern-wrap').remove();
		
		var words = delays.val().split(','),
			len   = words.length,
			val;
		
		if(len && words[0]) {
		
			for(var i = 0; i < len; i++) {
				
				val = unescape(words[i]).split('|');
				addWordDelay(val[0], val[1], i + 1);
				
			}
			
		}
		
	}
	
	function writeDelays() {
		
		var words = [];
		
		$('.typewriter-word-pattern-wrap').each(function() {
			
			var $this  = jQuery(this).find('input'),
				delay1 = $.trim($this.eq(0).val()),
				delay2 = $.trim($this.eq(1).val());
				
			if(delay1 && delay2) words[words.length] = escape(delay1 + '|' + delay2);
			
		});
		
		delays.val(words.join());
		
	}
	
	function readLines() {
		
		$('.typerwriter-line-wrap').remove();
		
		var multi = lines.val().split(','),
			len   = multi.length;
		
		if(len && multi[0]) {
			
			for(var i = 0; i < len; i++) {
				
				$(skeleton1 + unescape(multi[i]) + skeleton2).appendTo(multiline);
				
			}
			
		}
		else {
			
			$(skeleton1 + skeleton2).appendTo(multiline);
			
		}
		
		multiline.perfectScrollbar('update');
		
	}
	
	function writeLines() {
		
		var multi = [];
		
		$('.typewriter-line').each(function() {
			
			var txt = $.trim($(this).val());
			if(txt) multi[multi.length] = escape(txt);
			
		});
		
		lines.val(multi.join());
		
	}
	
	function read(obj) {
		
		var settings = obj[title] || $.extend({}, defaults),
			value,
			$this;
		
		for(var prop in settings) {
			
			if(!settings.hasOwnProperty(prop)) continue;
			
			value = settings[prop];
			$this = plug.find('*[name=' + slug + prop + ']').val(value);
			
			if($this.attr('type') === 'checkbox') {
				
				value = value === 'on';
				
				if(value) $this.attr('checked', 'checked');
				else $this.removeAttr('checked');
				
				if(value && $this.hasClass('cj-addon-advanced')) opens = true;	
				RevSliderSettings.onoffStatus($this);
				
			}
			
		}
		
		readLines();
		readDelays();
		checkEnabled(enabled.val() === 'on');
		
	}
	
	function write(obj) {
		
		var settings = obj[title] || $.extend({}, defaults);
		
		writeLines();
		writeDelays();
		
		options.each(function() {
			
			var $this = $(this);
			settings[$this.attr('name').split(slug)[1]] = $this.val();
			
		});
		
		obj[title] = settings;

	}
	
	function addHooks() {
		
		var rev    = UniteLayersRev,
			cb     = rev.addon_callbacks,
			fields = [],
			i      = 0;
		
		// READ
		cb[cb.length] = {
			
			environment       : 'updateLayerFormFields',
			function_position : 'start',
			callback          : function(obj) {

				read(obj); 
				activeLayer = obj;
				return obj;
				
			}
			
		};
		
		// WRITE
		cb[cb.length] = {
			
			environment       : 'updateLayerFromFields_Core',
			function_position : 'start',
			callback          : function(obj) {

				write(obj);
				activeLayer = obj;
				return obj;
				
			}
			
		};
			
		for(var prop in defaults) {
			
			if(defaults.hasOwnProperty(prop)) {
			
				fields[i++] = 'typewriter.' + prop;
				
			}
			
		}
		
		UniteLayersRev.attributegroups.push({
			
			id        : 'typewriter', 
			icon      : 'print', 
			groupname : 'Typewriter', 
			keys      : fields
			
		});
		
	}
	
	function checkGlobals() {
		
		return typeof RsTypeWriter === 'undefined' || typeof RsTypeWriterSliderType === 'undefined' ? true :  
			   typeof UniteLayersRev === 'undefined' ? 'UniteLayersRev not available' : 
			   typeof RevSliderSettings === 'undefined' ? 'RevSliderSettings not available' : false;
		
	}
	
	$(function() {
		
		var check = checkGlobals();
		if(check) {
			
			if(typeof check === 'string') console.log(check);
			return;
			
		}
		
		writeVars();
		addHooks();
		
	});



})(typeof jQuery !== 'undefined' ? jQuery : false);




