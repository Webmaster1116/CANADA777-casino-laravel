///////////////////////////////
//	 INIT BACKUP SCRIPTS //
/////////////////////////////// 
jQuery("document").ready(function() {
	
	// CHECK EDITOR MODE
	var editor_view = jQuery('#form_slider_params').length>0 ? "slider_settings" : "slide_settings";
	
	if (editor_view==="slide_settings"){
		rs_backup_slide_init();
	}
});



/********************************************************************

	LAYER / SLIDE SETTINGS BACKEND jQUERY EXTENSION

**********************************************************************/

var rs_backup_slide_init = function() {
	if(typeof(rs_backup_loaded) === 'undefined') return false; //WILL BE WRITTEN BY admin/includes/slide.class.php DEPENDING IF BACKUP IS ENABLED/DISABLED
}
