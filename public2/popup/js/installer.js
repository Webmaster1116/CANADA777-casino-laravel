"use strict";
var loading = false;
function continue_handler() {
	jQuery("#continue").html('<i class="fas fa-spinner fa-spin"></i> Loading...');
	jQuery("#message").slideUp(250);
	loading = true;
	var post_data = {};
	jQuery(".front-box").find("input, textarea, select").each(function() {
		var name = jQuery(this).attr("name");
		if (jQuery(this).is(":checked")) post_data[name] = "on";
		else post_data[name] = jQuery(this).val();
	});

	jQuery.ajax({
		type	: "POST",
		url		: "install.php", 
		data	: post_data,
		success	: function(return_data) {
			loading = false;
			jQuery("#continue").html('<i class="fas fa-angle-double-right"></i> Continue');
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					jQuery("#content").fadeOut(250, function(){
						jQuery("#content").html(data.html);
						jQuery("#content").fadeIn(250);
					});
				} else if (status == "ERROR") {
					jQuery("#message").html(data.message);
					jQuery("#message").slideDown(250);
				} else {
					jQuery("#message").html('Something went wrong. We got unexpected server response.');
					jQuery("#message").slideDown(250);
				}
			} catch(error) {
				jQuery("#message").html('Something went wrong. We got unexpected server response.');
				jQuery("#message").slideDown(250);
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			loading = false;
			jQuery("#continue").html('<i class="fas fa-angle-double-right"></i> Continue');
			jQuery("#message").html('Something went wrong. We got unexpected server response.');
			jQuery("#message").slideDown(250);
		}
	});
	return false;
}
