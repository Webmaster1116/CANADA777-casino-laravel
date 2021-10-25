<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

require_once(RS_TYPEWRITER_PLUGIN_PATH . 'framework/slide.admin.class.php');

class RsTypewriterSlideAdmin extends RsAddonSlideAdmin {
	
	protected static $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_textDomain = 'rs_' . static::$_Title;
		
		$_delays         = $_slider->getParam('typewriter_defaults_delays', '');
		$_speed          = $_slider->getParam('typewriter_defaults_speed', '30');
		$_cursor         = $_slider->getParam('typewriter_defaults_cursor_type', 'one');
		$_startDelay     = $_slider->getParam('typewriter_defaults_start_delay', '1000');
		$_newlineDelay   = $_slider->getParam('typewriter_defaults_newline_delay', '1000');
		$_blinkingSpeed  = $_slider->getParam('typewriter_defaults_blinking_speed', '500');
		$_deletionSpeed  = $_slider->getParam('typewriter_defaults_deletion_speed', '20');
		$_linebreakDelay = $_slider->getParam('typewriter_defaults_linebreak_delay', '60');
		$_deletionDelay  = $_slider->getParam('typewriter_defaults_deletion_delay', '1000');
		
		$_looped         = $_slider->getParam('typewriter_defaults_looped', false) == 'true' ? 'on' : 'off';
		$_blinking       = $_slider->getParam('typewriter_defaults_blinking', false) == 'true' ? 'on' : 'off';
		$_sequenced      = $_slider->getParam('typewriter_defaults_sequenced', false) == 'true' ? 'on' : 'off';
		$_wordDelay      = $_slider->getParam('typewriter_defaults_word_delay', false) == 'true' ? 'on' : 'off';
		$_hideCursor     = $_slider->getParam('typewriter_defaults_hide_cursor', false) == 'true' ? 'on' : 'off';
		
		if($_cursor === 'one') {
			
			$_cursorOne = ' selected';
			$_cursorTwo = '';
			
		}
		else {
			
			$_cursorOne = '';
			$_cursorTwo = ' selected';
			
		}
		
		static::$_Markup = 
		
		'<div id="typewriter-addon-settings">
			
			<div id="typewriter-addon-settings-regular" class="typewriter-settings">
			
				<span class="rs-layer-toolbar-box cj-addon-enable">
				
					<i class="rs-mini-layer-icon eg-icon-print rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Enable/Disable Typewriter Effect for the selected Layer', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_enabled" name="typewriter_enabled" class="tp-moderncheckbox tipsy_enabled_top" value="off" original-title="' . __('Enable/Disable Typewriter Effect for the selected Layer', $_textDomain) . '" />
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon eg-icon-magic rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Use a Blinking Cursor to simulate the typing effect', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_blinking" class="tp-moderncheckbox cj-addon-advanced tipsy_enabled_top" data-toggle="cursor_type blinking_speed hide_cursor" value="' . $_blinking . '"  original-title="' . __('Use a Blinking Cursor to simulate the typing effect', $_textDomain) . '" />
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon eg-icon-list-add rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Allow for multiple/sequenced lines', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_sequenced" class="tp-moderncheckbox cj-addon-advanced tipsy_enabled_top" data-toggle="deletion_speed deletion_delay looped newline_delay" value="' . $_sequenced . '" original-title="' . __('Allow for multiple/sequenced lines', $_textDomain) . '" />
					
