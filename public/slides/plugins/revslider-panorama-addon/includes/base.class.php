<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_PANORAMA_PLUGIN_PATH . 'framework/base.class.php');

class RsPanoramaBase extends RsAddOnPanoramaBase {
	
	protected static $_PluginPath    = RS_PANORAMA_PLUGIN_PATH,
					 $_PluginUrl     = RS_PANORAMA_PLUGIN_URL,
					 $_PluginTitle   = 'panorama',
				     $_FilePath      = __FILE__,
				     $_Version       = '1.0.0';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_PANORAMA_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnPanoramaNotice($notice, static::$_PluginTitle);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>