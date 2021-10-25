<?php
/* HTML Form integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_customjs_class {
	var $default_form_options = array(
		"customjs-afterinit-enable" => "off",
		"customjs-afterinit-script" => "",
		"customjs-afterupdate-enable" => "off",
		"customjs-afterupdate-script" => "",
		"customjs-beforesubmit-enable" => "off",
		"customjs-beforesubmit-script" => "",
		"customjs-aftersubmitsuccess-enable" => "off",
		"customjs-aftersubmitsuccess-script" => "",
		"customjs-afterclose-enable" => "off",
		"customjs-afterclose-script" => ""
	);
	
	function __construct() {
		if (is_admin()) {
		}
		add_filter('lepopup_element_properties_meta', array(&$this, 'element_properties_meta'), 10, 1);
		add_filter('lepopup_form_suffix', array(&$this, 'front_form_suffix'), 10, 3);
	}
	
	function element_properties_meta($_meta) {
		$_meta['settings']['advanced-sections']['sections']['customjs'] = array('label' => esc_html__('Custom JavaScript Handlers', 'lepopup'), 'icon' => 'fab fa-js');
		$_meta_part = array(
			'start-customjs' => array('type' => 'section-start', 'section' => 'customjs'),
				'customjs-afterinit-enable' => array('value' => 'off', 'label' => esc_html__('AfterInit handler', 'lepopup'), 'tooltip' => esc_html__('Enable this feature to add JS-code which is executed when popup initialized.', 'lepopup'), 'type' => 'checkbox'),
				'customjs-afterinit-script' => array('value' => '', 'label' => esc_html__('AfterInit code', 'lepopup'), 'tooltip' => esc_html__('JavaScript code which is executed when popup initialized. Do not use script-tags (just put regular javascript-code) and make sure your javascript-code does not have any syntax errors.', 'lepopup'), 'type' => 'textarea', 'monospace' => 'on', 'visible' => array('customjs-afterinit-enable' => array('on'))),
				'customjs-afterupdate-enable' => array('value' => 'off', 'label' => esc_html__('AfterUpdate handler', 'lepopup'), 'tooltip' => esc_html__('Enable this feature to add JS-code which is executed when field value changed.', 'lepopup'), 'type' => 'checkbox'),
				'customjs-afterupdate-script' => array('value' => '', 'label' => esc_html__('AfterUpdate code', 'lepopup'), 'tooltip' => esc_html__('JavaScript code which is executed when field value changed. Do not use script-tags (just put regular javascript-code) and make sure your javascript-code does not have any syntax errors.', 'lepopup'), 'type' => 'textarea', 'monospace' => 'on', 'visible' => array('customjs-afterupdate-enable' => array('on'))),
				'customjs-beforesubmit-enable' => array('value' => 'off', 'label' => esc_html__('BeforeSubmit handler', 'lepopup'), 'tooltip' => esc_html__('Enable this feature to add JS-code which is executed before form submitted.', 'lepopup'), 'type' => 'checkbox'),
				'customjs-beforesubmit-script' => array('value' => '', 'label' => esc_html__('BeforeSubmit code', 'lepopup'), 'tooltip' => esc_html__('JavaScript code which is executed before form submitted. Do not use script-tags (just put regular javascript-code) and make sure your javascript-code does not have any syntax errors.', 'lepopup'), 'type' => 'textarea', 'monospace' => 'on', 'visible' => array('customjs-beforesubmit-enable' => array('on'))),
				'customjs-aftersubmitsuccess-enable' => array('value' => 'off', 'label' => esc_html__('AfterSubmitSuccess handler', 'lepopup'), 'tooltip' => esc_html__('Enable this feature to add JS-code which is executed when form successfully submitted.', 'lepopup'), 'type' => 'checkbox'),
				'customjs-aftersubmitsuccess-script' => array('value' => '', 'label' => esc_html__('AfterSubmitSuccess code', 'lepopup'), 'tooltip' => esc_html__('JavaScript code which is executed when form successfully submitted. Do not use script-tags (just put regular javascript-code) and make sure your javascript-code does not have any syntax errors.', 'lepopup'), 'type' => 'textarea', 'monospace' => 'on', 'visible' => array('customjs-aftersubmitsuccess-enable' => array('on'))),
				'customjs-afterclose-enable' => array('value' => 'off', 'label' => esc_html__('AfterClose handler', 'lepopup'), 'tooltip' => esc_html__('Enable this feature to add JS-code which is executed when popup closed.', 'lepopup'), 'type' => 'checkbox'),
				'customjs-afterclose-script' => array('value' => '', 'label' => esc_html__('AfterClose code', 'lepopup'), 'tooltip' => esc_html__('JavaScript code which is executed when popup closed. Do not use script-tags (just put regular javascript-code) and make sure your javascript-code does not have any syntax errors.', 'lepopup'), 'type' => 'textarea', 'monospace' => 'on', 'visible' => array('customjs-afterclose-enable' => array('on'))),
			'end-customjs' => array('type' => 'section-end')
		);
		$_meta['settings'] = array_merge($_meta['settings'], $_meta_part);
		return $_meta;
	}

	function front_form_suffix($_suffix, $_element_id, $_form_object) {
		global $lepopup;
		$form_options = array_merge($this->default_form_options, $_form_object->form_options);
		$suffix = '
<script>
lepopup_customjs_handlers["'.$_element_id.'"] = {'.($form_options['customjs-afterinit-enable'] == 'on' && !empty($form_options['customjs-afterinit-script']) ? '
	afterinit:			function(){
		'.$form_options['customjs-afterinit-script'].'
	},': '').($form_options['customjs-aftersubmitsuccess-enable'] == 'on' && !empty($form_options['customjs-aftersubmitsuccess-script']) ? '
	aftersubmitsuccess:	function(){
		'.$form_options['customjs-aftersubmitsuccess-script'].'
	},': '').($form_options['customjs-afterupdate-enable'] == 'on' && !empty($form_options['customjs-afterupdate-script']) ? '
	afterupdate:		function(element_id){
		'.$form_options['customjs-afterupdate-script'].'
	},': '').($form_options['customjs-beforesubmit-enable'] == 'on' && !empty($form_options['customjs-beforesubmit-script']) ? '
	beforesubmit:		function(){
		'.$form_options['customjs-beforesubmit-script'].'
	},': '').($form_options['customjs-afterclose-enable'] == 'on' && !empty($form_options['customjs-afterclose-script']) ? '
	afterclose:			function(){
		'.$form_options['customjs-afterclose-script'].'
	},': '').'
	dom_id:				"'.$_element_id.'",
	popup_id:			"'.$_form_object->id.'",
	popup_slug:			"'.$_form_object->slug.'",
	errors:				{},
	user_data:			{},
	get_field_value:	function(_element_id) {return jQuery(".lepopup-form[data-id=\'"+this.dom_id+"\']").find("[name=\'lepopup-"+_element_id+"\']").val();},
	set_field_value:	function(_element_id, _value) {return jQuery(".lepopup-form[data-id=\'"+this.dom_id+"\']").find("[name=\'lepopup-"+_element_id+"\']").val(_value);}
};
</script>';
		return $_suffix.$suffix;
	}
	
}
$lepopup_customjs = new lepopup_customjs_class();
?>