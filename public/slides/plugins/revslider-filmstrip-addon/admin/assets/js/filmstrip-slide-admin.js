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
	
	var addNew,
		speeds,
		slider,
		bodies,
		itmSets,
		settings,
		imgDialog,
		customAlt,
		externalUrl,
		supressEvents,
		shell = '<div class="filmstrip-item" data-url="[url]" data-ids="[ids]" data-type="[type]" data-size="[size]" data-thumb="[thumb]" data-alt="[alt]" data-custom="">' + 
					'<img src="[thumb]" />' + 
					'<span class="filmstrip-item-toolbar">' + 
						'<i class="eg-icon-cog filmstrip-edit-settings revblack"></i><i class="eg-icon-trash filmstrip-delete-item revred"></i>' + 
					'</span>' + 
				'</div>';
	
	function newItem(img, thumb, id, type, size) {
		
		var alt, ids;
		switch(type) {
			
			case 'wpimage':
				
				ids = id;
				alt = FilmStripSettings.alt;
			
			break;
			
			case 'objlib':
				
				ids = '';
				alt = FilmStripSettings.objAlt;
			
			break;
			
			case 'external':
				
				ids = '';
				size = '';
				alt = FilmStripSettings.objAlt;
			
			break;
			
		}
		
		$(shell.replace('[url]', img)
			   .replace('[ids]', ids)
			   .replace('[alt]', alt)
			   .replace('[size]', size)
			   .replace('[type]', type)
			   .replace(/\[thumb\]/g, thumb)).insertBefore(addNew).click();

		updateData();
		
	}
	
	function updateData() {
		
		var ar = [];
		$('.filmstrip-item').each(function(i) {
			
			ar[i] = {
				
				url: this.getAttribute('data-url') || '',
				ids: this.getAttribute('data-ids') || '',
				alt: this.getAttribute('data-alt') || '',
				type: this.getAttribute('data-type') || '',
				size: this.getAttribute('data-size') || '',
				thumb: this.getAttribute('data-thumb') || '',
				custom: this.getAttribute('data-custom') || ''
				
			};
			
		});
		
		if(ar.length) {
			
			settings.value = JSON.stringify(ar);
			toggleClass(true, 'filmstrip-has-item', itmSets);
			
		}
		else {
			
			settings.value = '';
			toggleClass(false, 'filmstrip-has-item', itmSets);
			
		}
		
	}
	
	function onImageWP(url, id) {
		
		newItem(url, url, id, 'wpimage', FilmStripSettings.size);
		
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
			src = getAttribute(selection, 'data-origsrc');
		
		switch(this.getAttribute('data-s')) {
			
			case 'l':
				
				size = 'large';
				imgSize = '-75.jpg'
				
			break;
			
			case 'm':
				
				size = 'medium';
				imgSize = '-50.jpg';
			
			break;
			
			default:
				
				size = 'original'
				imgSize = '.jpg';
			
			// end default
			
		}
		
		if(src.search('/') === -1) {
		
			UniteAdminRev.ajaxRequest('load_library_object', {
				
				'handle': src, 
				'type': 'orig'
				
			}, function onAjax(response) {
			
				if(response && response.success && response.url) {					
					
					var url = response.url;
					setAttribute(selection, 'data-origsrc', url);
					newItem(url.replace('.jpg', imgSize), url.replace('.jpg', '-10.jpg'), false, 'objlib', size);
					
				}
				else {
					
					console.log('Filmstrip Object Library image failed to load');
					
				}
				
			});
			
		}
		else {
			
			newItem(src.replace('.jpg', imgSize), src.replace('.jpg', '-10.jpg'), false, 'objlib', size);
			
		}
		
		$('#dialog_addobj').dialog('close');
			
	}
	
	function onImageExternal() {
		
		var src = externalUrl.value;
		if(src && src.search('http') !== -1) {
		
			newItem(src, src, false, 'external');
			imgDialog.dialog('close');
			
		}
		
	}	
	
	function onEnter() {
		
		$('.obj-item-size-selector').off('click.filmstrip').one('click.filmstrip', onImageObj);
		
	}
	
	function timeEach(i) {
		
		var val = this.value;	
		if(!val || val === '0') {
				
			val = '30';
			this.value = '30';
			
		}
		
		if(i !== 0) speeds += ',';
		speeds += parseInt(val, 10) || '30';
		
	}
	
	function sanitizeEl(el) {
		
		if(el instanceof $) {
			
			if(!el.length) return false;
			el = el[0];
			
		}
		
		return el || false;
		
	}
	
	function toggleClass(add, clas, el) {
		
		el = sanitizeEl(el);
		if(!el) return;
		
		var cl = el.className || '';
		if(add) {
			
			var space = cl ? ' ' : '';
			el.className = cl + space + clas;
			
		}
		else {

			var reg = new RegExp(clas, 'g');
			el.className = cl.replace(reg, '').trim();
			
		}
		
	}
	
	function hasClass(el, clas) {
		
		el = sanitizeEl(el);
		if(!el) return false;
		
		var cl = el.className || '';
		return cl.search(clas) !== -1;
		
	}
	
	function getAttribute(el, attr) {
		
		el = sanitizeEl(el);
		if(!el) return '';
		
		return el.getAttribute(attr) || '';
		
	}
	
	function setAttribute(el, attr, val) {
		
		el = sanitizeEl(el);
		if(!el) return;
		
		el.setAttribute(attr, val);
		
	}
	
	$(function() {
		
		if(typeof FilmStripSettings === 'undefined') return;
		
		var inited,
			content = $('#filmstrip-addon-content'),
			customAlt = $('#filmstrip_alt_custom'),
			timings = document.getElementById('filmstrip_times');
		
		
		itmSets = $('#filmstrip-item-settings');
		addNew = document.getElementById('filmstrip-add-new');
		settings = document.getElementById('filmstrip_settings');
		slider = document.getElementById('slide_main_settings_wrapper');		
		
		
		$('#filmstrip_addon_objlib').click(function() {
			
			setExampleButtons();			
			UniteLayersRev.callObjectLibraryDialog('background');
			bodies.off('mouseover.filmstrip').one('mouseover.filmstrip', '.obj_lib_container_img', onEnter);
			
		});
		
		$('#filmstrip_addon_wpimage').click(function() {
			
			UniteAdminRev.openAddImageDialog(rev_lang.select_layer_image, onImageWP);
			
		});
		
		$('#filmstrip_addon_external').click(function() {
			
			if(!imgDialog) {
				
				externalUrl = document.getElementById('filmstrip_external_url');
				imgDialog = $('#filmstrip_external_image').dialog({
				
					width: 700,
					modal: true,
					autoOpen: false,
					resizable: false,
					closeOnEscape: true,
					buttons: {'Load Image': onImageExternal},
					create: function(ui) {
						
						var tar = $(ui.target).parent().find('.ui-dialog-titlebar');
						toggleClass(true, 'tp-slider-new-dialog-title', tar);
						
					}
					
				});
				
			}
			
			externalUrl.value = '';
			imgDialog.dialog('open');
			
		});
		
		$('#button_change_image_objlib, #button_add_object_layer').on('click', function() {
			
			bodies.off('.filmstrip');
			
		});
		
		$('#filmstrip_enabled').change(function() {
			
			if(this.checked) {
			
				content[0].style.display = 'block';
				toggleClass(true, 'filmstrip-addon-toggle', slider);
				
			}
			else {
				
				content[0].style.display = 'none';
				toggleClass(false, 'filmstrip-addon-toggle', slider);
				
			}
			
		});
		
		$('.filmstrip-item-option').change(function() {
			
			if(supressEvents) return;
			
			var val = this.value || '',
				setting = getAttribute(this, 'data-setting'),
				selected = $('.filmstrip-item-selected');
				
			setAttribute(selected, 'data-' + setting, val);
			if(setting !== 'size') {
					
				updateData();
				return;
				
			}
			
			// update urls based on size selection
			if(getAttribute(selected, 'data-type') === 'objlib') {
				
				var imgSize;
				switch(val) {
						
					case 'large':	
						imgSize = '-75.jpg';
					break;
					
					case 'medium':
						imgSize = '-50.jpg';
					break;
					
					case 'small':
						imgSize = '-25.jpg';
					break;
					
					case 'thumb':
						imgSize = '-10.jpg';
					break;
					
					default:
						imgSize = '.jpg'
					// end default
					
				}
				
				var url = getAttribute(selected, 'data-url').replace(/\-75|\-50|\-25|\-10|\.jpg/gi, '');
				setAttribute(selected, 'data-url', url + imgSize);
				
			}
			
			updateData();
			
		});
		
		$('.filmstrip-option-alt').change(function() {
			
			if(supressEvents) return;
			var custom = $this.next();
			custom[this.value === 'custom' ? 'show' : 'hide']();
			
		});
		
		var toggle = $('#filmstrip-toggle-settings').click(function() {
			
			if(!hasClass(toggle, 'filmstrip-toggle-active')) {
				
				toggleClass(true, 'filmstrip-toggle-active', toggle);
				itmSets.slideDown(200);
				
			}
			else {
				
				toggleClass(false, 'filmstrip-toggle-active', toggle);
				itmSets.slideUp(200);
				
			}
			
		});
		
		var times = $('.filmstrip-speed').change(function() {
			
			speeds = '';
			times.each(timeEach);
			timings.value = speeds;
			
		});
		
		bodies = $('body').on('click', '.filmstrip-item', function(e) {
			
			if(inited && hasClass(this, 'filmstrip-item-selected') || this.id === 'filmstrip-add-new') return;
			
			if(customAlt.is(':focus')) customAlt.change();
			supressEvents = true;
			
			toggleClass(false, 'filmstrip-item-selected', $('.filmstrip-item-selected'));
			toggleClass(true, 'filmstrip-item-selected', this);
			
			var curOptions = $('#filmstrip-' + getAttribute(this, 'data-type') + '-options'),
				alt = getAttribute(this, 'data-alt');
			
			// update form selections
			curOptions.find('.filmstrip-option-alt').val(alt);
			curOptions.find('.filmstrip-option-size').val(getAttribute(this, 'data-size'));
			
			customAlt = curOptions.find('.filmstrip-option-custom');
			customAlt.val(getAttribute(this, 'data-custom'));
			customAlt[alt === 'custom' ? 'show' : 'hide']();
			
			if(!curOptions.is(':visible')) {
				
				$('.filmstrip-item-options').hide();
				curOptions.show();
				
			}
			
			inited = true;
			supressEvents = false;
			
		}).on('click', '.filmstrip-delete-item', function(e) {
			
			e.stopImmediatePropagation();
			if(window.confirm(rev_lang.really_want_to_delete)) {
 			
				$(this).closest('.filmstrip-item').remove();
				$('.filmstrip-item-options').hide();
				updateData();
				
			}

		}).on('click', '.filmstrip-edit-settings', function(e) {
			
			e.stopImmediatePropagation();
			
			var itm = $(this).closest('.filmstrip-item');
			if(!hasClass(itm, 'filmstrip-item-selected')) {
				
				itm.click();
				if(!hasClass(toggle, 'filmstrip-toggle-active')) toggle.click();
				
			}
			else {
				
				toggle.click();
				
			}
			
		});
		
		content.sortable({
				
			items: '.filmstrip-item',
			stop: updateData
			
		}).disableSelection();
		
		if(FilmStripSettings.enabled) {
			
			toggleClass(true, 'filmstrip-addon-toggle', slider)
			$('li[data-content="#slide-addon-wrapper"]').click();
			$('#rs-addon-settings-trigger-filmstrip').click();
			
		}
		
		var items = $('.filmstrip-item-selected').click();
		if(items.length) toggleClass(true, 'filmstrip-has-item', itmSets);
		
	});


})(typeof jQuery !== 'undefined' ? jQuery : false);




