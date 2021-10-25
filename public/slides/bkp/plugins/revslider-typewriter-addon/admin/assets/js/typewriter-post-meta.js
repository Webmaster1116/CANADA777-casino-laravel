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
	var lines,
		skeleton1,		
		skeleton2,		
		multiline;
	
	// document.ready
	$(function() {
		
		var box = $('#rs-addon-typewriter-meta .inside');
		lines = box.find('input[name=rs_addon_typewriter_meta]');
		
		if(!lines.length || !box.length) {
			
			console.log('Typewriter Meta Fields do not exist');
			return;
			
		}
		
		skeleton1 = '<div class="rs-addon-typerwriter-wrap">' + 
						'<input type="text" class="rs-addon-typewriter-line" placeholder="enter text..." value="';	
		skeleton2 = '" />' + 
						'<span class="rs-addon-typewriter-add-remove button-primary revblue">Add</span>' + 
						'<span class="rs-addon-typewriter-add-remove button-primary revred">Remove</span>' + 
					'</div>';
		
		multiline = $('<div id="rs-addon-typewriter-lines" />').appendTo(box).on(
		
			'click', '.rs-addon-typewriter-add-remove', handleMultilines
			
		).on('change', '.rs-addon-typewriter-line', writeLines);
		
		var multi = lines.val().split(','),
			len   = multi.length;
		
		// add fields if meta exists
		if(len && multi[0]) {
			
			for(var i = 0; i < len; i++) {
				
				$(skeleton1 + unescape(multi[i]) + skeleton2).appendTo(multiline);
				
			}
			
		}
		// create at least one field
		else {
			
			$(skeleton1 + skeleton2).appendTo(multiline);
			
		}
		
	});
	
	// add/remove lines
	function handleMultilines() {
		
		var $this = $(this);
		
		if($this.hasClass('revblue')) {
			
			$(skeleton1 + skeleton2).insertAfter($this.closest('div'));
			
		}
		else {
			
			if(multiline.children('.rs-addon-typerwriter-wrap').length > 1) {
			
				$this.closest('div').remove();
				
			}
			else {
				
				$this.closest('div').children('input').val('');
				
			}
			
			writeLines();
			
		}
		
	}
	
	// write meta data to hidden input field
	function writeLines() {
		
		var multi = [];
		
		$('.rs-addon-typewriter-line').each(function() {
			
			var txt = $.trim($(this).val());
			if(txt) multi[multi.length] = escape(txt);
			
		});
		
		lines.val(multi.join());
		
	}
	

})(typeof jQuery !== 'undefined' ? jQuery : false);












