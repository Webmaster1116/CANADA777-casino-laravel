<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_REVEALER_PLUGIN_PATH . 'public/includes/preloaders.class.php');

class RsAddonRevealerSliderFront {
	
	protected function enqueueScripts() {
		
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10, 1);
		
	}
	
	protected function enqueuePreview() {
		
		add_action('revslider_preview_slider_head', array($this, 'enqueue_preview'));
		
	}
	
	protected function writeInitScript() {
		
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		
	}
	
	public function enqueue_scripts($_record) {
		
		if(empty($_record)) return $_record;
		
		$_params = RevSliderFunctions::getVal($_record, 'params', false);
		$_sliderId = RevSliderFunctions::getVal($_record, 'slider_id', false);
		
		if(empty($_params) || empty($_sliderId)) return $_record;
		
		$_params = json_decode($_params);
		if(empty($_params)) return $_record;
		
		$_slider = new RevSlider();
		$_slider->initByID($_sliderId);
		
		if(empty($_slider)) return $_record;
			
		$_settings = $_slider->getParams();
		if(empty($_settings)) return $_record;
		
		$_enabled = RevSliderFunctions::getVal($_settings, static::$_PluginTitle . '_enabled', false) == 'true';
		if(empty($_enabled)) return $_record;
		
		$_handle       = 'rs-' . static::$_PluginTitle . '-front';
		$_base         = static::$_PluginUrl . 'public/assets/';
		
		wp_enqueue_style(
		
			$_handle, 
			$_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css', 
			array(), 
			static::$_Version
			
		);
		
		wp_enqueue_style(
		
			$_handle . '-preloaders', 
			$_base . 'css/revolution.addon.' . static::$_PluginTitle . '.preloaders.css', 
			array(), 
			static::$_Version
			
		);
		
		wp_enqueue_script(
		
			$_handle, 
			$_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js', 
			array('jquery', 'revmin'), 
			static::$_Version, 
			true
			
		);
		
		remove_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10);
		return $_record;
		
	}
	
	public function enqueue_preview() {
		
		$_base = static::$_PluginUrl . 'public/assets/';
		
		?>
		<link type="text/css" rel="stylesheet" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css'; ?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.preloaders.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		$_title = static::$_PluginTitle;
		if($_slider->getParam($_title . '_enabled', false) == 'true') {
		
			$_id         = $_slider->getID();
			$_preloader  = $_slider->getParam('revealer_spinner', 'default');
			$_preloaders = RsAddOnRevealPreloaders::getPreloaders();
			
			if($_preloader !== 'default' && array_key_exists($_preloader, $_preloaders)) {
				$_preloader = $_preloaders[$_preloader];
				$_preloader = json_encode($_preloader);
			}
			else {
				$_preloader = 'false';
			}

			echo      "\n";
			echo '    Rs' . ucfirst($_title) . 'AddOn(tpj, revapi' . $_id . ', ' . $_preloader . ');' . "\n";
			
		}
		
	}
	
}
?>