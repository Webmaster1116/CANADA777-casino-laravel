<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonPolyfoldSliderFront {
	
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
		
		$_enabled = RevSliderFunctions::getVal($_settings, static::$_PluginTitle . '_top_enabled', false) == 'true';
		if(empty($_enabled)) $_enabled = RevSliderFunctions::getVal($_settings, static::$_PluginTitle . '_bottom_enabled', false) == 'true';
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
		
		return $_record;
		
	}
	
	public function enqueue_preview() {
		
		$_base = static::$_PluginUrl . 'public/assets/';
		
		?>
		<link type="text/css" rel="stylesheet" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		$_topEnabled    = $_slider->getParam('polyfold_top_enabled', false) == 'true';
		$_bottomEnabled = $_slider->getParam('polyfold_bottom_enabled', false) == 'true';
		
		if(wp_is_mobile()) {
			
			if($_topEnabled) $_topEnabled = $_slider->getParam('polyfold_top_hide_mobile', false) == 'false';
			if($_bottomEnabled) $_bottomEnabled = $_slider->getParam('polyfold_bottom_hide_mobile', false) == 'false';
			
		}
		
		$_title = static::$_PluginTitle;
		$_id = $_slider->getID();
		
		for($i = 0; $i < 2; $i++) {
			
			if($i === 0) {
				
				if(!$_topEnabled) continue;
				$alias = 'top';
				
			}
			else {
				
				if(!$_bottomEnabled) break;
				$alias = 'bottom';
				
			}
			
			$_scroll     = $_slider->getParam('polyfold_' . $alias . '_scroll',     true)  == 'true' ? 'true' : 'false';
			$_responsive = $_slider->getParam('polyfold_' . $alias . '_responsive', true)  == 'true' ? 'true' : 'false';
			$_negative   = $_slider->getParam('polyfold_' . $alias . '_negative',   false) == 'true' ? 'true' : 'false';
			$_animated   = $_slider->getParam('polyfold_' . $alias . '_animated',   false) == 'true' ? 'true' : 'false';
			$_inverted   = $_slider->getParam('polyfold_' . $alias . '_inverted',   false) == 'true' ? 'true' : 'false';
			
			$_color      =            $_slider->getParam('polyfold_' . $alias . '_color',       '#ffffff');
			$_range      =            $_slider->getParam('polyfold_' . $alias . '_range',       'slider');
			$_point      =            $_slider->getParam('polyfold_' . $alias . '_point',       'sides');
			$_placement  =     intval($_slider->getParam('polyfold_' . $alias . '_placement',   1));
			$_height     = abs(intval($_slider->getParam('polyfold_' . $alias . '_height',      100)));
			$_leftWidth  = abs(intval($_slider->getParam('polyfold_' . $alias . '_left_width',  50)) * .01);
			$_rightWidth = abs(intval($_slider->getParam('polyfold_' . $alias . '_right_width', 50)) * .01);
			
			if(!$_color) $_color = '#ffffff';
			$_maxWidth = $_point === 'sides' ? 1 : 0.5;
			
			$_leftWidth  = max(min($_leftWidth, $_maxWidth), 0);
			$_rightWidth = max(min($_rightWidth, $_maxWidth), 0);
			
			echo '                Rs' . ucfirst($_title) . 'AddOn(tpj, revapi' . $_id . ',{';
			echo 'position: "'  . $alias . '", ';
			echo 'color: "'     . $_color . '", ';
			echo 'scroll: '     . $_scroll . ', ';
			echo 'height: '     . $_height . ', ';
			echo 'range: "'     . $_range . '", ';
			echo 'point: "'     . $_point . '", ';
			echo 'placement: '  . $_placement . ', ';
			echo 'responsive: ' . $_responsive . ', ';
			echo 'negative: '   . $_negative . ', ';
			echo 'leftWidth: '  . $_leftWidth . ', ';
			echo 'rightWidth: ' . $_rightWidth;
			
			if($_scroll === 'true') {
				
				echo ', inverted: ' . $_inverted . ', ';
				echo 'animated: '   . $_animated;
				if($_animated === 'true') {

					echo ', ease: "' . $_slider->getParam('polyfold_' . $alias . '_ease', 'ease-out') . '", ';
					echo 'time: ' . abs(floatval($_slider->getParam('polyfold_' . $alias . '_time', 0.3)));
					
				}
				
			}
			
			echo '});' . "\n";
			
		}
		
	}
	
}
?>