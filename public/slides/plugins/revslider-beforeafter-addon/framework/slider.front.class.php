<?php
/*
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonBeforeAfterSliderFront {

	protected function enqueueScripts() {

        add_action('revslider_slide_initByData', array($this, 'enqueue_scripts'), 10, 1);

	}

	protected function enqueuePreview() {

		add_action('revslider_preview_slider_head', array($this, 'enqueue_preview'));
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

			'rs-icon-set-fa-icon-',
			RS_PLUGIN_URL .  'public/assets/fonts/font-awesome/css/font-awesome.css',
			array(),
			RevSliderGlobals::SLIDER_REVISION

		);

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

	public function enqueue_preview() {

		$_base = static::$_PluginUrl . 'public/assets/';
		?>

		<link type="text/css" rel="stylesheet" href="<?php echo RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css'; ?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo $_base . 'css/revolution.addon.' . static::$_PluginTitle . '.css'; ?>" />
		<?php

	}

	public function enqueue_preview_footer() {

		$_base = static::$_PluginUrl . 'public/assets/';
		?>

		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php

	}

	public function write_init_script($_slider, $_id) {

		// enabled from slider settings
		$_enabled = $_slider->getParam('beforeafter_enabled', false) == 'true';
		if(empty($_enabled)) return;

		// check to see if at least one individual slide is enabled
		$_slides = $_slider->getSlides();
		foreach($_slides as $_slide) {

			$_enabled = $_slide->getParam('beforeafter_enabled', false) == 'true';
			if(!empty($_enabled)) break;

		}

		if(!empty($_enabled)) {

			$_id             = $_slider->getID();
			$_cursor         = $_slider->getParam('beforeafter_cursor',         'pointer');
			$_arrow_left     = $_slider->getParam('beforeafter_left_arrow',     'fa-icon-caret-left');
			$_arrow_right    = $_slider->getParam('beforeafter_right_arrow',    'fa-icon-caret-right');
			$_arrow_top      = $_slider->getParam('beforeafter_top_arrow',      'fa-icon-caret-up');
			$_arrow_bottom   = $_slider->getParam('beforeafter_bottom_arrow',   'fa-icon-caret-down');
			$_arrow_size     = $_slider->getParam('beforeafter_arrow_size',     '28');
			$_arrow_color    = $_slider->getParam('beforeafter_arrow_color',    '#ffffff');
			$_arrow_bg       = $_slider->getParam('beforeafter_arrow_bg_color', 'transparent');
			$_arrow_padding  = $_slider->getParam('beforeafter_arrow_padding',  '0');
			$_arrow_spacing  = $_slider->getParam('beforeafter_arrow_spacing',  '3');
			$_arrow_radius   = $_slider->getParam('beforeafter_arrow_radius',   '0');
			$_divider_size   = $_slider->getParam('beforeafter_divider_size',   '1');
			$_divider_color  = $_slider->getParam('beforeafter_divider_color',  '#ffffff');
			$_arrow_shadow   = $_slider->getParam('beforeafter_arrow_shadow',   false) == 'true';
			$_divider_shadow = $_slider->getParam('beforeafter_divider_shadow', false) == 'true';
			$_arrow_border   = $_slider->getParam('beforeafter_arrow_border',   false) == 'true';
			$_box_shadow     = $_slider->getParam('beforeafter_box_shadow',     false) == 'true';
			$_onclick        = $_slider->getParam('beforeafter_onclick',        false) == 'true';
			$_carousel       = $_slider->getParam('slider-type',                'standard')  !== 'carousel' ? 'false' : 'true';

			echo "\n";
			echo '    RevSliderBeforeAfter(tpj, revapi' . $_id . ', {' . "\n";
			echo '        arrowStyles: {' . "\n";
			echo '            leftIcon: "'     . $_arrow_left    . '",' . "\n";
			echo '            rightIcon: "'    . $_arrow_right   . '",' . "\n";
			echo '            topIcon: "'      . $_arrow_top     . '",' . "\n";
			echo '            bottomIcon: "'   . $_arrow_bottom  . '",' . "\n";
			echo '            size: "'         . $_arrow_size    . '",' . "\n";
			echo '            color: "'        . $_arrow_color   . '",' . "\n";
			echo '            bgColor: "'      . $_arrow_bg      . '",' . "\n";
			echo '            spacing: "'      . $_arrow_spacing . '",' . "\n";
			echo '            padding: "'      . $_arrow_padding . '",'  . "\n";
			echo '            borderRadius: "' . $_arrow_radius  . '"'  . "\n";
			echo '        },' . "\n";
			echo '        dividerStyles: {' . "\n";
			echo '            width: "' . $_divider_size . '",' . "\n";
			echo '            color: "' . $_divider_color . '"' . "\n";
			echo '        }';

			if(!empty($_arrow_shadow)) {

				$_color    = $_slider->getParam('beforeafter_arrow_shadow_color', 'rgba(0, 0, 0, 0.35)');
				$_blur     = $_slider->getParam('beforeafter_arrow_shadow_blur', '10');

				echo ',' . "\n";
				echo '        arrowShadow: {' . "\n";
				echo '            color: "' . $_color . '",' . "\n";
				echo '            blur: "' . $_blur . '"' . "\n";
				echo '        }';

			}

			if(!empty($_box_shadow)) {

				$_strength = $_slider->getParam('beforeafter_box_shadow_strength', '3');
				$_color    = $_slider->getParam('beforeafter_box_shadow_color', 'rgba(0, 0, 0, 0.35)');
				$_blur     = $_slider->getParam('beforeafter_box_shadow_blur', '10');

				echo ',' . "\n";
				echo '        boxShadow: {' . "\n";
				echo '            strength: "' . $_strength . '",' . "\n";
				echo '            color: "' . $_color . '",' . "\n";
				echo '            blur: "' . $_blur . '"' . "\n";
				echo '        }';

			}

			if(!empty($_arrow_border)) {

				$_size  = $_slider->getParam('beforeafter_arrow_border_size', '1');
				$_color = $_slider->getParam('beforeafter_arrow_border_color', '#000000');

				echo ',' . "\n";
				echo '        arrowBorder: {' . "\n";
				echo '            size: "' . $_size . '",' . "\n";
				echo '            color: "' . $_color . '"' . "\n";
				echo '        }';

			}

			if(!empty($_divider_shadow)) {

				$_strength = $_slider->getParam('beforeafter_divider_shadow_strength', '3');
				$_color    = $_slider->getParam('beforeafter_divider_shadow_color', 'rgba(0, 0, 0, 0.35)');
				$_blur     = $_slider->getParam('beforeafter_divider_shadow_blur', '10');

				echo ',' . "\n";
				echo '        dividerShadow: {' . "\n";
				echo '            strength: "' . $_strength . '",' . "\n";
				echo '            color: "' . $_color . '",' . "\n";
				echo '            blur: "' . $_blur . '"' . "\n";
				echo '        }';

			}

			if(!empty($_onclick)) {

				$_time   = $_slider->getParam('beforeafter_click_time',   '300');
				$_easing = $_slider->getParam('beforeafter_click_easing', 'Power2.easeOut');

				echo ',' . "\n";
				echo '        onClick: {' . "\n";
				echo '            time: "'   . $_time   . '",' . "\n";
				echo '            easing: "' . $_easing . '",' . "\n";
				echo '        }';

			}

			echo ',' . "\n";
			echo '        cursor: "' . $_cursor . '",' . "\n";
			echo '        carousel: ' . $_carousel . "\n";
			echo '    });'."\n";

		}

	}

}