				</span>
					
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon rs-icon-transition rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Typing Speed: time in which each character is &quot;typed&quot; (in milliseconds)', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_speed" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Typing Speed: time in which each character is &quot;typed&quot; (in milliseconds)', $_textDomain) . '" 
						value="' . $_speed . '" 
						data-selects="Custom||20ms||30ms||50ms||75ms||100ms||250ms" 
						data-svalues ="25||20||30||50||75||100||250" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
					
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon rs-icon-clock rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Start Delay: time before the typing officially starts (in milliseconds) after the Layer first appears', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_start_delay" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Start Delay: time before the typing officially starts (in milliseconds) after the Layer first appears', $_textDomain) . '" 
						value="' . $_startDelay . '" 
						data-selects="Custom||250ms||500ms||1000ms||1500ms||2000ms||3000ms" 
						data-svalues ="750||250||500||1000||1500||2000||3000" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
				
				<span class="rs-layer-toolbar-box">
				
					<i class="rs-mini-layer-icon fa-icon-sliders rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Enable/Disable Word Delays: add delays between words to further simulate real-time typing', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_word_delay" class="tp-moderncheckbox cj-addon-advanced-2 tipsy_enabled_top" value="' . $_wordDelay . '" original-title="' . __('Enable/Disable Word Delays: add delays between words to further simulate real-time typing', $_textDomain) . '" />
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-align-left rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Line-break Delay: add a delay for HTML <br> tags that are included in the Layer&#39;s text', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_linebreak_delay" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Line-break Delay: add a delay for HTML <br> tags that are included in the Layer&#39;s text', $_textDomain) . '" 
						value="' . $_linebreakDelay . '" 
						data-selects="Custom||60ms||100ms||150ms||200ms||250ms||500ms" 
						data-svalues ="90||60||100||150||200||250||500" 
						data-icons="wrench||filter||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
				
			</div>
			
			<div id="typewriter-addon-settings-advanced">
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-eye rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Blinking Speed for the Cursor (in milliseconds)', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_blinking_speed" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Blinking Speed for the Cursor (in milliseconds)', $_textDomain) . '" 
						value="' . $_blinkingSpeed . '" 
						data-selects="Custom||500ms||750ms||1000ms||1500ms" 
						data-svalues ="600||500||750||1000||1500" 
						data-icons="wrench||filter||filter||filter||filter" 
						
					/>
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-terminal rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Use an &quot;underscore&quot; or &quot;vertical bar&quot; for the Blinking Cursor', $_textDomain) . '"></i>
					<select name="typewriter_cursor_type" class="rs-layer-input-field tipsy_enabled_top" original-title="' . __('Use an &quot;underscore&quot; or &quot;vertical bar&quot; for the Blinking Cursor', $_textDomain) . '">
						<option value="one"' . $_cursorOne . '>' . __('Text', $_textDomain) . ' _</option>
						<option value="two"' . $_cursorTwo . '>' . __('Text', $_textDomain) . ' |</option>
					</select>
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-ban rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Hide the Blinking Cursor when typing has completed for the Layer', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_hide_cursor" class="tp-moderncheckbox tipsy_enabled_top" value="' . $_hideCursor . '" original-title="' . __('Hide the Blinking Cursor when typing has completed for the Layer', $_textDomain) . '" />
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon rs-icon-transition rs-toolbar-icon tipsy_enabled_top cj-reverse-icon" original-title="' . __('Character Deletion Speed (in milliseconds)', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_deletion_speed" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Character Deletion Speed (in milliseconds)', $_textDomain) . '" 
						value="' . $_deletionSpeed . '" 
						data-selects="Custom||20ms||30ms||50ms||100ms" 
						data-svalues ="25||20||30||50||100" 
						data-icons="wrench||filter||filter||filter||filter" 
						
					/>
					
				</span>
					
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-dashboard rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Deletion Delay: time before the current line begins to erase itself (in milliseconds)', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_deletion_delay" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('Deletion Delay: time before the current line begins to erase itself (in milliseconds)', $_textDomain) . '" 
						value="' . $_deletionDelay . '" 
						data-selects="Custom||500ms||750ms||1000ms||1500ms||2000ms" 
						data-svalues ="600||500||750||1000||1500||2000" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon fa-icon-history rs-toolbar-icon tipsy_enabled_top" original-title="' . __('New Line Delay: time before the next sequenced line is &quot;typed&quot; (once the previous line has been erased)', $_textDomain) . '"></i>
					<input 
					
						type="text" 
						name="typewriter_newline_delay" 
						class="textbox-caption input-deepselects rs-layer-input-field tipsy_enabled_top" 
						original-title="' . __('New Line Delay: time before the next sequenced line is &quot;typed&quot; (once the previous line has been erased)', $_textDomain) . '" 
						value="' . $_newlineDelay . '" 
						data-selects="Custom||500ms||750ms||1000ms||1500ms||2000ms" 
						data-svalues ="600||500||750||1000||1500||2000" 
						data-icons="wrench||filter||filter||filter||filter||filter" 
						
					/>
					
				</span>
				
				<span class="rs-layer-toolbar-box">
					
					<i class="rs-mini-layer-icon eg-icon-arrows-ccw rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Constant Loop: restart the sequence after the last line has been shown', $_textDomain) . '"></i>
					<input type="checkbox" name="typewriter_looped" class="tp-moderncheckbox tipsy_enabled_top" value="' . $_looped . '" original-title="' . __('Constant Loop: restart the sequence after the last line has been shown', $_textDomain) . '" />
					
				</span>
				
			</div>
			
			<div id="typewriter-addon-settings-advanced-2">
				
				<span id="typewriter-word-patterns" class="rs-layer-toolbar-box">
					
					<span class="button-primary revgreen rs-toolbar-icon tipsy_enabled_top" original-title="' . __('Add a delay (in milliseconds) after a set amount of words', $_textDomain) . '"><i class="eg-icon-plus"></i>Word Delay Pattern</span>
					
				</span>
				<div class="clear"></div>
			
			</div>
			
			<input type="hidden" name="typewriter_lines" value="" />
			<input type="hidden" name="typewriter_delays" value="' . $_delays . '" />
		
		</div>';
		
		static::$_JavaScript = 
		
			'var RsTypeWriter = {
				
				"lines"            : "",
				"enabled"          : "off",
				"speed"            : "' . $_speed . '",
				"delays"           : "' . $_delays . '",
				"looped"           : "' . $_looped . '",
				"cursorType"       : "' . $_cursor . '",
				"blinking"         : "' . $_blinking . '",
				"word_delay"       : "' . $_wordDelay . '",
				"sequenced"        : "' . $_sequenced . '",
				"hide_cursor"      : "' . $_hideCursor . '",
				"start_delay"      : "' . $_startDelay . '",
				"newline_delay"    : "' . $_newlineDelay . '",
				"deletion_speed"   : "' . $_deletionSpeed . '",
				"deletion_delay"   : "' . $_deletionDelay . '",
				"blinking_speed"   : "' . $_blinkingSpeed . '",
				"linebreak_delay"  : "' . $_linebreakDelay . '",
				
			}, RsTypeWriterSliderType = "' . $_slider->getParam('source_type', 'gallery') . '";';
		
	}
}
?>