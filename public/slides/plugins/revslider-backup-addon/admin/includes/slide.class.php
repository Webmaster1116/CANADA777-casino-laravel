<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_backup_slide extends RevSliderAdmin {
	
	public static function init_backup(){
		
		add_filter('revslider_slide_settings_addons', array('rs_backup_slide', 'add_backup_settings'), 10, 3); //adds interface / menu to the Slide General Settings
		add_filter('revslider_admin_onAjaxAction_switch', array('rs_backup_slide', 'add_backup_ajax_functions'), 10, 6); //adds new ajax calls to the Slider
		
		add_action('revslider_slide_updateSlideFromData_post', array('rs_backup_slide', 'check_add_new_backup'), 10, 3); //hooks into the saving process of a Slide
		add_action('revslider_slide_updateStaticSlideFromData_post', array('rs_backup_slide', 'check_add_new_backup_static'), 10, 3); //hooks into the saving process of a Static Slide

		add_action('revslider_slide_deleteSlide', array('rs_backup_slide', 'delete_backup_full')); //hooks into the deletion process of a Slide
		add_action('revslider_slider_deleteAllSlides', array('rs_backup_slide', 'delete_backup_full_slider')); //hooks into the deletion process of a Slide
		
		self::create_tables(); //creates tables needed for this plugin to work
		
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			add_action('admin_enqueue_scripts', array('rs_backup_slide', 'wb_enqueue_styles'));
			add_action('admin_enqueue_scripts', array('rs_backup_slide', 'wb_enqueue_scripts'));
		}
	}
	
	public static function wb_enqueue_styles(){
		wp_register_style('revslider-backup-plugin-settings', RS_BACKUP_PLUGIN_URL . 'admin/assets/css/backup-admin.css', array(), RS_BACKUP_VERSION);
		wp_enqueue_style('revslider-backup-plugin-settings');
	}
	
	
	public static function wb_enqueue_scripts(){
		wp_register_script('revslider-backup-plugin-js', RS_BACKUP_PLUGIN_URL . 'admin/assets/js/backup-admin.js', array(), RS_BACKUP_VERSION);
		wp_enqueue_script('revslider-backup-plugin-js');
	}
	
	
	/**
	 * adds interface / menu to the Slide General Settings
	 * @since: 1.0.0
	 */
	public static function add_backup_settings($settings, $slide, $slider){
		
		$slide_id = ($slide->isStaticSlide()) ? 'static_'.$slider->getID() : $slide->getID();
		
		$markup = '<input type="hidden" id="rs-session-id" value="'. substr(md5(rand()), 0, 7) .'" />';
		$markup .= 
'<div class="slide-show-backups-wrapper"><div class="slide-show-backups rs-addon-backup-trigger-button" data-slideid="'.$slide_id.'">'.__("Show available Backups for this Slide",'rs_backup').'</div></div>

<div id="dialog_select_slide_backup" class="dialog_select_slide_backup" title="'.__('Select Backup', 'rs_backup').'" style="display:none;">
	<div id="rs-backup-wrapper">
		
	</div>
</div>';

		$settings['backup'] = array(
			'title'		=> __('Backup', 'rs_backup'),
			'markup'	=> $markup,
			'javascript'=> "var rs_backup_loaded = true;
			
jQuery('body').append('<form id=\"rs-backup-preview-form\" name=\"rs-backup-preview-form\" action=\"".RevSliderBase::$url_ajax_actions."\" target=\"rs-frame-preview\" method=\"post\"><input type=\"hidden\" id=\"rs-backup-client-action\" name=\"client_action\" value=\"\"><input type=\"hidden\" id=\"rs-nonce\" name=\"rs-nonce\" value=\"".wp_create_nonce("revslider_actions")."\"><!-- SPECIFIC FOR SLIDE PREVIEW --><input type=\"hidden\" name=\"data[id]\" value=\"\" id=\"preview-slide-backup-data\"><input type=\"hidden\" name=\"data[slide_id]\" value=\"\" id=\"preview-slide-backup-data-slide_id\"><!-- SPECIFIC FOR SLIDER PREVIEW --></form>');

jQuery('body').on('click', '.slide-show-backups', function(){
	var slideID = jQuery(this).data('slideid');
	
	var data = {'slideID':slideID};
	UniteAdminRev.ajaxRequest('fetch_slide_backups', data, function(response){
		jQuery('#rs-backup-wrapper').html('');
		
		if(response.slides !== undefined && response.slides.length > 0){
			for(var key in response.slides){
				jQuery('#rs-backup-wrapper').append('<div class=\"rs-backup-data-holder\" data-backup=\"'+response.slides[key]['id']+'\" data-slide_id=\"'+slideID+'\"><span class=\"rs-backup-time\"><span class=\"rs-backup-id\">'+response.slides[key]['id']+'.</span><i class=\"eg-icon-calendar\"></i>'+response.slides[key]['created']+'</span><span class=\"rs-load-backup\" style=\"float: right;\">".__('Load Backup', 'rs_backup')."</span><span class=\"rs-preview-backup\" style=\"float: right;\">".__('Preview Backup', 'rs_backup')."</span></div>');
			}
		}else{
			jQuery('#rs-backup-wrapper').append('<div class=\"nobackups\">".__('No backups found for the selected Slide', 'rs_backup')."</div>');
		}
		
		jQuery('#dialog_select_slide_backup').dialog({
			modal:true,
			width: 580,
			height:480,
			resizable:false,
			closeOnEscape:true,
			create:function(ui) {				
				jQuery(ui.target).parent().find('.ui-dialog-titlebar').addClass('tp-slider-new-dialog-title');
			}
		});
	});
	
});

jQuery('body').on('click', '.rs-close-preview', function(){
	jQuery('#dialog_preview_slide_backup').hide();
	var rs_form = jQuery('#rs-backup-preview-form');
	
	jQuery('#rs-backup-client-action').val('preview_slide_backup');
	jQuery(\"#preview-slide-backup-data\").val(\"empty_output\");
	jQuery(\"#preview-slide-backup-data-slide_id\").val(\"empty_output\");
	
	jQuery('#rs-preview-wrapper').hide();
	
	jQuery('.rs-temp-backup-holder').remove();
	
	rs_form.submit();
});

jQuery(document).keyup(function(e){
	if (e.keyCode == 27) jQuery('.rs-close-backup-preview').click(); // 27 == esc
});

jQuery(window).resize(function() {
	jQuery('.rs-preview-width').text(jQuery('.rs-frame-preview-wrapper').width());
	jQuery('.rs-preview-height').text(jQuery('.rs-frame-preview-wrapper').height());
});


jQuery('body').on('click', '.rs-load-backup', function(){
	var dh = jQuery(this).closest('.rs-backup-data-holder');
	if(confirm('".__('Restore Slide Backup from', 'rs_backup')." '+dh.find('.rs-backup-time').text()+'?')){
		
		jQuery('.rs-close-preview').click(); //to hide the overlay if it is open
		
		var backup_id = dh.data('backup');
		var slide_id = dh.data('slide_id');
		var session_id = jQuery('#rs-session-id').val();
		
		
		var data = {'id': backup_id, 'slide_id': slide_id, 'session_id': session_id};
		
		jQuery('#dialog_select_slide_backup').dialog('close');
		
		UniteAdminRev.ajaxRequest('restore_slide_backup', data, function(response){
			
		});
	}
});


jQuery('body').on('click', '.rs-preview-backup', function(){
	var dh = jQuery(this).closest('.rs-backup-data-holder');
	var backup_id = dh.data('backup');
	var slide_id = dh.data('slide_id');
	var backup_time = dh.find('.rs-backup-time').html();
	
	jQuery('#dialog_select_slide_backup').dialog('close');
	
	var rs_form = jQuery('#rs-backup-preview-form');
	
	//set action and data
	jQuery('#rs-backup-client-action').val('preview_slide_backup');
	jQuery('#preview-slide-backup-data').val(backup_id);
	jQuery('#preview-slide-backup-data-slide_id').val(slide_id);
	
	jQuery('#rs-preview-wrapper').show();
	
	//add apply button under the form
	jQuery('#rs-preview-wrapper').append('<div class=\"rs-backup-data-holder rs-temp-backup-holder\"  data-backup=\"'+backup_id+'\" data-slide_id=\"'+slide_id+'\"><span class=\"rs-backup-time\">'+backup_time+'</span><span class=\"rs-load-backup\">".__('Load Backup', 'rs_backup')."</span></div>');
	
	rs_form.submit();
	
	jQuery(window).trigger('resize');
});


var call_wb_saveEditSlide = {
	callback : function(data) {
		data.session_id = jQuery('#rs-session-id').val();
		return data;
	},		
	environment : 'saveEditSlide',
	function_position : 'data'

};


// ADD CALLBACKS
UniteLayersRev.addon_callbacks.push(call_wb_saveEditSlide);"
);
		
		return $settings;
	}
	
	
	/**
	 * adds ajax functions
	 * @since: 1.0.0
	 */
	public static function add_backup_ajax_functions($remove_error, $action, $data, $slider, $slide, $operations){
		
		switch($action){
			case 'fetch_slide_backups':
				$slide_id = $data['slideID'];
				$slide_data = self::fetch_slide_backups($slide_id, true);
				
				self::ajaxResponseData(array('slides' => $slide_data));
			break;
			case 'restore_slide_backup':
				$backup_id = intval($data['id']);
				$slide_id = $data['slide_id'];
				$session_id = esc_attr($data['session_id']);
				$response = self::restore_slide_backup($backup_id, $slide_id, $session_id, $slide);
				
				if($response !== true) RevSliderFunctions::throwError(__("Backup restoration failed...",'rs_backup'));
				
				$urlRedirect = self::getViewUrl(self::VIEW_SLIDE,"id=$slide_id");
				$responseText = __("Backup restored, redirecting...",'rs_backup');
				self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);
			break;
			case 'preview_slide_backup':
				//check if we are static or not
				
				$operations = new RevSliderOperations();
				
				ob_start();
				//first get data
				$backup_id = intval($data['id']);
				$slide_id = $data['slide_id'];
				
				if($backup_id == "empty_output"){
					echo '<div class="message_loading_preview">'.__("Loading Preview...",'rs_backup').'</div>';
					exit();
				}
				
				$my_data = self::fetch_backup($backup_id);
				
				$sliderID = $my_data['slider_id'];
				
				if(strpos($slide_id, 'static_') !== false){
					$my_data['slideid'] = $slide_id;
					
					add_filter('revslider_enable_static_layers', array('rs_backup_slide', 'disable_static_slide_for_preview'));
					
				}else{
					$my_data['slideid'] = $my_data['slide_id'];
				}
				
				$my_data['params'] = (array)json_decode($my_data['params']);
				$my_data['layers'] = (array)json_decode($my_data['layers']);
				$my_data['layers'] = RevSliderFunctions::convertStdClassToArray($my_data['layers']);
				$my_data['settings'] = (array)json_decode($my_data['settings']);
				
				
				//asort($my_data['layers']);
				
				$output = new RevSliderOutput();
				$output->setOneSlideMode($my_data);

				$operations->previewOutput($sliderID,$output);
				$html = ob_get_contents();
				
				ob_clean();
				ob_end_clean();
				
				//add button to apply the Backup
				//$html .= '<div >'.__('', 'rs_backup').'</div>';
				echo $html;
				exit;
				//self::ajaxResponseData(array('markup' => $html));
			break;
		}
		
		return $remove_error; // if true, then the script will just exit instead of printing an error
	}
	
	
	/**
	 * disable static layer in preview for static slide
	 * @since: 1.0.0
	 */
	public static function disable_static_slide_for_preview($do_static){
		return false;
	}
	
	
	/**
	 * fetch backup by backup_id
	 * @since: 1.0.0
	 */
	public static function fetch_backup($backup_id){
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'revslider_backup_slides'." WHERE id = %s", array($backup_id)), ARRAY_A);
		
		if(!empty($record)) $record = $record[0];
		
		return $record;
		
	}
	
	
	/**
	 * restore slide backup
	 * @since: 1.0.0
	 */
	public static function restore_slide_backup($backup_id, $slide_id, $session_id, $slide = false){
		global $wpdb;
		
		$backup = self::fetch_backup($backup_id);
		
		if($slide == false){
			$slide = new RevSliderSlide();
		}
		
		$current = $slide->getDataByID($slide_id);
		
		//update the current
		if(!empty($backup) && !empty($current)){
			
			//self::add_new_backup($current, $session_id);
			
			$current['params'] = $backup['params'];
			$current['layers'] = $backup['layers'];
			$current['settings'] = $backup['settings'];
			$update_id = $current['id'];
			unset($current['id']);
			
			if(strpos($slide_id, 'static_') !== false){
				$return = $wpdb->update(RevSliderGlobals::$table_static_slides, $current, array('id' => $update_id));
			}else{
				$return = $wpdb->update(RevSliderGlobals::$table_slides, $current, array('id' => $update_id));
			}
			//now change the backup date to current date, to set it to the last version
			$backup['created'] =  date('Y-m-d H:i:s');
			$update_id = $backup['id'];
			unset($backup['id']);
			
			$return1 = $wpdb->update($wpdb->prefix . 'revslider_backup_slides', $backup, array('id' => $update_id));
			
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * fetch all slide revisions by slide_id
	 * @since: 1.0.0
	 */
	public static function fetch_slide_backups($slide_id, $basic = false){
		global $wpdb;
		
		if(strpos($slide_id, 'static_') !== false){
			$slide = new RevSliderSlide();
			$slide_id = $slide->getStaticSlideID(str_replace('static_', '', $slide_id));
			$where = array($slide_id);
			$where[] = 'true';
		}else{
			$where = array($slide_id);
			$where[] = 'false';
		}
		
		if($basic){
			$record = $wpdb->get_results($wpdb->prepare("SELECT `id`, `slide_id`, `slider_id`, `created` FROM ".$wpdb->prefix . 'revslider_backup_slides'." WHERE slide_id = %s AND static = %s ORDER BY `created` ASC", $where),ARRAY_A);
			if(!empty($record)){
				foreach($record as $k => $rec){
					$record[$k]['created'] = RevSliderFunctionsWP::convertPostDate($rec['created'], true);
				}
			}
		}else{
			$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s AND static = %s", $where), ARRAY_A);
		}
		
		return $record;
	}
	
	
	/**
	 * check if a new backup should be created
	 * @since: 1.0.0
	 */
	public static function check_add_new_backup($slide_data, $ajax_data, $slide_class){
		
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".RevSliderGlobals::$table_slides." WHERE id = %s", array($slide_class->getID())), ARRAY_A);
		
		if(!empty($record)){
			self::add_new_backup($record[0], esc_attr($ajax_data['session_id']));
		}
	}
	
	
	/**
	 * check if a new backup should be created
	 * @since: 1.0.0
	 */
	public static function check_add_new_backup_static($slide_data, $ajax_data, $slide_class){
		
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".RevSliderGlobals::$table_static_slides." WHERE id = %s", array($slide_class->getID())), ARRAY_A);
		
		if(!empty($record)){
			self::add_new_backup($record[0], esc_attr($ajax_data['session_id']), 'true');
		}
	}
	
	
	/**
	 * add new slide backup
	 * @since: 1.0.0
	 */
	public static function add_new_backup($slide, $session_id, $static = 'false'){
		global $wpdb;
		
		$slide['slide_id'] = $slide['id'];
		unset($slide['id']);
		
		$slide['created'] = date('Y-m-d H:i:s');
		$slide['session'] = $session_id;
		$slide['static'] = $static;
		
		//check if session_id exists, if yes then update
		$row = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix . "revslider_backup_slides WHERE session = %s AND slide_id = %s", array($session_id, $slide['slide_id'])), ARRAY_A);
		if(!empty($row) && isset($row[0]) && !empty($row[0])){
			$wpdb->update($wpdb->prefix . "revslider_backup_slides", $slide, array('id' => $row[0]['id']));
		}else{
			$wpdb->insert($wpdb->prefix . "revslider_backup_slides", $slide);
		}
		
		$cur = self::check_backup_num($slide['slide_id']);
		
		if($cur > 11){
			$early = self::get_oldest_backup($slide['slide_id']);
			
			if($early !== false){
				self::delete_backup($early['id']);
			}
		}
	}
	
	
	/**
	 * delete a backup of a slide
	 * @since: 1.0.0
	 */
	public static function delete_backup($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE id = %s", array($id)));
		
	}
	
	
	/**
	 * delete all backup of a slide
	 * @since: 1.0.0
	 */
	public static function delete_backup_full($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s", array($id)));
		
	}
	
	
	/**
	 * delete all backup of a slide
	 * @since: 1.0.0
	 */
	public static function delete_backup_full_slider($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slider_id = %s", array($id)));
		
	}
	
	
	/**
	 * get all slide backups by slide ID
	 * @since: 1.0.0
	 **/
	public static function get_slide_backup($slide_id, $static = 'false'){
		
		global $wpdb;
		
		$slides = $wpdb->get_results($wpdb->prepare("SELECT `id`, `slide_id`, `slide_order`, `params`, `layers`, `settings`, `created` FROM ".$wpdb->prefix."revslider_backup_slides WHERE slide_id = %s AND static = %s ORDER BY id ASC", array($slide_id, $static)), ARRAY_A);
		if(!empty($slides)){
			return $slides[0];
		}else{
			
		}
		
	}
	
	
	/**
	 * get oldest backup of a slide
	 * @since: 1.0.0
	 */
	public static function get_oldest_backup($slide_id){
		global $wpdb;
		
		$early = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s ORDER BY `created` ASC LIMIT 0,1", array($slide_id)), ARRAY_A);
		if(!empty($early)){
			return $early[0];
		}else{
			return false;
		}
	}
	
	
	/**
	 * check for the number of backups for a slide
	 * @since: 1.0.0
	 */
	public static function check_backup_num($slide_id){
		global $wpdb;
		
		$cur = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) AS `row` FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s GROUP BY `slide_id`", array($slide_id)), ARRAY_A);
		
		if(!empty($cur)){
			return $cur[0]['row'];
		}else{
			return 0;
		}
	}
	
	
	/**
	 * Create/Update Database Tables
	 */
	public static function create_tables($networkwide = false){
		global $wpdb;
		
		if(function_exists('is_multisite') && is_multisite() && $networkwide){ //do for each existing site
		
			$old_blog = $wpdb->blogid;
			
            // Get all blog ids and create tables
			$blogids = $wpdb->get_col("SELECT blog_id FROM ".$wpdb->blogs);
			
            foreach($blogids as $blog_id){
				switch_to_blog($blog_id);
				self::_create_tables();
            }
			
            switch_to_blog($old_blog); //go back to correct blog
			
		}else{  //no multisite, do normal installation
		
			self::_create_tables();
			
		}
		
	}
	
	
	/**
	 * Create Tables, edited for multisite
	 * @since 1.5.0
	 */
	public static function _create_tables(){
		
		global $wpdb;
		
		//Create/Update Grids Database
		$grid_ver = get_option("revslider_backup_table_version", '0.99');
		
		if(version_compare($grid_ver, '1', '<')){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$table_name = $wpdb->prefix . 'revslider_backup_slides';
			$sql = "CREATE TABLE " .$table_name ." (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slide_id int(9) NOT NULL,
			  slider_id int(9) NOT NULL,
			  slide_order int not NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings TEXT NOT NULL,
			  created DATETIME NOT NULL,
			  session VARCHAR(100) NOT NULL,
			  static VARCHAR(20) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
			dbDelta($sql);
			
			update_option('revslider_backup_table_version', '1');
			
			$grid_ver = '1';
		}
		
	}
}
?>