<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class RsAddOnBase {
	
	const MINIMUM_VERSION = '5.2.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnTypewriterUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin ,'set_update_transient'));
			add_filter('plugins_api', array($update_admin,'set_updates_api_results'), 10, 3);
			
			// Add-Ons page
			add_filter('rev_addon_dash_slideouts', array($this, 'addons_page_content'));
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			// post meta box
			add_action('add_meta_boxes', array($this, 'register_meta_box'));
			add_action('save_post', array($this, 'save_meta_box'), 10, 3);
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			require_once(static::$_PluginPath . 'admin/includes/slide.class.php');
			
			// admin init
			new RsTypewriterSliderAdmin(static::$_PluginTitle);
			new RsTypewriterSlideAdmin(static::$_PluginTitle);
			
		}
		
		add_shortcode('rs-typewriter', array($this, 'post_shortcode'));
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsTypewriterSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new RsTypewriterSlideFront(static::$_PluginTitle);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
	// AddOn's page slideout panel
	public function addons_page_content() {
		
		include_once(static::$_PluginPath . 'admin/views/admin-display.php');
		
	}
	
	public function enqueue_admin_scripts($hook) {
		
		$_handle = 'rs-' . static::$_PluginTitle . '-admin';
		$_base   = static::$_PluginUrl . 'admin/assets/';
		
		if($hook === 'toplevel_page_revslider' || $hook === 'slider-revolution_page_rev_addon') {
			
			if(!isset($_GET['page'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider' && $page !== 'rev_addon') return;
			
			switch($page) {
				
				case 'revslider':
				
					if(isset($_GET['view'])) {
						
						switch($_GET['view']) {
							
							case 'slide':
							
								wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-slide-admin.css', array(), static::$_Version);
								wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-slide-admin.js', array('jquery'), static::$_Version, true);
							
							break;
							
							/*
							case 'slider':
								
								wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-slider-admin.css', array(), static::$_Version);
								wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-slider-admin.js', array('jquery'), static::$_Version, true);
							
							break;
							*/
							
						}
						
					}
				
				break;
				
				case 'rev_addon':
					
					wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-dash-admin.css', array(), static::$_Version);
					wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-dash-admin.js', array('jquery'), static::$_Version, true);

				break;
				
			}
			
		}
		else if($hook === 'post.php' || $hook === 'post-new.php') {
			
			wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-post-meta.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-post-meta.js', array('jquery'), static::$_Version, true);
			
		}
		
	}
	
	// add meta box to post editor
	public function register_meta_box() {
		
		add_meta_box(
		
			'rs-addon-' . static::$_PluginTitle . '-meta', 
			'Revolution Slider: ' . ucwords(static::$_PluginTitle) . ' Add-On', 
			array($this, 'add_meta_box'), 
			get_post_types()
			
		);
		
	}
	
	// return MetaBox content
	public function add_meta_box($obj) {
		
		wp_nonce_field(basename(__FILE__), 'rs-addon-typewriter-nonce');
		static::populateMetaBox($obj);
		
	}
	
	public function save_meta_box($post_id, $post, $update) {
		
		if( /* verify nonce */
			isset($_POST['rs-addon-typewriter-nonce']) && 
			wp_verify_nonce($_POST['rs-addon-typewriter-nonce'], basename(__FILE__)) && 
			
			/* prevent meta fields from being cleared */
			!(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) && 
			!(defined('DOING_AJAX') && DOING_AJAX) &&
			
			/* user can edit and meta exists */
			current_user_can('edit_post', $post_id) && 
			isset($_POST['rs_addon_typewriter_meta'])
			
		) update_post_meta($post_id, 'rs-addon-typewriter', $_POST['rs_addon_typewriter_meta']);
		
	}
	
	// Example: [rs-typewriter default="{{title}}"]
	// shortcode that can be added to a post-based slide template Layer 
	// used for multiline compatibility (see meta box above)
	public function post_shortcode($atts) {
		
		extract(shortcode_atts(array('default' => ''), $atts));
		return $default;
		
	}

}
	
?>