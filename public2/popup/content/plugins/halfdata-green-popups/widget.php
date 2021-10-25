<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, esc_html__('Green Popups', 'lepopup'));
	}

	function widget($args, $instance) {
		global $lepopup, $wpdb;
		$content = '';
		$form_id = $lepopup->wpml_parse_form_id($instance['form-id']);
		include_once(dirname(__FILE__).'/modules/core-front.php');
		$html = lepopup_front_class::shortcode_handler(array('id' => $form_id));
		if (!empty($html)) {
			$content = $args['before_widget'].'<div style="clear:both; max-width:'.$instance['width-max'].'px; margin:'.$instance['margin-top'].'px '.$instance['margin-right'].'px '.$instance['margin-bottom'].'px '.$instance['margin-left'].'px;">'.$html.'</div>'.$args['after_widget'];
		}
		echo $content;
	}

	function update($new_instance, $old_instance) {
		global $lepopup, $wpdb;
		$instance = $old_instance;
		$instance['form-id'] = $lepopup->wpml_compile_form_id(strip_tags($new_instance['form-id']), $instance['form-id']);
		$instance['width-max'] = intval($new_instance['width-max']);
		$instance['margin-top'] = intval($new_instance['margin-top']);
		$instance['margin-bottom'] = intval($new_instance['margin-bottom']);
		$instance['margin-left'] = intval($new_instance['margin-left']);
		$instance['margin-right'] = intval($new_instance['margin-right']);
		return $instance;
	}

	function form($instance) {
		global $lepopup, $wpdb;
		$instance = wp_parse_args((array)$instance, array('form-id' => '', 'margin-top' => 0, 'margin-bottom' => 0, 'margin-left' => 0, 'margin-right' => 0, 'width-max' => 320));
		$popup_selected = $lepopup->wpml_parse_form_id(strip_tags($instance['form-id']));
		$margin_top = intval($instance['margin-top']);
		$margin_bottom = intval($instance['margin-bottom']);
		$margin_right = intval($instance['margin-right']);
		$margin_left = intval($instance['margin-left']);
		$width_max = intval($instance['width-max']);

		$forms = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE deleted = '0' AND active = '1' ORDER BY active DESC, id DESC", ARRAY_A);
		
		echo '
		<p>
			<label for="'.$this->get_field_id('form-id').'">'.esc_html__('Popups', 'lepopup').':</label>';
		if (sizeof($forms) > 0) {
			$status = -1;
			echo '
			<select class="widefat" id="'.$this->get_field_id('form-id').'" name="'.$this->get_field_name('form-id').'">';
			foreach($forms as $form) {
				if ($form['active'] != $status) {
					if ($form['active'] == 1) echo '<option disabled="disabled">--------- '.esc_html__('Active popups', 'lepopup').' ---------</option>';
					else echo '<option disabled="disabled">--------- '.esc_html__('Inactive popups', 'lepopup').' ---------</option>';
					$status = $form['active'];
				}
				if ($popup_selected == $form['id']) {
					echo '
				<option value="'.$form['id'].'" selected="selected"'.($form['active'] == 1 ? '' : ' disabled="disabled"').'>'.esc_html($form['name']).'</option>';
				} else {
					echo '
				<option value="'.$form['id'].'"'.($form['active'] == 1 ? '' : ' disabled="disabled"').'>'.esc_html($form['name']).'</option>';
				}
			}
			echo '
			</select>';
		} else {
			echo esc_html__('Create at least one popup.', 'lepopup');
		}
		echo '
		</p>
		<p>
			<label class="lepopup-widget-label" for="'.$this->get_field_id("margin-top").'">'.esc_html__('Top margin', 'lepopup').':</label>
			<input class="lepopup-widget-tiny-text" id="'.$this->get_field_id('margin-top').'" name="'.$this->get_field_name('margin-top').'" type="number" step="1" min="-20" value="'.$margin_top.'" size="3"> '.esc_html__('px', 'lepopup').'
			<label class="lepopup-widget-label" for="'.$this->get_field_id("margin-bottom").'">'.esc_html__('Bottom margin', 'lepopup').':</label>
			<input class="lepopup-widget-tiny-text" id="'.$this->get_field_id('margin-bottom').'" name="'.$this->get_field_name('margin-bottom').'" type="number" step="1" min="-20" value="'.$margin_bottom.'" size="3"> '.esc_html__('px', 'lepopup').'
			<label class="lepopup-widget-label" for="'.$this->get_field_id("margin-left").'">'.esc_html__('Left margin', 'lepopup').':</label>
			<input class="lepopup-widget-tiny-text" id="'.$this->get_field_id('margin-left').'" name="'.$this->get_field_name('margin-left').'" type="number" step="1" min="-20" value="'.$margin_left.'" size="3"> '.esc_html__('px', 'lepopup').'
			<label class="lepopup-widget-label" for="'.$this->get_field_id("margin-right").'">'.esc_html__('Right margin', 'lepopup').':</label>
			<input class="lepopup-widget-tiny-text" id="'.$this->get_field_id('margin-right').'" name="'.$this->get_field_name('margin-right').'" type="number" step="1" min="-20" value="'.$margin_right.'" size="3"> '.esc_html__('px', 'lepopup').'
		</p>
		<p>
			<label class="lepopup-widget-label" for="'.$this->get_field_id("width-max").'">'.esc_html__('Max width', 'lepopup').':</label>
			<input class="lepopup-widget-tiny-text" id="'.$this->get_field_id('width-max').'" name="'.$this->get_field_name('width-max').'" type="number" step="1" min="120" value="'.$width_max.'" size="3"> '.esc_html__('px', 'lepopup').'
		</p>';
	}
}
?>