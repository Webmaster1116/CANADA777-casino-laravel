<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsAddonParticlesSliderAdmin {
	
	protected function init() {
		
		add_filter('revslider_slider_addons', array($this, 'add_addon_settings'), 10, 2);
		add_action('wp_ajax_revslider_particles', array($this, 'revslider_particles_ajax'));
		
	}
	
	public function add_addon_settings($_settings, $_slider){
		
		static::_init($_slider);
		
		$_settings[static::$_Title] = array(
		
			'title'		 => ucfirst(static::$_Title),
			'icon'		 => static::$_Icon,
			'markup'	 => static::$_Markup,
		    'javascript' => static::$_JavaScript
		   
		);
		
		return $_settings;
		
	}
	
	public function revslider_particles_ajax() {
		
		if(!isset($_POST['revslider_particles_nonce']) || !wp_verify_nonce($_POST['revslider_particles_nonce'], 'revslider-particles-nonce')) {
			
			die('Particles Templates Ajax Error');
			
		}
		
		$_custom = get_option('revslider_addon_particles_templates');
		if(isset($_POST['name']) && isset($_POST['task'])) {
			
			$_name = $_POST['name'];
			switch($_POST['task']) {
				
				case 'read':
					
					$_core = RsParticlesTemplates::$_Templates;
					if($_core && array_key_exists($_name, $_core)) {
						
						echo json_encode($_core[$_name]);
						
					}	
					else if($_custom && array_key_exists($_name, $_custom)) {
						
						echo json_encode($_custom[$_name]);
						
					}
					
				break;
				
				case 'write':
					
					if(isset($_POST['settings'])) {
						
						if($_custom) {
							
							if(!array_key_exists($_name, $_custom)) {
							
								$_custom[$_name] = json_decode(stripslashes($_POST['settings']), true);
								update_option('revslider_addon_particles_templates', $_custom);
								
							}
							else {	
								_e('Particles Template Name already exists.  Please choose another name.', $_textDomain);
							}
							
						}
						else {
							
							$_custom = array($_name => json_decode(stripslashes($_POST['settings']), true));
							update_option('revslider_addon_particles_templates', $_custom);
							
						}
						
					}
					else {
						_e('Particles Template Failed to Save', $_textDomain);
					}
					
				break;
				
				case 'remove':
					
					if($_custom && array_key_exists($_name, $_custom)) {
						
						unset($_custom[$_name]);
						update_option('revslider_addon_particles_templates', $_custom);
						
					}
					else {
						_e('Particles Template Failed to Delete', $_textDomain);
					}
					
				break;
				
			}
			
		}
		else {
			_e('Particles Templates Ajax Request Failed', $_textDomain);
		}
		
		die();
		
	}
	
}
?>