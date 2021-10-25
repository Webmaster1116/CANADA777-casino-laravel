<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsAddonSliderFront {

	protected function enqueueScripts() {

		add_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10, 1);
	}

	protected function enqueuePreview() {

		add_action('revslider_preview_slider_head', array($this, 'enqueue_preview'));

	}

	protected function writeInitScript() {

		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 3);

	}

	public function enqueue_scripts($_record = array()) {
		if ($_record && $_sliderId = RevSliderFunctions::getVal($_record, 'slider_id', false)) {

			$_slider = new RevSlider();
			$_slider->initByID($_sliderId);
			$_settings = $_slider->getParams();

			if ($_settings && RevSliderFunctions::getVal($_settings, 'typewriter_defaults_enabled', false) == 'true') {

				$_handle       = 'rs-' . static::$_PluginTitle . '-front';
				$_base         = static::$_PluginUrl . 'public/assets/';

				wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '.css', array(), static::$_Version);
				wp_enqueue_script($_handle, $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js', array('jquery', 'revmin'), static::$_Version, false);
			}
		}

		remove_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10);
		return $_record;
	}

	public function enqueue_preview() {

		$_base = static::$_PluginUrl . 'public/assets/';

		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $_base . 'css/' . static::$_PluginTitle . '.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php

	}

	public function write_init_script($_slider, $_id) {

		//check if Slider is using typewriter
		if($_slider->getParam("typewriter_defaults_enabled","false") === 'true'){

			$_id    = $_slider->getID();
			$_title = static::$_PluginTitle;

			echo                  "\n";
			echo '                Rs' . ucfirst($_title) . 'AddOn(tpj, revapi' . $_id . ');'."\n";

		}
	}

}
?>