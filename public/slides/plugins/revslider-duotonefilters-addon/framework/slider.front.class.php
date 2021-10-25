<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonDuotoneFiltersSliderFront {
	
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
		
		wp_enqueue_script(
		
			$_handle, 
			$_base . 'js/revolution.addon.' . static::$_PluginTitle . '.js', 
			array('jquery'), 
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
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		// enabled from slider settings
		$_enabled = $_slider->getParam('duotonefilters_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		$_id = str_replace('rev_slider_', '', $_id);
		$_id = explode('_', $_id);
		$_id = $_id[0];
		
		$_simplified = $_slider->getParam('duotonefilters_simplified', false) == 'true' ? 'true' : 'false';
		$_easing     = $_slider->getParam('duotonefilters_easing', 'ease-in');
		$_timing     = $_slider->getParam('duotonefilters_timing', '750');
		
		echo 'RsAddonDuotone(tpj, revapi' . $_id . ', ' . $_simplified . ', "' . $_easing . '", "' . $_timing . '");';
		
	}
	
}
?>