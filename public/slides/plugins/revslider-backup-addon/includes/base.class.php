<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_backup_base {
	
	public function __construct(){
		try{
			if(class_exists('RevSliderFront')){ //check if Slider Revolution is installed
				if(version_compare(RevSliderGlobals::SLIDER_REVISION, '5.2.0', '>=')){
					if(get_option('revslider-valid', 'false') == 'true'){
						
						self::load_plugin_textdomain();
						
						if(is_admin()){
							
							require_once(RS_BACKUP_PLUGIN_PATH.'admin/includes/slide.class.php');
							
							rs_backup_slide::init_backup();

							//Updates
							require_once(RS_BACKUP_PLUGIN_PATH.'admin/includes/update.class.php');
							$update_admin = new rs_backup_update(RS_BACKUP_VERSION);
							add_filter( 'pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient') );
							add_filter( 'plugins_api', array($update_admin ,'set_updates_api_results'),10,3 );

							//Dashboard Slideout
							add_filter('rev_addon_dash_slideouts',array('rs_backup_base','display_plugin_admin_page'));
							add_action('admin_enqueue_scripts', array('rs_backup_base', 'enqueue_dash_scripts'));
							add_action('admin_enqueue_scripts', array('rs_backup_base', 'enqueue_dash_style'));
						}
					}else{
						add_action('admin_notices', array('rs_backup_base', 'add_notice_activation'));
						//add notification that slider revolution needs to be activated
					}
				}else{
					add_action('admin_notices', array('rs_backup_base', 'add_notice_version'));
					//add notification that plugin version of Slider Revolution has to be at least version 5.2.0
				}
			}else{
				add_action('admin_notices', array('rs_backup_base', 'add_notice_plugin'));
				//add notification that plugin Slider Revolution has to be installed
			}
		}catch(Exception $e){
			$message = $e->getMessage();
			$trace = $e->getTraceAsString();
			echo _e("Slider Revolution Backup Add-On:",'rs_backup')." <b>".$message."</b>";
		}
	}
	
	public static function add_notice_plugin(){
		?>
		<div class="error below-h2 wb-notice-wrap" id="message"><p><?php _e('To use Slider Revolution Backup Add-on, please install Slider Revolution for WordPress', 'rs_backup'); ?></p></div>
		<?php
	}
	
	
	public static function add_notice_version(){
		?>
		<div class="error below-h2 wb-notice-wrap" id="message"><p><?php _e('To use Slider Revolution Backup Add-on, please update Slider Revolution for WordPress to version 5.2.0 or later', 'rs_backup'); ?></p></div>
		<?php
	}
	
	
	public static function add_notice_activation(){
		?>
		<div class="error below-h2 wb-notice-wrap" id="message"><p><?php _e('To use Slider Revolution Backup Add-on, please activate Slider Revolution for WordPress', 'rs_backup'); ?></p></div>
		<?php
	}
	
	public static function load_plugin_textdomain(){
		load_plugin_textdomain('rs_backup', false, RS_BACKUP_PLUGIN_PATH . 'languages/');
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public static function display_plugin_admin_page() {
		include_once( RS_BACKUP_PLUGIN_PATH . 'admin/views/admin-display.php' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_dash_scripts() {
		if(isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_script( "rs_backup_dash", RS_BACKUP_PLUGIN_URL . 'admin/assets/js/backup_dash-admin.js', array( 'jquery' ), RS_BACKUP_VERSION, false );
			wp_localize_script( 'rs_backup', 'rs_backup', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			));
		}
	}

	/**
	 * Register the CSS for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_dash_style() {
		if(isset($_GET["page"]) && $_GET["page"]=="rev_addon"){
			wp_enqueue_style( "rs_backup_dash", RS_BACKUP_PLUGIN_URL . 'admin/assets/css/backup-dash-admin.css', array() , RS_BACKUP_VERSION );
		}
	}
}
?>