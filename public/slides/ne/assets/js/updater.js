jQuery(document).ready(function() {
	jQuery('#download_addon').click(function(){

		UniteAdminRev.setAjaxLoaderID("download_loader");
		UniteAdminRev.setAjaxHideButtonID("download_addon");

		var data = {
			code: jQuery('input[name="addon_purchase_token"]').val().replace(/[^0-9a-z-]/gi, '')
		}

		UniteAdminRev.ajaxRequest("download_addon",data);
	});

	jQuery('#deactivate_addon').click(function(){

		UniteAdminRev.setAjaxLoaderID("download_loader");
		UniteAdminRev.setAjaxHideButtonID("download_addon");

		var data = {
			code: jQuery('input[name="addon_purchase_token"]').val().replace(/[^0-9a-z-]/gi, '')
		}

		UniteAdminRev.ajaxRequest("deactivate_addon",data);
	});
});