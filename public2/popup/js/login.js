"use strict";
var loading = false;
function switch_login() {
	jQuery("#message").slideUp(250);
	jQuery("#login-form").fadeOut(250, function() {jQuery("#reset-form").fadeIn(250);});
	return false;
}
function switch_reset() {
	jQuery("#message").slideUp(250);
	jQuery("#reset-form").fadeOut(250, function() {jQuery("#login-form").fadeIn(250);});
	return false;
}
function login() {
	jQuery("#login").html('<i class="fas fa-spinner fa-spin"></i> Login');
	jQuery("#message").slideUp(250);
	loading = true;
	var post_data = {};
	jQuery("#login-form").find("input, textarea, select").each(function() {
		var name = jQuery(this).attr("name");
		if (jQuery(this).is(":checked")) post_data[name] = "on";
		else post_data[name] = jQuery(this).val();
	});
	jQuery.ajax({
		type	: "POST",
		url		: login_handler, 
		data	: post_data,
		success	: function(return_data) {
			loading = false;
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					location.href = data.url;
				} else if (status == "ERROR") {
					jQuery("#login").html('<i class="fas fa-angle-double-right"></i> Login');
					jQuery("#message").html(data.message);
					jQuery("#message").slideDown(250);
				} else {
					jQuery("#login").html('<i class="fas fa-angle-double-right"></i> Login');
					jQuery("#message").html('Something went wrong. We got unexpected server response.');
					jQuery("#message").slideDown(250);
				}
			} catch(error) {
				jQuery("#login").html('<i class="fas fa-angle-double-right"></i> Login');
				jQuery("#message").html('Something went wrong. We got unexpected server response.');
				jQuery("#message").slideDown(250);
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			loading = false;
			jQuery("#login").html('<i class="fas fa-angle-double-right"></i> Login');
			jQuery("#message").html('Something went wrong. We got unexpected server response.');
			jQuery("#message").slideDown(250);
		}
	});
	return false;
}
function reset_password() {
	jQuery("#reset").html('<i class="fas fa-spinner fa-spin"></i> Reset');
	jQuery("#message").slideUp(250);
	loading = true;
	var post_data = {};
	jQuery("#reset-form").find("input, textarea, select").each(function() {
		var name = jQuery(this).attr("name");
		if (jQuery(this).is(":checked")) post_data[name] = "on";
		else post_data[name] = jQuery(this).val();
	});
	jQuery.ajax({
		type	: "POST",
		url		: login_handler, 
		data	: post_data,
		success	: function(return_data) {
			loading = false;
			jQuery("#reset").html('<i class="fas fa-angle-double-right"></i> Reset');
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					jQuery("#reset-form").fadeOut(250, function() {
						jQuery("#reset-form").html(data.html);
						jQuery("#reset-form").fadeIn(250);
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
			jQuery("#reset").html('<i class="fas fa-angle-double-right"></i> Reset');
			jQuery("#message").html('Something went wrong. We got unexpected server response.');
			jQuery("#message").slideDown(250);
		}
	});
	return false;
}
jQuery(document).keyup(function(e) {
	if (e.keyCode == 13) {
		if (jQuery(document.activeElement).hasClass("input-field")) {
			if (jQuery(document.activeElement).prop("tagName").toLowerCase() == "textarea" && !e.ctrlKey) {
				return;
			}
			var content_box = jQuery(document.activeElement).parents(".content-box");
			if (content_box) {
				var button = jQuery(content_box).find(".button");
				if (button) jQuery(button).click();
			}
		}
	}
});
