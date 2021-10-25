<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_whiteboard_fe_slider {


	public static function init(){
		add_action('revslider_fe_javascript_option_output', array('rs_whiteboard_fe_slider', 'add_whiteboard_javascript_options'));

		add_action('revslider_fe_javascript_output', array('rs_whiteboard_fe_slider', 'add_whiteboard_javascript'), 10, 2);

		if (is_admin()) {
			add_action('revslider_preview_slider_head' ,array('rs_whiteboard_fe_slider', 'enqueue_scripts_html')); //add the JavaScript to the Preview
		} else {
			add_action('revslider_slide_initByData' ,array('rs_whiteboard_fe_slider', 'enqueue_scripts'), 10, 1);
		}
	}


	public static function add_whiteboard_javascript($slider, $htmlid){
		//check if whiteboard is used in this Slider, if yes, initialize it
		if($slider->getParam("wb_enable","off") === 'on' && $slider->getParam('wb_is_used', false) === true){
			echo '				tpj("#'.$htmlid.'").rsWhiteBoard();'."\n";
		}
	}


	public static function add_whiteboard_javascript_options($slider){
		if($slider->getParam("wb_enable","off") === 'on' && $slider->getParam('wb_is_used', false) === true){
			$hands = array("movehand","writehand");
			$tabs = "\t\t\t\t\t\t";
			$tabsa = "\t\t\t\t\t\t\t";
			$tabsb = "\t\t\t\t\t\t\t\t";
			$tabsc = "\t\t\t\t\t\t\t\t\t";
			echo $tabs.'whiteboard:{'."\n";
			for ($i=0;$i<count($hands); $i++) {
				switch($slider->getParam("wb_".$hands[$i]."_source","1")){
					case '1':
						if ($hands[$i]=="movehand")
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
						else
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
					break;
					case '2':
						if ($hands[$i]=="movehand")
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
						else
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
					break;
					case '3':
						if ($hands[$i]=="movehand")
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
						else
							$writehandsrc = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
					break;
					case "custom":
						$writehandsrc = $slider->getParam('wb_'.$hands[$i].'_source_custom','');
					break;
				}
				echo $tabsa.$hands[$i].": {"."\n";
					echo $tabsb.'src:"'.$writehandsrc.'",'."\n";
					echo $tabsb.'width:'.$slider->getParam('wb_'.$hands[$i].'_width','200').','."\n";
					echo $tabsb.'height:'.$slider->getParam('wb_'.$hands[$i].'_height','200').','."\n";
					echo $tabsb.'handtype:"'.$slider->getParam('wb_'.$hands[$i].'_type','right').'",'."\n";
					echo $tabsb.'transform:{'."\n";
					echo $tabsc.'transformX:'.$slider->getParam('wb_'.$hands[$i].'_origin_x','0').','."\n";
					echo $tabsc.'transformY:'.$slider->getParam('wb_'.$hands[$i].'_origin_y','0')."\n";
					echo $tabsb.'},'."\n";
					echo $tabsb.'jittering:{'."\n";
					echo $tabsc.'distance:"'.$slider->getParam('wb_global_'.$hands[$i].'_jitter','80').'",'."\n";
					echo $tabsc.'distance_horizontal:"'.$slider->getParam('wb_global_'.$hands[$i].'_jitter_horizontal','100').'",'."\n";
					echo $tabsc.'repeat:"'.$slider->getParam('wb_global_'.$hands[$i].'_jitter_repeat','5').'",'."\n";
					echo $tabsc.'offset:"'.$slider->getParam('wb_global_'.$hands[$i].'_jitter_offset','10').'",'."\n";
					echo $tabsc.'offset_horizontal:"'.$slider->getParam('wb_global_'.$hands[$i].'_jitter_offset_horizontal','0').'"'."\n";
					echo $tabsb.'},'."\n";
					echo $tabsb.'rotation:{'."\n";
					echo $tabsc.'angle:"'.$slider->getParam('wb_global_'.$hands[$i].'_angle','10').'",'."\n";
					echo $tabsc.'repeat:"'.$slider->getParam('wb_global_'.$hands[$i].'_angle_repeat','3').'"'."\n";
					echo $tabsb.'}'."\n";
				echo $tabsa.'}';
				if ($i<count($hands)-1) {
					echo ',';
				}
				echo "\n";
			}
			echo $tabs.'},'."\n";
		}
	}

	public static function enqueue_scripts($_record = array()){
		if ($_record && $_sliderId = RevSliderFunctions::getVal($_record, 'slider_id', false)) {

			$_slider = new RevSlider();
			$_slider->initByID($_sliderId);
			$_settings = $_slider->getParams();

			if ($_settings && RevSliderFunctions::getVal($_settings, 'wb_enable', false) == 'on') {
				wp_enqueue_script('rs-whiteboard', WHITEBOARD_PLUGIN_URL .'public/assets/js/revolution.addon.whiteboard.min.js', array('jquery','revmin'), WHITEBOARD_VERSION, false);
			}
		}

		remove_action('revslider_slide_initByData', array('rs_whiteboard_fe_slider', 'enqueue_scripts'), 10);
		return $_record;
	}

	public static function enqueue_scripts_html(){
		?>
		<script type="text/javascript" src="<?php echo WHITEBOARD_PLUGIN_URL .'public/assets/js/revolution.addon.whiteboard.min.js'; ?>"></script>
		<?php
	}

}
?>