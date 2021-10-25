/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

;(function($) {
	
	if(!$) return;
	$(function() {
		
		if(typeof RevAddonPaintBrushEnabled === 'undefined' ||
		   typeof setExampleButtons === 'undefined' || 
		   typeof UniteAdminRev === 'undefined' || 
		   typeof UniteLayersRev === 'undefined' || 
		   typeof rs_plugin_validated === 'undefined' || 
		   typeof show_premium_dialog === 'undefined') return;
		
		var bodies;
		
		function setPreview(url, custom) {
			
			if(!custom) {
				
				RevAddonPaintBrushEnabled.image = url;
				document.getElementById('paintbrush_img').value = url;
			
			}
			
			document.getElementById('paintbrush-bg-img').style.backgroundImage = 'url(' + url + ')';
			document.getElementById('paintbrush_bg_preview').style.display = 'inline-block';
			
		}
		
		function onEnter() {
			
			$('.obj-item-size-selector').off('click.beforeafter').one('click.beforeafter', onImageObj);
			
		}
		
		function removeObjEvent() {
			
			bodies.off('.paintbrush');
			
		}
		
		function onImg(url) {
			
			setPreview(url);
			
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
				
					if(response && response.success && response.url) setPreview(response.url);
					else console.log('PaintBrush Object Library image failed to load');
					
				});
				
			}
			else {
				
				setPreview(src);
				
			}
			
			$('#dialog_addobj').dialog('close');
				
		}
		
		document.getElementById('button_change_image_paintbrush').addEventListener('click', function() {
			
			UniteAdminRev.openAddImageDialog('Select Brush Image', onImg);
			
		});
		
		document.getElementById('button_change_image_objlib_paintbrush').addEventListener('click', function() {
			
			setExampleButtons();			
			UniteLayersRev.callObjectLibraryDialog('background');
			bodies.off('mouseover.paintbrush').one('mouseover.paintbrush', '.obj_lib_container_img', onEnter);
			
		});
		
		$('#paintbrush_enabled').on('change', function() {
			
			if(this.checked) bodies.addClass('paintbrush-enabled');
			else bodies.removeClass('paintbrush-enabled');
			
		});
		
		$('.paintbrush-min-max').on('change', function() {
		
			var val = this.value;
			if(val === '') val = 0;
			
			val = Math.max(parseInt(this.getAttribute('data-min'), 10), 
				  Math.min(parseInt(this.getAttribute('data-max'), 10), parseInt(val, 10)));
			
			if(!isNaN(val)) this.value = val;
			else this.value = this.getAttribute('data-default-value');
			
		});
		
		var bg = $('.paintbrush-bg').on('change', function() {
			
			$('.bgsrcchanger-div-paintbrush').hide();
			
			var container = this.getAttribute('data-container');
			if(!container) return;
			
			var val,
				tpe = this.value;
				
			document.getElementById(container).style.display = 'inline-block';
			
			switch(tpe) {
			
				case 'local':
				
					val = document.getElementById('paintbrush_img').value;
					if(val) setPreview(RevAddonPaintBrushEnabled.image);
				
				break;
				
				case 'main':
					
					var img = document.getElementById('radio_back_image');	
					if(img && img.checked) val = document.getElementById('image_url');	
					if(val) {
					
						val = val.value;
						if(val) setPreview(val, true);
						
					}
					
				break;
			
			}
			
		});
		
		$('li[data-content="#slide-addon-wrapper"]').on('click', function() {
			
			$('.paintbrush-bg:checked').change();
			
		});
		
		bodies = $('body').on('click', '.ui-dialog-titlebar-close', removeObjEvent);
		if(RevAddonPaintBrushEnabled.enabled) bodies.addClass('paintbrush-enabled');
		
	});

	$(window).on('load', function() {
		
		var items = $('.paintbrush-menu-item').off().on('click', function() {
			
			items.removeClass('selected');
			this.className = 'paintbrush-menu-item selected';
			
			$('.paintbrush-container').hide();
			document.getElementById(this.getAttribute('data-content')).style.display = 'block';
			
		});
		
	});
		
})(typeof jQuery !== 'undefined' ? jQuery : false);