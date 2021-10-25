<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_DUOTONEFILTERS_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsDuotoneFiltersSlideAdmin extends RsAddonDuotoneFiltersSlideAdmin {
	
	protected static $_Path,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title, $_path) {
		
		static::$_Title = $_title;
		static::$_Path = $_path;
		parent::init();
		
	}
	
	protected static function _init($_slider, $_slide) {
		
		$_selectedFilter  = $_slide->getParam('duotonefilter_addon', 'rs-duotone-none');
		$_textDomain      = 'rs_' . static::$_Title;
		$_filters         = array(
			
			'blue',
			'blue-dark',
			'blue-light',
			'orange',
			'orange-dark',
			'orange-light',
			'red',
			'red-dark',
			'red-light',
			'green',
			'green-dark',
			'green-light',
			'yellow',
			'yellow-dark',
			'yellow-light',
			'purple',
			'purple-dark',
			'purple-light',
			'pink',
			'pink-dark',
			'pink-light',
			'blue-yellow',
			'blue-yellow-dark',
			'blue-yellow-light',
			'pink-yellow',
			'pink-yellow-dark',
			'pink-yellow-light',
			'red-blue',
			'red-blue-dark',
			'red-blue-light'
		
		);
		
		$_markup = '<div id="duotonefilters-addon-settings-wrap">';
			
			foreach($_filters as $_filter) {
				
				$_selected = 'rs-duotone-' . $_filter !== $_selectedFilter ? '' : ' selected';
				$_markup .= '
				
					<div class="rsaddon-duotone-filter' . $_selected . '" data-filter="rs-duotone-' . $_filter . '" data-title="' . ucwords(preg_replace('/\-/', ' ', $_filter)) . '">
						
						<div class="' . 'rs-duotone-' . $_filter . ' rsaddon-duotone-blend"><div class="rs-duotone-thumb"></div></div>
						<div class="rsaddon-duotone-img"></div>
					
					</div>
				
				';
				
			}
			
		$_markup .= '<input type="hidden" id="duotonefilter_addon" name="duotonefilter_addon" value="' . $_selectedFilter . '" />

		</div>'; 
		
		static::$_Markup     = $_markup;
		static::$_JavaScript = '
			
			var RsAddonDuotone = {
					
				reg: "' . implode('|', $_filters) . '",
				selected: "' . $_selectedFilter . '"
				
			};

		';
		
	}
}
?>