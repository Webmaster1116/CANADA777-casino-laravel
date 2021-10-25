/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */
 
;(function($) {

	if(!$) return;

	var title = 'beforeafter',
		slug = title + '_',
		sourceSettings,
		globalSettings,
		layerDefaults,
		settingsInput,
		sourceOptions,
		imageSettings,
		imageSource,
		youtubeSets,
		videoInput,
		afterImage,
		references,
		bgImageUrl,
		bgImageId,
		bgSources,
		vimeoSets,
		viewIcon,
		layersBg,
		options,
		globals,
		timings,
		speeds,
		baIcon,
		bodies,
		color,
		bgImg,
		bgObj,
		times,
		view,
		plug,
		bgs,
		
	sourceShell = 
	
		'<div id="mainbg-sub-source-after" style="display: none;">' + 

			'<div class="bg-settings-block">' + 
														
				'<label>Main / Background Image</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="image" class="bgsrcchanger-beforeafter" data-container="tp-bgimagewpsrc_beforeafter" id="radio_back_image_beforeafter" /> ' + 
				'<span id="tp-bgimagewpsrc_beforeafter" class="bgsrcchanger-div-beforeafter">' + 														
					'<a href="javascript:void(0)" id="button_change_image_beforeafter" class="button-primary revblue" original-title=""><i class="fa-icon-wordpress"></i>Media Library</a><a href="javascript:void(0)" id="button_change_image_objlib_beforeafter" class="button-primary revpurple" original-title=""><i class="fa-icon-book"></i>Object Library</a>' + 
				'</span>' + 

			'</div>' + 
			
			'<div class="bg-settings-block">' + 

				'<label>External URL</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="external" data-container="tp-bgimageextsrc_beforeafter" class="bgsrcchanger-beforeafter" id="radio_back_external_beforeafter" /> ' + 
				'<span id="tp-bgimageextsrc_beforeafter" class="bgsrcchanger-div-beforeafter">' + 
					'<input type="text" name="bg_external_beforeafter" id="slide_bg_external_beforeafter" value="{{external}}" />' + 
					'<a href="javascript:void(0)" id="button_change_external_beforeafter" class="button-primary revblue" original-title="">Get External</a>' + 
				'</span>' + 
				
			'</div>' + 
			
			'<div class="bg-settings-block"> ' + 

				'<label>Transparent</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="trans" data-container="" class="bgsrcchanger-beforeafter" id="radio_back_trans_beforeafter" />' + 
				
			'</div>' + 
			
			'<div class="bg-settings-block"> ' + 

				'<label>Colored</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="solid" data-container="tp-bgcolorsrc_beforeafter" class="bgsrcchanger-beforeafter" id="radio_back_solid_beforeafter" /> ' + 
				'<span id="tp-bgcolorsrc_beforeafter" class="bgsrcchanger-div-beforeafter">' + 
					'<input type="hidden" name="bg_color_beforeafter" id="slide_bg_color_beforeafter" value="{{color}}" />' + 
				'</span>' +
				
			'</div>' + 
			
			'<div class="bg-settings-block">' + 

				'<label>YouTube Video</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="youtube" data-container="tp-bgyoutubesrc_beforeafter" class="bgsrcchanger-beforeafter" id="radio_back_youtube_beforeafter" /> ' + 
				'<span id="tp-bgyoutubesrc_beforeafter" class="bgsrcchanger-div-beforeafter">' + 
					'<input type="text" name="bg_youtube_beforeafter" id="slide_bg_youtube_beforeafter" placeholder="enter video id here.." value="{{youtube}}" /> example: OIJShHQlMx0' + 
				'</span>' + 
				
			'</div>' + 
			
			'<div class="bg-settings-block">' + 

				'<label>Vimeo Video</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="vimeo" data-container="tp-bgvimeosrc_beforeafter" class="bgsrcchanger-beforeafter" id="radio_back_vimeo_beforeafter" /> ' + 
				'<span id="tp-bgvimeosrc_beforeafter" class="bgsrcchanger-div-beforeafter">' + 
					'<input type="text" name="bg_vimeo_beforeafter" id="slide_bg_vimeo_beforeafter" placeholder="enter video id here.." value="{{vimeo}}" /> example: 30300114' + 
				'</span>' + 
				
			'</div>' + 
			
			'<div class="bg-settings-block">' + 

				'<label>HTML5 Video</label> ' + 
				'<input type="radio" name="background_type_beforeafter" value="html5" data-container="tp-bghtml5src_beforeafter" class="bgsrcchanger-beforeafter" id="radio_back_html5_beforeafter" /> ' + 
				'<br>' + 
				'<div id="tp-bghtml5src_beforeafter" class="bgsrcchanger-div-beforeafter">' + 
					'<div>' + 
						'<label>MPEG:</label>' + 
						'<input type="text" name="bg_mpeg_beforeafter" id="slide_bg_mpeg_beforeafter" value="{{mpeg}}" />' +
						'<a href="javascript:void(0)" data-input="slide_bg_mpeg_beforeafter" class="button_change_video_beforeafter button-primary revblue" original-title="">Change Video</a>' + 
					'</div>' + 
					'<div>' + 
						'<label>WEBM:</label>' + 
						'<input type="text" name="bg_webm_beforeafter" id="slide_bg_webm_beforeafter" value="{{webm}}" />' +
						'<a href="javascript:void(0)" data-input="slide_bg_webm_beforeafter" class="button_change_video_beforeafter button-primary revblue" original-title="">Change Video</a>' + 
					'</div>' + 
					'<div>' + 
						'<label>OGV:</label>' + 
						'<input type="text" name="bg_ogv_beforeafter" id="slide_bg_ogv_beforeafter" value="{{ogv}}" />' +
						'<a href="javascript:void(0)" data-input="slide_bg_ogv_beforeafter" class="button_change_video_beforeafter button-primary revblue" original-title="">Change Video</a>' + 
					'</div>' + 
				'</div>' + 
				
			'</div>' + 
			
			'<input type="hidden" id="image_url_beforeafter" name="image_url_beforeafter" value="{{image}}" />' + 
			'<input type="hidden" id="image_id_beforeafter" name="image_id_beforeafter" value="{{id}}" />' + 

		'</div>',
		
	settingsShell = 
	
		'<div id="mainbg-sub-setting-after">' + 
			
			'<div id="beforeafter_image_settings" class="before-after-source-settings">' + 
			
				'<div class="bg-settings-block beforeafter-image-setting">' + 
				
					'<label>Source Info:</label> ' + 
					'<span class="text-selectable" id="the_image_source_url_beforeafter">{{image}}</span> ' + 
					'<span class="description">Read Only ! Image can be changed from "Source Tab"</span>' + 
				
				'</div>' + 
				
				'<div class="bg-settings-block beforeafter-image-setting">' + 
				
					'<label>Image Source Size:</label> ' + 
					'<select id="image_source_type_beforeafter" name="image_source_type_beforeafter">{{sizes}}</select>' + 
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Background Fit:</label> ' + 
					'<select name="bg_fit_beforeafter" id="slide_bg_fit_beforeafter" class="beforeafter-change-container beforeafter-source-option beforeafter-source-changeable" data-styling="size" data-container="bg_fit_beforeafter_wrap">' +
						'<option value="cover">cover</option>' +
						'<option value="contain">contain</option>' +
						'<option value="percentage">(%, %)</option>' +
						'<option value="normal">normal</option>' +
					'</select>' +
					'<span id="bg_fit_beforeafter_wrap">' +
						'<input type="text" id="bg_fit_x_beforeafter" name="bg_fit_x_beforeafter" class="beforeafter-small-input beforeafter-source-changeable" value="{{bgfitx}}">' + 
						'<input type="text" id="bg_fit_y_beforeafter" name="bg_fit_y_beforeafter" class="beforeafter-small-input beforeafter-source-changeable" value="{{bgfity}}">' + 
					'</span>' +
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Background Position:</label> ' + 
					'<select name="bg_position_beforeafter" id="slide_bg_position_beforeafter" class="beforeafter-change-container beforeafter-source-option beforeafter-source-changeable" data-container="bg_pos_beforeafter_wrap" data-styling="position">' +
						'<option value="center top">center top</option>' +
						'<option value="center right">center right</option>' +
						'<option value="center bottom">center bottom</option>' +
						'<option value="center center">center center</option>' +
						'<option value="left top">left top</option>' +
						'<option value="left center">left center</option>' +
						'<option value="left bottom">left bottom</option>' +
						'<option value="right top">right top</option>' +
						'<option value="right center">right center</option>' +
						'<option value="right bottom">right bottom</option>' +
						'<option value="percentage">(x%, y%)</option>' +
					'</select>' +
					'<span id="bg_pos_beforeafter_wrap">' +
						'<input type="text" id="bg_position_x_beforeafter" name="bg_position_x_beforeafter" class="beforeafter-small-input beforeafter-source-changeable" value="{{bgposx}}">' + 
						'<input type="text" id="bg_position_y_beforeafter" name="bg_position_y_beforeafter" class="beforeafter-small-input beforeafter-source-changeable" value="{{bgposy}}">' + 
					'</span>' +
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Background Repeat:</label> ' + 
					'<select name="bg_repeat_beforeafter" id="slide_bg_repeat_beforeafter" class="beforeafter-source-option beforeafter-source-changeable" data-styling="repeat">' +
						'<option value="no-repeat" selected="selected">no-repeat</option>' +
						'<option value="repeat">repeat</option>' +
						'<option value="repeat-x">repeat-x</option>' +
						'<option value="repeat-y">repeat-y</option>' +
					'</select>' +
				
				'</div>' + 
				
			'</div>' + 
			
			'<div id="beforeafter_video_settings" class="before-after-source-settings">' + 
			
				'<div id="beforeafter_video_force_cover" class="bg-settings-block">' + 
				
					'<label>Force Cover:</label>' + 
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Dotted Overlay:</label>' + 
					'<select id="video_dotted_overlay_beforeafter" name="video_dotted_overlay_beforeafter">' + 
						'<option value="none">none</option>' + 
						'<option value="twoxtwo">2 x 2 Black</option>' + 
						'<option value="twoxtwowhite">2 x 2 White</option>' + 
						'<option value="threexthree">3 x 3 Black</option>' + 
						'<option value="threexthreewhite">3 x 3 White</option>' + 
					'</select>' +
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Aspect Ratio:</label>' + 
					'<select id="video_ratio_beforeafter" name="video_ratio_beforeafter">' + 
						'<option value="16:9">16:9</option>' +
						'<option value="4:3">4:3</option>' +
					'</select>' +
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Start At:</label>' + 
					'<input type="text" id="video_start_at_beforeafter" name="video_start_at_beforeafter" value="{{startat}}"> For Example: 00:17' + 
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>End At:</label>' + 
					'<input type="text" id="video_end_at_beforeafter" name="video_end_at_beforeafter" value="{{endat}}"> For Example: 02:17' + 
				
				'</div>' + 
				
				'<div class="bg-settings-block">' + 
				
					'<label>Loop Video:</label>' + 
					'<select id="video_loop_beforeafter" name="video_loop_beforeafter">' + 
						'<option value="none">Disable</option>' + 
						'<option value="loop">Loop, Slide is paused</option>' + 
						'<option value="loopandnoslidestop">Loop, Slide does not stop</option>' + 
					'</select>' +
				
				'</div>' + 
				
				'<div id="beforeafter_video_nextslide" class="bg-settings-block">' + 
				
					'<label>Next Slide on End:</label>' + 
				
				'</div>' + 
				
				'<div id="beforeafter_video_force_rewind" class="bg-settings-block">' + 
				
					'<label>Rewind at Slide Start:</label>' + 
				
				'</div>' + 
				
				'<div id="beforeafter_video_mute" class="bg-settings-block">' + 
				
					'<label>Mute Video:</label>' + 
				
				'</div>' + 
				
				'<div class="youtube-beforeafter vimeo-beforeafter bg-settings-block">' + 
				
					'<label>Video Volume:</label>' + 
					'<input type="text" id="video_volume_beforeafter" name="video_volume_beforeafter" value="{{videovolume}}">' + 
				
				'</div>' + 
				
				'<div class="youtube-beforeafter bg-settings-block">' + 
				
					'<label>Video Speed:</label>' + 
					'<select id="video_speed_beforeafter" name="video_speed_beforeafter">' + 
						'<option value="0.25">0.25</option>' +
						'<option value="0.50">0.50</option>' +
						'<option value="1">1</option>' +
						'<option value="1.5">1.5</option>' +
						'<option value="2">2</option>' +
					'</select>' +
				
				'</div>' + 
				
				'<div class="youtube-beforeafter bg-settings-block">' + 
				
					'<label>Arguments YouTube:</label>' + 
					'<input type="text" id="video_arguments_beforeafter" class="beforeafter-video-param" name="video_arguments_beforeafter" value="{{youtubeparams}}">' + 
				
				'</div>' + 
				
				'<div class="vimeo-beforeafter bg-settings-block">' + 
				
					'<label>Arguments Vimeo:</label>' + 
					'<input type="text" id="video_arguments_vim_beforeafter" class="beforeafter-video-param" name="video_arguments_vim_beforeafter" value="{{vimeoparams}}">' + 
				
				'</div>' + 
			
			'</div>' + 
		
		'</div>';
	
	function read(obj) {
		
		if(!obj) return; 
		
		var settings = obj[title] || $.extend({}, layerDefaults),
			value;
		
		for(var prop in settings) {
			
			if(!settings.hasOwnProperty(prop)) continue;
			
			value = settings[prop];
			plug.find('*[name=' + slug + prop + ']').val(value);
			
		}
		
		references = obj.references;
		setupLayer(obj);
		
		globals.each(resetValues);
		settingsInput.val(JSON.stringify(globalSettings));
		
	}
	
	function write(obj) {
		
		var settings = obj[title] || $.extend({}, layerDefaults);

		options.each(function() {
			
			settings[this.getAttribute('name').split(slug)[1]] = this.value;
			
		});
		
		obj[title] = settings;
		setupLayer(obj);
		
	}
	
	function resetValues() {
		
		this.value = this.getAttribute('data-beforeafter-value');
		
	}
	
	function readGlobals() {
		
		var val = this.type !== 'checkbox' ? this.value : this.hasAttribute('checked') ? 'true' : 'false';
		globalSettings[this.getAttribute('name').replace('beforeafter_', '')] = val;
		
	}
	
	function globalChange() {
		
		this.setAttribute('data-beforeafter-value', this.value);
		globals.each(readGlobals);
		settingsInput.val(JSON.stringify(globalSettings));
	
	}
	
	function setupLayer(obj) {
		
		var pos = obj.hasOwnProperty('beforeafter') ? obj.beforeafter.position : layerDefaults.position;
		if(pos === 'before') baIcon.style.transform = 'none';
		else baIcon.style.transform = 'rotate(180deg)';
		
		var layr = references.quicklayer;
		if(layr.length) layr.removeClass('beforeafter-before beforeafter-after').addClass('beforeafter beforeafter-' + pos);
		
		layr = references.sorttable.layer.find('.mastertimer-timeline-zindex-row');
		if(layr.length) layr.removeClass('beforeafter-before beforeafter-after').addClass('beforeafter beforeafter-' + pos);
		
	}
	
	function timeEach(i) {
			
		var $this = $(this),
			val = $this.val(),
			perc = "";
		
		if(!val) {
			
			val = '50%';
			$this.val('50%');
			
		}
		
		if(val.search('%') !== -1) {
			
			perc = '%';
			val = val.replace('%', '');
			
			if(isNaN(val)) val = '50';
			else val = Math.min(Math.max(0, val), 100);
			
		}
		else {
			
			perc = /[0-9]*\.?[0-9]+(px)?/.test(val) ? 'px' : '';
			
		}
		
		val = parseInt(val, 10);
		if(isNaN(val)) {
			
			val = 50;
			perc = '%';
			
		}
		
		val += perc;
		$this.val(val);
		
		if(i !== 0) speeds += '|';
		speeds += val;
		
	}
	
	function toggleVisibility() {
		
		$('.beforeafter-visibility.selected').removeClass('selected');
		this.className = 'beforeafter-visibility selected';
		
		view = this.id.split('beforeafter-visibility-')[1];
		$('.quicksortlayer').each(setVisibility);
		
	}
	
	function setVisibility() {
		
		var $this = $(this),
			position = $this.hasClass('beforeafter-before') ? 'before' : 'after';
		
		$this = $this.find('.quick-layer-view');
		
		switch(view) {
			
			case 'all':
			
				if($this.hasClass('in-off')) $this.click();
			
			break;
			
			case 'before':
				
				if(position === 'before') {
					
					if($this.hasClass('in-off')) $this.click();
					
				}
				else {
					
					if(!$this.hasClass('in-off')) $this.click();
					
				}
			
			break;
			
			case 'after':
				
				if(position === 'after') {
					
					if($this.hasClass('in-off')) $this.click();
					
				}
				else {
					
					if(!$this.hasClass('in-off')) $this.click();
					
				}
				
			break;
			
		}

	}
	
	function checkValue() {
		
		var val = parseInt(this.value, 10);
		if(!isNaN(val)) this.value = val;
		else this.value = this.getAttribute('data-default-value');
		
	}
	
	function checkEnabled() {
		
		if(this.checked) bodies.addClass('before-after-enabled');
		else bodies.removeClass('before-after-enabled');
		
	}
	
	function onBounceChange() {
					
		var display = this.value !== "none" ? "block" : "none";
		document.getElementById("beforeafter-bounce-options").style.display = display;
		
	}
	
	function initSettings() {
		
		var lang = RsAddonBeforeAfter.lang;
		layerDefaults = RsAddonBeforeAfter.layers;
		globalSettings = RsAddonBeforeAfter.globals;
		
		plug = $('#beforeafter-addon-wrap');
		baIcon = document.getElementById('beforeafter-icon');
		options = $('#beforeafter-layer-settings').find('input, select');
		globals = $('.beforeafter-globals').find('input, select').not('.beforeafter-pos').on('change', globalChange);
		
		settingsInput = $("<input name='beforeafter_globals' type='hidden' value='" + JSON.stringify(globalSettings) + "' />");
		document.getElementById('form_slide_params').appendChild(settingsInput[0]);
		document.getElementById('beforeafter_bouncearrows').addEventListener('change', onBounceChange);
		
		var shell = '<span class="beforeafter-visibility tipsy_enabled_top" id="beforeafter-visibility-',
			showBefore = $(shell + 'before" original-title="' + lang.before + '" />'),
			showAfter = $(shell + 'after" original-title="' + lang.after + '" />'),
			showAll = $(shell + 'all" original-title="' + lang.all + '" />');
			
		$([showBefore[0], showAfter[0], showAll[0]]).insertAfter($('#layer-short-toolbar .quick-layer-lock'))
													.tipsy({gravity: 's', delayIn: 70})
													.on('click', toggleVisibility);
													
		$('.before-after-input').on('focusout', checkValue);
		timings = $('#beforeafter_moveto');
		times = $('.beforeafter-moveto').on('focusout', function() {
			
			speeds = '';
			times.each(timeEach);
			timings.val(speeds).change();
			
		});
		
		bodies = $('body').on('click', '.ui-dialog-titlebar-close', removeObjEvent);
		var enabled = $('#beforeafter_enabled').on('change', checkEnabled);
		checkEnabled.call(enabled[0]);
		
	}
	
	function updateBgPerc(nam, prop) {
		
		var perc1 = document.getElementById('bg_' + nam + '_x_beforeafter').value,
			perc2 = document.getElementById('bg_' + nam + '_y_beforeafter').value;
			
		perc1 = parseInt(perc1, 10);
		perc2 = parseInt(perc2, 10);
		
		if(isNaN(perc1)) perc1 = nam === 'fit' ? '100' : '0';
		if(isNaN(perc2)) perc2 = nam === 'fit' ? '100' : '0';
		
		layersBg.css('background-' + prop, perc1 + '% ' + perc2 + '%');
		
	}
	
	function sourceSettingChange() {
		
		var prop = this.getAttribute('data-styling'),
			val = this.value,
			nam = this.name;
		
		if(nam.search(/x_beforeafter|y_beforeafter/) !== -1) {
			
			val = 'percentage';
			prop = nam.search('fit') !== -1 ? 'size' : 'position';
			
		}
		
		if(val === 'percentage') {
			
			if(prop === 'size') updateBgPerc('fit', 'size');
			else updateBgPerc('position', 'position');
			return;
			
		}
		else if(prop === 'size' && val === 'normal') {
			
			val = 'auto';
			
		}
		
		layersBg.css('background-' + prop, val);
		
	}	
	
	function bgChange() {
		
		layersBg.css('background', '');
		afterImage.css('background', '').removeClass('beforeafter-transparent beforeafter-image beforeafter-video');
		
		youtubeSets.hide();
		vimeoSets.hide();
		
		var val = this.value,
			hide,
			show;
			
		switch(val) {
			
			case 'image':
				
				hide = 'video';
				show = 'image';
				
				imageSettings.show();
				afterImage.addClass('beforeafter-image');
				
				var imgVal = bgImageUrl.value;
				if(imgVal) {
					
					afterImage.css('background', 'url(' + imgVal + ')');
					layersBg.css('background-image', 'url(' + imgVal + ')');
					sourceOptions.each(sourceSettingChange);
					
				}
			
			break;
			
			case 'solid':
				
				var value = color.data('gradient') || color.val();
				if(value.search('&') !== -1) value = color.data('color');
				
				afterImage.css('background', value);
				layersBg.css('background', value);
			
			break;
			
			case 'trans':
				
				afterImage.addClass('beforeafter-transparent');
			
			break;
			
			case 'external':
				
				hide = 'video';
				show = 'image';
				
				imageSettings.hide();
				afterImage.addClass('beforeafter-image');
				
				var external = document.getElementById('slide_bg_external_beforeafter');
				if(external.value) {
					
					afterImage.css('background', 'url(' + external.value + ')');
					layersBg.css('background-image', 'url(' + external.value + ')');
					sourceOptions.each(sourceSettingChange);
					
				}
			
			break;
			
			case 'youtube':
			
				hide = 'image';
				show = 'video';
				youtubeSets.show();
				afterImage.addClass('beforeafter-video');
			
			break;
			
			case 'vimeo':
			
				hide = 'image';
				show = 'video';
				vimeoSets.show();
				afterImage.addClass('beforeafter-video');
			
			break;
			
			case 'html5':
			
				hide = 'image';
				show = 'video';
				afterImage.addClass('beforeafter-video');
			
			break;
			
		}
		
		var id = this.getAttribute('data-container'),
			container = document.getElementById(id);	
		
		if(!bgs) bgs = $('.bgsrcchanger-div-beforeafter');
		bgs.hide();
		
		if(container) container.style.display = 'inline-block';
		if(hide) {
			
			sourceSettings.removeClass('hide-beforeafter-itm');
			document.getElementById('beforeafter_' + hide + '_settings').style.display = 'none';
			document.getElementById('beforeafter_' + show + '_settings').style.display = 'block';
			
		}
		else {
			
			sourceSettings.addClass('hide-beforeafter-itm');
			
		}
		
	}
	
	function onSource() {
		
		var itm = $('.rs-layer-main-image-tabs .selected').removeClass('selected'),
			$this = $(this).addClass('selected');
		
		document.getElementById(itm.attr('data-content').replace('#', '')).style.display = 'none';
		document.getElementById($this.attr('data-content').replace('#', '')).style.display = 'inline-block';
		
	}
	
	function updateImages(url, id) {
		
		bgImageId.value = id;
		bgImageUrl.value = url;
		imageSource.innerHTML = url;
		afterImage.css('background', 'url(' + url + ')');
		
	}
	
	function onImageWP(url, id) {
		
		updateImages(url, id);
		
	}
	
	function onVideoWP(url, id) {
		
		document.getElementById(videoInput).value = url;
		
	}
	
	function changeImageSource() {
		
		UniteAdminRev.openAddImageDialog(bgSources.lang.select_after_image, onImageWP);
		
	}
	
	function changeVideoSource() {
		
		videoInput = this.getAttribute('data-input');
		UniteAdminRev.openAddImageDialog(bgSources.lang.select_after_video, onVideoWP);
		
	}
	
	function onImageObj(e) {
			
		e.stopImmediatePropagation();
		
		if(!rs_plugin_validated) {
			
			show_premium_dialog('register-to-acess-object-library');
			$('#dialog_addobj').dialog('close');
			return;
			
		}
		
		var size,
			imgSize,
			$this = $(this),
			selection = $this.closest('.objadd-single-item'),
			src = selection.attr('data-origsrc');
		
		switch(this.getAttribute('data-s')) {
			
			case 'l':
				
				size = 'large';
				imgSize = '-75.jpg';
				
			break;
			
			case 'm':
				
				size = 'medium';
				imgSize = '-50.jpg';
			
			break;
			
			default:
				
				size = 'original';
				imgSize = '.jpg';
			
			// end default
			
		}
		
		if(src.search('/') === -1) {
		
			UniteAdminRev.ajaxRequest('load_library_object', {
				
				'handle': src, 
				'type': 'orig'
				
			}, function onAjax(response) {
			
				if(response && response.success && response.url) updateImages(response.url, '');
				else console.log('BeforeAfter Object Library image failed to load');
				
			});
			
		}
		else {
			
			updateImages(src, '');
			
		}
		
		$('#dialog_addobj').dialog('close');
			
	}
	
	function onEnter() {
		
		$('.obj-item-size-selector').off('click.beforeafter').one('click.beforeafter', onImageObj);
		
	}
	
	function removeObjEvent() {
		
		bodies.off('.beforeafter');
		
	}
	
	function changeObjSource() {
		
		setExampleButtons();			
		UniteLayersRev.callObjectLibraryDialog('background');
		bodies.off('mouseover.beforeafter').one('mouseover.beforeafter', '.obj_lib_container_img', onEnter);
		
	}
	
	function onColorChange(input, color, clr) {
		
		if(typeof clr !== 'undefined') {
			
			if(color[0].id !== 'slide_bg_color_beforeafter') return;
			color = clr;
			
		}
		
		afterImage.css('background', color);
		layersBg.css('background', color);
		
	}
	
	function getExternal() {
		
		var external = document.getElementById('slide_bg_external_beforeafter'),
			val = external.value;
			
		if(val) {
			
			afterImage.css('background', 'url(' + val + ')');
			layersBg.css('background-image', 'url(' + val + ')');
			
		}
		
	}
	
	function updateShell(shell, sources) {
		
		var len = sources.length,
			source,
			reg;
			
		while(len--) {
			
			source = sources[len];
			reg = new RegExp('{{' + source + '}}', 'g');
			shell = shell.replace(reg, bgSources[source]);
			
		}
		
		return shell;
		
	}
	
	function changeContainer() {
		
		var display = this.value !== 'percentage' ? 'none' : 'inline-block';
		document.getElementById(this.getAttribute('data-container')).style.display = display;
		
	}
	
	function updateValue(id, prop) {
		
		var val = bgSources[prop];
		$('#' + id).val(val).children('option[value="' + val + '"]').attr('selected', true);
		
	}
	
	function onBgChange() {
		
		if(this.value === 'after') {
			
			viewIcon.style.transform = 'rotate(180deg)';
			layersBg.show();
			
		}
		else {
			
			viewIcon.style.transform = 'none';
			layersBg.hide();
			
		}
		
	}
	
	function initBgSources() {
		
		bgSources = RsAddonBeforeAfterBgSources;
		
		sourceShell = updateShell(sourceShell, ['id', 'image', 'color', 'youtube', 'vimeo', 'external', 'mpeg', 'webm', 'ogv']);
		settingsShell = updateShell(settingsShell, ['image', 'bgfitx', 'bgfity', 'bgposx', 'bgposy', 'startat', 'endat', 'videovolume', 'youtubeparams', 'vimeoparams']);
		
		var imgSizes = $('select[name="image_source_type"]');
		settingsShell = settingsShell.replace('{{sizes}}', imgSizes.html());
		
		$('#slide-main-image-settings-content').append([sourceShell, settingsShell]);
		
		var sizeValue = bgSources.imageSize || imgSizes.val(),
			sizes = $('#image_source_type_beforeafter').val(sizeValue);
			
		sizes.find('option:selected').attr('selected', false);
		sizes.val(sizeValue).children('option[value="' + sizeValue + '"]').attr('selected', true);
		
		updateValue('slide_bg_fit_beforeafter', 'bgFit');
		updateValue('slide_bg_position_beforeafter', 'bgPos');
		updateValue('slide_bg_repeat_beforeafter', 'bgRepeat');
		updateValue('video_dotted_overlay_beforeafter', 'overlay');
		updateValue('video_ratio_beforeafter', 'aspect');
		updateValue('video_loop_beforeafter', 'loopvideo');
		updateValue('video_speed_beforeafter', 'videoSpeed');
		
		$('.beforeafter-change-container').on('change', changeContainer).each(function() {$(this).change();});
		
		var menuItm = $('li[data-content="#mainbg-sub-source"]');
		menuItm.clone().attr('data-content', '#mainbg-sub-source-after')
					   .removeClass('selected').addClass('beforeafter-bgsource-menu')
					   .text('Source "After"').on('click', onSource).insertAfter(menuItm);
		
		menuItm = $('li[data-content="#mainbg-sub-setting"]');
		sourceSettings = menuItm.clone().attr('data-content', '#mainbg-sub-setting-after')
					   .attr('id', 'beforeafter-source-settings')
					   .removeClass('selected').addClass('beforeafter-bgsource-menu')
					   .text('Source Settings "After"').on('click', onSource).insertAfter(menuItm);
		
		color = $('#slide_bg_color_beforeafter').tpColorPicker({
			
			editing: 'Background "After" Color',
			wrapper: '<span class="rev-colorpickerspan">',
			onEdit: onColorChange
			
		});
		
		$(document).on('revcolorpickerupdate', onColorChange);
		$('#button_change_external_beforeafter').on('click', getExternal);
		$('.button_change_video_beforeafter').on('click', changeVideoSource);
		
		bgObj = $('#button_change_image_objlib_beforeafter').on('click', changeObjSource);
		bgImg = $('#button_change_image_beforeafter').on('click', changeImageSource);
		
		youtubeSets = $('.youtube-beforeafter');
		vimeoSets = $('.vimeo-beforeafter');
		
		imageSettings = $('.beforeafter-image-setting');
		imageSource = document.getElementById('the_image_source_url_beforeafter');
		
		var bgs = JSON.parse(bgSources.slideBgs),
			step = 0;
			
		$('.list_slide_links li').each(function() {
			
			// must be a regular slide menu item
			if(!this.id) return;
			
			var bg = bgs[step++];
			if(!bg.active) return;
			
			var img = $('<div class="beforeafter-bg-preview" />'),
				$this = $(this);
			
			switch(bg.type) {
				
				case 'image':
				
					if(bg.source) {
							
						img.addClass('beforeafter-image');
						img.css('background', 'url(' + bg.source + ')');
						
					}
			
				break;
				
				case 'solid':
					
					var val = bg.source;
					if(val.search('&') !== -1 && typeof RevColor !== 'undefined') val = RevColor.get(val);
					img.css('background', val);
				
				break;
				
				case 'trans':
					
					img.addClass('beforeafter-transparent');
				
				break;
				
				case 'external':
					
					if(bg.source) {
							
						img.addClass('beforeafter-image');
						img.css('background', 'url(' + bg.source + ')');
						
					}
				
				break;
				
			}
			
			if($this.hasClass('selected')) {
				
				img[0].id = 'beforeafter-bg-preview';
				afterImage = img;
			
			}
			
			var container = $this.addClass('before-after-preview').find('.slide-media-container').addClass('beforeafter-default-img');
			img.insertAfter(container);
			
		});
		
		if(!afterImage) {
			
			var selected = $('.list_slide_links li.selected').addClass('before-after-preview').find('.slide-media-container').addClass('beforeafter-default-img');
			afterImage = $('<div id="beforeafter-bg-preview" class="beforeafter-bg-preview" />').insertAfter(selected);
			
		}
		
		/*
		var kb = $('#kenburn_effect:checked');
		if(kb.length) kb.attr('checked', false).change();
		*/
		
		bgImageUrl = document.getElementById('image_url_beforeafter');
		bgImageId = document.getElementById('image_id_beforeafter');
		
		sourceOptions = $('.beforeafter-source-option');
		layersBg = $('<div id="beforeafter-editor-bg" />').insertBefore('#divLayers');
		
		viewIcon = document.getElementById('beforeafter-view-icon');
		document.getElementById('beforeafter_bg_view').addEventListener('change', onBgChange);
		
		$('.bgsrcchanger-beforeafter').on('change', bgChange);
		$('.beforeafter-source-changeable').on('change', sourceSettingChange);
		$('.bgsrcchanger-beforeafter[value="' + bgSources.bgType + '"]').attr('checked', true).change();
		
	}
	
	$(function() {
		
		if(typeof RsAddonBeforeAfter === 'undefined' || typeof RsAddonBeforeAfterBgSources === 'undefined' || typeof UniteAdminRev === 'undefined' || typeof UniteLayersRev === 'undefined') {
			
			// addon not enabled for this slider or something broke
			return;
			
		}
		
		initBgSources();
		initSettings();
		
		var callbacks = UniteLayersRev.addon_callbacks,
			fields = [],
			i = 0;
		
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
				
				references = obj.references;
				setupLayer(obj);
				return obj;
				
			}
			
		};
		
		for(var prop in layerDefaults) {
			
			if(layerDefaults.hasOwnProperty(prop)) {
			
				fields[i++] = 'beforeafter.' + prop;
				
			}
			
		}
		
		UniteLayersRev.attributegroups.push({
			
			id        : 'beforeafter', 
			icon      : 'arrow-combo', 
			groupname : 'BeforeAfter', 
			keys      : fields
			
		});
		
		var layrs = UniteLayersRev.arrLayers,
			obj;
		
		for(prop in layrs) {
			
			if(layrs.hasOwnProperty(prop)) {
				
				obj = layrs[prop];
				references = obj.references;
				setupLayer(obj);
				
			}
			
		}
		
	});
	
	$(window).on('load', function() {
		
		$('.beforeafter-settings-onoff').each(function() {
			
			var $this = $(this);
			document.getElementById($this.attr('data-placement')).appendChild($(this).closest('.tp-onoffbutton')[0]);
			
		});
		
	});


})(typeof jQuery !== 'undefined' ? jQuery : false);


















