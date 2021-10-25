
/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

var EditAccount = new function(){

	var t = this;

	t.init = function() {
		jQuery('#edit_account_link').on('click', function(e) {
			openDialog();
			e.preventDefault();
		});
	}

	var openDialog = function() {
		jQuery('<div class="edit_account_dialog" />').load(g_urlEditAccount, function() {
			initDialog();
		}).dialog({
			minWidth:600,
			minHeight:200,
			modal:true,
            closeOnEscape:true,
			dialogClass:"tpdialogs",
			create:function(ui) {
				jQuery(ui.target).parent().find('.ui-dialog-titlebar').addClass("tp-slider-new-dialog-title");
			},
			open: function(event, ui) {
				jQuery(event.target).parents('.ui-dialog').attr('id', 'viewWrapper');
			},
            close: function() {
			    jQuery(this).empty().remove();
            }
		});
	}

	var initDialog = function() {
		jQuery('.edit_account_dialog').dialog('option', 'title', jQuery('#edit_account_dialog').data('title') );
		jQuery("#button_save_account").click(function(){
			var data = RevSliderSettings.getSettingsObject("form_edit_account");
			UniteAdminRev.ajaxRequest("update_account",data,function(response){
				jQuery(".edit_account_dialog").dialog("close");
			});
		});
	}

}

jQuery(document).ready(function(){
	EditAccount.init();
});