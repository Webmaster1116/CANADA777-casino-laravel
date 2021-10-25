<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsAddonPanoramaSlideAdmin {
	
	protected function init() {
		
		add_filter('revslider_slide_settings_addons', array($this, 'add_addon_settings'), 10, 3);
		add_action( 'rs_action_add_layer_action', array($this, 'add_layer_actions'), 10);
		add_action('rs_action_add_layer_action_details', array($this, 'add_layer_actions_details'), 10);
		
	}
	
	public function add_layer_actions() {
		
		$_textDomain = 'rs_' . static::$_Title;
		
		echo '<option disabled></option>';
		echo '<option disabled>---- Panorama Controls ----</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_left\'         ) { #>selected <# } #> value="panorama_left">'         . __('Panorama Pan-Left',        $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_leftstart\'    ) { #>selected <# } #> value="panorama_leftstart">'    . __('Panorama Pan-Left Start',  $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_leftend\'      ) { #>selected <# } #> value="panorama_leftend">'      . __('Panorama Pan-Left End',    $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_right\'        ) { #>selected <# } #> value="panorama_right">'        . __('Panorama Pan-Right',       $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_rightstart\'   ) { #>selected <# } #> value="panorama_rightstart">'   . __('Panorama Pan-Right Start', $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_rightend\'     ) { #>selected <# } #> value="panorama_rightend">'     . __('Panorama Pan-Right End',   $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_up\'           ) { #>selected <# } #> value="panorama_up">'           . __('Panorama Pan-Up',          $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_upstart\'      ) { #>selected <# } #> value="panorama_upstart">'      . __('Panorama Pan-Up Start',    $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_upend\'        ) { #>selected <# } #> value="panorama_upend">'        . __('Panorama Pan-Up End',      $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_down\'         ) { #>selected <# } #> value="panorama_down">'         . __('Panorama Pan-Down',        $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_downstart\'    ) { #>selected <# } #> value="panorama_downstart">'    . __('Panorama Pan-Down Start',  $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_downend\'      ) { #>selected <# } #> value="panorama_downend">'      . __('Panorama Pan-Down End',    $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoomin\'       ) { #>selected <# } #> value="panorama_zoomin">'       . __('Panorama Zoom-In',         $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoominstart\'  ) { #>selected <# } #> value="panorama_zoominstart">'  . __('Panorama Zoom-In Start',   $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoominend\'    ) { #>selected <# } #> value="panorama_zoominend">'    . __('Panorama Zoom-In End',     $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoomout\'      ) { #>selected <# } #> value="panorama_zoomout">'      . __('Panorama Zoom-Out',        $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoomoutstart\' ) { #>selected <# } #> value="panorama_zoomoutstart">' . __('Panorama Zoom-Out Start',  $_textDomain) . '</option>';
		echo '<option <# if(data[\'action\'] == \'panorama_zoomoutend\'   ) { #>selected <# } #> value="panorama_zoomoutend">'   . __('Panorama Zoom-Out End',    $_textDomain) . '</option>';
	
	}
	
	public function add_layer_actions_details() {
		
		$_textDomain = 'rs_' . static::$_Title;
		
		?>
		<span class="panorama-action"<# if(data.action) {if(data.action.search('panorama') !== -1 && data.action.search(/zoom|end/) === -1) { #> style="display: inline" <# }} #>>
			
			<select type="text" 
			        class="rs-layer-input-field panorama-action-input" 
			        title="<?php _e('The distance the image should pan/zoom to', $_textDomain); ?>" 
			        name="<# if(data.edit == false) { #>no_<# } #>panorama_amount[]" 
			        value="{{ data['panorama_amount'] }}">
				
				<option value="5"  <# if(data['panorama_amount'] == 5)  { #> selected<# } #>>5%</option>
				<option value="10" <# if(data['panorama_amount'] == 10) { #> selected<# } #>>10%</option>
				<option value="15" <# if(data['panorama_amount'] == 15) { #> selected<# } #>>15%</option>
				<option value="20" <# if(data['panorama_amount'] == 20) { #> selected<# } #>>20%</option>
				<option value="25" <# if(data['panorama_amount'] == 25) { #> selected<# } #>>25%</option>
				<option value="33" <# if(data['panorama_amount'] == 33) { #> selected<# } #>>33%</option>
				<option value="50" <# if(data['panorama_amount'] == 50) { #> selected<# } #>>50%</option>
				
			</select>
		</span>
		<?php
	
	}
	
	public function add_addon_settings($_settings, $_slide, $_slider) {
		
		// only add to slide editor if enabled from slider settings first
		if($_slider->getParam(static::$_Title . '_enabled', false) == 'true') {
		
			static::_init($_slider, $_slide);
			
			$_settings[static::$_Title] = array(
			
				'title'		 => 'Panorama',
				'markup'	 => static::$_Markup,
				'javascript' => static::$_JavaScript
			   
			);
			
		}
		
		return $_settings;
		
	}
	
}
?>