<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonPanoramaSliderFront {
	
	protected function enqueueScripts() {
		
		add_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10, 1);
		
	}
	
	protected function enqueuePreview() {
		
		add_action('revslider_preview_slider_head', array($this, 'enqueue_preview'));
		
	}
	
	protected function writeInitScript() {
		
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		
	}
	
	public function enqueue_scripts($_record) {
		
		$_params = json_decode($_record['params']);
		if(empty($_params)) return $_record;
			
		$_enabled = RevSliderFunctions::getVal($_params, static::$_PluginTitle . '_enabled', false) == 'true';
		if(empty($_enabled)) return $_record;
		
        /*print '<pre>';
        print_r($_record);
        print '</pre>';
		*/
		$_handle = 'rs-' . static::$_PluginTitle . '-front';
		$_base   = static::$_PluginUrl . 'public/assets/';
		
		wp_enqueue_style(
		
			$_handle, 
			$_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css', 
			array(), 
			static::$_Version
			
		);
		
		wp_enqueue_script(
		
			'three', 
			$_base . 'js/three.min.js', 
			array(), 
			static::$_Version, 
			true
			
		);
		
		wp_enqueue_script(
		
			$_handle, 
			$_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js', 
			array('jquery', 'revmin'), 
			static::$_Version, 
			true
			
		);
		
		return $_record;
		
	}
	
	public function enqueue_preview() {
		
		$_base = static::$_PluginUrl . 'public/assets/';
		?>
		
		<link type="text/css" rel="stylesheet" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo $_base . 'js/three.min.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		// enabled from slider settings
		$_enabled = $_slider->getParam('panorama_enabled', false) == 'true';
		
		if(!empty($_enabled)) {
		
			$_id = $_slider->getID();
			echo "\n";
			echo '    RsAddonPanorama(tpj, revapi' . $_id . ');'."\n";
			
		}
		
	}
	
}
?>