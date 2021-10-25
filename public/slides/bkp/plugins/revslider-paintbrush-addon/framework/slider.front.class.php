<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonPaintBrushSliderFront {
	
	protected function enqueueScripts() {
		
		add_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10, 1);
		
	}
	
	protected function enqueuePreview() {
		
		add_action('revslider_preview_slider_footer', array($this, 'enqueue_preview_footer'));
		
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
			$_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js', 
			array('jquery', 'revmin'), 
			static::$_Version, 
			true
			
		);
		
		remove_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10);
		return $_record;
		
	}
	
	public function enqueue_preview_footer() {
		
		$_base = static::$_PluginUrl . 'public/assets/';
		?>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		// enabled from slider settings
		$_enabled = $_slider->getParam('paintbrush_enabled', false) == 'true';
		if(empty($_enabled)) return;
		
		$_slides = $_slider->getSlides();
		$_mobile = wp_is_mobile();
		
		// check to see if at least one individual slide is enabled
		foreach($_slides as $_slide) {
				
			$_enabled = $_slide->getParam('paintbrush_enabled', false) == 'true';
			if(!empty($_enabled)) {
				
				if(!$_mobile) {
					
					break;
					
				}
				else {
					
					$_enabled = $_slide->getParam('paintbrush_mobile', false) != 'true';
					if(!empty($_enabled)) break;
					
				}
				
			}
				
		}
		
		if(!empty($_enabled)) {
	
			$_id             = $_slider->getID();
			
			echo "\n";
			echo '    RevSliderPaintBrush(tpj, revapi' . $_id . ');';
			echo "\n";
			
		}
		
	}
	
}
?>