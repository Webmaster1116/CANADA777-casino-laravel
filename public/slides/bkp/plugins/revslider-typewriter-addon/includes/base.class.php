<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if(!defined('ABSPATH')) exit();

require_once(RS_TYPEWRITER_PLUGIN_PATH . 'framework/base.class.php');

class RsTypewriterBase extends RsAddOnBase {

	protected static $_PluginPath    = RS_TYPEWRITER_PLUGIN_PATH,
					 $_PluginUrl     = RS_TYPEWRITER_PLUGIN_URL,
					 $_PluginTitle   = 'typewriter',
				     $_FilePath      = __FILE__,
				     $_Version       = '1.0.3.1';

	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		parent::_loadPluginTextDomain();

		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {

			require_once(RS_TYPEWRITER_PLUGIN_PATH . 'framework/notices.class.php');

			new RsAddOnNotice($notice, static::$_PluginTitle);
			return;

		}

		parent::loadClasses();

	}

	// page/post meta box
	protected static function populateMetaBox($obj) {

		echo '<input type="hidden" name="rs_addon_typewriter_meta" value="' . implode(get_post_meta($obj->ID, 'rs-addon-typewriter')) . '" />';

	}

}
?>