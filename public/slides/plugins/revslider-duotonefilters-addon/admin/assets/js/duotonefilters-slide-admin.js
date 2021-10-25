/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

;(function($) {
	
	if(!$) return;
	
	var reg,
		slotholder,
		instagrm,
		field;
	
	function onDuotoneClick() {
					
		$('.rsaddon-duotone-filter.selected').removeClass('selected');
		
		if(!slotholder) slotholder = $('#divbgholder').find('.slotholder');
		slotholder.attr('class', '');
			
		if(!instagrm) instagrm = $('#inst-filter-grid').find('.filter_none');
		instagrm.click().removeClass('selected');
		
		var $this = $(this).addClass('selected'),
			filter = $this.attr('data-filter');
			// clas = slotholder.attr('class');
		
		slotholder.addClass(filter);
		if(!field) field = document.getElementById('duotonefilter_addon');
		field.value = filter;
		
	}
	
	function onInstagrmClick() {
		
		$('.rsaddon-duotone-filter.selected').removeClass('selected');
		slotholder.attr('class', '');
		
		if(!field) field = document.getElementById('duotonefilter_addon');
		field.value = 'rs-duotone-none';
			
	}
	
	function removeClasses() {
					
		var classes = slotholder.attr('class').match(reg) || [];
		return classes.join(' ');
		
	}
	
	function addEvents() {
		
		$('.rsaddon-duotone-filter').on('click', onDuotoneClick);
		$('.inst-filter-griditem').on('click', onInstagrmClick);
		
	}
	
	$(function() {
		
		if(typeof RsAddonDuotone === 'undefined') return;
		reg = new RegExp(RsAddonDuotone.reg);
		
		$('#rs-addon-settings-trigger-duotonefilters').closest('.rs-layer-toolbar-box').remove();
		if($('#rs-addon-wrapper-button-row').children('.rs-layer-toolbar-box').length === 1) {
			
			$('li[data-content="#slide-addon-wrapper"]').hide();
			
		}
		
		var filters = $('#inst-filter-grid');
		$('#duotonefilters-addon-settings-wrap').children().appendTo(filters);
		
		// the default filter items have right-margins alternating by 1 additional pixel for every other item
		// this removes the white-space for uniformity when the new filters are added
		filters.html(filters.html().replace(/>\s+</g, '><'));		
		
		if(RsAddonDuotone.selected !== 'rs-duotone-none') {

			setTimeout(function() {
				
				instagrm = $('#inst-filter-grid').find('.filter_none').click().removeClass('selected');
				slotholder = $('#divbgholder');//.find('.slotholder');
				
				var clas = slotholder.attr('class');
				slotholder.attr('class', RsAddonDuotone.selected + ' ' + clas);
				addEvents();
				
			}, 500);
		
		}
		else {
		
			addEvents();
			
		}
		
	});
	
})(typeof jQuery !== 'undefined' ? jQuery : false);







