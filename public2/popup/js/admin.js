"use strict";
var UAP_CORE = true;
function toggle_mail_method(object) {
	if (jQuery(object).val() == "smtp") {
		jQuery("#mail-method-mail").fadeOut(250, function() {
			jQuery("#mail-method-smtp").fadeIn(250);
		});
	} else {
		jQuery("#mail-method-smtp").fadeOut(250, function() {
			jQuery("#mail-method-mail").fadeIn(250);
		});
	}
}
function save_settings() {
	jQuery("#save-settings-button").attr('disabled', 'disabled');
	jQuery("#save-settings-button i").attr('class', 'fas fa-spinner fa-spin');
	if (jQuery('#save-settings-message').length == 0) jQuery("#global-message-container").append('<div id="save-settings-message"></div>');
	var post_data = {};
	jQuery("#settings-data").find("input, textarea, select").each(function() {
		var name = jQuery(this).attr("name");
		if (jQuery(this).prop("type") == "radio") {
			if (jQuery(this).is(":checked")) post_data[name] = jQuery(this).val();
		} else if (jQuery(this).prop("type") == "checkbox") {
			if (jQuery(this).is(":checked")) post_data[name] = "on";
		} else post_data[name] = jQuery(this).val();
	});
	jQuery.ajax({
		type	: "POST",
		url		: ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery("#save-settings-button").removeAttr('disabled');
			jQuery("#save-settings-button i").attr('class', 'fas fa-check');
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					jQuery("#save-settings-message").html("<div class='global-message global-message-success'>"+data.message+"</div>");
					jQuery("#save-settings-message").slideDown(250);
				} else if (status == "ERROR") {
					jQuery("#save-settings-message").html("<div class='global-message global-message-danger'>"+data.message+"</div>");
					jQuery("#save-settings-message").slideDown(250);
				} else {
					jQuery("#save-settings-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
					jQuery("#save-settings-message").slideDown(250);
				}
				jQuery('html,body').animate({scrollTop: 0});
			} catch(error) {
				jQuery("#save-settings-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
				jQuery("#save-settings-message").slideDown(250);
				jQuery('html,body').animate({scrollTop: 0});
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery("#save-settings-button").removeAttr('disabled');
			jQuery("#save-settings-button i").attr('class', 'fas fa-check');
			jQuery("#save-settings-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
			jQuery("#save-settings-message").slideDown(250);
			jQuery('html,body').animate({scrollTop: 0});
		}
	});
	return false;
}
function plugin_uploaded() {
	if (jQuery('#upload-message').length == 0) jQuery("#global-message-container").append('<div id="upload-message"></div>');
	try {
		var return_data = jQuery("#upload-target").contents().find("body").html();
		var data = jQuery.parseJSON(return_data);
		var status = data.status;
		if (status == "OK") {
			location.href = data.url;
		} else if (status == "ERROR") {
			jQuery("#plugins-item-new i").attr("class", "fas fa-plus");
			jQuery("#upload-message").html("<div class='global-message global-message-danger'>"+data.message+"</div>");
			jQuery("#upload-message").slideDown(250);
		} else {
			jQuery("#plugins-item-new i").attr("class", "fas fa-plus");
			jQuery("#upload-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
			jQuery("#upload-message").slideDown(250);
		}
	} catch(error) {
		jQuery("#plugins-item-new i").attr("class", "fas fa-plus");
	}
}
function toggle_plugin(object, slug, action) {
	if (jQuery('#toggle-plugin-message').length == 0) jQuery("#global-message-container").append('<div id="toggle-plugin-message"></div>');
	jQuery(object).attr('disabled', 'disabled');
	jQuery(object).find(".plugins-item-spinner").fadeIn(250);
	var post_data = {
		"slug"		: slug,
		"type"		: action,
		"action"	: "toggle-plugin"
	};
	jQuery.ajax({
		type	: "POST",
		url		: ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					location.href = data.url;
				} else if (status == "ERROR") {
					jQuery(object).removeAttr('disabled');
					jQuery(object).find(".plugins-item-spinner").fadeOut(250);
					jQuery("#toggle-plugin-message").html("<div class='global-message global-message-danger'>"+data.message+"</div>");
					jQuery("#toggle-plugin-message").slideDown(250);
				} else {
					jQuery(object).removeAttr('disabled');
					jQuery(object).find(".plugins-item-spinner").fadeOut(250);
					jQuery("#toggle-plugin-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
					jQuery("#toggle-plugin-message").slideDown(250);
				}
				jQuery('html,body').animate({scrollTop: 0});
			} catch(error) {
				jQuery(object).removeAttr('disabled');
				jQuery(object).find(".plugins-item-spinner").fadeOut(250);
				jQuery("#toggle-plugin-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
				jQuery("#toggle-plugin-message").slideDown(250);
				jQuery('html,body').animate({scrollTop: 0});
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(object).removeAttr('disabled');
			jQuery(object).find(".plugins-item-spinner").fadeOut(250);
			jQuery("#toggle-plugin-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
			jQuery("#toggle-plugin-message").slideDown(250);
			jQuery('html,body').animate({scrollTop: 0});
		}
	});
	return false;
}
function test_mailing() {
	jQuery("#test-mailing-button").attr('disabled', 'disabled');
	jQuery("#test-mailing-button i").attr('class', 'fas fa-spinner fa-spin');
	if (jQuery('#test-mailing-message').length == 0) jQuery("#global-message-container").append('<div id="test-mailing-message"></div>');
	var post_data = {action : "test-mailing"};
	if (jQuery("#mail_method_smtp").is(":checked")) {
		post_data["mail_method"] = "smtp";
		post_data["smtp_from_name"] = jQuery("#smtp_from_name").val();
		post_data["smtp_from_email"] = jQuery("#smtp_from_email").val();
		post_data["smtp_secure"] = jQuery("#smtp_secure").val();
		post_data["smtp_server"] = jQuery("#smtp_server").val();
		post_data["smtp_port"] = jQuery("#smtp_port").val();
		post_data["smtp_username"] = jQuery("#smtp_username").val();
		post_data["smtp_password"] = jQuery("#smtp_password").val();
	} else {
		post_data["mail_method"] = "mail";
		post_data["mail_from_name"] = jQuery("#mail_from_name").val();
		post_data["mail_from_email"] = jQuery("#mail_from_email").val();
	}
	jQuery.ajax({
		type	: "POST",
		url		: ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery("#test-mailing-button").removeAttr('disabled');
			jQuery("#test-mailing-button i").attr('class', 'far fa-envelope');
			var data;
			try {
				var temp = /<hap-debug>(.*?)<\/hap-debug>/g.exec(return_data);
				if (temp) return_data = temp[1];
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					jQuery("#test-mailing-message").html("<div class='global-message global-message-success'>"+data.message+"</div>");
					jQuery("#test-mailing-message").slideDown(250);
				} else if (status == "ERROR") {
					jQuery("#test-mailing-message").html("<div class='global-message global-message-danger'>"+data.message+"</div>");
					jQuery("#test-mailing-message").slideDown(250);
				} else {
					jQuery("#test-mailing-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
					jQuery("#test-mailing-message").slideDown(250);
				}
				jQuery('html,body').animate({scrollTop: 0});
			} catch(error) {
				jQuery("#test-mailing-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
				jQuery("#test-mailing-message").slideDown(250);
				jQuery('html,body').animate({scrollTop: 0});
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery("#test-mailing-button").removeAttr('disabled');
			jQuery("#test-mailing-button i").attr('class', 'far fa-envelope');
			jQuery("#test-mailing-message").html("<div class='global-message global-message-danger'>Something went wrong. We got unexpected server response.</div>");
			jQuery("#test-mailing-message").slideDown(250);
			jQuery('html,body').animate({scrollTop: 0});
		}
	});
	return false;
}
jQuery(document).ready(function() {
	jQuery(".hap-sidebar-menu>ul>li>a").on("click", function(e) {
		var li = jQuery(this).parent();
		if (li.find("ul").length > 0) {
			e.preventDefault();
			li.find("ul").slideToggle(300);
		}
	});
	jQuery('.hap-container').css('min-height', jQuery(window).height());
	jQuery(window).resize(function(){
		jQuery('.hap-container').css('min-height', jQuery(window).height());
	});
});