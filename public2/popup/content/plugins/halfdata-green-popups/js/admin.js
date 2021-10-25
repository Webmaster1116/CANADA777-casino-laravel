"use strict";
var lepopup_sending = false;
var lepopup_context_menu_object = null;
var lepopup_properties_panel_object = null;
var lepopup_form_pages = new Array();
var lepopup_form_page_active = null;
var lepopup_form_elements = new Array();
var lepopup_form_last_id = 0;
var lepopup_integration_last_id = 0;
var lepopup_payment_gateway_last_id = 0;
var lepopup_form_changed = false;
var lepopup_css_tools = [{}];
var lepopup_font_weights = {
	'100' : 'Thin',
	'200' : 'Extra-light',
	'300' : 'Light',
	'400' : 'Normal',
	'500' : 'Medium',
	'600' : 'Demi-bold',
	'700' : 'Bold',
	'800' : 'Heavy',
	'900' : 'Black'
};
function lepopup_cookies_reset(_button) {
	if (lepopup_sending) return false;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	lepopup_sending = true;
	var post_data = {"action" : "lepopup-cookies-reset"};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-times");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			try {
				var data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
				}
			} catch(error) {
				lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-times");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", "Something went wrong. We got unexpected server response.");
			lepopup_sending = false;
		}
	});
	return false;
}

/* Dialog Popup - begin */
var lepopup_dialog_buttons_disable = false;
function lepopup_dialog_open(_settings) {
	var settings = {
		width: 				480,
		height:				210,
		title:				lepopup_esc_html__('Confirm action'),
		close_enable:		true,
		ok_enable:			true,
		cancel_enable:		true,
		ok_label:			lepopup_esc_html__('Yes'),
		cancel_label:		lepopup_esc_html__('Cancel'),
		echo_html:			function() {this.html(lepopup_esc_html__('Do you really want to continue?')); this.show();},
		ok_function:		function() {lepopup_dialog_close();},
		cancel_function:	function() {lepopup_dialog_close();},
		html:				function(_html) {jQuery("#lepopup-dialog .lepopup-dialog-content-html").html(_html);},
		show:				function() {jQuery("#lepopup-dialog .lepopup-dialog-loading").fadeOut(300);}
	}
	var objects = [settings, _settings],
    settings = objects.reduce(function (r, o) {
		Object.keys(o).forEach(function (k) {
			r[k] = o[k];
		});
		return r;
    }, {});
	
	lepopup_dialog_buttons_disable = false;
	jQuery("#lepopup-dialog .lepopup-dialog-loading").show();
	jQuery("#lepopup-dialog .lepopup-dialog-title h3 label").html(settings.title);
	if (settings.close_enable) jQuery("#lepopup-dialog .lepopup-dialog-title a").show();
	else jQuery("#lepopup-dialog .lepopup-dislog-title a").hide();
	
	settings.echo_html();
	var window_height = Math.min(2*parseInt((jQuery(window).height() - 100)/2, 10), settings.height);
	var window_width = Math.min(Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 880), 960), settings.width);
	jQuery("#lepopup-dialog").height(window_height);
	jQuery("#lepopup-dialog").width(window_width);
	jQuery("#lepopup-dialog .lepopup-dialog-inner").height(window_height);
	jQuery("#lepopup-dialog .lepopup-dialog-content").height(window_height - 104);
	
	jQuery("#lepopup-dialog .lepopup-dialog-button").off("click");
	jQuery("#lepopup-dialog .lepopup-dialog-button").removeClass("lepopup-dialog-button-disabled");

	if (settings.ok_enable) {
		jQuery("#lepopup-dialog .lepopup-dialog-button-ok").find("label").html(settings.ok_label);
		jQuery("#lepopup-dialog .lepopup-dialog-button-ok").on("click", function(e){
			e.preventDefault();
			if (!lepopup_dialog_buttons_disable && typeof settings.ok_function == "function") {
				settings.ok_function();
			}
		});
		jQuery("#lepopup-dialog .lepopup-dialog-button-ok").show();
	} else jQuery("#lepopup-dialog .lepopup-dialog-button-ok").hide();
	
	if (settings.cancel_enable) {
		jQuery("#lepopup-dialog .lepopup-dialog-button-cancel").find("label").html(settings.cancel_label);
		jQuery("#lepopup-dialog .lepopup-dialog-button-cancel").on("click", function(e){
			e.preventDefault();
			if (!lepopup_dialog_buttons_disable && typeof settings.cancel_function == "function") {
				settings.cancel_function();
			}
		});
		jQuery("#lepopup-dialog .lepopup-dialog-button-cancel").show();
	} else jQuery("#lepopup-dialog .lepopup-dialog-button-cancel").hide();
	
	jQuery("#lepopup-dialog-overlay").fadeIn(300);
	jQuery("#lepopup-dialog").css({
		'top': 					'50%',
		'transform': 			'translate(-50%, -50%) scale(1)',
		'-webkit-transform': 	'translate(-50%, -50%) scale(1)'
	});
}
function lepopup_dialog_close() {
	jQuery("#lepopup-dialog-overlay").fadeOut(300);
	jQuery("#lepopup-dialog").css({
		'transform': 			'translate(-50%, -50%) scale(0)',
		'-webkit-transform': 	'translate(-50%, -50%) scale(0)'
	});
	setTimeout(function(){jQuery("#lepopup-dialog").css("top", "-3000px")}, 300);
	return false;
}
/* Dialog Popup - end */

/* Settings - begin */
function lepopup_settings_save(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: jQuery(".lepopup-settings-form").serialize(),
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show('success', data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
/* Settings - end */

/* Campaigns - begin */
function lepopup_campaign_save(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: jQuery(".lepopup-campaign-properties-form").serialize(),
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show('success', data.message);
					location.reload(true);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_campaigns_status_toggle(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var campaign_id = jQuery(_object).attr("data-id");
	var campaign_status = jQuery(_object).attr("data-status");
	var campaign_status_label = jQuery(_object).closest("tr").find("td.column-active").html();
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	jQuery(_object).closest("tr").find("td.column-active").html("<i class='fas fa-spinner fa-spin'></i>");
	var post_data = {"action" : "lepopup-campaigns-status-toggle", "campaign-id" : campaign_id, "campaign-status" : campaign_status};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).html(do_label);
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).html(data.campaign_action);
					jQuery(_object).attr("data-status", data.campaign_status);
					jQuery(_object).attr("data-doing", data.campaign_action_doing);
					if (data.campaign_status == "active") jQuery(_object).closest("tr").find(".lepopup-table-list-badge-status").html("");
					else jQuery(_object).closest("tr").find(".lepopup-table-list-badge-status").html("<span class='lepopup-badge lepopup-badge-danger'>Inactive</span>");
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					jQuery(_object).closest("tr").find("td.column-active").html(campaign_status_label);
					lepopup_global_message_show("danger", data.message);
				} else {
					jQuery(_object).closest("tr").find("td.column-active").html(campaign_status_label);
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				jQuery(_object).closest("tr").find("td.column-active").html(campaign_status_label);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find("td.column-active").html(campaign_status_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_campaigns_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the campaign.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_campaigns_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}
function _lepopup_campaigns_delete(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var campaign_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-campaigns-delete", "campaign-id" : campaign_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest("tr").fadeOut(300, function(){
						jQuery(_object).closest("tr").remove();
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_campaigns_stats_reset(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to reset campaign statistics.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Reset',
		ok_function:	function(e){
			_lepopup_campaigns_stats_reset(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}
function _lepopup_campaigns_stats_reset(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var campaign_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-campaigns-stats-reset", "campaign-id" : campaign_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_campaigns_resize() {
	if (lepopup_more_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
		jQuery("#lepopup-more-using").height(popup_height);
		jQuery("#lepopup-more-using").width(popup_width);
		jQuery("#lepopup-more-using .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-more-using .lepopup-admin-popup-content").height(popup_height - 52);
	}
	if (lepopup_campaign_stats_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
		jQuery("#lepopup-campaign-stats").height(popup_height);
		jQuery("#lepopup-campaign-stats").width(popup_width);
		jQuery("#lepopup-campaign-stats .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-campaign-stats .lepopup-admin-popup-content").height(popup_height - 52);
	}
	if (lepopup_campaign_properties_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
		jQuery("#lepopup-campaign-properties").height(popup_height);
		jQuery("#lepopup-campaign-properties").width(popup_width);
		jQuery("#lepopup-campaign-properties .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-campaign-properties .lepopup-admin-popup-content").height(popup_height - 104);
	}
}
function lepopup_campaigns_ready() {
	lepopup_campaigns_resize();
	jQuery(window).resize(function() {
		lepopup_campaigns_resize();
	});
}
/* Campaigns - end */

/* Form Editor - begin */
function lepopup_create() {
	var name = jQuery("#lepopup-create-name").val();
	if (name.length < 1) name = lepopup_esc_html__("Untitled Popup");
	lepopup_form_options["name"] = name;
	jQuery(".lepopup-admin-create-overlay").fadeOut(300);
	jQuery(".lepopup-admin-create").fadeOut(300);
	if (lepopup_gettingstarted_enable == "on") lepopup_gettingstarted("create-form", 0);
	return false;
}
function lepopup_save(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	if (lepopup_element_properties_active) {
		lepopup_properties_panel_close();
	}	
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	var post_pages = new Array();
	jQuery(".lepopup-pages-bar-item, .lepopup-pages-bar-item-confirmation").each(function(){
		var page_id = jQuery(this).attr("data-id");
		for (var i=0; i<lepopup_form_pages.length; i++) {
			if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] == page_id) {
				post_pages.push(lepopup_encode64(JSON.stringify(lepopup_form_pages[i])));
				break;
			}
		}
	});
	var post_elements = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (jQuery("#lepopup-element-"+i).length && lepopup_form_elements[i] != null) post_elements.push(lepopup_encode64(JSON.stringify(lepopup_form_elements[i])));
	}
	var post_data = {"action" : "lepopup-form-save", "form-id" : jQuery("#lepopup-id").val(), "form-slug" : jQuery("#lepopup-slug").val(), "form-options" : lepopup_encode64(JSON.stringify(lepopup_form_options)), "form-pages" : post_pages, "form-elements" : post_elements};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_form_changed = false;
					lepopup_element_properties_data_changed = false;
					jQuery("#lepopup-id").val(data.form_id);
					var url = window.location.href;
					if (url.indexOf("&id=") < 0) {
						history.pushState(null, null, url+"&id="+data.form_id);
						if (lepopup_gettingstarted_enable == "on") lepopup_gettingstarted("form-saved", 0);
					}
					jQuery(".lepopup-header-using span").attr("data-id", data.form_id);
					jQuery(".lepopup-header-using span").fadeIn(300);
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				console.log(error);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).find("i").attr("class", "far fa-save");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "far fa-save");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
/* Form Editor - end */

/* Properties Panel - begin */
var lepopup_properties_panel_loading = false;
	function _lepopup_rebuild_active_element() {
		var idx;
		var output;
		if (lepopup_element_properties_active) {
			idx = jQuery(lepopup_element_properties_active).attr("id");
			if (idx) {
				idx = idx.replace("lepopup-element-", "");
				jQuery("#lepopup-element-style-"+idx).remove();
				if (lepopup_form_elements[idx] && lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[idx]["type"]) && lepopup_form_elements[idx]["type"] == "hidden") {
					output = _lepopup_build_hidden(lepopup_form_page_active, idx);
				} else {
					output = _lepopup_build_children(lepopup_form_page_active, idx);
				}
				jQuery(lepopup_element_properties_active).replaceWith(output["html"]);
				lepopup_element_properties_active = jQuery("#lepopup-element-"+idx);
				for (var i=0; i<output["webfonts"].length; i++) {
					jQuery("#lepopup-element-style-"+idx).append("<link href='//fonts.googleapis.com/css?family="+output["webfonts"][i].replace(" ", "+")+":100,200,300,400,500,600,700,800,900&subset=arabic,vietnamese,hebrew,thai,bengali,latin,latin-ext,cyrillic,cyrillic-ext,greek' rel='stylesheet' type='text/css'>");
				}
				_lepopup_init_elements("#lepopup-element-"+idx);
			}
		}
	}

	function lepopup_properties_panel_open(_object) {
		var idx;
		if (lepopup_element_properties_active) {
			if (jQuery(lepopup_element_properties_active).attr("id") == jQuery(_object).attr("id")) return;
		}
		if (lepopup_properties_panel_loading) return;
		lepopup_properties_panel_loading = true;
		clearTimeout(lepopup_rebuild_active_element_timer);
		jQuery(".lepopup-properties-panel-loading").fadeIn(300);
		if (lepopup_element_properties_active) {
			lepopup_properties_populate();
			_lepopup_rebuild_active_element();
			jQuery(".lepopup-element-selected").removeClass("lepopup-element-selected");
			jQuery(".lepopup-layer-selected").removeClass("lepopup-layer-selected");
			jQuery(".lepopup-properties-panel .lepopup-color").minicolors("destroy");
			jQuery(".lepopup-properties-panel .lepopup-admin-popup-content-form").html("");
		}
		lepopup_element_properties_target = ".lepopup-properties-panel";
		lepopup_element_properties_active = _object;
		jQuery("body").animate({"left" : "-420px"}).addClass("lepopup-body-shifted");
		jQuery(".lepopup-properties-panel").animate({"right" : "0px"});
		jQuery(".lepopup-properties-panel-close").show();
		jQuery(".lepopup-properties-panel-close").animate({"right" : "0px"});
		jQuery(lepopup_element_properties_active).addClass("lepopup-element-selected");
		idx = jQuery(lepopup_element_properties_active).attr("id");
		if (idx) {
			idx = idx.replace("lepopup-element-", "");
			jQuery(".lepopup-layers-list").find("li.lepopup-layer-"+idx).addClass("lepopup-layer-selected");
		}
		setTimeout(function(){
			_lepopup_properties_prepare(_object);
			jQuery(".lepopup-properties-panel-loading").fadeOut(300);
			lepopup_properties_panel_loading = false;
		}, 500);
		return false;
	}

	function lepopup_properties_panel_close() {
		clearTimeout(lepopup_rebuild_active_element_timer);
		jQuery("body").animate({"left" : "0px"}).removeClass("lepopup-body-shifted");
		jQuery(".lepopup-properties-panel").animate({"right" : "-420px"});
		jQuery(".lepopup-properties-panel-close").fadeOut(300);
		jQuery(".lepopup-properties-panel-close").animate({"right" : "-420px"});
		if (!lepopup_properties_panel_loading) {
			lepopup_properties_populate();
			_lepopup_rebuild_active_element();
		}
		jQuery(".lepopup-element-selected").removeClass("lepopup-element-selected");
		jQuery(".lepopup-layer-selected").removeClass("lepopup-layer-selected");
		lepopup_element_properties_active = null;
		lepopup_element_properties_target = null;
		setTimeout(function() {
			jQuery(".lepopup-properties-panel .lepopup-color").minicolors("destroy");
			jQuery(".lepopup-properties-panel .lepopup-admin-popup-content-form").html("");
		}, 300);
		return false;
	}
/* Properties Panel - end */

/* Element actions - begin */
function _lepopup_element_delete(_i) {
	if (lepopup_element_properties_active) {
		lepopup_properties_panel_close();
	}
	if (lepopup_form_elements[_i] == null) return;
	lepopup_form_elements[_i] = null;
	_lepopup_layers_sync(lepopup_form_page_active);
}
function lepopup_element_delete(_object) {
	var i = jQuery(_object).attr("id");
	i = i.replace("lepopup-element-", "");
	if (lepopup_form_elements[i] == null) return false;
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the element.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			_lepopup_element_delete(i);
			lepopup_build();
			lepopup_dialog_close();
		}
	});
	return false;
}
function _lepopup_element_duplicate(_parent_id, _new_parent_id, _i) {
	if (lepopup_element_properties_active) {
		lepopup_properties_panel_close();
	}
	if (lepopup_form_elements[_i] == null) return;
	var clone = Object.assign({}, lepopup_form_elements[_i]);
	var j = lepopup_form_elements.push(clone);
	lepopup_form_last_id++;
	lepopup_form_elements[j-1]["id"] = lepopup_form_last_id;
	lepopup_form_elements[j-1]["_parent"] = _new_parent_id;
	if (_parent_id != _new_parent_id) {
		lepopup_form_elements[j-1]["_seq"] = lepopup_form_last_id;
	}
	_lepopup_layers_sync(lepopup_form_page_active);
}
function lepopup_element_duplicate(_object, _page_num) {
	var i = jQuery(_object).attr("id");
	i = i.replace("lepopup-element-", "");
	if (lepopup_form_elements[i] == null) return false;
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to duplicate the element.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Duplicate'),
		ok_function:	function(e){
			if (lepopup_is_numeric(_page_num) && _page_num < lepopup_form_pages.length && lepopup_form_pages[_page_num] != null) {
				_lepopup_element_duplicate(lepopup_form_elements[i]['_parent'], lepopup_form_pages[_page_num]['id'], i);
			} else {
				_lepopup_element_duplicate(lepopup_form_elements[i]['_parent'], lepopup_form_elements[i]['_parent'], i);
			}
			lepopup_build();
			lepopup_dialog_close();
		}
	});
	return false;
}
function _lepopup_element_move(_parent_id, _i) {
	if (lepopup_element_properties_active) {
		lepopup_properties_panel_close();
	}
	if (lepopup_form_elements[_i] == null) return;
	lepopup_form_elements[_i]["_parent"] = _parent_id;
	lepopup_form_elements[_i]["_seq"] = lepopup_form_last_id;
}
function lepopup_element_move(_object, _page_num) {
	var i = jQuery(_object).attr("id");
	i = i.replace("lepopup-element-", "");
	if (lepopup_form_elements[i] == null) return false;
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to move the element to other page.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Move'),
		ok_function:	function(e){
			if (lepopup_is_numeric(_page_num) && _page_num < lepopup_form_pages.length && lepopup_form_pages[_page_num] != null) {
				_lepopup_element_move(lepopup_form_pages[_page_num]['id'], i);
			}
			lepopup_build();
			lepopup_dialog_close();
		}
	});
	return false;
}
var lepopup_element_properties_active = null;
var lepopup_element_properties_target = null;
var lepopup_element_properties_data_changed = false;
function _lepopup_properties_prepare(_object) {
	var properties, i, id, input_fields = new Array();
	var html = "", tab_html = "", tooltip_html = "";
	var sections_opened = 0;
	var icon_left, icon_right, options, options2, fonts, selected, temp;
	var type = jQuery(_object).attr("data-type");
	if (typeof type == undefined || type == "") return false;

	if (type == "settings") {
		properties = lepopup_form_options;
		jQuery("#lepopup-element-properties").find(".lepopup-admin-popup-title h3").html("<i class='fas fa-cogs'></i> "+lepopup_esc_html__("Popup Settings"));
	} else if (type == "page" || type == "page-confirmation") {
		id = jQuery(_object).closest("li").attr("data-id");
		properties = null;
		for (var i=0; i<lepopup_form_pages.length; i++) {
			if (lepopup_form_pages[i] != null && lepopup_form_pages[i]["id"] == id) {
				properties = lepopup_form_pages[i];
				break;
			}
		}
		jQuery("#lepopup-element-properties").find(".lepopup-admin-popup-title h3").html("<i class='far fa-copy'></i> "+lepopup_esc_html__("Page Settings"));
	} else {
		i = jQuery(_object).attr("id");
		i = i.replace("lepopup-element-", "");
		properties = lepopup_form_elements[i];
		jQuery("#lepopup-element-properties").find(".lepopup-admin-popup-title h3").html("<i class='fas fa-cog'></i> "+lepopup_esc_html__("Element Properties")+"<span><i class='"+lepopup_toolbar_tools[properties["type"]]["icon"]+"'></i> "+lepopup_escape_html(properties["name"])+"</span>");
	}
	
	input_fields = lepopup_input_sort();
	
	// Prepare editor state - begin
	for (var key in lepopup_meta[type]) {
		if (lepopup_meta[type].hasOwnProperty(key)) {
			tooltip_html = "";
			if (lepopup_meta[type][key].hasOwnProperty('tooltip')) {
				tooltip_html = "<i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_meta[type][key]['tooltip']+"</div>";
			}
			switch(lepopup_meta[type][key]['type']) {
				case 'tab':
					for (var j=0; j<sections_opened; j++) html += "</div>";
					sections_opened = 0;
					if (tab_html == "") tab_html += "<div id='lepopup-properties-tabs' class='lepopup-tabs'>";
					else html += "</div>";
					tab_html += "<a class='lepopup-tab' href='#lepopup-tab-"+lepopup_meta[type][key]['value']+"'>"+lepopup_meta[type][key]['label']+"</a>";
					html += "<div id='lepopup-tab-"+lepopup_meta[type][key]['value']+"' class='lepopup-tab-content'>";
					break;
				
				case 'sections':
					options = "";
					for (var section_key in lepopup_meta[type][key]['sections']) {
						if (lepopup_meta[type][key]['sections'].hasOwnProperty(section_key)) {
							if (options == "") selected = "lepopup-section-active";
							else selected = "";
							options += "<a class='"+selected+"' href='#lepopup-section-"+lepopup_escape_html(section_key)+"'><i class='"+lepopup_meta[type][key]['sections'][section_key]['icon']+"'></i> "+lepopup_escape_html(lepopup_meta[type][key]['sections'][section_key]['label'])+"</a>";
						}
					}
					html += "<h3 id='lepopup-"+key+"' class='lepopup-sections'>"+options+"</h3>";
					break;

				case 'section-start':
					html += "<div id='lepopup-section-"+lepopup_escape_html(lepopup_meta[type][key]['section'])+"' class='lepopup-section-content'>";
					sections_opened++;
					break;

				case 'section-end':
					if (sections_opened > 0) {
						html += "</div>";
						sections_opened--;
					}
					break;
				
				case 'key-fields':
					options = "";
					options2 = "";
					temp = "";
					if (input_fields.length > 0) {
						for (var j=0; j<input_fields.length; j++) {
							if (temp != input_fields[j]['page-id']) {
								if (temp != "") {
									options += "</optgroup>";
									options2 += "</optgroup>";
								}
								options += "<optgroup label='"+lepopup_escape_html(input_fields[j]['page-name'])+"'>";
								options2 += "<optgroup label='"+lepopup_escape_html(input_fields[j]['page-name'])+"'>";
								temp = input_fields[j]['page-id'];
							}
							options += "<option value='"+input_fields[j]['id']+"'"+(input_fields[j]['id'] == properties[key+'-primary'] ? " selected='selected'" : "")+">"+input_fields[j]['id']+" | "+lepopup_escape_html(input_fields[j]['name'])+"</option>";
							options2 += "<option value='"+input_fields[j]['id']+"'"+(input_fields[j]['id'] == properties[key+'-secondary'] ? " selected='selected'" : "")+">"+input_fields[j]['id']+" | "+lepopup_escape_html(input_fields[j]['name'])+"</option>";
						}
						options += "</optgroup>";
						options2 += "</optgroup>";
					}
					temp = "<div class='lepopup-properties-content-half'><select name='lepopup-"+key+"-primary' id='lepopup-"+key+"-primary'><option value=''>"+lepopup_meta[type][key]['placeholder']['primary']+"</option>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['primary'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-half'><select name='lepopup-"+key+"-secondary' id='lepopup-"+key+"-secondary'><option value=''>"+lepopup_meta[type][key]['placeholder']['secondary']+"</option>"+options2+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['primary'])+"</label></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'personal-keys':
					options = "";
					if (input_fields.length > 0) {
						for (var j=0; j<input_fields.length; j++) {
							options += "<input class='lepopup-properties-tile' type='checkbox' name='lepopup-"+key+"' id='lepopup-"+key+"-"+input_fields[j]['id']+"' value='"+input_fields[j]['id']+"'"+(properties[key].indexOf(input_fields[j]['id']) >= 0 ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-"+input_fields[j]['id']+"'>"+input_fields[j]['id']+" | "+lepopup_escape_html(input_fields[j]['name'])+"</label>";
						}
					} else options = "No fields added.";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+options+"</div></div>";
					break;

				case 'datetime-args':
					options = "";
					for (var option_key in lepopup_meta[type][key]['date-format-options']) {
						if (lepopup_meta[type][key]['date-format-options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key+"-date-format"]) selected = " selected='selected'";
							options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['date-format-options'][option_key])+"</option>";
						}
					}
					temp = "<div class='lepopup-properties-content-third'><select name='lepopup-"+key+"-date-format' id='lepopup-"+key+"-date-format'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['date-format-label'])+"</label></div>";
					options = "";
					for (var option_key in lepopup_meta[type][key]['time-format-options']) {
						if (lepopup_meta[type][key]['time-format-options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key+"-time-format"]) selected = " selected='selected'";
							options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['time-format-options'][option_key])+"</option>";
						}
					}
					temp += "<div class='lepopup-properties-content-third'><select name='lepopup-"+key+"-time-format' id='lepopup-"+key+"-time-format'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['time-format-label'])+"</label></div>";
					options = "";
					for (var j=0; j<(lepopup_meta[type][key]['locale-options']).length; j++) {
						selected = "";
						if (lepopup_meta[type][key]['locale-options'][j] == properties[key+"-locale"]) selected = " selected='selected'";
						options += "<option"+selected+" value='"+lepopup_escape_html(lepopup_meta[type][key]['locale-options'][j])+"'>"+lepopup_escape_html(lepopup_meta[type][key]['locale-options'][j])+"</option>";
					}
					temp += "<div class='lepopup-properties-content-third'><select name='lepopup-"+key+"-locale' id='lepopup-"+key+"-locle'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['locale-label'])+"</label></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'color':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-content-color'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='...' /></div></div></div>";
					break;
					
				case 'two-colors':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color1' id='lepopup-"+key+"-color1' value='"+lepopup_escape_html(properties[key+'-color1'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color2' id='lepopup-"+key+"-color2' value='"+lepopup_escape_html(properties[key+'-color2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'three-colors':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color1' id='lepopup-"+key+"-color1' value='"+lepopup_escape_html(properties[key+'-color1'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color2' id='lepopup-"+key+"-color2' value='"+lepopup_escape_html(properties[key+'-color2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color3' id='lepopup-"+key+"-color3' value='"+lepopup_escape_html(properties[key+'-color3'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'four-colors':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color1' id='lepopup-"+key+"-color1' value='"+lepopup_escape_html(properties[key+'-color1'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color2' id='lepopup-"+key+"-color2' value='"+lepopup_escape_html(properties[key+'-color2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color3' id='lepopup-"+key+"-color3' value='"+lepopup_escape_html(properties[key+'-color3'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color4' id='lepopup-"+key+"-color4' value='"+lepopup_escape_html(properties[key+'-color4'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color4'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'five-colors':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color1' id='lepopup-"+key+"-color1' value='"+lepopup_escape_html(properties[key+'-color1'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color2' id='lepopup-"+key+"-color2' value='"+lepopup_escape_html(properties[key+'-color2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color3' id='lepopup-"+key+"-color3' value='"+lepopup_escape_html(properties[key+'-color3'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color4' id='lepopup-"+key+"-color4' value='"+lepopup_escape_html(properties[key+'-color4'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color4'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='false' name='lepopup-"+key+"-color5' id='lepopup-"+key+"-color5' value='"+lepopup_escape_html(properties[key+'-color5'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color5'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'width-height':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><div class='lepopup-number'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width' value='"+lepopup_escape_html(properties[key+'-width'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['width'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><div class='lepopup-number'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-height' id='lepopup-"+key+"-height' value='"+lepopup_escape_html(properties[key+'-height'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['height'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'top-left':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><div class='lepopup-number'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-top' id='lepopup-"+key+"-top' value='"+lepopup_escape_html(properties[key+'-top'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['top'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><div class='lepopup-number'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-left' id='lepopup-"+key+"-left' value='"+lepopup_escape_html(properties[key+'-left'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['left'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'two-numbers':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value1' id='lepopup-"+key+"-value1' value='"+lepopup_escape_html(properties[key+'-value1'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value2' id='lepopup-"+key+"-value2' value='"+lepopup_escape_html(properties[key+'-value2'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'three-numbers':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value1' id='lepopup-"+key+"-value1' value='"+lepopup_escape_html(properties[key+'-value1'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value2' id='lepopup-"+key+"-value2' value='"+lepopup_escape_html(properties[key+'-value2'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value3' id='lepopup-"+key+"-value3' value='"+lepopup_escape_html(properties[key+'-value3'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'four-numbers':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value1' id='lepopup-"+key+"-value1' value='"+lepopup_escape_html(properties[key+'-value1'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value2' id='lepopup-"+key+"-value2' value='"+lepopup_escape_html(properties[key+'-value2'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value3' id='lepopup-"+key+"-value3' value='"+lepopup_escape_html(properties[key+'-value3'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value4' id='lepopup-"+key+"-value4' value='"+lepopup_escape_html(properties[key+'-value4'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value4'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'number-string-number':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value1' id='lepopup-"+key+"-value1' value='"+lepopup_escape_html(properties[key+'-value1'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value1'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third lepopup-properties-panel-block'><input type='text' name='lepopup-"+key+"-value2' id='lepopup-"+key+"-value2' value='"+lepopup_escape_html(properties[key+'-value2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"-value3' id='lepopup-"+key+"-value3' value='"+lepopup_escape_html(properties[key+'-value3'])+"' placeholder='...' /></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value3'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'block-width':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-value' id='lepopup-"+key+"-value' value='"+lepopup_escape_html(properties[key+'-value'])+"' placeholder='Ex. 960' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['value'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='px' name='lepopup-"+key+"-unit' id='lepopup-"+key+"-unit-px'"+(properties[key+'-unit'] == "px" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-unit-px'>px</label><input type='radio' value='%' name='lepopup-"+key+"-unit' id='lepopup-"+key+"-unit-percent'"+(properties[key+'-unit'] == "%" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-unit-percent'>%</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['unit'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-left'"+(properties[key+'-position'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-left'><i class='fas fa-align-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-center'"+(properties[key+'-position'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-center'><i class='fas fa-align-center'></i></label><input type='radio' value='right' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-right'"+(properties[key+'-position'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-right'><i class='fas fa-align-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'imageselect-style':
					temp = "";
					options = "";
					for (var option_key in lepopup_meta[type][key]['options']) {
						if (lepopup_meta[type][key]['options'].hasOwnProperty(option_key)) {
							options += "<option"+(option_key == properties[key+"-effect"] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['options'][option_key])+"</option>";
						}
					}
					temp += "<div class='lepopup-properties-content-two-third'><select name='lepopup-"+key+"-effect' id='lepopup-"+key+"-effect'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['effect'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-left'"+(properties[key+'-align'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-left'><i class='fas fa-align-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-center'"+(properties[key+'-align'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-center'><i class='fas fa-align-center'></i></label><input type='radio' value='right' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-right'"+(properties[key+'-align'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-right'><i class='fas fa-align-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'local-imageselect-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width' value='"+lepopup_escape_html(properties[key+'-width'])+"' placeholder='' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['width'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-height' id='lepopup-"+key+"-height' value='"+lepopup_escape_html(properties[key+'-height'])+"' placeholder='' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['height'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='auto' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-auto'"+(properties[key+'-size'] == "auto" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-auto'>Auto</label><input type='radio' value='contain' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-contain'"+(properties[key+'-size'] == "contain" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-contain'><i class='fas fa-compress-arrows-alt'></i></label><input type='radio' value='cover' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-cover'"+(properties[key+'-size'] == "cover" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-cover'><i class='fas fa-expand-arrows-alt'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'imageselect-mode':
				case 'tile-mode':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-radio'"+(properties[key] == "radio" ? " checked='checked'" : "")+" onchange='lepopup_properties_imageselect_mode_set(this);'><label for='lepopup-"+key+"-radio'>Radio button</label><input type='radio' value='checkbox' name='lepopup-"+key+"' id='lepopup-"+key+"-checkbox'"+(properties[key] == "checkbox" ? " checked='checked'" : "")+" onchange='lepopup_properties_imageselect_mode_set(this);'><label for='lepopup-"+key+"-checkbox'>Checkbox</label></div></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'popup-position':
					options = "";
					options += "<div class='lepopup-position-element lepopup-position-element-top-left'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-top-left' value='top-left'"+(properties[key] == "top-left" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-top-left'></label><label for='lepopup-"+key+"-top-left'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-top-center'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-top-center' value='top-center'"+(properties[key] == "top-center" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-top-center'></label><label for='lepopup-"+key+"-top-center'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-top-right'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-top-right' value='top-right'"+(properties[key] == "top-right" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-top-right'></label><label for='lepopup-"+key+"-top-right'></label></div>";
					options += "<br />";
					options += "<div class='lepopup-position-element lepopup-position-element-middle-left'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-middle-left' value='middle-left'"+(properties[key] == "middle-left" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-middle-left'></label><label for='lepopup-"+key+"-middle-left'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-middle-center'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-middle-center' value='middle-center'"+(properties[key] == "middle-center" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-middle-center'></label><label for='lepopup-"+key+"-middle-center'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-middle-right'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-middle-right' value='middle-right'"+(properties[key] == "middle-right" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-middle-right'></label><label for='lepopup-"+key+"-middle-right'></label></div>";
					options += "<br />";
					options += "<div class='lepopup-position-element lepopup-position-element-bottom-left'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-bottom-left' value='bottom-left'"+(properties[key] == "bottom-left" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-bottom-left'></label><label for='lepopup-"+key+"-bottom-left'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-bottom-center'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-bottom-center' value='bottom-center'"+(properties[key] == "bottom-center" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-bottom-center'></label><label for='lepopup-"+key+"-bottom-center'></label></div>";
					options += "<div class='lepopup-position-element lepopup-position-element-bottom-right'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-bottom-right' value='bottom-right'"+(properties[key] == "bottom-right" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-bottom-right'></label><label for='lepopup-"+key+"-bottom-right'></label></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+options+"</div></div>";
					break;

				case 'animation':
					temp = "";
					options = "";
					for (var option_key in lepopup_animations_in) {
						if (lepopup_animations_in.hasOwnProperty(option_key)) {
							options += "<option"+(option_key == properties[key+'-in'] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_animations_in[option_key])+"</option>";
						}
					}
					options2 = "";
					for (var option_key in lepopup_animations_out) {
						if (lepopup_animations_out.hasOwnProperty(option_key)) {
							options2 += "<option"+(option_key == properties[key+'-out'] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_animations_out[option_key])+"</option>";
						}
					}
					temp += "<div class='lepopup-properties-content-quarter lepopup-properties-panel-block'><select name='lepopup-"+key+"-in' id='lepopup-"+key+"-in'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['in'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-ms'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-duration' id='lepopup-"+key+"-duration' value='"+lepopup_escape_html(properties[key+'-duration'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['duration'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-ms'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-delay' id='lepopup-"+key+"-delay' value='"+lepopup_escape_html(properties[key+'-delay'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['delay'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-quarter lepopup-properties-panel-block'><select name='lepopup-"+key+"-out' id='lepopup-"+key+"-out'>"+options2+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['out'])+"</label></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'overlay-animation':
					temp = "";
					options = "";
					for (var option_key in lepopup_animations_in) {
						if (lepopup_animations_in.hasOwnProperty(option_key)) {
							options += "<option"+(option_key == properties[key] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_animations_in[option_key])+"</option>";
						}
					}
					temp += "<div class='lepopup-properties-content-third'><select name='lepopup-"+key+"' id='lepopup-"+key+"'>"+options+"</select></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'text-style':
					temp = "";
					options = "<option value=''>"+lepopup_esc_html__("Default")+"</option>";
					options += "<optgroup label='"+lepopup_esc_html__("Standard Fonts")+"'>";
					for (var j=0; j<lepopup_localfonts.length; j++) {
						options += "<option"+(lepopup_localfonts[j] == properties[key+'-family'] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(lepopup_localfonts[j])+"'>"+lepopup_escape_html(lepopup_localfonts[j])+"</option>";
					}
					options += "</optgroup>";
					options += "<optgroup label='"+lepopup_esc_html__("Google Fonts")+"'>";
					for (var j=0; j<lepopup_webfonts.length; j++) {
						options += "<option"+(lepopup_webfonts[j] == properties[key+'-family'] ? " selected='selected'" : "")+" value='"+lepopup_escape_html(lepopup_webfonts[j])+"'>"+lepopup_escape_html(lepopup_webfonts[j])+"</option>";
					}
					options += "</optgroup>";
					options2 = "";
					if (!properties.hasOwnProperty(key+'-weight') || properties[key+'-weight'] == "") {
						if (properties.hasOwnProperty(key+'-bold') && properties[key+'-bold'] == "on") selected = '700';
						else selected = 'inherit';
					} else selected = properties[key+'-weight'];
					for (var option_key in lepopup_font_weights) {
						if (lepopup_font_weights.hasOwnProperty(option_key)) {
							options2 += "<option"+(option_key == selected ? " selected='selected'" : "")+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(option_key + " - " + lepopup_font_weights[option_key])+"</option>";
						}
					}
					temp += "<div class='lepopup-properties-line'>";
					temp += "<div class='lepopup-properties-content-two-third lepopup-properties-panel-block'><select name='lepopup-"+key+"-family' id='lepopup-"+key+"-family'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['family'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size' value='"+lepopup_escape_html(properties[key+'-size'])+"' placeholder='Ex. 15' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-left'"+(properties[key+'-align'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-left'><i class='fas fa-align-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-center'"+(properties[key+'-align'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-center'><i class='fas fa-align-center'></i></label><input type='radio' value='right' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-right'"+(properties[key+'-align'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-right'><i class='fas fa-align-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='checkbox' value='off' name='lepopup-"+key+"-italic' id='lepopup-"+key+"-italic'"+(properties[key+'-italic'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-italic'><i class='fas fa-italic'></i></label><input type='checkbox' value='off' name='lepopup-"+key+"-underline' id='lepopup-"+key+"-underline'"+(properties[key+'-underline'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-underline'><i class='fas fa-underline'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['style'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'></div>";
					temp += "</div>";
					temp += "<div class='lepopup-properties-line'>";
					temp += "<div class='lepopup-properties-content-quarter lepopup-properties-panel-block'><select name='lepopup-"+key+"-weight' id='lepopup-"+key+"-weight'>"+options2+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['weight'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-quarter lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color' id='lepopup-"+key+"-color' value='"+lepopup_escape_html(properties[key+'-color'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					temp += "</div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'background-style':
					temp = "";
					temp += "<div class='lepopup-properties-line'>";
					temp += "<div class='lepopup-properties-content-two-third lepopup-properties-panel-block lepopup-image-url'><input type='text' name='lepopup-"+key+"-image' id='lepopup-"+key+"-image' value='"+lepopup_escape_html(properties[key+'-image'])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['image'])+"</label><span><i class='far fa-image'></i></span></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='auto' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-auto'"+(properties[key+'-size'] == "auto" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-auto'>Auto</label><input type='radio' value='contain' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-contain'"+(properties[key+'-size'] == "contain" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-contain'><i class='fas fa-compress-arrows-alt'></i></label><input type='radio' value='cover' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-cover'"+(properties[key+'-size'] == "cover" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-cover'><i class='fas fa-expand-arrows-alt'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-left'"+(properties[key+'-horizontal-position'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-left'><i class='fas fa-arrow-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-center'"+(properties[key+'-horizontal-position'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-center'><i class='far fa-dot-circle'></i></label><input type='radio' value='right' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-right'"+(properties[key+'-horizontal-position'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-right'><i class='fas fa-arrow-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['horizontal-position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='top' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-top'"+(properties[key+'-vertical-position'] == "top" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-top'><i class='fas fa-arrow-up'></i></label><input type='radio' value='center' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-center'"+(properties[key+'-vertical-position'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-center'><i class='far fa-dot-circle'></i></label><input type='radio' value='bottom' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-bottom'"+(properties[key+'-vertical-position'] == "bottom" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-bottom'><i class='fas fa-arrow-down'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['vertical-position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='repeat' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat'"+(properties[key+'-repeat'] == "repeat" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat'><i class='fas fa-arrows-alt'></i></label><input type='radio' value='repeat-x' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat-x'"+(properties[key+'-repeat'] == "repeat-x" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat-x'><i class='fas fa-arrows-alt-h'></i></label><input type='radio' value='repeat-y' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat-y'"+(properties[key+'-repeat'] == "repeat-y" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat-y'><i class='fas fa-arrows-alt-v'></i></label><input type='radio' value='no-repeat' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-no-repeat'"+(properties[key+'-repeat'] == "no-repeat" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-no-repeat'>No</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['repeat'])+"</label></div>";
					temp += "</div>";
					temp += "<div class='lepopup-properties-line'>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color' id='lepopup-"+key+"-color' value='"+lepopup_escape_html(properties[key+'-color'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><div class='lepopup-bar-selector'><input type='radio' value='no' name='lepopup-"+key+"-gradient' id='lepopup-"+key+"-gradient-no'"+(properties[key+'-gradient'] == "no" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-gradient-no' onclick='jQuery(\"#lepopup-content-"+key+"-color2\").fadeOut(300);'>No</label><input type='radio' value='2shades' name='lepopup-"+key+"-gradient' id='lepopup-"+key+"-gradient-2shades'"+(properties[key+'-gradient'] == "2shades" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-gradient-2shades' onclick='jQuery(\"#lepopup-content-"+key+"-color2\").fadeOut(300);'>2 Shades</label><input type='radio' value='horizontal' name='lepopup-"+key+"-gradient' id='lepopup-"+key+"-gradient-horizontal'"+(properties[key+'-gradient'] == "horizontal" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-gradient-horizontal' onclick='jQuery(\"#lepopup-content-"+key+"-color2\").fadeIn(300);'>Horizontal</label><input type='radio' value='vertical' name='lepopup-"+key+"-gradient' id='lepopup-"+key+"-gradient-vertical'"+(properties[key+'-gradient'] == "vertical" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-gradient-vertical' onclick='jQuery(\"#lepopup-content-"+key+"-color2\").fadeIn(300);'>Vertical</label><input type='radio' value='diagonal' name='lepopup-"+key+"-gradient' id='lepopup-"+key+"-gradient-diagonal'"+(properties[key+'-gradient'] == "diagonal" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-gradient-diagonal' onclick='jQuery(\"#lepopup-content-"+key+"-color2\").fadeIn(300);'>Diagonal</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['gradient'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block' id='lepopup-content-"+key+"-color2'"+(properties[key+'-gradient'] != "horizontal" && properties[key+'-gradient'] != "vertical" && properties[key+'-gradient'] != "diagonal" ? " style='display:none;'" : "")+"><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color2' id='lepopup-"+key+"-color2' value='"+lepopup_escape_html(properties[key+'-color2'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color2'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					temp += "</div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'image-style':
					temp = "";
					temp += "<div class='lepopup-properties-line'>";
					temp += "<div class='lepopup-properties-content-two-third lepopup-properties-panel-block lepopup-image-url'><input type='text' name='lepopup-"+key+"-image' id='lepopup-"+key+"-image' value='"+lepopup_escape_html(properties[key+'-image'])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['image'])+"</label><span><i class='far fa-image'></i></span></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='auto' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-auto'"+(properties[key+'-size'] == "auto" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-auto'>Auto</label><input type='radio' value='contain' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-contain'"+(properties[key+'-size'] == "contain" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-contain'><i class='fas fa-compress-arrows-alt'></i></label><input type='radio' value='cover' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-cover'"+(properties[key+'-size'] == "cover" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-cover'><i class='fas fa-expand-arrows-alt'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-left'"+(properties[key+'-horizontal-position'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-left'><i class='fas fa-arrow-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-center'"+(properties[key+'-horizontal-position'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-center'><i class='far fa-dot-circle'></i></label><input type='radio' value='right' name='lepopup-"+key+"-horizontal-position' id='lepopup-"+key+"-horizontal-position-right'"+(properties[key+'-horizontal-position'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-horizontal-position-right'><i class='fas fa-arrow-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['horizontal-position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='top' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-top'"+(properties[key+'-vertical-position'] == "top" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-top'><i class='fas fa-arrow-up'></i></label><input type='radio' value='center' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-center'"+(properties[key+'-vertical-position'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-center'><i class='far fa-dot-circle'></i></label><input type='radio' value='bottom' name='lepopup-"+key+"-vertical-position' id='lepopup-"+key+"-vertical-position-bottom'"+(properties[key+'-vertical-position'] == "bottom" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-vertical-position-bottom'><i class='fas fa-arrow-down'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['vertical-position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='repeat' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat'"+(properties[key+'-repeat'] == "repeat" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat'><i class='fas fa-arrows-alt'></i></label><input type='radio' value='repeat-x' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat-x'"+(properties[key+'-repeat'] == "repeat-x" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat-x'><i class='fas fa-arrows-alt-h'></i></label><input type='radio' value='repeat-y' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-repeat-y'"+(properties[key+'-repeat'] == "repeat-y" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-repeat-y'><i class='fas fa-arrows-alt-v'></i></label><input type='radio' value='no-repeat' name='lepopup-"+key+"-repeat' id='lepopup-"+key+"-repeat-no-repeat'"+(properties[key+'-repeat'] == "no-repeat" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-repeat-no-repeat'>No</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['repeat'])+"</label></div>";
					temp += "</div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'border-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width' value='"+lepopup_escape_html(properties[key+'-width'])+"' placeholder='Ex. 1' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['width'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='solid' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-solid'"+(properties[key+'-style'] == "solid" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-solid'>Solid</label><input type='radio' value='dashed' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-dashed'"+(properties[key+'-style'] == "dashed" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-dashed'>Dashed</label><input type='radio' value='dotted' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-dotted'"+(properties[key+'-style'] == "dotted" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-dotted'>Dotted</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['style'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='0' name='lepopup-"+key+"-radius' id='lepopup-"+key+"-radius-0'"+(properties[key+'-radius'] == "0" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-radius-0'>0px</label><input type='radio' value='3' name='lepopup-"+key+"-radius' id='lepopup-"+key+"-radius-3'"+(properties[key+'-radius'] == "3" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-radius-3'>3px</label><input type='radio' value='5' name='lepopup-"+key+"-radius' id='lepopup-"+key+"-radius-5'"+(properties[key+'-radius'] == "5" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-radius-5'>5px</label><input type='radio' value='10' name='lepopup-"+key+"-radius' id='lepopup-"+key+"-radius-10'"+(properties[key+'-radius'] == "10" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-radius-10'>10px</label><input type='radio' value='max' name='lepopup-"+key+"-radius' id='lepopup-"+key+"-radius-max'"+(properties[key+'-radius'] == "max" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-radius-max'>Max</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['radius'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='checkbox' value='off' name='lepopup-"+key+"-left' id='lepopup-"+key+"-left'"+(properties[key+'-left'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-left'><i class='fas fa-arrow-left'></i></label><input type='checkbox' value='off' name='lepopup-"+key+"-top' id='lepopup-"+key+"-top'"+(properties[key+'-top'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-top'><i class='fas fa-arrow-up'></i></label><input type='checkbox' value='off' name='lepopup-"+key+"-bottom' id='lepopup-"+key+"-bottom'"+(properties[key+'-bottom'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-bottom'><i class='fas fa-arrow-down'></i></label><input type='checkbox' value='off' name='lepopup-"+key+"-right' id='lepopup-"+key+"-right'"+(properties[key+'-right'] == "on" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-right'><i class='fas fa-arrow-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['border'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color' id='lepopup-"+key+"-color' value='"+lepopup_escape_html(properties[key+'-color'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'global-tile-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-selector'><input type='radio' value='tiny' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-tiny'"+(properties[key+'-size'] == "tiny" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-tiny'>Tiny</label><input type='radio' value='small' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-small'"+(properties[key+'-size'] == "small" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-small'>Small</label><input type='radio' value='medium' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-medium'"+(properties[key+'-size'] == "medium" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-medium'>Medium</label><input type='radio' value='large' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-large'"+(properties[key+'-size'] == "large" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-large'>Large</label><input type='radio' value='huge' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-huge'"+(properties[key+'-size'] == "huge" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-huge'>Huge</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-selector'><input type='radio' value='default' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width-default'"+(properties[key+'-width'] == "default" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-width-default'>Default</label><input type='radio' value='full' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width-full'"+(properties[key+'-width'] == "full" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-width-full'>Full</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['width'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-left'"+(properties[key+'-position'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-left'><i class='fas fa-arrow-left'></i></label><input type='radio' value='right' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-right'"+(properties[key+'-position'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-right'><i class='fas fa-arrow-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-selector'><input type='radio' value='inline' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-inline'"+(properties[key+'-layout'] == "inline" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-inline'><i class='fas fa-arrow-right'></i></label><input type='radio' value='1' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-1'"+(properties[key+'-layout'] == "1" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-1'><i class='fas fa-arrow-down'></i></label><input type='radio' value='2' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-2'"+(properties[key+'-layout'] == "2" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-2'>2c</label><input type='radio' value='3' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-3'"+(properties[key+'-layout'] == "3" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-3'>3c</label><input type='radio' value='4' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-4'"+(properties[key+'-layout'] == "4" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-4'>4c</label><input type='radio' value='6' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-6'"+(properties[key+'-layout'] == "6" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-6'>6c</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['layout'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'local-tile-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-size'])+"' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size'><span class='"+(properties[key+'-size'] == "tiny" ? 'lepopup-bar-option-selected' : '')+"' data-value='tiny'>Tiny</span><span class='"+(properties[key+'-size'] == "small" ? 'lepopup-bar-option-selected' : '')+"' data-value='small'>Small</span><span class='"+(properties[key+'-size'] == "medium" ? 'lepopup-bar-option-selected' : '')+"' data-value='medium'>Medium</span><span class='"+(properties[key+'-size'] == "large" ? 'lepopup-bar-option-selected' : '')+"' data-value='large'>Large</span><span class='"+(properties[key+'-size'] == "huge" ? 'lepopup-bar-option-selected' : '')+"' data-value='huge'>Huge</span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-width'])+"' name='lepopup-"+key+"-width' id='lepopup-"+key+"-width'><span class='"+(properties[key+'-width'] == "default" ? 'lepopup-bar-option-selected' : '')+"' data-value='default'>Default</span><span class='"+(properties[key+'-width'] == "full" ? 'lepopup-bar-option-selected' : '')+"' data-value='full'>Full</span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['width'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-position'])+"' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position'><span class='"+(properties[key+'-position'] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-arrow-left'></i></span><span class='"+(properties[key+'-position'] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-arrow-right'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-layout'])+"' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout'><span class='"+(properties[key+'-layout'] == "inline" ? 'lepopup-bar-option-selected' : '')+"' data-value='inline'><i class='fas fa-arrow-right'></i></span><span class='"+(properties[key+'-layout'] == "1" ? 'lepopup-bar-option-selected' : '')+"' data-value='1'><i class='fas fa-arrow-down'></i></span><span class='"+(properties[key+'-layout'] == "2" ? 'lepopup-bar-option-selected' : '')+"' data-value='2'>2c</span><span class='"+(properties[key+'-layout'] == "3" ? 'lepopup-bar-option-selected' : '')+"' data-value='3'>3c</span><span class='"+(properties[key+'-layout'] == "4" ? 'lepopup-bar-option-selected' : '')+"' data-value='4'>4c</span><span class='"+(properties[key+'-layout'] == "6" ? 'lepopup-bar-option-selected' : '')+"' data-value='6'>6c</span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['layout'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'icon-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='inside' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-inside'"+(properties[key+'-position'] == "inside" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-position-inside' onclick='if (jQuery(this).parent().find(\"input\").is(\":checked\")) jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-properties-icon-inside-only\").fadeIn(200);'>Inside</i></label><input type='radio' value='outside' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-outside'"+(properties[key+'-position'] == "outside" ? " checked='checked'" : "")+" /><label for='lepopup-"+key+"-position-outside' onclick='if (jQuery(this).parent().find(\"input\").is(\":checked\")) jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-properties-icon-inside-only\").fadeOut(200);'>Outside</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size' value='"+lepopup_escape_html(properties[key+'-size'])+"' placeholder='Ex. 15' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color' id='lepopup-"+key+"-color' value='"+lepopup_escape_html(properties[key+'-color'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block lepopup-properties-icon-inside-only'"+(properties[key+'-position'] == "outside" ? " style='display:none;'" : "")+"><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-background' id='lepopup-"+key+"-background' value='"+lepopup_escape_html(properties[key+'-background'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['background'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block lepopup-properties-icon-inside-only'"+(properties[key+'-position'] == "outside" ? " style='display:none;'" : "")+"><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-border' id='lepopup-"+key+"-border' value='"+lepopup_escape_html(properties[key+'-border'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['border'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'star-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='tiny' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-tiny'"+(properties[key+'-size'] == "tiny" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-tiny'>Tiny</label><input type='radio' value='small' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-small'"+(properties[key+'-size'] == "small" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-small'>Small</label><input type='radio' value='medium' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-medium'"+(properties[key+'-size'] == "medium" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-medium'>Medium</label><input type='radio' value='large' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-large'"+(properties[key+'-size'] == "large" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-large'>Large</label><input type='radio' value='huge' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-huge'"+(properties[key+'-size'] == "huge" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-huge'>Huge</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color-rated' id='lepopup-"+key+"-color-rated' value='"+lepopup_escape_html(properties[key+'-color-rated'])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color-rated'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color-unrated' id='lepopup-"+key+"-color-unrated' value='"+lepopup_escape_html(properties[key+'-color-unrated'])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color-unrated'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;
					
				case 'shadow':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline' id='lepopup-content-"+key+"-size'><div class='lepopup-bar-selector'><input type='radio' value='' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-no'"+(properties[key+'-size'] == "" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-no'>No</label><input type='radio' value='tiny' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-tiny'"+(properties[key+'-size'] == "tiny" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-tiny'><i class='fas fa-stop' style='font-size: 10px;'></i></label><input type='radio' value='small' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-small'"+(properties[key+'-size'] == "small" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-small'><i class='fas fa-stop' style='font-size: 12px;'></i></label><input type='radio' value='medium' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-medium'"+(properties[key+'-size'] == "medium" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-medium'><i class='fas fa-stop' style='font-size: 14px;'></i></label><input type='radio' value='large' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-large'"+(properties[key+'-size'] == "large" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-large'><i class='fas fa-stop' style='font-size: 16px;'></i></label><input type='radio' value='huge' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-huge'"+(properties[key+'-size'] == "huge" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-huge'><i class='fas fa-stop' style='font-size: 18px;'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='regular' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-regular'"+(properties[key+'-style'] == "regular" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-regular'>Regular</label><input type='radio' value='solid' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-solid'"+(properties[key+'-style'] == "solid" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-solid'>Solid</label><input type='radio' value='inset' name='lepopup-"+key+"-style' id='lepopup-"+key+"-style-inset'"+(properties[key+'-style'] == "inset" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-style-inset'>Inset</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['style'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-color' id='lepopup-"+key+"-color' value='"+lepopup_escape_html(properties[key+'-color'])+"' placeholder='Color' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;
					
				case 'padding':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-top' id='lepopup-"+key+"-top' value='"+lepopup_escape_html(properties[key+'-top'])+"' placeholder='Ex. 10' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['top'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-right' id='lepopup-"+key+"-right' value='"+lepopup_escape_html(properties[key+'-right'])+"' placeholder='Ex. 10' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['right'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-bottom' id='lepopup-"+key+"-bottom' value='"+lepopup_escape_html(properties[key+'-bottom'])+"' placeholder='Ex. 10' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['bottom'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-left' id='lepopup-"+key+"-left' value='"+lepopup_escape_html(properties[key+'-left'])+"' placeholder='Ex. 10' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['left'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'align':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key])+"' name='lepopup-"+key+"' id='lepopup-"+key+"'><span class='"+(properties[key] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-align-left'></i></span><span class='"+(properties[key] == "center" ? 'lepopup-bar-option-selected' : '')+"' data-value='center'><i class='fas fa-align-center'></i></span><span class='"+(properties[key] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-align-right'></i></span></div></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'checkbox-radio-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-left'"+(properties[key+'-position'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-left'><i class='fas fa-arrow-left'></i></label><input type='radio' value='right' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-right'"+(properties[key+'-position'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-right'><i class='fas fa-arrow-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='left' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-left'"+(properties[key+'-align'] == "left" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-left'><i class='fas fa-align-left'></i></label><input type='radio' value='center' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-center'"+(properties[key+'-align'] == "center" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-center'><i class='fas fa-align-center'></i></label><input type='radio' value='right' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align-right'"+(properties[key+'-align'] == "right" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-align-right'><i class='fas fa-align-right'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='small' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-small'"+(properties[key+'-size'] == "small" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-small'>Small</label><input type='radio' value='medium' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-medium'"+(properties[key+'-size'] == "medium" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-medium'>Medium</label><input type='radio' value='large' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-large'"+(properties[key+'-size'] == "large" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-large'>Large</label><input type='radio' value='huge' name='lepopup-"+key+"-size' id='lepopup-"+key+"-size-huge'"+(properties[key+'-size'] == "huge" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-size-huge'>Huge</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-selector'><input type='radio' value='inline' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-inline'"+(properties[key+'-layout'] == "inline" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-inline'><i class='fas fa-arrow-right'></i></label><input type='radio' value='1' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-1'"+(properties[key+'-layout'] == "1" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-1'><i class='fas fa-arrow-down'></i></label><input type='radio' value='2' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-2'"+(properties[key+'-layout'] == "2" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-2'>2c</label><input type='radio' value='3' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-3'"+(properties[key+'-layout'] == "3" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-3'>3c</label><input type='radio' value='4' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-4'"+(properties[key+'-layout'] == "4" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-4'>4c</label><input type='radio' value='6' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout-6'"+(properties[key+'-layout'] == "6" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-layout-6'>6c</label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['layout'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'local-checkbox-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-position'])+"' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position'><span class='"+(properties[key+'-position'] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-arrow-left'></i></span><span class='"+(properties[key+'-position'] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-arrow-right'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-align'])+"' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align'><span class='"+(properties[key+'-align'] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-align-left'></i></span><span class='"+(properties[key+'-align'] == "center" ? 'lepopup-bar-option-selected' : '')+"' data-value='center'><i class='fas fa-align-center'></i></span><span class='"+(properties[key+'-align'] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-align-right'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-layout'])+"' name='lepopup-"+key+"-layout' id='lepopup-"+key+"-layout'><span class='"+(properties[key+'-layout'] == "inline" ? 'lepopup-bar-option-selected' : '')+"' data-value='inline'><i class='fas fa-arrow-right'></i></span><span class='"+(properties[key+'-layout'] == "1" ? 'lepopup-bar-option-selected' : '')+"' data-value='1'><i class='fas fa-arrow-down'></i></span><span class='"+(properties[key+'-layout'] == "2" ? 'lepopup-bar-option-selected' : '')+"' data-value='2'>2c</span><span class='"+(properties[key+'-layout'] == "3" ? 'lepopup-bar-option-selected' : '')+"' data-value='3'>3c</span><span class='"+(properties[key+'-layout'] == "4" ? 'lepopup-bar-option-selected' : '')+"' data-value='4'>4c</span><span class='"+(properties[key+'-layout'] == "6" ? 'lepopup-bar-option-selected' : '')+"' data-value='6'>6c</span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['layout'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'checkbox-view':
					options = "";
					for (var j=0; j<lepopup_meta[type][key]['options'].length; j++) {
						selected = "";
						if (properties[key] == lepopup_meta[type][key]['options'][j]) selected = " checked='checked'";
						options += "<div class='lepopup-properties-preview-option lepopup-properties-preview-option-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"' value='"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'"+selected+" /><label class='far' for='lepopup-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'></label><div><input class='lepopup-checkbox lepopup-checkbox-large lepopup-checkbox-"+lepopup_meta[type][key]['options'][j]+"' type='checkbox' id='lepopup-demo-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"' checked='checked' /><label for='lepopup-demo-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'></label></div></div>";
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+options+"</div></div>";
					break;

				case 'radio-view':
					options = "";
					for (var j=0; j<lepopup_meta[type][key]['options'].length; j++) {
						selected = "";
						if (properties[key] == lepopup_meta[type][key]['options'][j]) selected = " checked='checked'";
						options += "<div class='lepopup-properties-preview-option lepopup-properties-preview-option-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'><input type='radio' name='lepopup-"+key+"' id='lepopup-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"' value='"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'"+selected+" /><label class='far' for='lepopup-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'></label><div><input class='lepopup-radio lepopup-radio-large lepopup-radio-"+lepopup_meta[type][key]['options'][j]+"' type='radio' id='lepopup-demo-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"' name='lepopup-demo-"+key+"'"+selected+" /><label for='lepopup-demo-"+key+"-"+lepopup_escape_html(lepopup_meta[type][key]['options'][j])+"'></label></div></div>";
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+options+"</div></div>";
					break;
					
				case 'multiselect-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-align'])+"' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align'><span class='"+(properties[key+'-align'] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-align-left'></i></span><span class='"+(properties[key+'-align'] == "center" ? 'lepopup-bar-option-selected' : '')+"' data-value='center'><i class='fas fa-align-center'></i></span><span class='"+(properties[key+'-align'] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-align-right'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-properties-group'><div class='lepopup-properties-content-dime'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-hover-background' id='lepopup-"+key+"-hover-background' value='"+lepopup_escape_html(properties[key+'-hover-background'])+"' /></div><div class='lepopup-properties-content-dime'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-hover-color' id='lepopup-"+key+"-hover-color' value='"+lepopup_escape_html(properties[key+'-hover-color'])+"' /></div></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['hover-color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-properties-group'><div class='lepopup-properties-content-dime'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-selected-background' id='lepopup-"+key+"-selected-background' value='"+lepopup_escape_html(properties[key+'-selected-background'])+"' /></div><div class='lepopup-properties-content-dime'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-selected-color' id='lepopup-"+key+"-selected-color' value='"+lepopup_escape_html(properties[key+'-selected-color'])+"' /></div></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['selected-color'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;
					
				case 'description-position':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime' id='lepopup-content-"+key+"-position'><div class='lepopup-bar-selector'><input type='radio' value='bottom' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-bottom'"+(properties[key+'-position'] == "bottom" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-bottom'><i class='fas fa-arrow-down'></i></label><input type='radio' value='none' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position-none'"+(properties[key+'-position'] == "none" ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-position-none'><i class='far fa-eye-slash'></i></label></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'description-style':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime' id='lepopup-content-"+key+"-position'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-position'])+"' name='lepopup-"+key+"-position' id='lepopup-"+key+"-position'><span class='"+(properties[key+'-position'] == "bottom" ? 'lepopup-bar-option-selected' : '')+"' data-value='bottom'><i class='fas fa-arrow-down'></i></span><span class='"+(properties[key+'-position'] == "none" ? 'lepopup-bar-option-selected' : '')+"' data-value='none'><i class='far fa-eye-slash'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['position'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime'><div class='lepopup-bar-options'><input type='hidden' value='"+lepopup_escape_html(properties[key+'-align'])+"' name='lepopup-"+key+"-align' id='lepopup-"+key+"-align'><span class='"+(properties[key+'-align'] == "left" ? 'lepopup-bar-option-selected' : '')+"' data-value='left'><i class='fas fa-align-left'></i></span><span class='"+(properties[key+'-align'] == "center" ? 'lepopup-bar-option-selected' : '')+"' data-value='center'><i class='fas fa-align-center'></i></span><span class='"+(properties[key+'-align'] == "right" ? 'lepopup-bar-option-selected' : '')+"' data-value='right'><i class='fas fa-align-right'></i></span></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['align'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'units':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number lepopup-input-units lepopup-input-"+lepopup_meta[type][key]['unit']+"'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='' /></div></div></div>";
					break;
					
				case 'id':
					html += "<div class='lepopup-properties-noitem'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+properties["id"]+"' readonly='readonly' onclick='this.focus();this.select();' /></div></div></div>";
					break;
					
				case 'text':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='' /></div></div>";
					break;

				case 'integer':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='' /></div></div></div>";
					break;
				
				case 'from':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'><div class='lepopup-properties-content-half lepopup-input-shortcode-selector'><input type='text' name='lepopup-"+key+"-email' id='lepopup-"+key+"-email' value='"+lepopup_escape_html(properties[key+"-email"])+"' placeholder='Email address...' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div><div class='lepopup-properties-content-half lepopup-input-shortcode-selector'><input type='text' name='lepopup-"+key+"-name' id='lepopup-"+key+"-name' value='"+lepopup_escape_html(properties[key+"-name"])+"' placeholder='Name...' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div></div>";
					break;
					
				case 'text-shortcodes':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
					break;

				case 'textarea-shortcodes':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-textarea-shortcode-selector'><textarea name='lepopup-"+key+"' id='lepopup-"+key+"'>"+properties[key]+"</textarea><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span class='lepopup-shortcode-selector-button'><i class='fas fa-code'></i></span></div></div></div></div>";
					break;
					
				case 'textarea':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><textarea name='lepopup-"+key+"' id='lepopup-"+key+"'>"+lepopup_escape_html(properties[key])+"</textarea></div></div>";
					break;
					
				case 'text-number':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='' /></div></div></div>";
					break;
					
				case 'checkbox':
					selected = "";
					if (properties[key] == "on") selected = " checked='checked'";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' name='lepopup-"+key+"' id='lepopup-"+key+"'"+selected+"' /><label for='lepopup-"+key+"'></label></div></div>";
					break;
					
				case 'select':
					options = "";
					for (var option_key in lepopup_meta[type][key]['options']) {
						if (lepopup_meta[type][key]['options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key]) selected = " selected='selected'";
							options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['options'][option_key])+"</option>";
						}
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-third lepopup-properties-panel-block'><select name='lepopup-"+key+"' id='lepopup-"+key+"'>"+options+"</select></div></div></div>";
					break;

				case 'select-image':
					options = "";
					for (var option_key in lepopup_meta[type][key]['options']) {
						if (lepopup_meta[type][key]['options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key]) selected = " checked='checked'";
							options += "<input class='lepopup-radio-image' type='radio'"+selected+" value='"+lepopup_escape_html(option_key)+"' name='lepopup-"+key+"' id='lepopup-"+key+"-"+option_key+"' /><label for='lepopup-"+key+"-"+option_key+"' style='width:"+lepopup_meta[type][key]['width']+"px;height:"+lepopup_meta[type][key]['height']+"px;background-image:url("+lepopup_escape_html(lepopup_meta[type][key]['options'][option_key])+");'></label>";
						}
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+options+"</div></div>";
					break;

				case 'mask':
					options = "<option value=''>None</option>";
					for (var option_key in lepopup_meta[type][key]['preset-options']) {
						if (lepopup_meta[type][key]['preset-options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key+"-preset"]) selected = " selected='selected'";
							options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['preset-options'][option_key])+"</option>";
						}
					}
					temp = "<div class='lepopup-properties-content-half lepopup-properties-panel-block'><select name='lepopup-"+key+"-preset' id='lepopup-"+key+"-preset' onchange='lepopup_properties_mask_preset_changed(this);'>"+options+"</select></div>";
					temp += "<div class='lepopup-properties-content-half lepopup-properties-panel-block'><input type='text' name='lepopup-"+key+"-mask' id='lepopup-"+key+"-mask' value='"+lepopup_escape_html(properties[key+"-mask"])+"'"+(properties[key+"-preset"] == "custom" ? "" : " readonly='readonly'")+" /></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'radio-bar':
					options = "";
					for (var option_key in lepopup_meta[type][key]['options']) {
						if (lepopup_meta[type][key]['options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key]) selected = " checked='checked'";
							options += "<input type='radio' value='"+lepopup_escape_html(option_key)+"' name='lepopup-"+key+"' id='lepopup-"+key+"-"+lepopup_escape_html(option_key)+"'"+(option_key == properties[key] ? " checked='checked'" : "")+"><label for='lepopup-"+key+"-"+lepopup_escape_html(option_key)+"'>"+lepopup_meta[type][key]['options'][option_key]+"</label>";
						}
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-bar-selector'>"+options+"</div></div></div>";
					break;
					
				case 'select-size':
					options = "";
					for (var option_key in lepopup_meta[type][key]['options']) {
						if (lepopup_meta[type][key]['options'].hasOwnProperty(option_key)) {
							selected = "";
							if (option_key == properties[key+"-size"]) {
								selected = " selected='selected'";
							}
							options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_meta[type][key]['options'][option_key])+"</option>";
						}
					}
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-240'><div><select name='lepopup-"+key+"-size' id='lepopup-"+key+"-size' onchange='if(jQuery(this).val()==\"custom\"){jQuery(\"#lepopup-content-"+key+"-custom\").fadeIn(300);}else{jQuery(\"#lepopup-content-"+key+"-custom\").fadeOut(300);}'>"+options+"</select></div><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['size'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-input-units lepopup-input-px'"+(properties[key+"-size"] == "custom" ? "" : " style='display:none;'")+" id='lepopup-content-"+key+"-custom'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-custom' id='lepopup-"+key+"-custom' value='"+lepopup_escape_html(properties[key+'-custom'])+"' placeholder='Ex. 480' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['custom'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'input-icons':
					temp = "";
					icon_left = properties[key+"-left-icon"];
					if (icon_left == "") icon_left = "lepopup-fa-noicon";
					icon_right = properties[key+"-right-icon"];
					if (icon_right == "") icon_right = "lepopup-fa-noicon";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><a class='lepopup-fa-selector-button' href='#' onclick=\"return lepopup_fa_selector_open(this);\" data-id='"+key+"-left-icon'><i class='"+icon_left+"'></i></a><input type='hidden' name='lepopup-"+key+"-left-icon' id='lepopup-"+key+"-left-icon' value='"+lepopup_escape_html(properties[key+"-left-icon"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['left'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-left-size' id='lepopup-"+key+"-left-size' value='"+lepopup_escape_html(properties[key+'-left-size'])+"' placeholder='Ex. 10' /></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><a class='lepopup-fa-selector-button' href='#' onclick=\"return lepopup_fa_selector_open(this);\" data-id='"+key+"-right-icon'><i class='"+icon_right+"'></i></a><input type='hidden' name='lepopup-"+key+"-right-icon' id='lepopup-"+key+"-right-icon' value='"+lepopup_escape_html(properties[key+"-right-icon"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['right'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline lepopup-input-units lepopup-input-px'><input type='text' class='lepopup-ta-right' name='lepopup-"+key+"-right-size' id='lepopup-"+key+"-right-size' value='"+lepopup_escape_html(properties[key+'-right-size'])+"' placeholder='Ex. 10' /></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'button-icons':
					temp = "";
					icon_left = properties[key+"-left"];
					if (icon_left == "") icon_left = "lepopup-fa-noicon";
					icon_right = properties[key+"-right"];
					if (icon_right == "") icon_right = "lepopup-fa-noicon";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><a class='lepopup-fa-selector-button' href='#' onclick=\"return lepopup_fa_selector_open(this);\" data-id='"+key+"-left'><i class='"+icon_left+"'></i></a><input type='hidden' name='lepopup-"+key+"-left' id='lepopup-"+key+"-left' value='"+lepopup_escape_html(properties[key+"-left"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['left'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><a class='lepopup-fa-selector-button' href='#' onclick=\"return lepopup_fa_selector_open(this);\" data-id='"+key+"-right'><i class='"+icon_right+"'></i></a><input type='hidden' name='lepopup-"+key+"-right' id='lepopup-"+key+"-right' value='"+lepopup_escape_html(properties[key+"-right"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['right'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;

				case 'icon':
					temp = "";
					icon_left = properties[key];
					if (icon_left == "") icon_left = "lepopup-fa-noicon";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-inline'><a class='lepopup-fa-selector-button' href='#' onclick=\"return lepopup_fa_selector_open(this);\" data-id='"+key+"'><i class='"+icon_left+"'></i></a><input type='hidden' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' /></div>";
					temp += "<div class='lepopup-properties-content-9dimes'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'css':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-content-css'></div><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_css_add(\""+type+"\", null);'><i class='fas fa-plus'></i><label>Add a style</label></a></div></div>";
					break;

				case 'confirmations':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><em>"+lepopup_meta[type][key]['message']+"</em><div class='lepopup-properties-content-confirmations'></div><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_confirmations_add(null);'><i class='fas fa-plus'></i><label>Add confirmation</label></a></div></div>";
					break;

				case 'math-expressions':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-content-math-expressions'></div><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_math_add(null);'><i class='fas fa-plus'></i><label>Add math expression</label></a></div></div>";
					break;
					
				case 'notifications':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><em>"+lepopup_meta[type][key]['message']+"</em><div class='lepopup-properties-content-notifications'></div><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_notifications_add(null);'><i class='fas fa-plus'></i><label>Add notification</label></a></div></div>";
					break;

				case 'integrations':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><em>"+lepopup_meta[type][key]['message']+"</em><div class='lepopup-properties-content-integrations'></div><div class='lepopup-properties-content-integrations-providers'>";
					if (lepopup_integration_providers.length == 0) {
						html += "<div class='lepopup-properties-inline-error'>Activate at least one marketing/CRM system on Advanced Settings page.</div>";
					} else {
						for (var provider_key in lepopup_integration_providers) {
							if (lepopup_integration_providers.hasOwnProperty(provider_key)) {
								html += "<a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_integrations_add(null, -1, \""+lepopup_escape_html(provider_key)+"\");'><i class='fas fa-plus'></i><label>"+lepopup_escape_html(lepopup_integration_providers[provider_key])+"</label></a>";
							}
						}
					}
					html += "</div></div></div>";
					break;

				case 'payment-gateways':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><em>"+lepopup_meta[type][key]['message']+"</em><div class='lepopup-properties-content-payment-gateways'></div><div class='lepopup-properties-content-payment-gateways-providers'>";
					if (lepopup_payment_providers.length == 0) {
						html += "<div class='lepopup-properties-inline-error'>Activate at least one payment provider on Advanced Settings page.</div>";
					} else {
						for (var provider_key in lepopup_payment_providers) {
							if (lepopup_payment_providers.hasOwnProperty(provider_key)) {
								html += "<a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_payment_gateways_add(null, -1, \""+lepopup_escape_html(provider_key)+"\");'><i class='fas fa-plus'></i><label>"+lepopup_escape_html(lepopup_payment_providers[provider_key])+"</label></a>";
							}
						}
					}
					html += "</div></div></div>";
					break;
					
				case 'validators':
					options = "";
					for (var j=0; j<lepopup_meta[type][key]['allowed-values'].length; j++) {
						if (lepopup_validators.hasOwnProperty(lepopup_meta[type][key]['allowed-values'][j])) {
							options += "<a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' title='"+lepopup_validators[lepopup_meta[type][key]['allowed-values'][j]]["tooltip"]+"' onclick='return lepopup_properties_validators_add(\""+properties["id"]+"\", \""+type+"\", \""+lepopup_meta[type][key]['allowed-values'][j]+"\", null);'><i class='fas fa-plus'></i><label>"+lepopup_validators[lepopup_meta[type][key]['allowed-values'][j]]["label"]+"</label></a> ";
						}
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-content-validators'></div><div class='lepopup-properties-content-validators-allowed'>"+options+"</div></div></div>";
					break;

				case 'filters':
					options = "";
					for (var j=0; j<lepopup_meta[type][key]['allowed-values'].length; j++) {
						if (lepopup_filters.hasOwnProperty(lepopup_meta[type][key]['allowed-values'][j])) {
							options += "<a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' title='"+lepopup_filters[lepopup_meta[type][key]['allowed-values'][j]]["tooltip"]+"' onclick='return lepopup_properties_filters_add(\""+type+"\", \""+lepopup_meta[type][key]['allowed-values'][j]+"\", null);'><i class='fas fa-plus'></i><label>"+lepopup_filters[lepopup_meta[type][key]['allowed-values'][j]]["label"]+"</label></a> ";
						}
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-content-filters'></div><div class='lepopup-properties-content-filters-allowed'>"+options+"</div></div></div>";
					break;
					
				case 'error':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label class='lepopup-red'>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' placeholder='"+lepopup_escape_html(lepopup_meta[type][key]['value'])+"' /><em>Default message: "+lepopup_escape_html(lepopup_meta[type][key]['value'])+"</em></div></div>";
					break;

				case 'options':
					options = "";
					for (var j=0; j<properties[key].length; j++) {
						selected = false;
						if (properties[key][j].hasOwnProperty("default") && properties[key][j]["default"] == "on") selected = true;
						options += lepopup_properties_options_item_get(null, properties[key][j]["label"], properties[key][j]["value"], selected);
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-options-table-header'><div>Label</div><div>Value</div><div></div></div><div class='lepopup-properties-options-box'><div class='lepopup-properties-options-container' data-multi='"+lepopup_escape_html(lepopup_meta[type][key]['multi-select'])+"'>"+options+"</div></div><div class='lepopup-properties-options-table-footer'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_options_new(null);'><i class='fas fa-plus'></i><label>Add option</label></a><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_bulk_options_open(this);'><i class='fas fa-plus'></i><label>Add bulk options</label></a></div></div></div>";
					break;

				case 'image-options':
					options = "";
					for (var j=0; j<properties[key].length; j++) {
						selected = "";
						if (properties[key][j].hasOwnProperty("default") && properties[key][j]["default"] == "on") selected = " lepopup-properties-options-item-default";
						options += "<div class='lepopup-properties-options-item"+selected+"'><div class='lepopup-properties-options-table'><div class='lepopup-image-url lepopup-properties-options-table-image'><input class='lepopup-properties-options-image' type='text' value='"+lepopup_escape_html(properties[key][j]["image"])+"' placeholder='URL'><span><i class='far fa-image'></i></span></div><div class='lepopup-properties-options-table-label'><input class='lepopup-properties-options-label' type='text' value='"+lepopup_escape_html(properties[key][j]["label"])+"' placeholder='Label'></div><div class='lepopup-properties-options-table-value'><input class='lepopup-properties-options-value' type='text' value='"+lepopup_escape_html(properties[key][j]["value"])+"' placeholder='Value'></div><div class='lepopup-properties-options-table-icons'><span onclick='return lepopup_properties_options_default(this);' title='Set the option as a default value'><i class='fas fa-check'></i></span><span onclick='return lepopup_properties_options_new(this);' title='Add the option after this one'><i class='fas fa-plus'></i></span><span onclick='return lepopup_properties_options_copy(this);' title='Duplicate the option'><i class='far fa-copy'></i></span><span onclick='return lepopup_properties_options_delete(this);' title='Delete the option'><i class='fas fa-trash-alt'></i></span><span title='Move the option'><i class='fas fa-arrows-alt lepopup-properties-options-item-handler'></i></span></div></div></div>";
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content lepopup-properties-image-options-table'><div class='lepopup-properties-options-table-header'><div>Image</div><div>Label</div><div>Value</div><div></div></div><div class='lepopup-properties-options-box'><div class='lepopup-properties-options-container' data-multi='"+(properties['mode'] == "radio" ? "off" : "on")+"'>"+options+"</div></div><div class='lepopup-properties-options-table-footer'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_options_new(null);'><i class='fas fa-plus'></i><label>Add option</label></a></div></div></div>";
					break;

				case 'logic-rules':
					var input_ids = new Array();
					for (var j=0; j<lepopup_form_elements.length; j++) {
						if (lepopup_form_elements[j] == null) continue;
						//if (lepopup_form_elements[j]["id"] == properties["id"]) continue;
						if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[j]['type']) && lepopup_toolbar_tools[lepopup_form_elements[j]['type']]['type'] == 'input') {
							input_ids.push(lepopup_form_elements[j]["id"]);
						}
					}
					if (input_ids.length > 0) {
						temp = "<div class='lepopup-properties-group lepopup-properties-logic-header'>";
						options = "";
						for (var option_key in lepopup_meta[type][key]['actions']) {
							if (lepopup_meta[type][key]['actions'].hasOwnProperty(option_key)) {
								options += "<option value='"+lepopup_escape_html(option_key)+"'"+(properties[key]["action"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_meta[type][key]['actions'][option_key])+"</option>";
							}
						}
						temp += "<div class='lepopup-properties-content-half lepopup-properties-panel-block'><select name='lepopup-"+key+"-action' id='lepopup-"+key+"-action'>"+options+"</select></div>";
						options = "";
						for (var option_key in lepopup_meta[type][key]['operators']) {
							if (lepopup_meta[type][key]['operators'].hasOwnProperty(option_key)) {
								options += "<option value='"+lepopup_escape_html(option_key)+"'"+(properties[key]["operator"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_meta[type][key]['operators'][option_key])+"</option>";
							}
						}
						temp += "<div class='lepopup-properties-content-half lepopup-properties-panel-block'><select name='lepopup-"+key+"-operator' id='lepopup-"+key+"-operator'>"+options+"</select></div>";
						temp += "</div>";
						options = "";
						for (var j=0; j<properties[key]["rules"].length; j++) {
							if (input_ids.indexOf(parseInt(properties[key]["rules"][j]["field"], 10)) != -1) {
								options += lepopup_properties_logic_rule_get(properties["id"], properties[key]["rules"][j]["field"], properties[key]["rules"][j]["rule"], properties[key]["rules"][j]["token"]);
							}
						}
						temp += "<div class='lepopup-properties-logic-rules'>"+options+"</div><div class='lepopup-properties-logic-buttons'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_logic_rule_new(this, \""+properties["id"]+"\");'><i class='fas fa-plus'></i><label>Add rule</label></a></div>";
					} else {
						temp = "<div class='lepopup-properties-inline-error'>There are no elements available to use for logic rules.</div>";
					}
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'>"+temp+"</div></div>";
					break;
					
				case 'colors':
					temp = "";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-background' id='lepopup-"+key+"-background' value='"+lepopup_escape_html(properties[key+'-background'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['background'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-border' id='lepopup-"+key+"-border' value='"+lepopup_escape_html(properties[key+'-border'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['border'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-dime lepopup-properties-panel-block'><input type='text' class='lepopup-color' data-alpha='true' name='lepopup-"+key+"-text' id='lepopup-"+key+"-text' value='"+lepopup_escape_html(properties[key+'-text'])+"' placeholder='...' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['text'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'date':
					temp = "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' /><span><i class='far fa-calendar-alt'></i></span></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'date-limit':
					options2 = "";
					for (var j=0; j<lepopup_form_elements.length; j++) {
						if (lepopup_form_elements[j] == null) continue;
						if (lepopup_form_elements[j]["id"] == properties["id"]) continue;
						if (lepopup_form_elements[j]["type"] == "date") {
							options2 += "<option value='"+lepopup_form_elements[j]["id"]+"'"+(properties[key+"-field"] == lepopup_form_elements[j]["id"] ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_form_elements[j]["id"]+" | "+lepopup_form_elements[j]["name"])+"</option>";
						}
					}
					options = "";
					for (var option_key in lepopup_meta[type][key]['type-values']) {
						if (lepopup_meta[type][key]['type-values'].hasOwnProperty(option_key)) {
							if (option_key != "field" || options2 != "") {
								options += "<option value='"+lepopup_escape_html(option_key)+"'"+(properties[key+"-type"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_meta[type][key]['type-values'][option_key])+"</option>";
							}
						}
					}
					temp = "<div class='lepopup-properties-content-third lepopup-properties-panel-block'><select name='lepopup-"+key+"-type' id='lepopup-"+key+"-type' onchange='var date = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-date-limit-date\"); var field = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-date-limit-field\"); var offset = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-date-limit-offset\"); if (jQuery(this).val() == \"date\") {jQuery(date).show();} else {jQuery(date).hide();} if (jQuery(this).val() == \"field\") {jQuery(field).show();} else {jQuery(field).hide();} if (jQuery(this).val() == \"offset\") {jQuery(offset).show();} else {jQuery(offset).hide();}'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['type'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date-limit-date lepopup-date'"+(properties[key+"-type"] == "date" ? "" : " style='display: none;'")+"><input type='text' name='lepopup-"+key+"-date' id='lepopup-"+key+"-date' value='"+lepopup_escape_html(properties[key+"-date"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['date'])+"</label><span><i class='far fa-calendar-alt'></i></span></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date-limit-field'"+(properties[key+"-type"] == "field" ? "" : " style='display: none;'")+"><select name='lepopup-"+key+"-field' id='lepopup-"+key+"-field'>"+options2+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['field'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date-limit-offset'"+(properties[key+"-type"] == "offset" ? "" : " style='display: none;'")+"><input type='text' name='lepopup-"+key+"-offset' id='lepopup-"+key+"-offset' value='"+lepopup_escape_html(properties[key+"-offset"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['offset'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'date-default':
					options = "";
					for (var option_key in lepopup_meta[type][key]['type-values']) {
						if (lepopup_meta[type][key]['type-values'].hasOwnProperty(option_key)) {
							if (option_key != "field") {
								options += "<option value='"+lepopup_escape_html(option_key)+"'"+(properties[key+"-type"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_meta[type][key]['type-values'][option_key])+"</option>";
							}
						}
					}
					temp = "<div class='lepopup-properties-content-third lepopup-properties-panel-block'><select name='lepopup-"+key+"-type' id='lepopup-"+key+"-type' onchange='var date = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-date-default-date\"); var offset = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-date-default-offset\"); if (jQuery(this).val() == \"date\") {jQuery(date).show();} else {jQuery(date).hide();} if (jQuery(this).val() == \"offset\") {jQuery(offset).show();} else {jQuery(offset).hide();}'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['type'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date-default-date lepopup-date'"+(properties[key+"-type"] == "date" ? "" : " style='display: none;'")+"><input type='text' name='lepopup-"+key+"-date' id='lepopup-"+key+"-date' value='"+lepopup_escape_html(properties[key+"-date"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['date'])+"</label><span><i class='far fa-calendar-alt'></i></span></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-date-default-offset'"+(properties[key+"-type"] == "offset" ? "" : " style='display: none;'")+"><input type='text' name='lepopup-"+key+"-offset' id='lepopup-"+key+"-offset' value='"+lepopup_escape_html(properties[key+"-offset"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['offset'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'time':
					temp = "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-time'><input type='text' name='lepopup-"+key+"' id='lepopup-"+key+"' value='"+lepopup_escape_html(properties[key])+"' /><span><i class='far fa-clock'></i></span></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;

				case 'time-limit':
					options2 = "";
					for (var j=0; j<lepopup_form_elements.length; j++) {
						if (lepopup_form_elements[j] == null) continue;
						if (lepopup_form_elements[j]["id"] == properties["id"]) continue;
						if (lepopup_form_elements[j]["type"] == "time") {
							options2 += "<option value='"+lepopup_form_elements[j]["id"]+"'"+(properties[key+"-field"] == lepopup_form_elements[j]["id"] ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_form_elements[j]["id"]+" | "+lepopup_form_elements[j]["name"])+"</option>";
						}
					}
					options = "";
					for (var option_key in lepopup_meta[type][key]['type-values']) {
						if (lepopup_meta[type][key]['type-values'].hasOwnProperty(option_key)) {
							if (option_key != "field" || options2 != "") {
								options += "<option value='"+lepopup_escape_html(option_key)+"'"+(properties[key+"-type"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_meta[type][key]['type-values'][option_key])+"</option>";
							}
						}
					}
					temp = "<div class='lepopup-properties-content-third lepopup-properties-panel-block'><select name='lepopup-"+key+"-type' id='lepopup-"+key+"-type' onchange='var time = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-time-limit-time\"); var field = jQuery(this).closest(\".lepopup-properties-content\").find(\".lepopup-time-limit-field\"); if (jQuery(this).val() == \"time\") {jQuery(time).show();} else {jQuery(time).hide();} if (jQuery(this).val() == \"field\") {jQuery(field).show();} else {jQuery(field).hide();}'>"+options+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['type'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-time-limit-time lepopup-time'"+(properties[key+"-type"] == "time" ? "" : " style='display: none;'")+"><input type='text' name='lepopup-"+key+"-time' id='lepopup-"+key+"-time' value='"+lepopup_escape_html(properties[key+"-time"])+"' /><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['time'])+"</label><span><i class='far fa-clock'></i></span></div>";
					temp += "<div class='lepopup-properties-content-third lepopup-properties-panel-block lepopup-time-limit-field'"+(properties[key+"-type"] == "field" ? "" : " style='display: none;'")+"><select name='lepopup-"+key+"-field' id='lepopup-"+key+"-field'>"+options2+"</select><label>"+lepopup_escape_html(lepopup_meta[type][key]['caption']['field'])+"</label></div>";
					temp += "<div class='lepopup-properties-content-two-third'></div>";
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_meta[type][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-properties-group'>"+temp+"</div></div></div>";
					break;
					
				case 'message':
					html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-content'><div class='lepopup-properties-message'>"+lepopup_meta[type][key]['message']+"</div></div></div>";
					break;

				case 'hr':
					html += '<hr>';
					break;
					
				default:
					break;
			}
		}
	}
	for (var j=0; j<sections_opened; j++) html += "</div>";
	sections_opened = 0;
	if (tab_html != "") {
		tab_html += "</div>";
		html += "</div>";
	}
	jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-content-form").html(tab_html + html);
	
	if (type == "settings") {
		for (var j=0; j<lepopup_form_elements.length; j++) {
			if (lepopup_form_elements[j] == null) continue;
			if (lepopup_form_elements[j]['type'] == 'signature') {
				var xd = jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-content-form").find("[name='lepopup-cross-domain']");
				jQuery(xd).prop("checked", false);
				jQuery(xd).prop("disabled", true);
				break;
			}
		}
	}
	jQuery("#lepopup-properties-tabs a").first().addClass("lepopup-tab-active");
	jQuery(jQuery("#lepopup-properties-tabs a").first().attr("href")).show();
	if (properties.hasOwnProperty("css") && Array.isArray(properties["css"])) {
		for (var j=0; j<properties["css"].length; j++) {
			lepopup_properties_css_add(type, properties["css"][j])
		}
	}
	if (properties.hasOwnProperty("validators") && Array.isArray(properties["validators"])) {
		for (var j=0; j<properties["validators"].length; j++) {
			lepopup_properties_validators_add(properties["id"], type, properties["validators"][j]["type"], properties["validators"][j]);
		}
	}
	if (properties.hasOwnProperty("filters") && Array.isArray(properties["filters"])) {
		for (var j=0; j<properties["filters"].length; j++) {
			lepopup_properties_filters_add(type, properties["filters"][j]["type"], properties["filters"][j]);
		}
	}
	if (properties.hasOwnProperty("confirmations") && Array.isArray(properties["confirmations"])) {
		for (var j=0; j<properties["confirmations"].length; j++) {
			lepopup_properties_confirmations_add(properties["confirmations"][j])
		}
		jQuery(".lepopup-properties-content-confirmations").sortable({
			items: ".lepopup-properties-sub-item",
			forcePlaceholderSize: true,
			dropOnEmpty: true,
			placeholder: "lepopup-properties-sub-item-placeholder"
		});
		jQuery(".lepopup-properties-sub-item").disableSelection();
	}
	if (properties.hasOwnProperty("notifications") && Array.isArray(properties["notifications"])) {
		for (var j=0; j<properties["notifications"].length; j++) {
			lepopup_properties_notifications_add(properties["notifications"][j])
		}
	}
	if (properties.hasOwnProperty("math-expressions") && Array.isArray(properties["math-expressions"])) {
		for (var j=0; j<properties["math-expressions"].length; j++) {
			lepopup_properties_math_add(properties["math-expressions"][j])
		}
	}
	if (properties.hasOwnProperty("integrations") && Array.isArray(properties["integrations"])) {
		for (var j=0; j<properties["integrations"].length; j++) {
			if (properties["integrations"][j]['id'] > lepopup_integration_last_id) lepopup_integration_last_id = properties["integrations"][j]['id'];
			lepopup_properties_integrations_add(properties["integrations"][j], j);
		}
	}
	if (properties.hasOwnProperty("payment-gateways") && Array.isArray(properties["payment-gateways"])) {
		for (var j=0; j<properties["payment-gateways"].length; j++) {
			if (properties["payment-gateways"][j]['id'] > lepopup_payment_gateway_last_id) lepopup_payment_gateway_last_id = properties["payment-gateways"][j]['id'];
			lepopup_properties_payment_gateways_add(properties["payment-gateways"][j], j);
		}
	}
	if (properties.hasOwnProperty("options")) {
		jQuery(".lepopup-properties-options-box").resizable({
			grid: [5, 5], 
			handles: "s"
		});
	
		jQuery(".lepopup-properties-options-container").sortable({
			items: ".lepopup-properties-options-item",
			forcePlaceholderSize: true,
			dropOnEmpty: true,
			placeholder: "lepopup-properties-options-item-placeholder",
			handle: ".lepopup-properties-options-item-handler",
			stop: function(event, ui) {
				lepopup_properties_change();
				lepopup_element_properties_data_changed = true;
			}
		});
		jQuery(".lepopup-properties-options-item").disableSelection();
	}
	jQuery(".lepopup-properties-content .lepopup-date input").each(function(){
		var object = this;
		var airdatepicker = jQuery(object).airdatepicker().data('airdatepicker');
		airdatepicker.destroy();
		jQuery(object).airdatepicker({
			inline_popup	: true,
			autoClose		: true,
			timepicker		: false,
			dateFormat		: lepopup_form_options["datetime-args-date-format"]
		});
	});
	jQuery(".lepopup-properties-content .lepopup-date span").on("click", function(e){
		e.preventDefault();
		var input = jQuery(this).parent().children("input");
		var airdatepicker = jQuery(input).airdatepicker().data('airdatepicker');
		airdatepicker.show();
	});
	jQuery(".lepopup-properties-content .lepopup-time input").each(function(){
		var object = this;
		var airdatepicker = jQuery(object).airdatepicker().data('airdatepicker');
		airdatepicker.destroy();
		jQuery(object).airdatepicker({
			inline_popup	: true,
			autoClose		: true,
			timepicker		: true,
			onlyTimepicker	: true,
			timeFormat		: lepopup_form_options["datetime-args-time-format"]
		});
	});
	jQuery(".lepopup-properties-content .lepopup-time span").on("click", function(e){
		e.preventDefault();
		var input = jQuery(this).parent().children("input");
		var airdatepicker = jQuery(input).airdatepicker().data('airdatepicker');
		airdatepicker.show();
	});
	jQuery("#lepopup-properties-tabs a").on("click", function(e){
		e.preventDefault();
		if (jQuery(this).hasClass("lepopup-tab-active")) return;
		var tab_set = jQuery(this).parent();
		var active_tab = jQuery(tab_set).find(".lepopup-tab-active").attr("href");
		jQuery(tab_set).find(".lepopup-tab-active").removeClass("lepopup-tab-active");
		var tab = jQuery(this).attr("href");
		jQuery(this).addClass("lepopup-tab-active");
		jQuery(active_tab).fadeOut(300, function(){
			jQuery(tab).fadeIn(300);
		});
	});
	jQuery(".lepopup-bar-options span").on("click", function(e){
		var parent = jQuery(this).parent();
		var value = jQuery(this).attr("data-value");
		var current_value = jQuery(parent).find("input").val();
		jQuery(parent).children("span").removeClass("lepopup-bar-option-selected");
		if (current_value == value) {
			value = "";
			jQuery(parent).find("input").val(value);
		} else {
			jQuery(this).addClass("lepopup-bar-option-selected");
			jQuery(parent).find("input").val(value);
		}
		lepopup_properties_change();
		if (jQuery(parent).find("input").attr("name") == "lepopup-label-style-position") {
			if (value == "left" || value == "right") jQuery("#lepopup-content-label-style-width").fadeIn(300);
			else jQuery("#lepopup-content-label-style-width").fadeOut(300);
		}
	});
	jQuery(".lepopup-image-url span").on("click", function(e){
		e.preventDefault();
		var input = jQuery(this).parent().children("input");
		var media_frame = wp.media({
			title: 'Select Image',
			library: {
				type: 'image'
			},
			multiple: false
		});
		media_frame.on("select", function() {
			var attachment = media_frame.state().get("selection").first();
			jQuery(input).val(attachment.attributes.url);
			lepopup_properties_change();
		});
		media_frame.open();
	});
	jQuery(".lepopup-sections").each(function(){
		jQuery(this).find("a").on("click", function(e){
			e.preventDefault();
			if (jQuery(this).hasClass("lepopup-section-active")) return;
			var sections_set = jQuery(this).parent();
			var active_section = jQuery(sections_set).find(".lepopup-section-active").attr("href");
			jQuery(sections_set).find(".lepopup-section-active").removeClass("lepopup-section-active");
			var section = jQuery(this).attr("href");
			jQuery(this).addClass("lepopup-section-active");
			if (jQuery(active_section).length > 0) {
				jQuery(active_section).fadeOut(300, function(){
					jQuery(section).fadeIn(300);
				});
			} else jQuery(section).fadeIn(300);
		});
		jQuery(jQuery(this).find("a").first().attr("href")).show();
	});
	jQuery(".lepopup-color").minicolors({
		format: 'rgb',
		opacity: true,
		change: function(value, opacity) {
			lepopup_properties_change();
		}
	});
	jQuery(".lepopup-slider").each(function(){
		var input = jQuery(this).parent().children("input");
		jQuery(this).slider({
			min: 	parseInt(jQuery(this).attr("data-min"), 10), 
			max: 	parseInt(jQuery(this).attr("data-max"), 10),
			step:	parseInt(jQuery(this).attr("data-step"), 10),
			value:	lepopup_is_numeric(jQuery(input).val()) ? parseInt(jQuery(input).val(), 10) : 4,
			create: function() {
				jQuery(this).find(".ui-slider-handle").text(jQuery(this).slider("value"));
			},
			slide: function( event, ui ) {
				jQuery(this).find(".ui-slider-handle").text(ui.value);
				jQuery(input).val(ui.value);
			}
		});
	});
	jQuery(".lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	jQuery(".lepopup-properties-content-validators-allowed a[title], .lepopup-properties-content-filters-allowed a[title]").tooltipster({
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom"
	});
	
//	jQuery(".lepopup-properties-content input").on("keyup", function(e){
//		lepopup_properties_change();
//	});
//	jQuery(".lepopup-properties-content input, .lepopup-properties-content select").on("change", function(e){
//		lepopup_properties_change();
//	});
	jQuery(".lepopup-properties-content input, .lepopup-properties-content textarea").on("input", function(e){
		lepopup_properties_change();
	});
	jQuery(".lepopup-properties-content select").on("change", function(e){
		lepopup_properties_change();
	});
	lepopup_properties_visible_conditions(_object);
	// Prepare editor state - end
	return false;
}
function lepopup_properties_open(_object) {
	if (lepopup_element_properties_active) {
		lepopup_properties_panel_close();
	}
	lepopup_element_properties_target = "#lepopup-element-properties";
	jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 880), 1080);
	jQuery("#lepopup-element-properties").height(window_height);
	jQuery("#lepopup-element-properties").width(window_width);
	jQuery("#lepopup-element-properties .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-element-properties .lepopup-admin-popup-content").height(window_height - 104);
	jQuery("#lepopup-element-properties-overlay").fadeIn(300);
	jQuery("#lepopup-element-properties").fadeIn(300);
	lepopup_element_properties_active = _object;
	lepopup_element_properties_data_changed = false;
	jQuery("#lepopup-element-properties .lepopup-admin-popup-loading").show();
	
	setTimeout(function(){
		_lepopup_properties_prepare(_object);
		jQuery("#lepopup-element-properties .lepopup-admin-popup-loading").hide();
	}, 500);
	return false;
}

function lepopup_properties_save() {
	jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-buttons .lepopup-admin-button").find("i").attr("class", "fas fa-spin fa-spinner");
	lepopup_properties_populate();
	_lepopup_properties_close();
	lepopup_build();
	return false;
}

function lepopup_properties_populate() {
	var properties, logic, attachments, input, page_i, temp, id;
	if (lepopup_element_properties_active == null) return false;
	var type = jQuery(lepopup_element_properties_active).attr("data-type");
	if (typeof type == undefined || type == "") return false;
	
//	jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-buttons .lepopup-admin-button").find("i").attr("class", "fas fa-spin fa-spinner");
	if (type == "settings") {
		properties = lepopup_form_options;
	} else if (type == "page" || type == "page-confirmation") {
		id = jQuery(lepopup_element_properties_active).closest("li").attr("data-id");
		jQuery(lepopup_element_properties_active).closest("li").attr("data-name", jQuery("[name='lepopup-name']").val());
		properties = null;
		for (var i=0; i<lepopup_form_pages.length; i++) {
			if (lepopup_form_pages[i] != null && lepopup_form_pages[i]["id"] == id) {
				page_i = i;
				properties = lepopup_form_pages[i];
				break;
			}
		}
	} else {
		i = jQuery(lepopup_element_properties_active).attr("id");
		i = i.replace("lepopup-element-", "");
		temp = jQuery("[name='lepopup-name']").val();
		if (temp == "") temp = "Untitled Element";
		jQuery(".lepopup-layers-list").find("li.lepopup-layer-"+i).html(lepopup_escape_html(temp));
		properties = lepopup_form_elements[i];
	}
	for (var key in properties) {
		if (properties.hasOwnProperty(key)) {
			input = jQuery("[name='lepopup-"+key+"']");
			if (key == "personal-keys") {
				properties[key] = new Array();
				jQuery(input).each(function(){
					if (jQuery(this).is(":checked")) {
						properties[key].push(parseInt(jQuery(this).val(), 10));
					}
				});
			} else if (input.length > 1) {
				jQuery(input).each(function(){
					if (jQuery(this).is(":checked")) {
						properties[key] = jQuery(this).val();
						return false;
					}
				});
			} else if (input.length > 0) {
				if (jQuery(input).is(":checked")) properties[key] = "on";
				else properties[key] = jQuery(input).val();
			}
		}
	}
	if (properties.hasOwnProperty("css")) {
		properties["css"] = new Array();
		jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item").each(function() {
			(properties["css"]).push({"selector" : jQuery(this).find(".lepopup-properties-sub-item-body select").val(), "css" : jQuery(this).find(".lepopup-properties-sub-item-body textarea").val()});
		});
	}
	if (properties.hasOwnProperty("validators")) {
		properties["validators"] = new Array();
		jQuery(".lepopup-properties-content-validators .lepopup-properties-sub-item").each(function() {
			var validator_type = jQuery(this).attr("data-type");
			if (lepopup_validators.hasOwnProperty(validator_type)) {
				var validator = {"type" : validator_type, "properties" : {}};
				for (var key in lepopup_validators[validator_type]["properties"]) {
					if (lepopup_validators[validator_type]["properties"].hasOwnProperty(key)) {
						if (jQuery(this).find("[name=lepopup-validators-"+key+"]").length > 0) {
							if (jQuery(this).find("[name=lepopup-validators-"+key+"]").is(":checked")) validator["properties"][key] = "on";
							else validator["properties"][key] = jQuery(this).find("[name=lepopup-validators-"+key+"]").val();
						}
					}
				}
				(properties["validators"]).push(validator);
			}
		});
	}
	if (properties.hasOwnProperty("filters")) {
		properties["filters"] = new Array();
		jQuery(".lepopup-properties-content-filters .lepopup-properties-sub-item").each(function() {
			var filter_type = jQuery(this).attr("data-type");
			if (lepopup_filters.hasOwnProperty(filter_type)) {
				var filter = {"type" : filter_type, "properties" : {}};
				for (var key in lepopup_filters[filter_type]["properties"]) {
					if (lepopup_filters[filter_type]["properties"].hasOwnProperty(key)) {
						if (jQuery(this).find("[name=lepopup-filters-"+key+"]").length > 0) {
							if (jQuery(this).find("[name=lepopup-filters-"+key+"]").is(":checked")) filter["properties"][key] = "on";
							else filter["properties"][key] = jQuery(this).find("[name=lepopup-filters-"+key+"]").val();
						}
					}
				}
				(properties["filters"]).push(filter);
			}
		});
	}
	if (properties.hasOwnProperty("options")) {
		properties["options"] = new Array();
		jQuery(".lepopup-properties-options-container .lepopup-properties-options-item").each(function() {
			var selected = "off";
			if (jQuery(this).hasClass("lepopup-properties-options-item-default")) selected = "on";
			(properties["options"]).push({"default" : selected, "label" : jQuery(this).find(".lepopup-properties-options-label").val(), "value" : jQuery(this).find(".lepopup-properties-options-value").val(), "image" : jQuery(this).find(".lepopup-properties-options-image").val()});
		});
	}
	if (properties.hasOwnProperty("confirmations")) {
		properties["confirmations"] = new Array();
		jQuery(".lepopup-properties-content-confirmations .lepopup-properties-sub-item").each(function() {
			logic = {
				"action"	: jQuery(this).find("[name='lepopup-confirmations-logic-action']").val(),
				"operator"	: jQuery(this).find("[name='lepopup-confirmations-logic-operator']").val(),
				"rules"		: new Array()
			};
			jQuery(this).find(".lepopup-properties-logic-rule").each(function() {
				(logic["rules"]).push({"field" : parseInt(jQuery(this).find(".lepopup-properties-logic-rule-field").val(), 10), "rule" : jQuery(this).find(".lepopup-properties-logic-rule-rule").val(), "token" : jQuery(this).find(".lepopup-properties-logic-rule-token").val()});
			});
			(properties["confirmations"]).push({
				"name" 				: jQuery(this).find("[name='lepopup-confirmations-name']").val(),
				"type" 				: jQuery(this).find("[name='lepopup-confirmations-type']").val(),
				"form" 				: jQuery(this).find("[name='lepopup-confirmations-form']").val(),
				"url" 				: jQuery(this).find("[name='lepopup-confirmations-url']").val(),
				"delay" 			: jQuery(this).find("[name='lepopup-confirmations-delay']").val(),
				"payment-gateway"	: jQuery(this).find("[name='lepopup-confirmations-payment-gateway']").val(),
				"reset-form" 		: jQuery(this).find("[name='lepopup-confirmations-reset-form']").is(":checked") ? "on" : "off",
				"logic-enable" 		: jQuery(this).find("[name='lepopup-confirmations-logic-enable']").is(":checked") ? "on" : "off",
				"logic"				: logic
			});
		});
	}
	if (properties.hasOwnProperty("notifications")) {
		properties["notifications"] = new Array();
		jQuery(".lepopup-properties-content-notifications .lepopup-properties-sub-item").each(function() {
			logic = {
				"action"	: jQuery(this).find("[name='lepopup-notifications-logic-action']").val(),
				"operator"	: jQuery(this).find("[name='lepopup-notifications-logic-operator']").val(),
				"rules"		: new Array()
			};
			jQuery(this).find(".lepopup-properties-logic-rule").each(function() {
				(logic["rules"]).push({"field" : parseInt(jQuery(this).find(".lepopup-properties-logic-rule-field").val(), 10), "rule" : jQuery(this).find(".lepopup-properties-logic-rule-rule").val(), "token" : jQuery(this).find(".lepopup-properties-logic-rule-token").val()});
			});
			attachments = new Array();
			jQuery(this).find(".lepopup-properties-attachment").each(function() {
				attachments.push({"source" : jQuery(this).find(".lepopup-properties-attachment-source").val(), "token" : jQuery(this).find(".lepopup-properties-attachment-token").val()});
			});
			
			(properties["notifications"]).push({
				"name" 				: jQuery(this).find("[name='lepopup-notifications-name']").val(),
				"enabled"	 		: jQuery(this).find("[name='lepopup-notifications-enabled']").is(":checked") ? "on" : "off",
				"action"	 		: jQuery(this).find("[name='lepopup-notifications-action']").val(),
				"recipient-email" 	: jQuery(this).find("[name='lepopup-notifications-recipient-email']").val(),
				"subject" 			: jQuery(this).find("[name='lepopup-notifications-subject']").val(),
				"message" 			: jQuery(this).find("[name='lepopup-notifications-message']").val(),
				"attachments"		: attachments,
				"reply-email" 		: jQuery(this).find("[name='lepopup-notifications-reply-email']").val(),
				"from-email" 		: jQuery(this).find("[name='lepopup-notifications-from-email']").val(),
				"from-name" 		: jQuery(this).find("[name='lepopup-notifications-from-name']").val(),
				"logic-enable" 		: jQuery(this).find("[name='lepopup-notifications-logic-enable']").is(":checked") ? "on" : "off",
				"logic"				: logic
			});
		});
	}
	if (properties.hasOwnProperty("math-expressions")) {
		properties["math-expressions"] = new Array();
		jQuery(".lepopup-properties-content-math-expressions .lepopup-properties-sub-item").each(function() {
			(properties["math-expressions"]).push({
				"id" 				: jQuery(this).find("[name='lepopup-math-id']").val(),
				"name" 				: jQuery(this).find("[name='lepopup-math-name']").val(),
				"expression" 		: jQuery(this).find("[name='lepopup-math-expression']").val(),
				"decimal-digits" 	: parseInt(jQuery(this).find("[name='lepopup-math-decimal-digits']").val(), 10),
				"default" 			: jQuery(this).find("[name='lepopup-math-default']").val()
			});
		});
	}
	var integrations;
	if (properties.hasOwnProperty("integrations")) {
		integrations = new Array();
		jQuery(".lepopup-properties-content-integrations .lepopup-properties-sub-item").each(function() {
			logic = {
				"action"	: jQuery(this).find("[name='lepopup-integrations-logic-action']").val(),
				"operator"	: jQuery(this).find("[name='lepopup-integrations-logic-operator']").val(),
				"rules"		: new Array()
			};
			jQuery(this).find(".lepopup-properties-logic-rule").each(function() {
				(logic["rules"]).push({"field" : parseInt(jQuery(this).find(".lepopup-properties-logic-rule-field").val(), 10), "rule" : jQuery(this).find(".lepopup-properties-logic-rule-rule").val(), "token" : jQuery(this).find(".lepopup-properties-logic-rule-token").val()});
			});
			var content = jQuery(this).find(".lepopup-integrations-content");
			var data = {};
			var idx = jQuery(this).find("[name='lepopup-integrations-idx']").val();
			var data_loaded = jQuery(this).attr("data-loaded");
			
			if (Array.isArray(properties["integrations"]) && properties["integrations"][idx] !== void 0 && data_loaded == "off") {
				data = properties["integrations"][idx]["data"];
			} else {
				jQuery(content).find("input, select, textarea").each(function(){
					if (jQuery(this).attr("data-skip") == "on") return;
					if (jQuery(this).attr("data-custom") == "on") return;
					var input_type = jQuery(this).attr("type");
					var name = jQuery(this).attr("name");
					var include_empty = jQuery(this).attr("data-empty");
					var name_parts = name.split(/(.*?)\[(.*?)\]/);
					if (name_parts.length > 2) {
						if (!data.hasOwnProperty(name_parts[1])) data[name_parts[1]] = {};
						if (input_type == "checkbox") {
							if (jQuery(this).is(":checked")) (data[name_parts[1]])[name_parts[2]] = jQuery(this).val();
						} else if (jQuery(this).val().length > 0 || include_empty == "on") (data[name_parts[1]])[name_parts[2]] = jQuery(this).val();
					} else {
						if (input_type == "checkbox") {
							if (jQuery(this).is(":checked")) data[name_parts[0]] = "on";
							else data[name_parts[0]] = "off";
						} else if (jQuery(this).val().length > 0 || include_empty == "on") data[name_parts[0]] = jQuery(this).val();
					}
				});
				jQuery(content).find(".lepopup-integrations-custom").each(function(){
					var name, value;
					var param_names = jQuery(this).attr("data-names");
					var param_values = jQuery(this).attr("data-values");
					var param_all = jQuery(this).attr("data-all");
					if (param_all != "on") param_all = "off";
					data[param_names] = new Array();
					data[param_values] = new Array();
					var names = jQuery(this).find("input.lepopup-integrations-custom-name");
					var values = jQuery(this).find("input.lepopup-integrations-custom-value");
					for (var j=0; j<names.length; j++) {
						name = jQuery(names[j]).val();
						value = jQuery(values[j]).val();
						if (name.length > 0 && (value.length > 0 || param_all == "on")) {
							(data[param_names]).push(name);
							(data[param_values]).push(value);
						}
					}
				});
			}
			integrations.push({
				"name" 			: jQuery(this).find("[name='lepopup-integrations-name']").val(),
				"enabled"	 	: jQuery(this).find("[name='lepopup-integrations-enabled']").is(":checked") ? "on" : "off",
				"action" 		: jQuery(this).find("[name='lepopup-integrations-action']").val(),
				"provider" 		: jQuery(this).find("[name='lepopup-integrations-provider']").val(),
				"data"			: data,
				"logic-enable" 	: jQuery(this).find("[name='lepopup-integrations-logic-enable']").is(":checked") ? "on" : "off",
				"logic"			: logic
			});
		});
		properties["integrations"] = integrations;
	}
	if (properties.hasOwnProperty("payment-gateways")) {
		integrations = new Array();
		jQuery(".lepopup-properties-content-payment-gateways .lepopup-properties-sub-item").each(function() {
			var content = jQuery(this).find(".lepopup-payment-gateways-content");
			var data = {};
			var idx = jQuery(this).find("[name='lepopup-payment-gateways-idx']").val();
			var data_loaded = jQuery(this).attr("data-loaded");
			if (Array.isArray(properties["payment-gateways"]) && properties["payment-gateways"][idx] !== void 0 && data_loaded == "off") {
				data = properties["payment-gateways"][idx]["data"];
			} else {
				jQuery(content).find("input, select, textarea").each(function(){
					if (jQuery(this).attr("data-skip") == "on") return;
					var input_type = jQuery(this).attr("type");
					var name = jQuery(this).attr("name");
					if (name) {
						var name_parts = name.split(/(.*?)\[(.*?)\]/);
						if (name_parts.length > 2) {
							if (!data.hasOwnProperty(name_parts[1])) data[name_parts[1]] = {};
							if (input_type == "checkbox") {
								if (jQuery(this).is(":checked")) (data[name_parts[1]])[name_parts[2]] = jQuery(this).val();
							} else if (jQuery(this).val().length > 0) (data[name_parts[1]])[name_parts[2]] = jQuery(this).val();
						} else {
							if (input_type == "checkbox") {
								if (jQuery(this).is(":checked")) data[name_parts[0]] = jQuery(this).val();
							} else if (jQuery(this).val().length > 0) data[name_parts[0]] = jQuery(this).val();
						}
					}
				});
			}
			integrations.push({
				"id" 			: jQuery(this).find("[name='lepopup-payment-gateways-id']").val(),
				"name" 			: jQuery(this).find("[name='lepopup-payment-gateways-name']").val(),
				"provider" 		: jQuery(this).find("[name='lepopup-payment-gateways-provider']").val(),
				"data"			: data
			});
		});
		properties["payment-gateways"] = integrations;
	}
	
	if (properties.hasOwnProperty("logic")) {
		properties["logic"] = {};
		if (jQuery("#lepopup-logic-action").length > 0) properties["logic"]["action"] = jQuery("#lepopup-logic-action").val();
		else properties["logic"]["action"] = lepopup_meta[properties['type']]['logic']['values']['action'];
		if (jQuery("#lepopup-logic-operator").length > 0) properties["logic"]["operator"] = jQuery("#lepopup-logic-operator").val();
		else properties["logic"]["operator"] = lepopup_meta[properties['type']]['logic']['values']['operator'];
		properties["logic"]["rules"] = new Array();
		jQuery(".lepopup-properties-logic-rules .lepopup-properties-logic-rule").each(function() {
			(properties["logic"]["rules"]).push({"field" : parseInt(jQuery(this).find(".lepopup-properties-logic-rule-field").val(), 10), "rule" : jQuery(this).find(".lepopup-properties-logic-rule-rule").val(), "token" : jQuery(this).find(".lepopup-properties-logic-rule-token").val()});
		});
	}
	if (type == "settings") {
		lepopup_form_options = properties;
	} else if (type == "page" || type == "page-confirmation") {
		lepopup_form_pages[page_i] = properties;
		jQuery(".lepopup-pages-bar-item, .lepopup-pages-bar-item-confirmation").each(function(){
			var page_id = jQuery(this).attr("data-id");
			if (page_id == properties['id']) jQuery(this).find("label").text(properties['name']);
		});
	} else {
		lepopup_form_elements[i] = properties;
	}
	lepopup_form_changed = true;
}

function lepopup_properties_close() {
	if (lepopup_element_properties_data_changed) {
		lepopup_dialog_open({
			echo_html:		function() {
				this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Seems you didn't save changes. Are you sure, you want to close Properties?", "lepopup")+"</div>");
				this.show();
			},
			ok_label:		'Close Properties',
			ok_function:	function(e){
				_lepopup_properties_close();
				lepopup_dialog_close();
			}
		});
	} else _lepopup_properties_close();
	return false;
}
function _lepopup_properties_close() {
	lepopup_element_properties_data_changed = false;
	lepopup_element_properties_active = null;
	jQuery("#lepopup-element-properties-overlay").fadeOut(300);
	jQuery("#lepopup-element-properties").fadeOut(300, function() {
		jQuery(lepopup_element_properties_target+" .lepopup-color").minicolors("destroy");
		jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-content-form").html("");
		jQuery(lepopup_element_properties_target+" .lepopup-admin-popup-buttons .lepopup-admin-button").find("i").attr("class", "fas fa-check");
		lepopup_element_properties_target = null;
		jQuery("body").removeClass("lepopup-static");
	});
}
var lepopup_rebuild_active_element_timer = null;
function lepopup_properties_change() {
	if (lepopup_element_properties_active == null) return false;
	if (jQuery(lepopup_element_properties_active).hasClass("lepopup-element")) {
		clearTimeout(lepopup_rebuild_active_element_timer);
		lepopup_rebuild_active_element_timer = setTimeout(function(){
			lepopup_properties_populate();
			_lepopup_rebuild_active_element();
			jQuery(lepopup_element_properties_active).addClass("lepopup-element-selected");
		}, 1000);
	}
	lepopup_element_properties_data_changed = true;
	lepopup_properties_visible_conditions(lepopup_element_properties_active);
	return false;
}
function lepopup_properties_visible_conditions(_object) {
	var type = jQuery(_object).attr("data-type");
	var input;
	if (typeof type == undefined || type == "") return false;
	var visible, value = "";
	for (var key in lepopup_meta[type]) {
		if (lepopup_meta[type].hasOwnProperty(key)) {
			if (lepopup_meta[type][key].hasOwnProperty('visible')) {
				visible = false;
				for (var condition_key in lepopup_meta[type][key]['visible']) {
					if (lepopup_meta[type][key]['visible'].hasOwnProperty(condition_key)) {
						input = jQuery("[name='lepopup-"+condition_key+"']");
						if (input.length > 1) {
							jQuery(input).each(function(){
								if (jQuery(this).is(":checked")) {
									value = jQuery(this).val();
									return false;
								}
							});
						} else if (jQuery(input).is(":checked")) value = "on";
						else value = jQuery(input).val();
						if (Array.isArray(lepopup_meta[type][key]['visible'][condition_key])) {
							if (jQuery.inArray(value, lepopup_meta[type][key]['visible'][condition_key]) != -1) visible = true;
						} else if (value == lepopup_meta[type][key]['visible'][condition_key]) visible = true;
					}
				}
				if (visible) jQuery(".lepopup-properties-item[data-id='"+key+"']").fadeIn(300);
				else jQuery(".lepopup-properties-item[data-id='"+key+"']").fadeOut(300);
			}
		}
	}
}
function lepopup_properties_mask_preset_changed(_object) {
	var preset = jQuery(_object).val();
	var mask_object = jQuery(_object).closest(".lepopup-properties-content").find("input");
	if (preset == "custom") {
		jQuery(mask_object).removeAttr("readonly");
		jQuery(mask_object).focus();
	} else {
		jQuery(mask_object).val(preset);
		jQuery(mask_object).attr("readonly", "readonly");
	}
	return false;
}
function lepopup_properties_options_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-options-item").fadeOut(300, function(){
				jQuery(this).remove();
			});
			lepopup_properties_change();
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_options_copy(_object) {
	var option = jQuery(_object).closest(".lepopup-properties-options-item").clone();
	jQuery(option).removeClass("lepopup-properties-options-item-default");
	jQuery(_object).closest(".lepopup-properties-options-item").after(option);
	jQuery(option).find(".lepopup-image-url span").on("click", function(e){
		e.preventDefault();
		var input = jQuery(this).parent().children("input");
		var media_frame = wp.media({
			title: 'Select Image',
			library: {
				type: 'image'
			},
			multiple: false
		});
		media_frame.on("select", function() {
			var attachment = media_frame.state().get("selection").first();
			jQuery(input).val(attachment.attributes.url);
		});
		media_frame.open();
	});
	jQuery(".lepopup-properties-options-box input").off("input");
	jQuery(".lepopup-properties-options-box input").on("input", function(e){
		lepopup_properties_change();
		lepopup_element_properties_data_changed = true;
	});
	lepopup_properties_change();
	lepopup_element_properties_data_changed = true;
	return false;
}
function lepopup_properties_options_default(_object) {
	var multi = jQuery(_object).closest(".lepopup-properties-options-container").attr("data-multi");
	var option = jQuery(_object).closest(".lepopup-properties-options-item");
	if (jQuery(option).hasClass("lepopup-properties-options-item-default")) {
		jQuery(option).removeClass("lepopup-properties-options-item-default");
	} else {
		if (multi != "on") jQuery(_object).closest(".lepopup-properties-options-container").find(".lepopup-properties-options-item").removeClass("lepopup-properties-options-item-default");
		jQuery(option).addClass("lepopup-properties-options-item-default");
	}
	lepopup_properties_change();
	lepopup_element_properties_data_changed = true;
	return false;
}
function lepopup_properties_options_new(_object) {
	var option;
	if (_object != null) {
		option = jQuery(_object).closest(".lepopup-properties-options-item").clone();
		jQuery(option).removeClass("lepopup-properties-options-item-default");
		jQuery(option).find("input").val("");
		jQuery(_object).closest(".lepopup-properties-options-item").after(option);
	} else {
		//option = jQuery(".lepopup-properties-options-container .lepopup-properties-options-item").first().clone();
		//jQuery(option).removeClass("lepopup-properties-options-item-default");
		//jQuery(option).find("input").val("");
		if (jQuery(".lepopup-properties-options-container").closest(".lepopup-properties-content").hasClass("lepopup-properties-image-options-table")) {
			option = lepopup_properties_options_item_get("", "", "", false);
		} else {
			option = lepopup_properties_options_item_get(null, "", "", false);
		}
		jQuery(".lepopup-properties-options-container").append(option);
	}
	jQuery(option).find(".lepopup-image-url span").on("click", function(e){
		e.preventDefault();
		var input = jQuery(this).parent().children("input");
		var media_frame = wp.media({
			title: 'Select Image',
			library: {
				type: 'image'
			},
			multiple: false
		});
		media_frame.on("select", function() {
			var attachment = media_frame.state().get("selection").first();
			jQuery(input).val(attachment.attributes.url);
			lepopup_properties_change();
		});
		media_frame.open();
	});
	jQuery(".lepopup-properties-options-box input").off("input");
	jQuery(".lepopup-properties-options-box input").on("input", function(e){
		lepopup_properties_change();
		lepopup_element_properties_data_changed = true;
	});
	lepopup_properties_change();	
	lepopup_element_properties_data_changed = true;
	return false;
}
function lepopup_properties_options_item_get(_image, _label, _value, _selected) {
	var html, selected = "";
	if (_selected) selected = " lepopup-properties-options-item-default";
	html = "<div class='lepopup-properties-options-item"+selected+"'><div class='lepopup-properties-options-table'>"+(_image != null ? "<div class='lepopup-image-url lepopup-properties-options-table-image'><input class='lepopup-properties-options-image' type='text' value='"+lepopup_escape_html(_image)+"' placeholder='URL'><span><i class='far fa-image'></i></span></div>" : "")+"<div class='lepopup-properties-options-table-label'><input class='lepopup-properties-options-label' type='text' value='"+lepopup_escape_html(_label)+"' placeholder='Label'></div><div class='lepopup-properties-options-table-value'><input class='lepopup-properties-options-value' type='text' value='"+lepopup_escape_html(_value)+"' placeholder='Value'></div><div class='lepopup-properties-options-table-icons'><span onclick='return lepopup_properties_options_default(this);' title='Set the option as a default value'><i class='fas fa-check'></i></span><span onclick='return lepopup_properties_options_new(this);' title='Add the option after this one'><i class='fas fa-plus'></i></span><span onclick='return lepopup_properties_options_copy(this);' title='Duplicate the option'><i class='far fa-copy'></i></span><span onclick='return lepopup_properties_options_delete(this);' title='Delete the option'><i class='fas fa-trash-alt'></i></span><span title='Move the option'><i class='fas fa-arrows-alt lepopup-properties-options-item-handler'></i></span></div></div></div>";
	return html;
}
function lepopup_properties_imageselect_mode_set(_object) {
	var value = jQuery(_object).val();
	var options = jQuery(_object).closest(".lepopup-properties-item").parent().find(".lepopup-properties-options-container");
	if (value == 'radio') {
		jQuery(options).attr("data-multi", "off");
		var first_selected = jQuery(options).find(".lepopup-properties-options-item-default").first();
		jQuery(options).find(".lepopup-properties-options-item").removeClass("lepopup-properties-options-item-default");
		if (first_selected.length > 0) jQuery(first_selected).addClass("lepopup-properties-options-item-default");
	} else {
		jQuery(options).attr("data-multi", "on");
	}
}

function lepopup_properties_css_add(_type, _values) {
	var extra_class = "", html = "", tools = "";
	if (lepopup_meta[_type].hasOwnProperty("css")) {
		if (_values == null) { 
			extra_class = " lepopup-properties-sub-item-new";
			lepopup_element_properties_data_changed = true;
		} else extra_class = " lepopup-properties-sub-item-exist";
		html += "<div class='lepopup-properties-sub-item"+extra_class+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_css_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_css_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label></label></div><div class='lepopup-properties-sub-item-body'><div class='lepopup-properties-item'><div class='lepopup-properties-label'><label>Selector</label></div><div class='lepopup-properties-content'><select onchange='return lepopup_properties_css_selector_change(this);'><option value=''>Please select</option>";
		for (var key in lepopup_meta[_type]["css"]["selectors"]) {
			if (lepopup_meta[_type]["css"]["selectors"].hasOwnProperty(key)) {
				html += "<option value='"+key+"'>"+lepopup_meta[_type]["css"]["selectors"][key]['label']+"</option>"
			}
		}
		tools = "<div class='lepopup-properties-css-toolbar'><span onclick='return lepopup_properties_css_style_add(this);' data-css='background-color: ;' title='Background color'><i class='material-icons'>format_color_fill</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='background: url() top left no-repeat;' title='Background'><i class='material-icons'>wallpaper</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='border-color: ;' title='Border color'><i class='material-icons'>border_color</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='color: ;' title='Text color'><i class='material-icons'>format_color_text</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='padding: ;' title='Padding'><i class='fas fa-external-link-alt'></i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='margin: ;' title='Margin'><i class='fas fa-external-link-alt'></i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='border-radius: ;' title='Border radius'><i class='material-icons'>crop_free</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='font-size: ;' title='Font size'><i class='material-icons'>format_size</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='line-height: ;' title='Line height'><i class='material-icons'>format_line_spacing</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='font-weight: bold;' title='Bold'><i class='material-icons'>format_bold</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='text-decoration: underline;' title='Underline'><i class='material-icons'>format_underlined</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='text-transform: uppercase;' title='Uppercase'><i class='material-icons'>title</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='text-align: left;' title='Text align left'><i class='material-icons'>format_align_left</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='text-align: center;' title='Text align center'><i class='material-icons'>format_align_center</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='text-align: right;' title='Text align right'><i class='material-icons'>format_align_right</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='width: ;' title='Width'><i class='material-icons'>keyboard_tab</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='height: ;' title='Height'><i class='material-icons'>vertical_align_top</i></span><span onclick='return lepopup_properties_css_style_add(this);' data-css='display: none;' title='Hide'><i class='material-icons'>visibility_off</i></span></div>";
		html += "</select></div></div><div class='lepopup-properties-item'><div class='lepopup-properties-label'><label>CSS</label></div><div class='lepopup-properties-content'><textarea></textarea>"+tools+"</div></div></div></div>";
		if (_values == null) jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item-body").slideUp(300);
		jQuery(".lepopup-properties-content-css").append(html);
		if (_values != null) {
			jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item:last").find(".lepopup-properties-sub-item-body select").val(_values["selector"]);
			if (_values["selector"] == "") jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item:last").find(".lepopup-properties-sub-item-header label").html("");
			else jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item:last").find(".lepopup-properties-sub-item-header label").html(jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item:last").find(".lepopup-properties-sub-item-body select option:selected").text());
			jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item:last").find(".lepopup-properties-sub-item-body textarea").val(_values["css"]);
		} else {
			jQuery(".lepopup-properties-sub-item-new textarea").on("input", function(e){
				lepopup_properties_change();
			});
			jQuery(".lepopup-properties-sub-item-new select").on("change", function(e){
				lepopup_properties_change();
			});
		}
		jQuery(".lepopup-properties-sub-item-new").slideDown(300);
		jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	}
	return false;
}
function lepopup_properties_css_style_add(_object) {
	var value = jQuery(_object).closest(".lepopup-properties-content").find("textarea").val();
	if (value != "") value += "\r\n";
	value += jQuery(_object).attr("data-css");
	jQuery(_object).closest(".lepopup-properties-content").find("textarea").val(value);
	lepopup_properties_change();
	return false;
}
function lepopup_properties_css_selector_change(_object) {
	if (jQuery(_object).val() == "") jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header label").html("");
	else jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header label").html(jQuery(_object).find("option:selected").text());
	return false;
}
function lepopup_properties_css_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-css .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_css_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_properties_change();
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_validators_add(_field_id, _type, _validator, _values) {
	var extra_class = "", html = "", tooltip_html, selected, options, property_value;
	var seq = 0, last;
	last = jQuery(".lepopup-properties-content-validators .lepopup-properties-sub-item").last();
	if (jQuery(last).length) seq = parseInt(jQuery(last).attr("data-seq"), 10) + 1;
	if (lepopup_meta[_type].hasOwnProperty("validators") && lepopup_validators.hasOwnProperty(_validator)) {
		if (_values == null) { 
			extra_class = " lepopup-properties-sub-item-new";
			lepopup_element_properties_data_changed = true;
		} else extra_class = " lepopup-properties-sub-item-exist";
		html += "<div class='lepopup-properties-sub-item"+extra_class+"' data-type='"+_validator+"' data-seq='"+seq+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_validators_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_validators_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label>"+lepopup_validators[_validator]["label"]+"</label></div><div class='lepopup-properties-sub-item-body'>";
		for (var key in lepopup_validators[_validator]["properties"]) {
			if (lepopup_validators[_validator]["properties"].hasOwnProperty(key)) {
				tooltip_html = "";
				if (lepopup_validators[_validator]["properties"][key].hasOwnProperty('tooltip')) {
					tooltip_html = "<i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_validators[_validator]["properties"][key]['tooltip']+"</div>";
				}
				property_value = "";
				if (_values != null && _values.hasOwnProperty("properties") && _values["properties"].hasOwnProperty(key)) property_value = _values["properties"][key];
				switch(lepopup_validators[_validator]["properties"][key]['type']) {
					case 'error':
						html += "<hr /><div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label class='lepopup-red'>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input type='text' name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"' value='"+lepopup_escape_html(property_value)+"' placeholder='"+lepopup_escape_html(lepopup_validators[_validator]["properties"][key]['value'])+"' /><em>Default message: "+lepopup_escape_html(lepopup_validators[_validator]["properties"][key]['value'])+"</em></div></div>";
						break;

					case 'text':
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input type='text' name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"' value='"+lepopup_escape_html(property_value)+"' placeholder='"+lepopup_escape_html(property_value)+"' /></div></div>";
						break;

					case 'field':
						options = "<option value=''>---</option>";
						for (var i=0; i<lepopup_form_elements.length; i++) {
							if (lepopup_form_elements[i] == null) continue;
							if (lepopup_form_elements[i]["id"] == _field_id) continue;
							if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]['type']) && lepopup_toolbar_tools[lepopup_form_elements[i]['type']]['type'] == 'input') {
								options += "<option value='"+lepopup_form_elements[i]['id']+"'"+(lepopup_form_elements[i]['id'] == property_value ? " selected='selected'" : "")+">"+lepopup_form_elements[i]['id']+" | "+lepopup_escape_html(lepopup_form_elements[i]['name'])+"</option>";
							}
						}
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><select name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"'>"+options+"</select></div></div>";
						break;

					case 'textarea':
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><textarea name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"'>"+lepopup_escape_html(property_value)+"</textarea></div></div>";
						break;
						
					case 'integer':
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"' value='"+lepopup_escape_html(property_value)+"' placeholder='' /></div></div></div>";
						break;

					case 'checkbox':
						selected = "";
						if (property_value == "on") selected = " checked='checked'";
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_validators[_validator]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' name='lepopup-validators-"+key+"' id='lepopup-validators-"+seq+"-"+key+"'"+selected+"' /><label for='lepopup-validators-"+seq+"-"+key+"'></label></div></div>";
						break;

					default:
						break;
				}
			}
		}
		html += "</div></div>";
		if (_values == null) jQuery(".lepopup-properties-content-validators .lepopup-properties-sub-item-body").slideUp(300);
		jQuery(".lepopup-properties-content-validators").append(html);
		jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
			contentAsHTML:	true,
			maxWidth:		360,
			theme:			"tooltipster-dark",
			side:			"bottom",
			content:		"Default",
			functionFormat: function(instance, helper, content){
				return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
			}
		});
		jQuery(".lepopup-properties-sub-item-new").slideDown(300);
		jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	}
	return false;
}
function lepopup_properties_validators_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-validators .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_validators_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}

function lepopup_properties_integrations_constantcontact_apikey_changed(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").find("input[name=token]").val("");
	var token_link = jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-constantcontact-token-link");
	jQuery(token_link).attr("href", jQuery(token_link).attr("data-href").replace("{api-key}", jQuery(_object).closest(".lepopup-properties-item").find("input").val()));
}
function lepopup_properties_integrations_name_changed(_object) {
	var label = jQuery(_object).val().substring(0,52)+(jQuery(_object).val().length > 52 ? "..." : "");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header>label").text(label);
	return false;
}
function lepopup_properties_integrations_logic_enable_changed(_object) {
	var parent = jQuery(_object).closest(".lepopup-properties-sub-item");
	if (jQuery(_object).is(":checked")) jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeIn(300);
	else jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeOut(300);
	return false;
}
function lepopup_integrations_ajax_options_selected(_object) {
	var item_id = jQuery(_object).attr("data-id");
	var item_title = jQuery(_object).attr("data-title");
	jQuery(_object).closest(".lepopup-integrations-ajax-options").find("input[type='text']").val(item_title);
	jQuery(_object).closest(".lepopup-integrations-ajax-options").find("input[type='hidden']").val(item_id);
	return false;
}
function lepopup_integrations_custom_add(_object) {
	var template = jQuery(_object).closest("table").find(".lepopup-integrations-custom-template");
	if (jQuery(template).length > 0) {
		jQuery(template).before("<tr>"+jQuery(template).html()+"</tr>");
	}
}
function lepopup_integrations_ajax_options_focus(_object) {
	var item = jQuery(_object).closest(".lepopup-properties-sub-item");
	var provider = jQuery(item).find("input[name='lepopup-integrations-provider']").val();
	var field = jQuery(_object).attr("name");
	var deps = {};
	if (jQuery(_object).attr("data-deps")) {
		var deps_array = jQuery(_object).attr("data-deps").split(",");
		for (var i=0; i<deps_array.length; i++) {
			if (jQuery(item).find("input[name='"+deps_array[i]+"']").is(":checked")) deps[deps_array[i]] = 'on';
			else deps[deps_array[i]] = jQuery(item).find("input[name='"+deps_array[i]+"'], select[name='"+deps_array[i]+"']").val();
		}
	}
	var post_data = {
		action: 	"lepopup-"+provider+"-"+field, 
		deps:		lepopup_encode64(JSON.stringify(deps))
	};
	if (jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list").length == 0) {
		jQuery(_object).parent().append("<div class='lepopup-integrations-ajax-options-list'><div class='lepopup-integrations-ajax-options-list-data'></div><i class='fas fa-spin fa-spinner'></i></div>");
	}
	jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").show();
	jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").hide();
	jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list").fadeIn(300);
	var default_error = jQuery(_object).attr("data-default-error");
	if (typeof default_error === typeof undefined || default_error === false) default_error = 'Unexpected server response.';
	
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					var items_html = "";
					for (var key in data.items) {
						if (data.items.hasOwnProperty(key)) {
							var title = lepopup_escape_html(key) + (data.items[key] == "" ? "" : " | " + lepopup_escape_html(data.items[key]));
							items_html += "<a href='#' data-id='"+lepopup_escape_html(key)+"' data-title='"+title+"' onclick='return lepopup_integrations_ajax_options_selected(this);'>"+title+"</a>";
						}
					}
					if (Object.keys(data.items).length > 4) jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list").addClass("lepopup-vertical-scroll");
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").html(items_html);
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").hide();
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").show();
				} else if (data.status == "ERROR") {
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").html('<div class="lepopup-integrations-ajax-options-list-data-error">'+data.message+'</div>');
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").hide();
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").show();
				} else {
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").html("<div class='lepopup-integrations-ajax-options-list-data-error'>"+default_error+"</div>");
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").hide();
					jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").show();
				}
			} catch(error) {
				jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").html("<div class='lepopup-integrations-ajax-options-list-data-error'>"+default_error+"</div>");
				jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").hide();
				jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").show();
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").html("<div class='lepopup-integrations-ajax-options-list-data-error'>"+default_error+"</div>");
			jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list i").hide();
			jQuery(_object).parent().find(".lepopup-integrations-ajax-options-list-data").show();
		}
	});
}
function lepopup_integrations_ajax_multiselect_scroll(_object) {
	if (jQuery(_object).attr("data-next-offset") == "-1") return;
	var content_height = jQuery(_object).prop('scrollHeight');
	var position = jQuery(_object).scrollTop();
	var height = jQuery(_object).height();
	if (content_height - height - position < 20) {
		if (lepopup_sending) return false;
		lepopup_sending = true;
		var item = jQuery(_object).closest(".lepopup-properties-sub-item");
		var provider = jQuery(item).find("input[name='lepopup-integrations-provider']").val();
		var sub_action = jQuery(_object).attr("data-action");
		var deps = {"offset" :	parseInt(jQuery(_object).attr("data-next-offset"), 10)};
		if (jQuery(_object).attr("data-deps")) {
			var deps_array = jQuery(_object).attr("data-deps").split(",");
			for (var i=0; i<deps_array.length; i++) {
				deps[deps_array[i]] = jQuery(item).find("input[name='"+deps_array[i]+"'], select[name='"+deps_array[i]+"']").val();
			}
		}
		var post_data = {
			"action" :	"lepopup-"+provider+"-"+sub_action,
			"deps":		lepopup_encode64(JSON.stringify(deps))
		};
		jQuery(_object).find(".lepopup-integrations-ajax-multiselect-loading").slideDown(300);
		jQuery.ajax({
			type	: "POST",
			url		: lepopup_ajax_handler, 
			data	: post_data,
			success	: function(return_data) {
				jQuery(_object).find(".lepopup-integrations-ajax-multiselect-loading").slideUp(300)
				var data;
				try {
					if (typeof return_data == "object") data = return_data;
					else data = jQuery.parseJSON(return_data);
					if (data.status == "OK") {
						jQuery(_object).find(".lepopup-integrations-ajax-multiselect-loading").before(data.html);
						jQuery(_object).attr("data-next-offset", data.offset);
					} else if (data.status == "ERROR") {
						jQuery(_object).attr("data-next-offset", "-1");
						lepopup_global_message_show("danger", data.message);
					} else {
						jQuery(_object).attr("data-next-offset", "-1");
						lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
					}
				} catch(error) {
					jQuery(_object).attr("data-next-offset", "-1");
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
				lepopup_sending = false;
			},
			error	: function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery(_object).find(".lepopup-integrations-ajax-multiselect-loading").slideUp(300)
				jQuery(_object).attr("data-next-offset", "-1");
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				lepopup_sending = false;
			}
		});
	}
}

function lepopup_integrations_ajax_inline_html(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var item = jQuery(_object).closest(".lepopup-properties-sub-item");
	var provider = jQuery(item).find("input[name='lepopup-integrations-provider']").val();
	var inline_action = jQuery(_object).attr("data-inline");
	var deps = {};

	if (jQuery(_object).attr("data-deps")) {
		var deps_array = jQuery(_object).attr("data-deps").split(",");
		for (var i=0; i<deps_array.length; i++) {
			if (jQuery(item).find("input[name='"+deps_array[i]+"']").is(":checked")) deps[deps_array[i]] = 'on';
			else deps[deps_array[i]] = jQuery(item).find("input[name='"+deps_array[i]+"'], select[name='"+deps_array[i]+"']").val();
		}
	}
	
	var post_data = {
		action: 	"lepopup-"+provider+"-"+inline_action, 
		deps:		lepopup_encode64(JSON.stringify(deps))
	};
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-button-disabled");
	jQuery(_object).parent().find(".lepopup-integrations-ajax-inline").slideUp(300);

	var default_error = jQuery(_object).attr("data-default-error");
	if (typeof default_error === typeof undefined || default_error === false) default_error = lepopup_esc_html__("Something went wrong. We got unexpected server response.");
	
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).find("i").attr("class", "fas fa-download");
			jQuery(_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).parent().find(".lepopup-integrations-ajax-inline").html(data.html);
					jQuery(_object).parent().find(".lepopup-integrations-ajax-inline").slideDown(300);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", default_error);
				}
			} catch(error) {
				lepopup_global_message_show("danger", default_error);
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-download");
			jQuery(_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", default_error);
			lepopup_sending = false;
		}
	});
}
/*function lepopup_integrations_field_add(_object) {
	var template_class = jQuery(_object).attr("data-template");
	var template_object = jQuery(_object).parent().find("."+template_class);
	if (template_object.length > 0) {
		jQuery(template_object).before("<tr>"+jQuery(template_object).html()+"</tr>");
	}
	return false;
}
function lepopup_integrations_field_remove(_object) {
	var row = jQuery(_object).closest("tr");
	jQuery(row).fadeOut(300, function() {
		jQuery(row).remove();
	});
	return false;
}*/
function lepopup_properties_integrations_details_toggle(_object) {
	if (typeof _object == "undefined") return;
	var item = jQuery(_object).closest(".lepopup-properties-sub-item");
	jQuery(item).addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-integrations .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(item).removeClass("lepopup-freeze");
	jQuery(item).find(".lepopup-properties-sub-item-body").slideToggle(300);
	if (jQuery(item).attr("data-loaded") != "on") {
		var provider = jQuery(item).find("input[name='lepopup-integrations-provider']").val();
		if (lepopup_sending) return false;
		lepopup_sending = true;
		var post_data = {
			action:		"lepopup-"+provider+"-settings-html"
		};
		var idx = jQuery(item).find("input[name='lepopup-integrations-idx']").val();
		if (idx >= 0 && idx <= lepopup_form_options["integrations"].length) {
			post_data["data"] = lepopup_encode64(JSON.stringify(lepopup_form_options["integrations"][idx]["data"]));
		}
		jQuery.ajax({
			type	: "POST",
			url		: lepopup_ajax_handler, 
			data	: post_data,
			success	: function(return_data) {
				var data;
				try {
					if (typeof return_data == 'object') data = return_data;
					else data = jQuery.parseJSON(return_data);
					if (data.status == "OK") {
						jQuery(item).attr("data-loaded", "on");
						jQuery(item).find(".lepopup-integrations-content").html(data.html);
						jQuery(item).find(".lepopup-integrations-content .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
							contentAsHTML:	true,
							maxWidth:		360,
							theme:			"tooltipster-dark",
							side:			"bottom",
							content:		"Default",
							functionFormat: function(instance, helper, content){
								return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
							}
						});
						jQuery(item).find(".lepopup-integrations-ajax-options input[type='text']").on("focus", function(){
							lepopup_integrations_ajax_options_focus(this);
						});
						jQuery(item).find(".lepopup-integrations-ajax-options input[type='text']").on("blur", function(){
							jQuery(this).parent().find(".lepopup-integrations-ajax-options-list").fadeOut(300);
						});
						jQuery(item).find(".lepopup-properties-sub-item-body-loading").hide();
						jQuery(item).find(".lepopup-properties-sub-item-body-content").slideDown(300);
					} else if (data.status == "ERROR") {
						jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
						lepopup_global_message_show("danger", data.message);
					} else {
						jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
						lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
					}
				} catch(error) {
					jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
				lepopup_sending = false;
			},
			error	: function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				lepopup_sending = false;
			}
		});
	}
	return false;
}
function lepopup_properties_integrations_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_integrations_add(_values, _idx, _provider) {
	var extra_class = "", html = "", temp = "", property_value, enabled, logic_enable, logic_enable_id, provider = "", label = "";

	if (typeof _provider != "undefined") {
		provider = _provider;
		label = (lepopup_integration_providers.hasOwnProperty(provider) ? lepopup_integration_providers[provider] : 'Integration');
	} else if (typeof _values == "object") {
		provider = _values["provider"];
		label = _values["name"];
	}
	
	if (_values == null) { 
		extra_class = " lepopup-properties-sub-item-new";
		lepopup_element_properties_data_changed = true;
	} else extra_class = " lepopup-properties-sub-item-exist";
	html += "<div class='lepopup-properties-sub-item"+extra_class+"' data-loaded='off'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_integrations_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_integrations_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label></label></div><div class='lepopup-properties-sub-item-body' style='display: none;'><div class='lepopup-properties-sub-item-body-content' style='display: none;'>";

	html += "<div class='lepopup-properties-item' data-id='name'><div class='lepopup-properties-label'><label>"+lepopup_integrations['name']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_integrations['name']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-integrations-name' value='"+lepopup_escape_html(label)+"' oninput='return lepopup_properties_integrations_name_changed(this);' /></div></div>";
	
	if (_values != null && _values.hasOwnProperty('enabled')) enabled = _values['enabled'];
	else enabled = lepopup_integrations['enabled']['value'];
	var enabled_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='enabled'><div class='lepopup-properties-label'><label>"+lepopup_integrations['enabled']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_integrations['enabled']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-integrations-enabled-"+enabled_id+"' name='lepopup-integrations-enabled'"+(enabled == "on" ? ' checked="checked"' : '')+"' /><label for='lepopup-integrations-enabled-"+enabled_id+"'></label></div></div>";

	if (_values != null && _values.hasOwnProperty('action')) property_value = _values['action'];
	else property_value = lepopup_integrations['action']['value'];
	var options = "";
	for (var option_key in lepopup_integrations['action']['options']) {
		if (lepopup_integrations['action']['options'].hasOwnProperty(option_key)) {
			options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_integrations['action']['options'][option_key])+"</option>";
		}
	}
	html += "<div class='lepopup-properties-item' data-id='action'><div class='lepopup-properties-label'><label>"+lepopup_integrations['action']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_integrations['action']['tooltip']+"</div></div><div class='lepopup-properties-content'><select name='lepopup-integrations-action'>"+options+"</select></div></div>";
	
	html += "<input type='hidden' name='lepopup-integrations-idx' value='"+_idx+"' /><input type='hidden' name='lepopup-integrations-provider' value='"+lepopup_escape_html(provider)+"' /><div class='lepopup-integrations-content'></div>";
	
	if (_values != null && _values.hasOwnProperty('logic-enable')) logic_enable = _values['logic-enable'];
	else logic_enable = lepopup_integrations['logic-enable']['value'];
	logic_enable_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='logic-enable'><div class='lepopup-properties-label'><label>"+lepopup_integrations['logic-enable']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_integrations['logic-enable']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-integrations-logic-enable-"+logic_enable_id+"' name='lepopup-integrations-logic-enable'"+(logic_enable == "on" ? ' checked="checked"' : '')+" onchange='return lepopup_properties_integrations_logic_enable_changed(this);' /><label for='lepopup-integrations-logic-enable-"+logic_enable_id+"'></label></div></div>";
	
	if (_values != null && _values.hasOwnProperty('logic')) property_value = _values['logic'];
	else property_value = lepopup_integrations['logic']['value'];
	var input_ids = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]['type']) && lepopup_toolbar_tools[lepopup_form_elements[i]['type']]['type'] == 'input') {
			input_ids.push(lepopup_form_elements[i]["id"]);
		}
	}
	if (input_ids.length > 0) {
		temp = "<div class='lepopup-properties-group lepopup-properties-logic-header'>";
		options = "";
		for (var option_key in lepopup_integrations['logic']['actions']) {
			if (lepopup_integrations['logic']['actions'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["action"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_integrations['logic']['actions'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-integrations-logic-action' id='lepopup-logic-action'>"+options+"</select></div>";
		options = "";
		for (var option_key in lepopup_integrations['logic']['operators']) {
			if (lepopup_integrations['logic']['operators'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["operator"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_integrations['logic']['operators'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-integrations-logic-operator' id='lepopup-logic-operator'>"+options+"</select></div>";
		temp += "</div>";
		options = "";
		for (var j=0; j<property_value["rules"].length; j++) {
			if (input_ids.indexOf(parseInt(property_value["rules"][j]["field"], 10)) != -1) {
				options += lepopup_properties_logic_rule_get(null, property_value["rules"][j]["field"], property_value["rules"][j]["rule"], property_value["rules"][j]["token"]);
			}
		}
		temp += "<div class='lepopup-properties-logic-rules'>"+options+"</div><div class='lepopup-properties-logic-buttons'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_logic_rule_new(this, null);'><i class='fas fa-plus'></i><label>Add rule</label></a></div>";
	} else {
		temp = "<div class='lepopup-properties-inline-error'>There are no elements available to use for logic rules.</div>";
	}
	html += "<div class='lepopup-properties-item' data-id='logic'"+(logic_enable == "on" ? "" : " style='display:none;'")+"><div class='lepopup-properties-label'><label>"+lepopup_integrations['logic']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_integrations['logic']['tooltip']+"</div></div><div class='lepopup-properties-content'>"+temp+"</div></div>";
	html += "</div><div class='lepopup-properties-sub-item-body-loading'><i class='fas fa-spin fa-spinner'></i></div></div></div>";
	
	if (_values == null) jQuery(".lepopup-properties-content-integrations .lepopup-properties-sub-item-body").slideUp(300);
	jQuery(".lepopup-properties-content-integrations").append(html);
	jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	lepopup_properties_integrations_name_changed(jQuery(".lepopup-properties-content-integrations .lepopup-properties-sub-item").last().find("[name='lepopup-integrations-name']"));
	if (jQuery(".lepopup-properties-sub-item-new").length > 0) lepopup_properties_integrations_details_toggle(jQuery(".lepopup-properties-sub-item-new").find(".lepopup-properties-sub-item-header-tools"));
	jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	return false;
}
function lepopup_integrations_zapier_connect(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var item = jQuery(_object).closest(".lepopup-properties-sub-item");
	var content = jQuery(item).find(".lepopup-integrations-custom");
	var deps = {};

	var fields = new Array();

	var name;
	var names = jQuery(content).find("input.lepopup-integrations-custom-name");
	for (var j=0; j<names.length; j++) {
		name = jQuery(names[j]).val();
		if (name.length > 0) {
			fields.push(name);
		}
	}
	var post_data = {
		"action": 		"lepopup-zapier-connect",
		"webhook-url":	lepopup_encode64(jQuery(item).find("[name='webhook-url']").val()),
		"fields":		lepopup_encode64(JSON.stringify(fields))
	};
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-button-disabled");
	jQuery(_object).parent().find(".lepopup-integrations-ajax-inline").slideUp(300);
	
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).find("i").attr("class", "fas fa-download");
			jQuery(_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-download");
			jQuery(_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});

}

function lepopup_properties_payment_gateways_details_toggle(_object) {
	if (typeof _object == "undefined") return;
	var item = jQuery(_object).closest(".lepopup-properties-sub-item");
	jQuery(item).addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-payment-gateways .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(item).removeClass("lepopup-freeze");
	jQuery(item).find(".lepopup-properties-sub-item-body").slideToggle(300);
	if (jQuery(item).attr("data-loaded") != "on") {
		var provider = jQuery(item).find("input[name='lepopup-payment-gateways-provider']").val();
		if (lepopup_sending) return false;
		lepopup_sending = true;
		var post_data = {
			action:		"lepopup-"+provider+"-settings-html"
		};
		var idx = jQuery(item).find("input[name='lepopup-payment-gateways-idx']").val();
		if (idx >= 0 && idx <= lepopup_form_options["payment-gateways"].length) {
			post_data["data"] = lepopup_encode64(JSON.stringify(lepopup_form_options["payment-gateways"][idx]["data"]));
		}
		jQuery.ajax({
			type	: "POST",
			url		: lepopup_ajax_handler, 
			data	: post_data,
			success	: function(return_data) {
				var data;
				try {
					if (typeof return_data == 'object') data = return_data;
					else data = jQuery.parseJSON(return_data);
					if (data.status == "OK") {
						jQuery(item).attr("data-loaded", "on");
						jQuery(item).find(".lepopup-payment-gateways-content").html(data.html);
						jQuery(item).find(".lepopup-payment-gateways-content .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
							contentAsHTML:	true,
							maxWidth:		360,
							theme:			"tooltipster-dark",
							side:			"bottom",
							content:		"Default",
							functionFormat: function(instance, helper, content){
								return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
							}
						});
						jQuery(item).find(".lepopup-properties-sub-item-body-loading").hide();
						jQuery(item).find(".lepopup-properties-sub-item-body-content").slideDown(300);
					} else if (data.status == "ERROR") {
						jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
						lepopup_global_message_show("danger", data.message);
					} else {
						jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
						lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
					}
				} catch(error) {
					jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
				lepopup_sending = false;
			},
			error	: function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery(item).find(".lepopup-properties-sub-item-body").slideUp(300);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				lepopup_sending = false;
			}
		});
	}
	return false;
}
function lepopup_properties_payment_gateways_name_changed(_object) {
	var label = jQuery(_object).val().substring(0,52)+(jQuery(_object).val().length > 52 ? "..." : "");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header>label").text(label);
	lepopup_properties_payment_gateways_select_update();
	return false;
}
function lepopup_properties_payment_gateways_select_update() {
	var payment_gateways = new Array();
	jQuery(".lepopup-properties-content-payment-gateways .lepopup-properties-sub-item").each(function() {
		payment_gateways.push({"id" : jQuery(this).find("[name='lepopup-payment-gateways-id']").val(), "name" : jQuery(this).find("[name='lepopup-payment-gateways-name']").val()});
	});
	jQuery(".lepopup-payment-gateways-select").each(function(){
		var value = jQuery(this).val();
		var options = "<option value=''"+(value == "" ? " selected='selected'" : "")+">Select payment gateway</option>";
		for (var i=0; i<payment_gateways.length; i++) {
			options += "<option value='"+lepopup_escape_html(payment_gateways[i]['id'])+"'"+(value == payment_gateways[i]['id'] ? " selected='selected'" : "")+">"+lepopup_escape_html(payment_gateways[i]['name'])+"</option>";
		}
		jQuery(this).html(options);
	});
}
function lepopup_properties_payment_gateways_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
				lepopup_properties_payment_gateways_select_update();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_payment_gateways_add(_values, _idx, _provider) {
	var extra_class = "", html = "", property_value, enabled, provider = "", label = "";

	if (typeof _provider != "undefined") {
		provider = _provider;
		label = (lepopup_payment_providers.hasOwnProperty(provider) ? lepopup_payment_providers[provider] : 'Payment Gateway');
	} else if (typeof _values == "object") {
		provider = _values["provider"];
		label = _values["name"];
	}

	var label_beauty = label.substring(0,52)+(label.length > 52 ? "..." : "");
	
	if (_values == null) { 
		extra_class = " lepopup-properties-sub-item-new";
		lepopup_element_properties_data_changed = true;
	} else extra_class = " lepopup-properties-sub-item-exist";
	html += "<div class='lepopup-properties-sub-item"+extra_class+"' data-loaded='off'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_payment_gateways_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_payment_gateways_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label>"+lepopup_escape_html(label_beauty)+"</label></div><div class='lepopup-properties-sub-item-body' style='display: none;'><div class='lepopup-properties-sub-item-body-content' style='display: none;'>";

	html += "<div class='lepopup-properties-item' data-id='name'><div class='lepopup-properties-label'><label>"+lepopup_payment_gateway['name']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_payment_gateway['name']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-payment-gateways-name' value='"+lepopup_escape_html(label)+"' oninput='return lepopup_properties_payment_gateways_name_changed(this);' /></div></div>";
	if (_values != null && _values.hasOwnProperty('id')) property_value = _values['id'];
	else {
		lepopup_form_last_id++;
		property_value = lepopup_form_last_id;
	}
	html += "<input type='hidden' name='lepopup-payment-gateways-id' value='"+property_value+"' /><input type='hidden' name='lepopup-payment-gateways-idx' value='"+_idx+"' /><input type='hidden' name='lepopup-payment-gateways-provider' value='"+lepopup_escape_html(provider)+"' /><div class='lepopup-payment-gateways-content'></div>";
	
	html += "</div><div class='lepopup-properties-sub-item-body-loading'><i class='fas fa-spin fa-spinner'></i></div></div></div>";
	
	if (_values == null) jQuery(".lepopup-properties-content-payment-gateways .lepopup-properties-sub-item-body").slideUp(300);
	jQuery(".lepopup-properties-content-payment-gateways").append(html);
	
	jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	if (_values == null) lepopup_properties_payment_gateways_select_update();

	if (jQuery(".lepopup-properties-sub-item-new").length > 0) lepopup_properties_payment_gateways_details_toggle(jQuery(".lepopup-properties-sub-item-new").find(".lepopup-properties-sub-item-header-tools"));
	jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	return false;
}

function lepopup_properties_notifications_name_changed(_object) {
	var label = jQuery(_object).val().substring(0,52)+(jQuery(_object).val().length > 52 ? "..." : "");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header>label").text(label);
	return false;
}
function lepopup_properties_notifications_logic_enable_changed(_object) {
	var parent = jQuery(_object).closest(".lepopup-properties-sub-item");
	if (jQuery(_object).is(":checked")) jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeIn(300);
	else jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeOut(300);
	return false;
}
function lepopup_properties_notifications_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-notifications .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_notifications_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_notifications_add(_values) {
	var extra_class = "", html = "", temp = "", tooltip_html, selected, property_value, enabled, logic_enable, logic_enable_id;

	var input_ids = new Array();
	var file_ids = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]['type']) && lepopup_toolbar_tools[lepopup_form_elements[i]['type']]['type'] == 'input') {
			input_ids.push(lepopup_form_elements[i]["id"]);
			if (lepopup_form_elements[i]['type'] == 'file') {
				file_ids.push(lepopup_form_elements[i]["id"]);
			}
		}
	}
	
	if (_values == null) { 
		extra_class = " lepopup-properties-sub-item-new";
		lepopup_element_properties_data_changed = true;
	} else extra_class = " lepopup-properties-sub-item-exist";
	html += "<div class='lepopup-properties-sub-item"+extra_class+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_notifications_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_notifications_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label></label></div><div class='lepopup-properties-sub-item-body'>";

	html += "<div class='lepopup-properties-item' data-id='name'><div class='lepopup-properties-label'><label>"+lepopup_notifications['name']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['name']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-notifications-name' value='"+(_values != null && _values.hasOwnProperty('name') ? lepopup_escape_html(_values['name']) : lepopup_escape_html(lepopup_notifications['name']['value']))+"' oninput='return lepopup_properties_notifications_name_changed(this);' /></div></div>";
	
	if (_values != null && _values.hasOwnProperty('enabled')) enabled = _values['enabled'];
	else enabled = lepopup_notifications['enabled']['value'];
	var enabled_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='enabled'><div class='lepopup-properties-label'><label>"+lepopup_notifications['enabled']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['enabled']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-notifications-enabled-"+enabled_id+"' name='lepopup-notifications-enabled'"+(enabled == "on" ? ' checked="checked"' : '')+"' /><label for='lepopup-notifications-enabled-"+enabled_id+"'></label></div></div>";

	if (_values != null && _values.hasOwnProperty('action')) property_value = _values['action'];
	else property_value = lepopup_notifications['action']['value'];
	var options = "";
	for (var option_key in lepopup_notifications['action']['options']) {
		if (lepopup_notifications['action']['options'].hasOwnProperty(option_key)) {
			options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_notifications['action']['options'][option_key])+"</option>";
		}
	}
	html += "<div class='lepopup-properties-item' data-id='action'><div class='lepopup-properties-label'><label>"+lepopup_notifications['action']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['action']['tooltip']+"</div></div><div class='lepopup-properties-content'><select name='lepopup-notifications-action'>"+options+"</select></div></div>";
	
	html += "<div class='lepopup-properties-item' data-id='recipient-email'><div class='lepopup-properties-label'><label>"+lepopup_notifications['recipient-email']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['recipient-email']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-notifications-recipient-email' value='"+(_values != null && _values.hasOwnProperty('recipient-email') ? lepopup_escape_html(_values['recipient-email']) : lepopup_escape_html(lepopup_notifications['recipient-email']['value']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
	html += "<div class='lepopup-properties-item' data-id='subject'><div class='lepopup-properties-label'><label>"+lepopup_notifications['subject']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['subject']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-notifications-subject' value='"+(_values != null && _values.hasOwnProperty('subject') ? lepopup_escape_html(_values['subject']) : lepopup_escape_html(lepopup_notifications['subject']['value']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
	var message_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='message'><div class='lepopup-properties-label'><label>"+lepopup_notifications['message']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['message']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-textarea-shortcode-selector'><textarea name='lepopup-notifications-message' id='lepopup-notifications-message-"+message_id+"'>"+(_values != null && _values.hasOwnProperty('message') ? lepopup_escape_html(_values['message']) : lepopup_escape_html(lepopup_notifications['message']['value']))+"</textarea><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span class='lepopup-shortcode-selector-button'><i class='fas fa-code'></i></span></div></div></div></div>";

	if (_values != null && _values.hasOwnProperty('attachments')) property_value = _values['attachments'];
	else property_value = lepopup_notifications['attachments']['value'];
	options = "";
	for (var j=0; j<property_value.length; j++) {
		options += lepopup_properties_attachment_get(property_value[j]["source"], property_value[j]["token"]);
	}
	html += "<div class='lepopup-properties-item' data-id='attachments'><div class='lepopup-properties-label'><label>"+lepopup_notifications['attachments']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['attachments']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-attachments'>"+options+"</div><div class='lepopup-properties-attachment-buttons'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_attachment_new(this);'><i class='fas fa-plus'></i><label>Add file</label></a></div></div></div>";

	html += "<div class='lepopup-properties-item' data-id='reply-email'><div class='lepopup-properties-label'><label>"+lepopup_notifications['reply-email']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['reply-email']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-notifications-reply-email' value='"+(_values != null && _values.hasOwnProperty('reply-email') ? lepopup_escape_html(_values['reply-email']) : lepopup_escape_html(lepopup_notifications['reply-email']['value']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
	html += "<div class='lepopup-properties-item' data-id='from'><div class='lepopup-properties-label'><label>"+lepopup_notifications['from']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['from']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group'><div class='lepopup-properties-content-half lepopup-input-shortcode-selector'><input type='text' name='lepopup-notifications-from-email' value='"+(_values != null && _values.hasOwnProperty('from-email') ? lepopup_escape_html(_values['from-email']) : lepopup_escape_html(lepopup_notifications['from']['value']['email']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div><div class='lepopup-properties-content-half lepopup-input-shortcode-selector'><input type='text' name='lepopup-notifications-from-name' value='"+(_values != null && _values.hasOwnProperty('from-name') ? lepopup_escape_html(_values['from-name']) : lepopup_escape_html(lepopup_notifications['from']['value']['name']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div></div>";

	if (_values != null && _values.hasOwnProperty('logic-enable')) logic_enable = _values['logic-enable'];
	else logic_enable = lepopup_notifications['logic-enable']['value'];
	logic_enable_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='logic-enable'><div class='lepopup-properties-label'><label>"+lepopup_notifications['logic-enable']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['logic-enable']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-notifications-logic-enable-"+logic_enable_id+"' name='lepopup-notifications-logic-enable'"+(logic_enable == "on" ? ' checked="checked"' : '')+"' onchange='return lepopup_properties_notifications_logic_enable_changed(this);' /><label for='lepopup-notifications-logic-enable-"+logic_enable_id+"'></label></div></div>";

	if (_values != null && _values.hasOwnProperty('logic')) property_value = _values['logic'];
	else property_value = lepopup_notifications['logic']['value'];
	if (input_ids.length > 0) {
		temp = "<div class='lepopup-properties-group lepopup-properties-logic-header'>";
		options = "";
		for (var option_key in lepopup_notifications['logic']['actions']) {
			if (lepopup_notifications['logic']['actions'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["action"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_notifications['logic']['actions'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-notifications-logic-action' id='lepopup-logic-action'>"+options+"</select></div>";
		options = "";
		for (var option_key in lepopup_notifications['logic']['operators']) {
			if (lepopup_notifications['logic']['operators'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["operator"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_notifications['logic']['operators'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-notifications-logic-operator' id='lepopup-logic-operator'>"+options+"</select></div>";
		temp += "</div>";
		options = "";
		for (var j=0; j<property_value["rules"].length; j++) {
			if (input_ids.indexOf(parseInt(property_value["rules"][j]["field"], 10)) != -1) {
				options += lepopup_properties_logic_rule_get(null, property_value["rules"][j]["field"], property_value["rules"][j]["rule"], property_value["rules"][j]["token"]);
			}
		}
		temp += "<div class='lepopup-properties-logic-rules'>"+options+"</div><div class='lepopup-properties-logic-buttons'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_logic_rule_new(this, null);'><i class='fas fa-plus'></i><label>Add rule</label></a></div>";
	} else {
		temp = "<div class='lepopup-properties-inline-error'>There are no elements available to use for logic rules.</div>";
	}
	html += "<div class='lepopup-properties-item' data-id='logic'"+(logic_enable == "on" ? "" : " style='display:none;'")+"><div class='lepopup-properties-label'><label>"+lepopup_notifications['logic']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_notifications['logic']['tooltip']+"</div></div><div class='lepopup-properties-content'>"+temp+"</div></div>";
	html += "</div></div>";
	
	if (_values == null) jQuery(".lepopup-properties-content-notifications .lepopup-properties-sub-item-body").slideUp(300);
	jQuery(".lepopup-properties-content-notifications").append(html);
	
	jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	
	lepopup_properties_notifications_name_changed(jQuery(".lepopup-properties-content-notifications .lepopup-properties-sub-item").last().find("[name='lepopup-notifications-name']"));
	jQuery(".lepopup-properties-sub-item-new").slideDown(300);
	jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	return false;
}

function lepopup_properties_math_name_changed(_object) {
	var label = jQuery(_object).val().substring(0,52)+(jQuery(_object).val().length > 52 ? "..." : "");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header>label").text(label);
	return false;
}
function lepopup_properties_math_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-math-expressions .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_math_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
				jQuery(".lepopup-shortcode-selector-list-input").remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_math_add(_values) {
	var extra_class = "", html = "", tooltip_html, property_value;

	if (_values == null) { 
		extra_class = " lepopup-properties-sub-item-new";
		lepopup_element_properties_data_changed = true;
	} else extra_class = " lepopup-properties-sub-item-exist";
	html += "<div class='lepopup-properties-sub-item"+extra_class+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_math_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_math_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label></label></div><div class='lepopup-properties-sub-item-body'>";

	if (_values != null && _values.hasOwnProperty('id')) property_value = _values['id'];
	else {
		lepopup_form_last_id++;
		property_value = lepopup_form_last_id;
	}
	html += "<div class='lepopup-properties-item' data-id='id'><div class='lepopup-properties-label'><label>"+lepopup_math_expressions_meta['id']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_math_expressions_meta['id']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-math-id' value='"+property_value+"' readonly='readonly' onclick='this.focus();this.select();' /></div></div></div>";
	html += "<div class='lepopup-properties-item' data-id='name'><div class='lepopup-properties-label'><label>"+lepopup_math_expressions_meta['name']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_math_expressions_meta['name']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-math-name' value='"+(_values != null && _values.hasOwnProperty('name') ? lepopup_escape_html(_values['name']) : lepopup_escape_html(lepopup_math_expressions_meta['name']['value']))+"' oninput='return lepopup_properties_math_name_changed(this);' /></div></div>";
	html += "<div class='lepopup-properties-item' data-id='expression'><div class='lepopup-properties-label'><label>"+lepopup_math_expressions_meta['expression']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_math_expressions_meta['expression']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-math-expression' value='"+(_values != null && _values.hasOwnProperty('expression') ? lepopup_escape_html(_values['expression']) : lepopup_escape_html(lepopup_math_expressions_meta['expression']['value']))+"' /><div class='lepopup-shortcode-selector' data-disabled-groups='math' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
	html += "<div class='lepopup-properties-item' data-id='default'><div class='lepopup-properties-label'><label>"+lepopup_math_expressions_meta['default']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_math_expressions_meta['default']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-math-default' value='"+(_values != null && _values.hasOwnProperty('default') ? lepopup_escape_html(_values['default']) : lepopup_escape_html(lepopup_math_expressions_meta['default']['value']))+"' /></div></div>";
	if (_values != null && _values.hasOwnProperty('decimal-digits')) property_value = _values['decimal-digits'];
	else property_value = lepopup_math_expressions_meta['decimal-digits']['value'];
	html += "<div class='lepopup-properties-item' data-id='decimal-digits'><div class='lepopup-properties-label'><label>"+lepopup_math_expressions_meta['decimal-digits']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_math_expressions_meta['decimal-digits']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-number'><select name='lepopup-math-decimal-digits'><option value='0'"+(property_value == 0 ? " selected='selected'" : "")+">0</option><option value='1'"+(property_value == 1 ? " selected='selected'" : "")+">1</option><option value='2'"+(property_value == 2 ? " selected='selected'" : "")+">2</option><option value='3'"+(property_value == 3 ? " selected='selected'" : "")+">3</option><option value='4'"+(property_value == 4 ? " selected='selected'" : "")+">4</option><option value='5'"+(property_value == 5 ? " selected='selected'" : "")+">5</option><option value='6'"+(property_value == 6 ? " selected='selected'" : "")+">6</option><option value='7'"+(property_value == 7 ? " selected='selected'" : "")+">7</option><option value='8'"+(property_value == 8 ? " selected='selected'" : "")+">8</option></select></div></div></div>";
	html += "</div></div>";
	
	if (_values == null) jQuery(".lepopup-properties-content-math-expressions .lepopup-properties-sub-item-body").slideUp(300);
	jQuery(".lepopup-properties-content-math-expressions").append(html);
	
	jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	
	lepopup_properties_math_name_changed(jQuery(".lepopup-properties-content-math-expressions .lepopup-properties-sub-item").last().find("[name='lepopup-math-name']"));
	jQuery(".lepopup-properties-sub-item-new").slideDown(300);
	jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	jQuery(".lepopup-shortcode-selector-list-input").remove();
	return false;
}

function lepopup_properties_confirmations_name_changed(_object) {
	var label = jQuery(_object).val().substring(0,52)+(jQuery(_object).val().length > 52 ? "..." : "");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-header>label").text(label);
	return false;
}
function lepopup_properties_confirmations_logic_enable_changed(_object) {
	var parent = jQuery(_object).closest(".lepopup-properties-sub-item");
	if (jQuery(_object).is(":checked")) jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeIn(300);
	else jQuery(parent).find(".lepopup-properties-item[data-id='logic']").fadeOut(300);
	return false;
}
function lepopup_properties_confirmations_type_changed(_object) {
	var parent = jQuery(_object).closest(".lepopup-properties-sub-item");
	switch (jQuery(_object).val()) {
		case 'close':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").hide();
			break;
		case 'page':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").hide();
			break;
		case 'form':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").hide();
			break;
		case 'page-redirect':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").hide();
			break;
		case 'page-payment':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").show();
			break;
		case 'redirect':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").show();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").hide();
			break;
		case 'payment':
			jQuery(parent).find(".lepopup-properties-item[data-id='form']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='url']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='delay']").hide();
			jQuery(parent).find(".lepopup-properties-item[data-id='payment-gateway']").show();
			break;
		default:
			break;
	}
	return false;
}
function lepopup_properties_confirmations_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-confirmations .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_confirmations_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_confirmations_add(_values) {
	var extra_class = "", html = "", temp = "", tooltip_html, selected, property_value, logic_enable, logic_enable_id;
	
	if (_values == null) { 
		extra_class = " lepopup-properties-sub-item-new";
		lepopup_element_properties_data_changed = true;
	} else extra_class = " lepopup-properties-sub-item-exist";
	html += "<div class='lepopup-properties-sub-item"+extra_class+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_confirmations_delete(this);'><i class='fas fa-trash-alt'></i></span><span onclick='return lepopup_properties_confirmations_details_toggle(this);'><i class='fas fa-cog'></i></span></div><label></label></div><div class='lepopup-properties-sub-item-body'>";
	html += "<div class='lepopup-properties-item' data-id='name'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['name']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['name']['tooltip']+"</div></div><div class='lepopup-properties-content'><input type='text' name='lepopup-confirmations-name' value='"+(_values != null && _values.hasOwnProperty('name') ? lepopup_escape_html(_values['name']) : lepopup_escape_html(lepopup_confirmations['name']['value']))+"' oninput='return lepopup_properties_confirmations_name_changed(this);' /></div></div>";
	var options = "";
	if (_values != null && _values.hasOwnProperty('type')) property_value = _values['type'];
	else property_value = lepopup_confirmations['type']['value'];
	for (var option_key in lepopup_confirmations['type']['options']) {
		if (lepopup_confirmations['type']['options'].hasOwnProperty(option_key)) {
			selected = "";
			if (option_key == property_value) selected = " selected='selected'";
			options += "<option"+selected+" value='"+lepopup_escape_html(option_key)+"'>"+lepopup_escape_html(lepopup_confirmations['type']['options'][option_key])+"</option>";
		}
	}
	html += "<div class='lepopup-properties-item' data-id='type'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['type']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['type']['tooltip']+"</div></div><div class='lepopup-properties-content'><select name='lepopup-confirmations-type' onchange='return lepopup_properties_confirmations_type_changed(this);'>"+options+"</select></div></div>";
	var message_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='url'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['url']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['url']['tooltip']+"</div></div><div class='lepopup-properties-content'><div class='lepopup-properties-group lepopup-input-shortcode-selector'><input type='text' name='lepopup-confirmations-url' value='"+(_values != null && _values.hasOwnProperty('url') ? lepopup_escape_html(_values['url']) : lepopup_escape_html(lepopup_confirmations['url']['value']))+"' /><div class='lepopup-shortcode-selector' onmouseover='lepopup_shortcode_selector_set(this)';><span><i class='fas fa-code'></i></span></div></div></div></div>";
	
	html += "<div class='lepopup-properties-item' data-id='delay'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['delay']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['delay']['tooltip']+"</div></div><div class='lepopup-properties-content'><div><input class='lepopup-number' type='text' name='lepopup-confirmations-delay' value='"+(_values != null && _values.hasOwnProperty('delay') ? lepopup_escape_html(_values['delay']) : lepopup_escape_html(lepopup_confirmations['delay']['value']))+"' />"+(lepopup_confirmations['delay'].hasOwnProperty("unit") ? " "+lepopup_confirmations['delay']["unit"] : "")+"</div></div></div>";
	
	property_value = (_values != null && _values.hasOwnProperty('payment-gateway') ? lepopup_escape_html(_values['payment-gateway']) : lepopup_escape_html(lepopup_confirmations['payment-gateway']['value']));
	options = "<option value=''>Select payment gateway</option>";
	for (var key in lepopup_form_options['payment-gateways']) {
		selected = "";
		if (lepopup_form_options['payment-gateways'][key]['id'] == property_value) selected = " selected='selected'";
		options += "<option"+selected+" value='"+lepopup_escape_html(lepopup_form_options['payment-gateways'][key]['id'])+"'>"+lepopup_escape_html(lepopup_form_options['payment-gateways'][key]['name'])+"</option>";
	}
	html += "<div class='lepopup-properties-item' data-id='payment-gateway'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['payment-gateway']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['payment-gateway']['tooltip']+"</div></div><div class='lepopup-properties-content'><select class='lepopup-payment-gateways-select' name='lepopup-confirmations-payment-gateway'>"+options+"</select></div></div>";

	property_value = (_values != null && _values.hasOwnProperty('form') ? lepopup_escape_html(_values['form']) : lepopup_escape_html(lepopup_confirmations['form']['value']));
	options = "<option value=''>Select popup</option>";
	for (var i=0; i<lepopup_forms.length; i++) {
		selected = "";
		if (lepopup_forms[i]['slug'] == property_value) selected = " selected='selected'";
		options += "<option"+selected+" value='"+lepopup_escape_html(lepopup_forms[i]['slug'])+"'>"+lepopup_escape_html(lepopup_forms[i]['name']+" ("+lepopup_forms[i]['slug']+")")+"</option>";
	}
	html += "<div class='lepopup-properties-item' data-id='form'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['form']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['form']['tooltip']+"</div></div><div class='lepopup-properties-content'><select name='lepopup-confirmations-form'>"+options+"</select></div></div>";

	var reset_form;
	if (_values != null && _values.hasOwnProperty('reset-form')) reset_form = _values['reset-form'];
	else reset_form = lepopup_confirmations['reset-form']['value'];
	var reset_form_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='reset-form'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['reset-form']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['reset-form']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-confirmations-reset-form-"+reset_form_id+"' name='lepopup-confirmations-reset-form'"+(reset_form == "on" ? ' checked="checked"' : '')+"' /><label for='lepopup-confirmations-reset-form-"+reset_form_id+"'></label></div></div>";
	
	if (_values != null && _values.hasOwnProperty('logic-enable')) logic_enable = _values['logic-enable'];
	else logic_enable = lepopup_confirmations['logic-enable']['value'];
	logic_enable_id = lepopup_random_string(16);
	html += "<div class='lepopup-properties-item' data-id='logic-enable'><div class='lepopup-properties-label'><label>"+lepopup_confirmations['logic-enable']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['logic-enable']['tooltip']+"</div></div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' id='lepopup-confirmations-logic-enable-"+logic_enable_id+"' name='lepopup-confirmations-logic-enable'"+(logic_enable == "on" ? ' checked="checked"' : '')+"' onchange='return lepopup_properties_confirmations_logic_enable_changed(this);' /><label for='lepopup-confirmations-logic-enable-"+logic_enable_id+"'></label></div></div>";
	
	if (_values != null && _values.hasOwnProperty('logic')) property_value = _values['logic'];
	else property_value = lepopup_confirmations['logic']['value'];
	var input_ids = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]['type']) && lepopup_toolbar_tools[lepopup_form_elements[i]['type']]['type'] == 'input') {
			input_ids.push(lepopup_form_elements[i]["id"]);
		}
	}
	if (input_ids.length > 0) {
		temp = "<div class='lepopup-properties-group lepopup-properties-logic-header'>";
		options = "";
		for (var option_key in lepopup_confirmations['logic']['actions']) {
			if (lepopup_confirmations['logic']['actions'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["action"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_confirmations['logic']['actions'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-confirmations-logic-action' id='lepopup-logic-action'>"+options+"</select></div>";
		options = "";
		for (var option_key in lepopup_confirmations['logic']['operators']) {
			if (lepopup_confirmations['logic']['operators'].hasOwnProperty(option_key)) {
				options += "<option value='"+lepopup_escape_html(option_key)+"'"+(property_value["operator"] == option_key ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_confirmations['logic']['operators'][option_key])+"</option>";
			}
		}
		temp += "<div class='lepopup-properties-content-half'><select name='lepopup-confirmations-logic-operator' id='lepopup-logic-operator'>"+options+"</select></div>";
		temp += "</div>";
		options = "";
		for (var j=0; j<property_value["rules"].length; j++) {
			if (input_ids.indexOf(parseInt(property_value["rules"][j]["field"], 10)) != -1) {
				options += lepopup_properties_logic_rule_get(null, property_value["rules"][j]["field"], property_value["rules"][j]["rule"], property_value["rules"][j]["token"]);
			}
		}
		temp += "<div class='lepopup-properties-logic-rules'>"+options+"</div><div class='lepopup-properties-logic-buttons'><a class='lepopup-admin-button lepopup-admin-button-gray lepopup-admin-button-small' href='#' onclick='return lepopup_properties_logic_rule_new(this, null);'><i class='fas fa-plus'></i><label>Add rule</label></a></div>";
	} else {
		temp = "<div class='lepopup-properties-inline-error'>There are no elements available to use for logic rules.</div>";
	}
	html += "<div class='lepopup-properties-item' data-id='logic'"+(logic_enable == "on" ? "" : " style='display:none;'")+"><div class='lepopup-properties-label'><label>"+lepopup_confirmations['logic']['label']+"</label></div><div class='lepopup-properties-tooltip'><i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_confirmations['logic']['tooltip']+"</div></div><div class='lepopup-properties-content'>"+temp+"</div></div>";
	html += "</div></div>";
	
	if (_values == null) jQuery(".lepopup-properties-content-confirmations .lepopup-properties-sub-item-body").slideUp(300);
	jQuery(".lepopup-properties-content-confirmations").append(html);
	
	jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom",
		content:		"Default",
		functionFormat: function(instance, helper, content){
			return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
		}
	});
	
	lepopup_properties_confirmations_name_changed(jQuery(".lepopup-properties-content-confirmations .lepopup-properties-sub-item").last().find("[name='lepopup-confirmations-name']"));
	lepopup_properties_confirmations_type_changed(jQuery(".lepopup-properties-content-confirmations .lepopup-properties-sub-item").last().find("[name='lepopup-confirmations-type']"));
	jQuery(".lepopup-properties-sub-item-new").slideDown(300);
	jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	return false;
}
function lepopup_properties_filters_add(_type, _filter, _values) {
	var extra_class = "", html = "", tooltip_html, selected, property_value = "";
	var seq = 0, last;
	last = jQuery(".lepopup-properties-content-filters .lepopup-properties-sub-item").last();
	if (jQuery(last).length) seq = parseInt(jQuery(last).attr("data-seq"), 10) + 1;
	if (lepopup_meta[_type].hasOwnProperty("filters") && lepopup_filters.hasOwnProperty(_filter)) {
		if (_values == null) { 
			extra_class = " lepopup-properties-sub-item-new";
			lepopup_element_properties_data_changed = true;
		} else extra_class = " lepopup-properties-sub-item-exist";
		if (lepopup_filters[_filter].hasOwnProperty("properties")) property_value = "<span onclick='return lepopup_properties_filters_details_toggle(this);'><i class='fas fa-cog'></i></span>";
		html += "<div class='lepopup-properties-sub-item"+extra_class+"' data-type='"+_filter+"' data-seq='"+seq+"'><div class='lepopup-properties-sub-item-header'><div class='lepopup-properties-sub-item-header-tools'><span onclick='return lepopup_properties_filters_delete(this);'><i class='fas fa-trash-alt'></i></span>"+property_value+"</div><label>"+lepopup_filters[_filter]["label"]+"</label></div><div class='lepopup-properties-sub-item-body'>";
		for (var key in lepopup_filters[_filter]["properties"]) {
			if (lepopup_filters[_filter]["properties"].hasOwnProperty(key)) {
				tooltip_html = "";
				if (lepopup_filters[_filter]["properties"][key].hasOwnProperty('tooltip')) {
					tooltip_html = "<i class='fas fa-question-circle lepopup-tooltip-anchor'></i><div class='lepopup-tooltip-content'>"+lepopup_filters[_filter]["properties"][key]['tooltip']+"</div>";
				}
				property_value = "";
				if (_values != null && _values.hasOwnProperty("properties") && _values["properties"].hasOwnProperty(key)) property_value = _values["properties"][key];
				switch(lepopup_filters[_filter]["properties"][key]['type']) {
					case 'text':
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_filters[_filter]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input type='text' name='lepopup-filters-"+key+"' id='lepopup-filters-"+seq+"-"+key+"' value='"+lepopup_escape_html(property_value)+"' placeholder='"+lepopup_escape_html(property_value)+"' /></div></div>";
						break;

					case 'integer':
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_filters[_filter]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><div class='lepopup-number'><input type='text' name='lepopup-filters-"+key+"' id='lepopup-filters-"+seq+"-"+key+"' value='"+lepopup_escape_html(property_value)+"' placeholder='' /></div></div></div>";
						break;

					case 'checkbox':
						selected = "";
						if (property_value == "on") selected = " checked='checked'";
						html += "<div class='lepopup-properties-item' data-id='"+key+"'><div class='lepopup-properties-label'><label>"+lepopup_filters[_filter]["properties"][key]['label']+"</label></div><div class='lepopup-properties-tooltip'>"+tooltip_html+"</div><div class='lepopup-properties-content'><input class='lepopup-checkbox-toggle' type='checkbox' value='off' name='lepopup-filters-"+key+"' id='lepopup-filters-"+seq+"-"+key+"'"+selected+"' /><label for='lepopup-filters-"+seq+"-"+key+"'></label></div></div>";
						break;

					default:
						break;
				}
			}
		}
		html += "</div></div>";
		if (_values == null) jQuery(".lepopup-properties-content-filters .lepopup-properties-sub-item-body").slideUp(300);
		jQuery(".lepopup-properties-content-filters").append(html);
		jQuery(".lepopup-properties-sub-item-new .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
			contentAsHTML:	true,
			maxWidth:		360,
			theme:			"tooltipster-dark",
			side:			"bottom",
			content:		"Default",
			functionFormat: function(instance, helper, content){
				return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
			}
		});
		
		jQuery(".lepopup-properties-sub-item-new").slideDown(300);
		jQuery(".lepopup-properties-sub-item-new").removeClass("lepopup-properties-sub-item-new");
	}
	return false;
}
function lepopup_properties_filters_details_toggle(_object) {
	jQuery(_object).closest(".lepopup-properties-sub-item").addClass("lepopup-freeze");
	jQuery(".lepopup-properties-content-filters .lepopup-properties-sub-item").each(function() {
		if (!jQuery(this).hasClass("lepopup-freeze")) jQuery(this).find(".lepopup-properties-sub-item-body").slideUp(300);
	});
	jQuery(_object).closest(".lepopup-properties-sub-item").removeClass("lepopup-freeze");
	jQuery(_object).closest(".lepopup-properties-sub-item").find(".lepopup-properties-sub-item-body").slideToggle(300);
	return false;
}
function lepopup_properties_filters_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-sub-item").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_logic_rule_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to delete the item.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Delete'),
		ok_function:	function(e) {
			jQuery(_object).closest(".lepopup-properties-logic-rule").slideUp(300, function() {
				jQuery(this).remove();
			});
			lepopup_element_properties_data_changed = true;
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_properties_logic_rule_token_change(_object) {
	var rule = jQuery(_object).closest(".lepopup-properties-logic-rule");
	var html = lepopup_properties_logic_rule_token_get(jQuery(rule).find(".lepopup-properties-logic-rule-field").val(), jQuery(rule).find(".lepopup-properties-logic-rule-rule").val(), "");
	jQuery(rule).find(".lepopup-properties-logic-rule-token-container").html(html);
	return false;
}
function lepopup_properties_logic_rule_token_get(_field, _rule, _token) {
	var html = "", input = null, options = "";

	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_form_elements[i]['id'] == _field) {
			input = lepopup_form_elements[i];
			break;
		}
	}
	if (input == null) html = "<input class='lepopup-properties-logic-rule-token' type='text' placeholder='Enter your value...' value='"+(_token ? lepopup_escape_html(_token) : "")+"' />";
	else {
		if (_rule == 'is-empty' || _rule == 'is-not-empty') html = "<input class='lepopup-properties-logic-rule-token' type='hidden' value='' />";
		else if (_rule == 'is' || _rule == 'is-not') {
			if (input.hasOwnProperty("options") && input["options"].length > 0) {
				for (var i=0; i<input["options"].length; i++) {
					options += "<option value='"+lepopup_escape_html(input["options"][i]["value"])+"'"+(input["options"][i]["value"] == _token ? " selected='selected'" : "")+">"+lepopup_escape_html(input["options"][i]["label"])+"</option>";
				}
				html = "<select class='lepopup-properties-logic-rule-token'>"+options+"</select>";
			} else html = "<input class='lepopup-properties-logic-rule-token' type='text' placeholder='Enter your value...' value='"+(_token ? lepopup_escape_html(_token) : "")+"' />";
		} else html = "<input class='lepopup-properties-logic-rule-token' type='text' placeholder='Enter your value...' value='"+(_token ? lepopup_escape_html(_token) : "")+"' />";
	}
	return html;
}
function lepopup_properties_logic_rule_get(_field_id, _field, _rule, _token) {
	var temp = "", html = "", field_options = "", rule_options = "";

	var field_selected = null, rule_selected = null;
	var input_fields = lepopup_input_sort();
	if (input_fields.length > 0) {
		for (var j=0; j<input_fields.length; j++) {
			if (temp != input_fields[j]['page-id']) {
				if (temp != "") field_options += "</optgroup>";
				field_options += "<optgroup label='"+lepopup_escape_html(input_fields[j]['page-name'])+"'>";
				temp = input_fields[j]['page-id'];
			}
			if (field_selected == null || _field == input_fields[j]['id']) field_selected = input_fields[j]['id'];
			field_options += "<option value='"+input_fields[j]['id']+"'"+(input_fields[j]['id'] == _field ? " selected='selected'" : "")+">"+input_fields[j]['id']+" | "+lepopup_escape_html(input_fields[j]['name'])+"</option>";
		}
		field_options += "</optgroup>";
	}
	for (var key in lepopup_logic_rules) {
		if (rule_selected == null || _rule == key) rule_selected = key;
		if (lepopup_logic_rules.hasOwnProperty(key)) {
			rule_options += "<option value='"+key+"'"+(key == _rule ? " selected='selected'" : "")+">"+lepopup_escape_html(lepopup_logic_rules[key])+"</option>";
		}
	}
	var field_token = lepopup_properties_logic_rule_token_get(field_selected, rule_selected, _token);
	html = "<div class='lepopup-properties-logic-rule'><div class='lepopup-properties-logic-rule-table'><div><select class='lepopup-properties-logic-rule-field' onchange='lepopup_properties_logic_rule_token_change(this);'>"+field_options+"</select></div><div><select class='lepopup-properties-logic-rule-rule' onchange='lepopup_properties_logic_rule_token_change(this);'>"+rule_options+"</select></div><div class='lepopup-properties-logic-rule-token-container'>"+field_token+"</div><div class='lepopup-properties-logic-rule-icons'><span onclick='return lepopup_properties_logic_rule_delete(this);' title='Delete the option'><i class='fas fa-trash-alt'></i></span></div></div></div>";
	return html;
}
function lepopup_properties_logic_rule_new(_object, _field_id) {
	var rule_html = lepopup_properties_logic_rule_get(_field_id, null, null, null);
	jQuery(_object).closest(".lepopup-properties-content").find(".lepopup-properties-logic-rules").append(rule_html);
	lepopup_element_properties_data_changed = true;
	return false;
}

function lepopup_properties_attachment_media(_object) {
	var input = jQuery(_object).parent().children("input");
	var media_frame = wp.media({
		title: 'Select Media',
		multiple: false
	});
	media_frame.on("select", function() {
		var attachment = media_frame.state().get("selection").first();
		jQuery(input).val(attachment.attributes.id+" | "+attachment.attributes.filename);
	});
	media_frame.open();
}
function lepopup_properties_attachment_delete(_object) {
	var attachment = jQuery(_object).closest(".lepopup-properties-attachment");
	jQuery(attachment).slideUp(300, function(){jQuery(attachment).remove();});
	lepopup_element_properties_data_changed = true;
	return false;
}
function lepopup_properties_attachment_token_change(_object) {
	var attachment = jQuery(_object).closest(".lepopup-properties-attachment");
	var html = lepopup_properties_attachment_token_get(jQuery(attachment).find(".lepopup-properties-attachment-source").val(), "");
	jQuery(attachment).find(".lepopup-properties-attachment-token-container").html(html);
	return false;
}
function lepopup_properties_attachment_token_get(_source, _token) {
	var html = "", input = null, options = "";
	if (_source == "media-library") html = "<div class='lepopup-media-id'><input class='lepopup-properties-attachment-token' type='text' placeholder='' readonly='readonly' value='"+lepopup_escape_html(_token)+"' onclick='lepopup_properties_attachment_media(this);' /><span onclick='lepopup_properties_attachment_media(this);'><i class='far fa-file'></i></span></div>";
	else if (_source == "file") html = "<input class='lepopup-properties-attachment-token' type='text' placeholder='Enter the FULL path of the file on the server (not URL!).' value='"+lepopup_escape_html(_token)+"' />";
	else {
		for (var i=0; i<lepopup_form_elements.length; i++) {
			if (lepopup_form_elements[i] == null) continue;
			if (lepopup_form_elements[i]['type'] == 'file') {
				options += "<option value='"+lepopup_form_elements[i]['id']+"'"+(lepopup_form_elements[i]['id'] == _token ? " selected='selected'" : "")+">"+lepopup_form_elements[i]['id']+" | "+lepopup_escape_html(lepopup_form_elements[i]['name'])+"</option>";
			}
		}
		if (options != "") html = "<select class='lepopup-properties-attachment-token'>"+options+"</select>";
		else html = "No form elements (files) found.";
	}
	return html;
}
function lepopup_properties_attachment_get(_source, _token) {
	var token = lepopup_properties_attachment_token_get(_source, _token);
	var html = "<div class='lepopup-properties-attachment'><div class='lepopup-properties-attachment-table'><div><select class='lepopup-properties-attachment-source' onchange='lepopup_properties_attachment_token_change(this);'><option value='form-element'"+(_source == "form-element" ? " selected='selected'" : "")+">Form Element</option>"+(typeof UAP_CORE == typeof undefined ? "<option value='media-library'"+(_source == "media-library" ? " selected='selected'" : "")+">Media Library</option>" : "")+"<option value='file'"+(_source == "file" ? " selected='selected'" : "")+">File on Server</option></select></div><div class='lepopup-properties-attachment-token-container'>"+token+"</div><div><span onclick='return lepopup_properties_attachment_delete(this);' title='Delete the attachment'><i class='fas fa-trash-alt'></i></span></div></div></div>";
	return html;
}
function lepopup_properties_attachment_new(_object) {
	var attachment_html = lepopup_properties_attachment_get(null, null);
	jQuery(_object).closest(".lepopup-properties-content").find(".lepopup-properties-attachments").append(attachment_html);
	lepopup_element_properties_data_changed = true;
	return false;
}

var lepopup_shortcode_selector_setting = false;
function lepopup_shortcode_selector_set(_object) {
	if (lepopup_shortcode_selector_setting) return;
	lepopup_shortcode_selector_setting = true;
	jQuery(".lepopup-shortcode-selector-list-input").find("li").show();
	var disabled_groups_raw = jQuery(_object).attr("data-disabled-groups");
	if (typeof disabled_groups_raw == typeof "string") {
		if (disabled_groups_raw.length > 0) {
			var disabled_groups = disabled_groups_raw.split(",");
			for (var j=0; j<disabled_groups.length; j++) {
				if (disabled_groups[j].length > 0) jQuery(".lepopup-shortcode-selector-list-input").find("li.lepopup-shortcode-selector-list-item-"+disabled_groups[j]).hide();
			}
		}
	}
	if (jQuery(_object).find(".lepopup-shortcode-selector-list-input").length > 0) {
		lepopup_shortcode_selector_setting = false;
		return;
	}
	if (jQuery(".lepopup-shortcode-selector-list-input").length > 0) {
		jQuery(".lepopup-shortcode-selector-list-input").appendTo(_object);
		lepopup_shortcode_selector_setting = false;
		return;
	}
	var html = lepopup_shortcode_selector_list_html("lepopup-shortcode-selector-list-input");
	jQuery(_object).append(html);
	jQuery(_object).find(".lepopup-shortcode-selector-list-item").on("click", function(e){
        var input = jQuery(this).closest(".lepopup-input-shortcode-selector, .lepopup-textarea-shortcode-selector").find("input, textarea");
        var caret_pos = input[0].selectionStart;
        var current_value = jQuery(input).val();
        jQuery(input).val(current_value.substring(0, caret_pos) + jQuery(this).attr("data-code") + current_value.substring(caret_pos) );
	});
	lepopup_shortcode_selector_setting = false;
	return;
}
function lepopup_shortcode_selector_list_html(_class) {
	var type, items, label, id;

	var temp = "<ul class='"+_class+"'><li class='lepopup-shortcode-selector-group lepopup-shortcode-selector-list-item-field'>Form values</li>";
	for (var j=0; j<lepopup_form_elements.length; j++) {
		if (lepopup_form_elements[j] == null) continue;
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[j]['type']) && lepopup_toolbar_tools[lepopup_form_elements[j]['type']]['type'] == 'input') {
			label = lepopup_form_elements[j]['name'].replace(new RegExp("}", 'g'), ")");
			label = label.replace(new RegExp("{", 'g'), "(");
			temp += "<li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-field' data-code='{{"+lepopup_form_elements[j]['id']+"|"+lepopup_escape_html(label)+"}}'>"+lepopup_form_elements[j]['id']+" | "+lepopup_escape_html(lepopup_form_elements[j]['name'])+"</li>";
		}
	}
	
	var math_from_window = false;
	if (lepopup_element_properties_active != null) {
		var type = jQuery(lepopup_element_properties_active).attr("data-type");
		if (type == "settings") math_from_window = true;
	}
	if (math_from_window) {
		items = jQuery(".lepopup-properties-content-math-expressions .lepopup-properties-sub-item");
		if (items.length > 0) {
			temp += "<li class='lepopup-shortcode-selector-group lepopup-shortcode-selector-list-item-math'>Math expressions</li>";
			jQuery(items).each(function() {
				label = jQuery(this).find("[name='lepopup-math-name']").val();
				label = label.replace(new RegExp("}", 'g'), ")");
				label = label.replace(new RegExp("{", 'g'), "(");
				id = jQuery(this).find("[name='lepopup-math-id']").val();
				temp += "<li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-math' data-code='{{"+id+"|"+lepopup_escape_html(label)+"}}'>"+id+" | "+jQuery(this).find("[name='lepopup-math-name']").val()+"</li>";
			});
		}
	} else {
		if (lepopup_form_options.hasOwnProperty("math-expressions")) {
			if (lepopup_form_options["math-expressions"].length > 0) {
				temp += "<li class='lepopup-shortcode-selector-group'>Math expressions</li>";
				for (var j=0; j<lepopup_form_options["math-expressions"].length; j++) {
					label = lepopup_form_options["math-expressions"][j]['name'].replace(new RegExp("}", 'g'), ")");
					label = label.replace(new RegExp("{", 'g'), "(");
					temp += "<li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-math' data-code='{{"+lepopup_form_options["math-expressions"][j]['id']+"|"+lepopup_escape_html(label)+"}}'>"+lepopup_form_options["math-expressions"][j]['id']+" | "+lepopup_escape_html(lepopup_form_options["math-expressions"][j]['name'])+"</li>";
				}
			}
		}
	}
	temp += "<li class='lepopup-shortcode-selector-group lepopup-shortcode-selector-list-item-general'>General</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general lepopup-shortcode-selector-list-item-form-data' data-code='{{form-data}}'>All Form Data</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general lepopup-shortcode-selector-list-item-record-id' data-code='{{record-id}}'>Record ID</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{ip}}'>IP Address</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{user-agent}}'>User Agent</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{date}}'>Date</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{time}}'>Time</li>"+(typeof lepopup_uap_core != typeof undefined && lepopup_uap_core === true ? "" : "<li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{wp-user-login}}'>WP User Login</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{wp-user-email}}'>WP User Email</li>")+"<li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{url}}'>Current URL</li><li class='lepopup-shortcode-selector-list-item lepopup-shortcode-selector-list-item-general' data-code='{{page-title}}'>Current Page Title</li>";
	temp += "</ul>";
	return temp;
}
/* Element actions - end */

/* Bulk Options - begin */
var lepopup_bulk_options_object = null;
function lepopup_bulk_options_open(_object) {
	lepopup_bulk_options_object = jQuery(_object).closest(".lepopup-properties-item");
	if (lepopup_bulk_options_object) {
		var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var window_width = Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 600);
		jQuery("#lepopup-bulk-options").height(window_height);
		jQuery("#lepopup-bulk-options").width(window_width);
		jQuery("#lepopup-bulk-options .lepopup-admin-popup-inner").height(window_height);
		jQuery("#lepopup-bulk-options .lepopup-admin-popup-content").height(window_height - 104);
		jQuery("#lepopup-bulk-options-overlay").fadeIn(300);
		jQuery("#lepopup-bulk-options").fadeIn(300);
		jQuery(".lepopup-bulk-editor textarea").val("");
	}
	return false;
}
function lepopup_bulk_options_close() {
	lepopup_bulk_options_object = null;
	jQuery("#lepopup-bulk-options-overlay").fadeOut(300);
	jQuery("#lepopup-bulk-options").fadeOut(300);
}
function lepopup_bulk_category_add(_object) {
	var category = jQuery(_object).attr("data-category");
	if (!category) return false;
	var value = jQuery(".lepopup-bulk-editor textarea").val();
	if (category == "existing") {
		if (lepopup_bulk_options_object) {
			jQuery(lepopup_bulk_options_object).find(".lepopup-properties-options-item").each(function() {
				 var option_label = jQuery(this).find('.lepopup-properties-options-label').val();
				 var option_value = jQuery(this).find('.lepopup-properties-options-value').val();
				if (value != "") value += "\r\n";
				 if (option_label != option_value) value += option_label+"|"+option_value;
				 else value += option_label;
			});
		}
	} else {
		if (lepopup_predefined_options != null && lepopup_predefined_options.hasOwnProperty(category)) {
			for (var i=0; i<lepopup_predefined_options[category]["options"].length; i++) {
				if (value != "") value += "\r\n";
				value += lepopup_predefined_options[category]["options"][i];
			}
		}
	}
	jQuery(".lepopup-bulk-editor textarea").val(value);
	return false;
}
function lepopup_bulk_options_add() {
	var option;
	var html = "";
	if (lepopup_bulk_options_object) {
		if (jQuery("#lepopup-bulk-options-overwrite").is(":checked")) {
			jQuery(lepopup_bulk_options_object).find(".lepopup-properties-options-container .lepopup-properties-options-item").remove();
		}
		var options_str = jQuery(".lepopup-bulk-editor textarea").val();
		var options = options_str.split("\n");
		for (var i=0; i<options.length; i++) {
			option = options[i].split("|");
			if (option.length == 1) html += lepopup_properties_options_item_get(null, option[0], option[0], false);
			else html += lepopup_properties_options_item_get(null, option[0], option[1], false);
		}
		jQuery(lepopup_bulk_options_object).find(".lepopup-properties-options-container").append(html);
	}
	lepopup_element_properties_data_changed = true;
	lepopup_bulk_options_close();
	lepopup_properties_change();
	jQuery(".lepopup-properties-options-box input").off("input");
	jQuery(".lepopup-properties-options-box input").on("input", function(e){
		lepopup_properties_change();
	});
}
/* Bulk Options - end */

/* Font Awesome selector - begin */
var lepopup_fa_selector_active = null;
function lepopup_fa_selector_open(_object) {
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.max(40*parseInt((jQuery(window).width() - 300)/40, 10) + 6, 606);
	jQuery(".lepopup-fa-selector").height(window_height);
	jQuery(".lepopup-fa-selector").width(window_width);
	jQuery(".lepopup-fa-selector-inner").height(window_height);
	jQuery(".lepopup-fa-selector-content").height(window_height - 72 - 20);
	jQuery(".lepopup-fa-selector-overlay").show();
	jQuery(".lepopup-fa-selector").show();
	lepopup_fa_selector_active = _object;
	return false;
}
function lepopup_fa_selector_close() {
	lepopup_fa_selector_active = null;
	jQuery(".lepopup-fa-selector-overlay").hide();
	jQuery(".lepopup-fa-selector").hide();
}
function lepopup_fa_selector_set(_object) {
	var icon_class;
	if (lepopup_fa_selector_active == null) return false;
	var icon = jQuery(_object).find("i").attr("class");
	if (icon == "") icon_class = "lepopup-fa-noicon";
	else icon_class = icon;
	var icon_element = jQuery(lepopup_fa_selector_active).attr("data-id");
	jQuery("#lepopup-"+icon_element).val(icon);
	jQuery(lepopup_fa_selector_active).find("i").attr("class", icon_class);
	lepopup_properties_change();
	lepopup_fa_selector_close();
	return false;
}
/* Font Awesome selector - end */

/* Pages - start */
function lepopup_pages_add() {
	var width, height;
	if (lepopup_meta.hasOwnProperty("page")) {
		lepopup_form_last_id++;
		var page = {"id" : lepopup_form_last_id, "type" : "page"};
		for (var key in lepopup_meta["page"]) {
			if (lepopup_meta["page"].hasOwnProperty(key)) {
				switch(lepopup_meta["page"][key]['type']) {
					default:
						if (lepopup_meta["page"][key].hasOwnProperty('value')) {
							if (typeof lepopup_meta["page"][key]['value'] == 'object') {
								for (var option_key in lepopup_meta["page"][key]['value']) {
									if (lepopup_meta["page"][key]['value'].hasOwnProperty(option_key)) {
										page[key+"-"+option_key] = lepopup_meta["page"][key]['value'][option_key];
									}
								}
							} else page[key] = lepopup_meta["page"][key]['value'];
						} else if (lepopup_meta["page"][key].hasOwnProperty('values')) page[key] = lepopup_meta["page"][key]['values'];
						break;
				}
			}
		}
		lepopup_form_pages.push(page);
		lepopup_form_changed = true;
		
		if (jQuery(".lepopup-pages-bar-item-confirmation").length > 0) jQuery(".lepopup-pages-bar-item-confirmation").before("<li class='lepopup-pages-bar-item' data-id='"+page["id"]+"' data-name='"+lepopup_escape_html(page['name'])+"'><label onclick='return lepopup_pages_activate(this);'>"+lepopup_escape_html(page['name'])+"</label><span><a href='#' data-type='page' onclick='return lepopup_properties_open(this);'><i class='fas fa-cog'></i></a><a href='#' class='lepopup-pages-bar-item-delete' onclick='return lepopup_pages_delete(this);'><i class='fas fa-trash-alt'></i></a></span></li>");
		else jQuery(".lepopup-pages-add").before("<li class='lepopup-pages-bar-item' data-id='"+page["id"]+"' data-name='"+lepopup_escape_html(page['name'])+"'><label onclick='return lepopup_pages_activate(this);'>"+lepopup_escape_html(page['name'])+"</label><span><a href='#' data-type='page' onclick='return lepopup_properties_open(this);'><i class='fas fa-cog'></i></a><a href='#' class='lepopup-pages-bar-item-delete' onclick='return lepopup_pages_delete(this);'><i class='fas fa-trash-alt'></i></a></span></li>");
		if (jQuery(".lepopup-pages-bar-item").length == 1) jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").addClass("lepopup-pages-bar-item-delete-disabled");
		else jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").removeClass("lepopup-pages-bar-item-delete-disabled");
		
		jQuery(".lepopup-builder").append("<div id='lepopup-form-"+page['id']+"' class='lepopup-form' _data-parent='"+page['id']+"'><div class='lepopup-basic-frame'><div class='lepopup-elements'></div></div><div class='lepopup-hidden-elements'></div></div>");
		if (lepopup_is_numeric(page["size-width"])) width = Math.min(parseInt(page["size-width"], 10), 1200);
		else width = 720;
		if (lepopup_is_numeric(page["size-height"])) height = Math.min(parseInt(page["size-height"], 10), 2400);
		else height = 540;
		jQuery("#lepopup-form-"+page['id']+" .lepopup-basic-frame").width(width);
		jQuery("#lepopup-form-"+page['id']+" .lepopup-basic-frame").height(height);
		_lepopup_init_basic_frame("#lepopup-form-"+page['id']+" .lepopup-basic-frame");
		lepopup_update_progress();
	}
	return false;
}
function _lepopup_pages_delete(_object) {
	var page_id = jQuery(_object).closest("li").attr("data-id");
	for (var i=0; i<lepopup_form_pages.length; i++) {
		if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] == page_id) {
			lepopup_form_pages[i] = null;
			break;
		}
	}
	jQuery(_object).closest("li").remove();
	jQuery("#lepopup-form-"+page_id).remove();

	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] != null && lepopup_form_elements[i]["_parent"] == page_id) {
			_lepopup_element_delete(i);
		}
		
	}
	if (jQuery(".lepopup-pages-bar-item").length == 1) jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").addClass("lepopup-pages-bar-item-delete-disabled");
	else jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").removeClass("lepopup-pages-bar-item-delete-disabled");

	if (lepopup_form_page_active == page_id) lepopup_pages_activate(jQuery(".lepopup-pages-bar-item").first().find("label"));
	lepopup_update_progress();
}
function lepopup_pages_delete(_object) {
	if (jQuery(".lepopup-pages-bar-item").length <= 1) return false;
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the page and all sub-elements.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e) {
			_lepopup_pages_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_pages_activate(_object) {
	var page_id = jQuery(_object).closest("li").attr("data-id");
	if (lepopup_form_page_active == page_id) return false;
	if (lepopup_form_page_active != null && jQuery("#lepopup-form-"+lepopup_form_page_active).length > 0) {
		jQuery("#lepopup-form-"+lepopup_form_page_active).fadeOut(300, function(){jQuery("#lepopup-form-"+page_id).fadeIn(300);});
	} else {
		jQuery("#lepopup-form-"+page_id).fadeIn(300);
	}
	lepopup_form_page_active = page_id;
	jQuery(".lepopup-pages-bar-item-active").removeClass("lepopup-pages-bar-item-active");
	jQuery(".lepopup-pages-bar-item[data-id='"+page_id+"'], .lepopup-pages-bar-item-confirmation[data-id='"+page_id+"']").addClass("lepopup-pages-bar-item-active");
	if (page_id == "confirmation") jQuery(".lepopup-toolbar-tool-input, .lepopup-toolbar-tool-submit").hide();
	else jQuery(".lepopup-toolbar-tool-input, .lepopup-toolbar-tool-submit").show();
	
	_lepopup_layers_sync(lepopup_form_page_active);
	return false;
}
function _lepopup_layers_sync(_page_id) {
	var adminbar_height;
	if (jQuery("#wpadminbar").length > 0) adminbar_height = parseInt(jQuery("#wpadminbar").height(), 10);
	else adminbar_height = 0;
	var idxs = new Array();
	var seqs = new Array();
	var layers_list = "";
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_form_elements[i]["_parent"] == _page_id) {
			idxs.push(i);
			seqs.push(parseInt(lepopup_form_elements[i]["_seq"], 10));
		}
	}
	var sorted;
	for (var i=0; i<seqs.length; i++) {
		sorted = -1;
		for (var j=0; j<seqs.length-1; j++) {
			if (seqs[j] > seqs[j+1]) {
				sorted = seqs[j];
				seqs[j] = seqs[j+1];
				seqs[j+1] = sorted;
				sorted = idxs[j];
				idxs[j] = idxs[j+1];
				idxs[j+1] = sorted;
			}
		}
		if (sorted == -1) break;
	}
	for (var k=0; k<idxs.length; k++) {
		i = idxs[k];
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]["type"]) && lepopup_form_elements[i]["type"] != "hidden") {
			layers_list += "<li class='lepopup-layer-"+i+" lepopup-layer' data-idx='"+i+"'>"+(lepopup_form_elements[i]["name"] != "" ? lepopup_escape_html(lepopup_form_elements[i]["name"]) : "Untitled Element")+"</li>";
		}
	}
	jQuery(".lepopup-layers-list").html(layers_list);
	jQuery(".lepopup-layers-list>li").on("mouseenter", function(e){
		var id = jQuery(this).attr("data-idx");
		jQuery(".lepopup-element-"+id).addClass("lepopup-element-hovered");
	});
	jQuery(".lepopup-layers-list>li").on("mouseleave", function(e){
		var id = jQuery(this).attr("data-idx");
		jQuery(".lepopup-element-"+id).removeClass("lepopup-element-hovered");
	});
	jQuery(".lepopup-layers-list>li").on("contextmenu", function(e) {
		e.preventDefault();
		jQuery(".lepopup-context-menu").hide();
		var id = jQuery(this).attr("data-idx");
		lepopup_context_menu_object = jQuery(".lepopup-element-"+id);
		jQuery(".lepopup-context-menu").css({"top" : (e.pageY - adminbar_height), "left" : e.pageX});
		jQuery(".lepopup-context-menu-multi-page").remove();
		var li_duplicate_pages = new Array();
		var li_move_pages = new Array();
		for (var i=0; i<lepopup_form_pages.length; i++) {
			if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] != "confirmation" && lepopup_form_pages[i]['id'] != lepopup_form_page_active) {
				li_duplicate_pages.push("<li><a href='#' onclick='return lepopup_element_duplicate(lepopup_context_menu_object, "+i+");'>"+lepopup_escape_html(lepopup_form_pages[i]["name"])+"</a></li>");
				li_move_pages.push("<li><a href='#' onclick='return lepopup_element_move(lepopup_context_menu_object, "+i+");'>"+lepopup_escape_html(lepopup_form_pages[i]["name"])+"</a></li>");
			}
		}
		if (li_duplicate_pages.length > 0) {
			jQuery(".lepopup-context-menu-last").after("<li class='lepopup-context-menu-multi-page'><a href='#' onclick='return false;'><i class='fas fa-caret-right'></i><i class='far fa-copy'></i>Duplicate to</a><ul>"+li_duplicate_pages.join("")+"</ul></li><li class='lepopup-context-menu-multi-page'><a href='#' onclick='return false;'><i class='fas fa-caret-right'></i><i class='far fa-arrow-alt-circle-right'></i>Move to</a><ul>"+li_move_pages.join("")+"</ul></li>");
		}
		jQuery(".lepopup-context-menu").addClass("lepopup-context-menu-high-priority");
		jQuery(".lepopup-context-menu").show();
		return false;
	});
	jQuery(".lepopup-layers-list>li").on("click", function(e) {
		e.preventDefault();
		jQuery(".lepopup-context-menu").hide();
		var id = jQuery(this).attr("data-idx");
		lepopup_properties_panel_object = jQuery(".lepopup-element-"+id);
		lepopup_properties_panel_open(lepopup_properties_panel_object);
		return false;
	});
	if (lepopup_element_properties_active) {
		jQuery(lepopup_element_properties_active).addClass("lepopup-element-selected");
		var idx = jQuery(lepopup_element_properties_active).attr("id");
		if (idx) {
			idx = idx.replace("lepopup-element-", "");
			jQuery(".lepopup-layers-list").find("li.lepopup-layer-"+idx).addClass("lepopup-layer-selected");
		}
	}
}
/* Pages - end */

function _lepopup_build_hidden_list(_parent) {
	var html = "";
	var output = _lepopup_build_hidden(_parent);
	if (output["html"] != "") html = "<div class='lepopup-hidden-container'><label>Hidden fields:</label>"+output["html"]+"</div>";
	return html;
}
function _lepopup_build_hidden(_parent, _only_idx) {
	var html = "";
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (_only_idx && i != _only_idx) continue;
		if (lepopup_form_elements[i]["type"] != "hidden") continue;
		if (lepopup_form_elements[i]["_parent"] != _parent) continue;
		html += "<div class='lepopup-hidden-element' id='lepopup-element-"+i+"' data-type='"+lepopup_form_elements[i]["type"]+"'>"+lepopup_escape_html(lepopup_form_elements[i]["name"])+"</div>";
	}
	return {"html" : html, "style" : "", "webfonts" : ""};
}
function _lepopup_build_children(_parent, _only_idx) {
	var adminbar_height = parseInt(jQuery("#wpadminbar").height(), 10);
	var resizable_handle = "all";
	var html = "", style = "", global_html = "", text_style = "";
	var webfonts = new Array();
	var label, options, selected, icon, option, extra_class, style_attr, content, div;
	var properties = {};
	var zindex_base = 500;
	
	var idxs = new Array();
	var seqs = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_form_elements[i]["_parent"] == _parent) {
			idxs.push(i);
			seqs.push(parseInt(lepopup_form_elements[i]["_seq"], 10));
		}
	}
	
	if (idxs.length == 0) return {"html" : "", "style" : "", "webfonts" : webfonts};
	var sorted;
	for (var i=0; i<seqs.length; i++) {
		sorted = -1;
		for (var j=0; j<seqs.length-1; j++) {
			if (seqs[j] > seqs[j+1]) {
				sorted = seqs[j];
				seqs[j] = seqs[j+1];
				seqs[j+1] = sorted;
				sorted = idxs[j];
				idxs[j] = idxs[j+1];
				idxs[j+1] = sorted;
			}
		}
		if (sorted == -1) break;
	}
	for (var k=0; k<idxs.length; k++) {
		html = "";
		style = "";
		i = idxs[k];
		icon = "";
		options = "";
		extra_class = "";
		properties = {};
		if (lepopup_form_elements[i] == null) continue;
		if (_only_idx && i != _only_idx) continue;
		
		if (lepopup_form_elements[i].hasOwnProperty("icon-left-icon")) {
			if (lepopup_form_elements[i]["icon-left-icon"] != "") {
				extra_class += " lepopup-icon-left";
				icon += "<i class='lepopup-icon-left "+lepopup_form_elements[i]["icon-left-icon"]+"'></i>";
				options = "";
				if (lepopup_form_elements[i]["icon-left-size"] != "") {
					options += "font-size:"+lepopup_form_elements[i]["icon-left-size"]+"px;";
				}
				if (options != "") style += "#lepopup-element-"+i+" div.lepopup-input>i.lepopup-icon-left{"+options+"}";
			}
		}
		if (lepopup_form_elements[i].hasOwnProperty("icon-right-icon")) {
			if (lepopup_form_elements[i]["icon-right-icon"] != "") {
				extra_class += " lepopup-icon-right";
				icon += "<i class='lepopup-icon-right "+lepopup_form_elements[i]["icon-right-icon"]+"'></i>";
				options = "";
				if (lepopup_form_elements[i]["icon-right-size"] != "") {
					options += "font-size:"+lepopup_form_elements[i]["icon-right-size"]+"px;";
				}
				if (options != "") style += "#lepopup-element-"+i+" div.lepopup-input>i.lepopup-icon-right{"+options+"}";
			}
		}
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]["type"])) {
			switch(lepopup_form_elements[i]["type"]) {
				case "button":
				case "link-button":
					icon = "";
					label = "";
					if (lepopup_form_elements[i]["label"] != "") label = "<span>"+lepopup_escape_html(lepopup_form_elements[i]["label"])+"</span>";
					else style += "#lepopup-element-"+i+" a.lepopup-button i{margin:0!important;}";
					if (lepopup_form_elements[i].hasOwnProperty("icon-left") && lepopup_form_elements[i]["icon-left"] != "") label = "<i class='lepopup-icon-left "+lepopup_form_elements[i]["icon-left"]+"'></i>" + label;
					if (lepopup_form_elements[i].hasOwnProperty("icon-right") && lepopup_form_elements[i]["icon-right"] != "") label += "<i class='lepopup-icon-right "+lepopup_form_elements[i]["icon-right"]+"'></i>";
					
					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-background") && lepopup_form_elements[i]["colors-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-border") && lepopup_form_elements[i]["colors-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-text") && lepopup_form_elements[i]["colors-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button{"+properties['style-attr']+"}";

					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-background") && lepopup_form_elements[i]["colors-hover-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-hover-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-border") && lepopup_form_elements[i]["colors-hover-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-hover-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-text") && lepopup_form_elements[i]["colors-hover-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-hover-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button:hover{"+properties['style-attr']+"}";

					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-background") && lepopup_form_elements[i]["colors-active-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-active-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-border") && lepopup_form_elements[i]["colors-active-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-active-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-text") && lepopup_form_elements[i]["colors-active-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-active-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button:active{"+properties['style-attr']+"}";
					
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='40' data-resizable-handles='all'><a class='lepopup-button lepopup-button-"+lepopup_form_options["button-active-transform"]+" "+lepopup_form_elements[i]["css-class"]+"' href='#' onclick='return false;'>"+label+"</a><div class='lepopup-element-cover'></div></div>";
					break;
					
				case "email":
				case "text":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<input type='text' class='"+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["default"])+"' /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "number":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<input type='text' class='"+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["number-value3"])+"' /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "numspinner":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					properties['value'] = parseFloat(lepopup_form_elements[i]["number-value2"]).toFixed(lepopup_form_elements[i]["decimal"]);
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='100' data-resizable-handles='all'><div class='lepopup-input lepopup-icon-left lepopup-icon-right"+extra_class+"'><i class='lepopup-icon-left lepopup-if lepopup-if-minus lepopup-numspinner-minus'></i><i class='lepopup-icon-right lepopup-if lepopup-if-plus lepopup-numspinner-plus'></i><input type='text' class='"+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='...' value='"+lepopup_escape_html(properties["value"])+"' readonly='readonly' /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "textarea":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='40' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<textarea class='"+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"'>"+lepopup_escape_html(lepopup_form_elements[i]["default"])+"</textarea></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "select":
					options = "";
					if (lepopup_form_elements[i]["please-select-option"] == "on") options += "<option value=''>"+lepopup_escape_html(lepopup_form_elements[i]["please-select-text"])+"</option>";
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " selected='selected'";
						options += "<option value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+">"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["label"])+"</option>";
					}
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'><select class='"+lepopup_form_elements[i]["css-class"]+"'>"+options+"</select></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "checkbox":
					style += "#lepopup-element-"+i+" div.lepopup-input{height:auto;line-height:1;}";
					properties['checkbox-size'] = lepopup_form_options['checkbox-radio-style-size'];
					if (lepopup_form_elements[i]['checkbox-style-position'] == "") properties['checkbox-position'] = lepopup_form_options['checkbox-radio-style-position'];
					else properties['checkbox-position'] = lepopup_form_elements[i]['checkbox-style-position'];
					if (lepopup_form_elements[i]['checkbox-style-align'] == "") properties['checkbox-align'] = lepopup_form_options['checkbox-radio-style-align'];
					else properties['checkbox-align'] = lepopup_form_elements[i]['checkbox-style-align'];
					if (lepopup_form_elements[i]['checkbox-style-layout'] == "") properties['checkbox-layout'] = lepopup_form_options['checkbox-radio-style-layout'];
					else properties['checkbox-layout'] = lepopup_form_elements[i]['checkbox-style-layout'];
					extra_class = " lepopup-cr-layout-"+properties['checkbox-layout']+" lepopup-cr-layout-"+properties['checkbox-align'];
					
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " checked='checked'";
						option = "<div class='lepopup-cr-box'><input class='lepopup-checkbox lepopup-checkbox-"+lepopup_form_options["checkbox-view"]+" lepopup-checkbox-"+properties["checkbox-size"]+"' type='checkbox' id='lepopup-checkbox-"+i+"-"+j+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+" /><label for='lepopup-checkbox-"+i+"-"+j+"'></label></div>";
						if (properties['checkbox-position'] == "left") option += "<div class='lepopup-cr-label lepopup-ta-"+properties['checkbox-align']+"'><label for='lepopup-checkbox-"+i+"-"+j+"'>"+lepopup_form_elements[i]["options"][j]["label"]+"</label></div>";
						else option = "<div class='lepopup-cr-label lepopup-ta-"+properties['checkbox-align']+"'><label for='lepopup-checkbox-"+i+"-"+j+"'>"+lepopup_form_elements[i]["options"][j]["label"]+"</label></div>" + option;
						options += "<div class='lepopup-cr-container lepopup-cr-container-"+properties["checkbox-size"]+" lepopup-cr-container-"+properties["checkbox-position"]+"'>"+option+"</div>";
					}
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='160' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input"+extra_class+"'>"+options+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "radio":
					style += "#lepopup-element-"+i+" div.lepopup-input{height:auto;line-height:1;}";
					properties['radio-size'] = lepopup_form_options['checkbox-radio-style-size'];
					if (lepopup_form_elements[i]['radio-style-position'] == "") properties['radio-position'] = lepopup_form_options['checkbox-radio-style-position'];
					else properties['radio-position'] = lepopup_form_elements[i]['radio-style-position'];
					if (lepopup_form_elements[i]['radio-style-align'] == "") properties['radio-align'] = lepopup_form_options['checkbox-radio-style-align'];
					else properties['radio-align'] = lepopup_form_elements[i]['radio-style-align'];
					if (lepopup_form_elements[i]['radio-style-layout'] == "") properties['radio-layout'] = lepopup_form_options['checkbox-radio-style-layout'];
					else properties['radio-layout'] = lepopup_form_elements[i]['radio-style-layout'];
					extra_class = " lepopup-cr-layout-"+properties['radio-layout']+" lepopup-cr-layout-"+properties['radio-align'];
					
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " checked='checked'";
						option = "<div class='lepopup-cr-box'><input class='lepopup-radio lepopup-radio-"+lepopup_form_options["radio-view"]+" lepopup-radio-"+properties["radio-size"]+"' type='radio' name='lepopup-radio-"+i+"' id='lepopup-radio-"+i+"-"+j+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+" /><label for='lepopup-radio-"+i+"-"+j+"'></label></div>";
						if (properties['radio-position'] == "left") option += "<div class='lepopup-cr-label lepopup-ta-"+properties['radio-align']+"'><label for='lepopup-radio-"+i+"-"+j+"'>"+lepopup_form_elements[i]["options"][j]["label"]+"</label></div>";
						else option = "<div class='lepopup-cr-label lepopup-ta-"+properties['radio-align']+"'><label for='lepopup-radio-"+i+"-"+j+"'>"+lepopup_form_elements[i]["options"][j]["label"]+"</label></div>" + option;
						options += "<div class='lepopup-cr-container lepopup-cr-container-"+properties["radio-size"]+" lepopup-cr-container-"+properties["radio-position"]+"'>"+option+"</div>";
					}
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='160' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input"+extra_class+"'>"+options+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "multiselect":
					style += "#lepopup-element-"+i+" div.lepopup-input{height:100%;line-height:1;}";
					if (lepopup_form_elements[i]['align'] != "") properties['align'] = lepopup_form_elements[i]['align'];
					else if (lepopup_form_options['multiselect-style-align'] != "") properties['align'] = lepopup_form_options['multiselect-style-align'];
					else properties['align'] = 'left';
					
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " checked='checked'";
						options += "<input type='checkbox' id='lepopup-checkbox-"+i+"-"+j+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+" /><label for='lepopup-checkbox-"+i+"-"+j+"'>"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["label"])+"</label>";
					}
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='40' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input'><div class='lepopup-multiselect lepopup-ta-"+properties["align"]+"'>"+options+"</div></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "imageselect":
					style += "#lepopup-element-"+i+" div.lepopup-input{width:100%;line-height:1;}";
					
					properties['image-size'] = lepopup_form_elements[i]['image-style-size'];
					properties["image-width"] = lepopup_form_elements[i]['image-style-width'];
					if (!lepopup_is_numeric(properties["image-width"])) properties["image-width"] = 120;
					properties["image-height"] = lepopup_form_elements[i]['image-style-height'];
					if (!lepopup_is_numeric(properties["image-height"])) properties["image-height"] = 120;
					properties["label-height"] = lepopup_form_elements[i]['label-height'];
					if (!lepopup_is_numeric(properties["label-height"]) || lepopup_form_elements[i]['label-enable'] != "on") properties["label-height"] = 0;
					properties["image-width"] = parseInt(properties["image-width"], 10);
					properties["image-height"] = parseInt(properties["image-height"], 10);
					properties["label-height"] = parseInt(properties["label-height"], 10);
					
					if (lepopup_form_options.hasOwnProperty('imageselect-selected-scale') && lepopup_form_options['imageselect-selected-scale'] == "on") {
						var scale = 1.10;
						if (properties["image-width"] > 0 && properties["image-height"] > 0) scale = Math.min(parseFloat((properties["image-width"]+8)/properties["image-width"]), parseFloat((properties["image-height"]+8)/properties["image-height"]));
						style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-imageselect:checked+label {transform: scale("+scale+");}";
					}
					extra_class += ' lepopup-ta-'+lepopup_form_options['imageselect-style-align']+' lepopup-imageselect-'+lepopup_form_options['imageselect-style-effect'];
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-imageselect+label {width:"+properties["image-width"]+"px;height:"+parseInt(properties["image-height"]+properties["label-height"], 10)+"px;}";
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-imageselect+label span.lepopup-imageselect-image {height:"+properties["image-height"]+"px;background-size:"+properties['image-size']+";}";
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " checked='checked'";
						properties['image-label'] = "";
						if (properties["label-height"] > 0) {
							properties['image-label'] = "<span class='lepopup-imageselect-label'>"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["label"])+"</span>";
						}
						options += "<input class='lepopup-imageselect' type='"+lepopup_form_elements[i]['mode']+"' name='lepopup-image-"+i+"' id='lepopup-image-"+i+"-"+j+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+" /><label for='lepopup-image-"+i+"-"+j+"'><span class='lepopup-imageselect-image' style='background-image: url("+lepopup_form_elements[i]["options"][j]["image"]+");'></span>"+properties['image-label']+"</label>";
					}

					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='"+properties["image-width"]+"' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input"+extra_class+"'>"+options+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "tile":
					style += "#lepopup-element-"+i+" div.lepopup-input{width:100%;line-height:1;}";
					if (lepopup_form_elements[i].hasOwnProperty("tile-style-size") && lepopup_form_elements[i]['tile-style-size'] != "") properties['size'] = lepopup_form_elements[i]['tile-style-size'];
					else properties['size'] = lepopup_form_options['tile-style-size'];
					if (lepopup_form_elements[i].hasOwnProperty("tile-style-width") && lepopup_form_elements[i]['tile-style-width'] != "") properties['width'] = lepopup_form_elements[i]['tile-style-width'];
					else properties['width'] = lepopup_form_options['tile-style-width'];
					if (lepopup_form_elements[i].hasOwnProperty("tile-style-position") && lepopup_form_elements[i]['tile-style-position'] != "") properties['position'] = lepopup_form_elements[i]['tile-style-position'];
					else properties['position'] = lepopup_form_options['tile-style-position'];
					if (lepopup_form_elements[i].hasOwnProperty("tile-style-layout") && lepopup_form_elements[i]['tile-style-layout'] != "") properties['layout'] = lepopup_form_elements[i]['tile-style-layout'];
					else properties['layout'] = lepopup_form_options['tile-style-layout'];
					extra_class = " lepopup-tile-layout-"+properties['layout']+" lepopup-tile-layout-"+properties['position']+" lepopup-tile-transform-"+lepopup_form_options['tile-selected-transform'];
					
					for (var j=0; j<lepopup_form_elements[i]["options"].length; j++) {
						selected = "";
						if (lepopup_form_elements[i]["options"][j].hasOwnProperty("default") && lepopup_form_elements[i]["options"][j]["default"] == "on") selected = " checked='checked'";
						option = "<div class='lepopup-tile-box'><input class='lepopup-tile lepopup-tile-"+properties["size"]+"' type='"+lepopup_form_elements[i]['mode']+"' name='lepopup-tile-"+i+"' id='lepopup-tile-"+i+"-"+j+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["value"])+"'"+selected+" /><label for='lepopup-tile-"+i+"-"+j+"'>"+lepopup_escape_html(lepopup_form_elements[i]["options"][j]["label"])+"</label></div>";
						options += "<div class='lepopup-tile-container lepopup-tile-"+properties["width"]+"'>"+option+"</div>";
					}
					
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='160' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input"+extra_class+"'>"+options+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "date":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<input type='text' class='lepopup-date "+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["default-date"])+"' /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "time":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<input type='text' class='lepopup-time "+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["default"])+"' /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "file":
					icon = "";
					label = "";
					if (lepopup_form_elements[i]["button-label"] != "") label = "<span>"+lepopup_escape_html(lepopup_form_elements[i]["button-label"])+"</span>";
					else style += "#lepopup-element-"+i+" a.lepopup-button i{margin:0!important;}";
					if (lepopup_form_elements[i].hasOwnProperty("icon-left") && lepopup_form_elements[i]["icon-left"] != "") label = "<i class='lepopup-icon-left "+lepopup_form_elements[i]["icon-left"]+"'></i>" + label;
					if (lepopup_form_elements[i].hasOwnProperty("icon-right") && lepopup_form_elements[i]["icon-right"] != "") label += "<i class='lepopup-icon-right "+lepopup_form_elements[i]["icon-right"]+"'></i>";
					
					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-background") && lepopup_form_elements[i]["colors-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-border") && lepopup_form_elements[i]["colors-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-text") && lepopup_form_elements[i]["colors-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button{"+properties['style-attr']+"}";

					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-background") && lepopup_form_elements[i]["colors-hover-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-hover-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-border") && lepopup_form_elements[i]["colors-hover-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-hover-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-hover-text") && lepopup_form_elements[i]["colors-hover-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-hover-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button:hover{"+properties['style-attr']+"}";

					properties['style-attr'] = "";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-background") && lepopup_form_elements[i]["colors-active-background"] != "") properties['style-attr'] += "background-color:"+lepopup_form_elements[i]["colors-active-background"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-border") && lepopup_form_elements[i]["colors-active-border"] != "") properties['style-attr'] += "border-color:"+lepopup_form_elements[i]["colors-active-border"]+";";
					if (lepopup_form_elements[i].hasOwnProperty("colors-active-text") && lepopup_form_elements[i]["colors-active-text"] != "") properties['style-attr'] += "color:"+lepopup_form_elements[i]["colors-active-text"]+";";
					if (properties['style-attr'] != "") style += "#lepopup-element-"+i+" .lepopup-button:active{"+properties['style-attr']+"}";

					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><a class='lepopup-button lepopup-button-"+lepopup_form_options["button-active-transform"]+" "+lepopup_form_elements[i]["css-class"]+"' href='#' onclick='return false;'>"+label+"</a><div class='lepopup-element-cover'></div></div>";
					break;

				case "password":
					style += "#lepopup-element-"+i+" div.lepopup-input .lepopup-icon-left, #lepopup-element-"+i+" div.lepopup-input .lepopup-icon-right {line-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'>"+icon+"<input type='password' class='"+(lepopup_form_elements[i]['align'] != "" ? "lepopup-ta-"+lepopup_form_elements[i]['align']+" " : "")+lepopup_form_elements[i]["css-class"]+"' placeholder='"+lepopup_escape_html(lepopup_form_elements[i]["placeholder"])+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["default"])+"' /></div><div class='lepopup-element-cover'></div></div>";
					break;
					
				case "signature":
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='40' data-resizable-min-width='80' data-resizable-handles='all'><div class='lepopup-input"+extra_class+"'><div class='lepopup-signature-box'><canvas class='lepopup-signature'></canvas><span><i class='lepopup-if lepopup-if-eraser'></i></span></div></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "rangeslider":
					style += "#lepopup-element-"+i+" div.lepopup-input{height:auto;line-height:1;}";
					options = (lepopup_form_elements[i]["readonly"] == "on" ? "data-from-fixed='true' data-to-fixed='true'" : "")+" "+(lepopup_form_elements[i]["double"] == "on" ? "data-type='double'" : "data-type='single'")+" "+(lepopup_form_elements[i]["grid-enable"] == "on" ? "data-grid='true'" : "data-grid='false'")+" "+(lepopup_form_elements[i]["min-max-labels"] == "on" ? "data-hide-min-max='false'" : "data-hide-min-max='true'")+" data-skin='"+lepopup_form_options['rangeslider-skin']+"' data-min='"+lepopup_form_elements[i]["range-value1"]+"' data-max='"+lepopup_form_elements[i]["range-value2"]+"' data-step='"+lepopup_form_elements[i]["range-value3"]+"' data-from='"+lepopup_form_elements[i]["handle"]+"' data-to='"+lepopup_form_elements[i]["handle2"]+"' data-prefix='"+lepopup_form_elements[i]["prefix"]+"' data-postfix='"+lepopup_form_elements[i]["postfix"]+"'";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='120' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input lepopup-rangeslider"+extra_class+"'><input type='text' class='lepopup-rangeslider "+lepopup_form_elements[i]["css-class"]+"' value='"+lepopup_escape_html(lepopup_form_elements[i]["handle"])+"' "+options+" /></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "star-rating":
					style += "#lepopup-element-"+i+" div.lepopup-input{height:auto;line-height:1;}";
					if (lepopup_form_elements[i]['star-style-color-unrated'] != "") style += "#lepopup-element-"+i+" .lepopup-star-rating>label{color:"+lepopup_form_elements[i]['star-style-color-unrated']+" !important;}";
					if (lepopup_form_elements[i]['star-style-color-rated'] != "") style += "#lepopup-element-"+i+" .lepopup-star-rating>input:checked~label, #lepopup-element-"+i+" .lepopup-star-rating:not(:checked)>label:hover, #lepopup-element-"+i+" .lepopup-star-rating:not(:checked)>label:hover~label{color:"+lepopup_form_elements[i]['star-style-color-rated']+" !important;}";
					options = "";
					for (var j=lepopup_form_elements[i]['total-stars']; j>0; j--) {
						options += "<input type='radio' id='lepopup-stars-"+i+"-"+j+"' name='lepopup-stars-"+i+"' value='"+j+"'"+(lepopup_form_elements[i]['default'] == j ? " checked='checked'" : "")+" /><label for='lepopup-stars-"+i+"-"+j+"'></label>";
					}
					extra_class = "";
					if (lepopup_form_elements[i]['star-style-size'] != "") extra_class += " lepopup-star-rating-"+lepopup_form_elements[i]['star-style-size'];
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='120' data-resizable-handles='e,w' data-resizable-auto-height='on'><div class='lepopup-input'><form><fieldset class='lepopup-star-rating"+extra_class+"'>"+options+"</fieldset></form></div><div class='lepopup-element-cover'></div></div>";
					break;
					
				case "html":
					text_style = lepopup_build_style_text(lepopup_form_elements[i], "text-style");
					if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
					style_attr = text_style["style"];
					style += "#lepopup-element-"+i+" *{"+style_attr+"}";
					style_attr += lepopup_build_style_background(lepopup_form_elements[i], "background-style");
					style_attr += lepopup_build_style_border(lepopup_form_elements[i], "border-style");
					style_attr += lepopup_build_shadow(lepopup_form_elements[i], "shadow");
					style += "#lepopup-element-"+i+"{"+style_attr+"}";
					style_attr = lepopup_build_style_padding(lepopup_form_elements[i], "padding");
					style += "#lepopup-element-"+i+" .lepopup-element-html-content {min-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;"+style_attr+"}";
					content = lepopup_form_elements[i]["content"];
					div = document.createElement('div');
					div.innerHTML = content;
					content = div.innerHTML;
					content = content.replace("autoplay=1", "");
					var script_regex = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;
					while (script_regex.test(content)) {
						content = content.replace(script_regex, "");
					}					
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-html' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='40' data-resizable-handles='all'><div class='lepopup-element-html-content'>"+content+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "video":
					style += "#lepopup-element-"+i+" .lepopup-element-html-content {min-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					content = lepopup_form_elements[i]["content"];
					var video_html;
					var iframe_regex = /<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi;
					var iframe_html = content.match(iframe_regex);
					if (iframe_html && iframe_html.length > 0) {
						properties['iframe-attr'] = "style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;'";
						video_html = jQuery.parseHTML(iframe_html[0]);
						jQuery(video_html).each(function(o, el){
							for (var j=0; j<el.attributes.length; j++) {
								if (el.attributes[j].name.toLowerCase() != 'style' && el.attributes[j].name.toLowerCase() != 'width' && el.attributes[j].name.toLowerCase() != 'height') {
									properties['iframe-attr'] += " "+el.attributes[j].name+"='"+el.attributes[j].value+"'";
								}
							}
							return false;
						});
						content = "<iframe "+properties['iframe-attr']+"></iframe>";
						content = content.replace("autoplay=1", "");
					} else {
						iframe_regex = /<video\b[^<]*(?:(?!<\/video>)<[^<]*)*<\/video>/gi;
						iframe_html = content.match(iframe_regex);
						if (iframe_html && iframe_html.length > 0) {
							properties['video-attr'] = "style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;'";
							video_html = jQuery.parseHTML(iframe_html[0]);
							jQuery(video_html).each(function(o, el){
								for (var j=0; j<el.attributes.length; j++) {
								if (el.attributes[j].name.toLowerCase() != 'style' && el.attributes[j].name.toLowerCase() != 'width' && el.attributes[j].name.toLowerCase() != 'height') {
										properties['video-attr'] += " "+el.attributes[j].name+"='"+el.attributes[j].value+"'";
									}
								}
								return false;
							});
							properties['video-children'] = "";
							iframe_html = /<video[^>]*>(.*?)<\/video>/gi.exec(content);
							if (iframe_html && iframe_html.length > 1) properties['video-children'] = iframe_html[1];
							content = "<video "+properties['video-attr']+">"+properties['video-children']+"</video>";
							content = content.replace("autoplay", "");
						} else {
							try {
								var url = new URL(content);
								content = "";
								if (url.host == "youtu.be" || url.host == "www.youtu.be") content = "<iframe style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' src='https://www.youtube.com/embed"+url.pathname+"'></iframe>";
								else if (url.host == "youtube.com" || url.host == "www.youtube.com") {
									var url_params = new URLSearchParams(url.search);
									var video_id = url_params.get('v');
									if (video_id) content = "<iframe style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' src='https://www.youtube.com/embed/"+video_id+"'></iframe>";
								} else if (url.host == "vimeo.com" || url.host == "www.vimeo.com") {
									if (url.pathname.length > 1) {
										var video_id = url.pathname.substring(1);
										if (lepopup_is_numeric(video_id)) content = "<iframe style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' src='https://player.vimeo.com/video"+url.pathname+"'></iframe>";
									}
								} else {
									content = "<video style='height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' src='"+lepopup_form_elements[i]["content"]+"'></video>";
								}
							} catch (_) {
								content = "";
							}
						}
					}
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-html' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='40' data-resizable-handles='all'><div class='lepopup-element-html-content'>"+content+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "label":
					text_style = lepopup_build_style_text(lepopup_form_elements[i], "text-style");
					if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
					style_attr = text_style["style"];
					style += "#lepopup-element-"+i+" *{"+style_attr+"}";
					content = lepopup_form_elements[i]["content"];
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-html' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='20' data-resizable-min-width='40' data-resizable-handles='all'><div class='lepopup-element-html-content'>"+lepopup_escape_html(content)+"</div><div class='lepopup-element-cover'></div></div>";
					break;

				case "rectangle":
					style_attr = lepopup_build_style_background(lepopup_form_elements[i], "background-style");
					style_attr += lepopup_build_style_border(lepopup_form_elements[i], "border-style");
					style_attr += lepopup_build_shadow(lepopup_form_elements[i], "shadow");
					style += "#lepopup-element-"+i+"{"+style_attr+"}";
					style += "#lepopup-element-"+i+" .lepopup-element-html-content {min-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-rectangle' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='1' data-resizable-min-width='1' data-resizable-handles='all'><div class='lepopup-element-html-content'></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "image":
					style_attr = lepopup_build_style_background(lepopup_form_elements[i], "image-style");
					style_attr += lepopup_build_style_border(lepopup_form_elements[i], "border-style");
					style_attr += lepopup_build_shadow(lepopup_form_elements[i], "shadow");
					style += "#lepopup-element-"+i+"{"+style_attr+"}";
					style += "#lepopup-element-"+i+" .lepopup-element-html-content {min-height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-rectangle' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;height:"+lepopup_escape_html(lepopup_form_elements[i]['size-height'])+"px;' data-resizable-min-height='1' data-resizable-min-width='1' data-resizable-handles='all'><div class='lepopup-element-html-content'></div><div class='lepopup-element-cover'></div></div>";
					break;

				case "close":
					if (lepopup_form_elements[i]['colors-color3'] != "") properties['shadow'] = "text-shadow:1px 1px 1px "+lepopup_escape_html(lepopup_form_elements[i]['colors-color3'])+";";
					else properties['shadow'] = "";
					if (lepopup_form_elements[i]['colors-color1'] != "") properties['main-color'] = "color:"+lepopup_escape_html(lepopup_form_elements[i]['colors-color1'])+";";
					else properties['main-color'] = "";
					if (lepopup_form_elements[i]['colors-color2'] != "") properties['hover-color'] = "color:"+lepopup_escape_html(lepopup_form_elements[i]['colors-color2'])+";";
					else properties['hover-color'] = "";
					style += "#lepopup-element-"+i+" span {font-size:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;"+properties['main-color']+properties['shadow']+"}";
					style += "#lepopup-element-"+i+" span i {"+properties['main-color']+"}";
					style += "#lepopup-element-"+i+" span:hover, #lepopup-element-"+i+" span:hover i {"+properties['hover-color']+"}";
					if (lepopup_form_elements[i]['view'] == "fa-1") properties['view'] = '<i class="lepopup-if lepopup-if-times"></i>';
					else if (lepopup_form_elements[i]['view'] == "fa-2") properties['view'] = '<i class="lepopup-if lepopup-if-cancel-circled"></i>';
					else if (lepopup_form_elements[i]['view'] == "fa-3") properties['view'] = '<i class="lepopup-if lepopup-if-cancel-circled2"></i>';
					else properties['view'] = '×';
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-close' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' data-resizable-min-height='20' data-resizable-min-width='20' data-resizable-handles='all' data-resizable-ratio='1'><span>"+properties["view"]+"</span><div class='lepopup-element-cover'></div></div>";
					break;

				case "fa-icon":
					if (lepopup_form_elements[i]['colors-color3'] != "") properties['shadow'] = "text-shadow:1px 1px 1px "+lepopup_escape_html(lepopup_form_elements[i]['colors-color3'])+";";
					else properties['shadow'] = "";
					if (lepopup_form_elements[i]['colors-color1'] != "") properties['main-color'] = "color:"+lepopup_escape_html(lepopup_form_elements[i]['colors-color1'])+";";
					else properties['main-color'] = "";
					if (lepopup_form_elements[i]['colors-color2'] != "") properties['hover-color'] = "color:"+lepopup_escape_html(lepopup_form_elements[i]['colors-color2'])+";";
					else properties['hover-color'] = "";
					style += "#lepopup-element-"+i+" span {font-size:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;"+properties['main-color']+properties['shadow']+"}";
					style += "#lepopup-element-"+i+" span i {"+properties['main-color']+"}";
					style += "#lepopup-element-"+i+" span:hover, #lepopup-element-"+i+" span:hover i {"+properties['hover-color']+"}";
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-icon' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;' data-resizable-min-height='10' data-resizable-min-width='10' data-resizable-handles='all' data-resizable-ratio='1'><span><i class='"+(lepopup_form_elements[i]['icon'] == "" ? "lepopup-fa-noicon" : lepopup_form_elements[i]['icon'])+"'></i></span><div class='lepopup-element-cover'></div></div>";
					break;
				
				case "progress":
					properties["progress"] = _lepopup_update_progress(lepopup_form_elements[i]["_parent"]);
					html += "<div id='lepopup-element-"+i+"' class='lepopup-element-"+i+" lepopup-element lepopup-element-progress' data-type='"+lepopup_form_elements[i]["type"]+"' style='z-index:"+parseInt(zindex_base+parseInt(seqs[k], 10), 10)+";top:"+lepopup_escape_html(lepopup_form_elements[i]['position-top'])+"px;left:"+lepopup_escape_html(lepopup_form_elements[i]['position-left'])+"px;"+(lepopup_is_numeric(lepopup_form_elements[i]['size-width']) ? "width:"+lepopup_escape_html(lepopup_form_elements[i]['size-width'])+"px;" : "")+"' data-resizable-min-height='20' data-resizable-min-width='120' data-resizable-handles='e,w' data-resizable-auto-height='on'>"+properties["progress"]+"<div class='lepopup-element-cover'></div></div>";
					break;
					
				default:
					break;
			}
		}
		if (lepopup_form_elements[i].hasOwnProperty("css") && lepopup_form_elements[i]["css"].length > 0) {
			if (lepopup_meta.hasOwnProperty(lepopup_form_elements[i]["type"]) && lepopup_meta[lepopup_form_elements[i]["type"]].hasOwnProperty("css")) {
				for (var j=0; j<lepopup_form_elements[i]["css"].length; j++) {
					if (lepopup_form_elements[i]["css"][j]["css"] != "" && lepopup_form_elements[i]["css"][j]["selector"] != "") {
						if (lepopup_meta[lepopup_form_elements[i]["type"]]["css"]["selectors"].hasOwnProperty(lepopup_form_elements[i]["css"][j]["selector"])) {
							properties["css-class"] = lepopup_meta[lepopup_form_elements[i]["type"]]["css"]["selectors"][lepopup_form_elements[i]["css"][j]["selector"]]["admin-class"];
							properties["css-class"] = properties["css-class"].replace(new RegExp("{element-id}", 'g'), i);
							style += properties["css-class"]+"{"+lepopup_form_elements[i]["css"][j]["css"]+"}";
						}
					}
				}
			}
		}
		global_html += "<div class='lepopup-element-style' id='lepopup-element-style-"+i+"'><style>"+style+"</style></div>"+html;
	}
	return {"html" : global_html, "style" : "", "webfonts" : webfonts};
}

function lepopup_build_style_text(_properties, _key) {
	var style = "", webfont = "";
	var integer;
	if (_properties.hasOwnProperty(_key+"-family") && _properties[_key+"-family"] != "") {
		style += "font-family:'"+_properties[_key+"-family"]+"','arial';";
		if (lepopup_localfonts.indexOf(_properties[_key+"-family"]) == -1) webfont = _properties[_key+"-family"];
	}
	if (_properties.hasOwnProperty(_key+"-size")) {
		integer = parseInt(_properties[_key+"-size"], 10);
		if (integer >= 8 && integer <= 256) style += "font-size:"+integer+"px;";
	}
	if (_properties.hasOwnProperty(_key+"-color") && _properties[_key+"-color"] != "") style += "color:"+_properties[_key+"-color"]+";";
	if (!_properties.hasOwnProperty(_key+"-weight") || _properties[_key+"-weight"] == "") {
		if (_properties.hasOwnProperty(_key+"-bold") && _properties[_key+"-bold"] == "on") style += "font-weight:bold;";
		else style += "font-weight:normal;";
	} else if (_properties[_key+"-weight"] != "inherit") style += "font-weight:"+_properties[_key+"-weight"]+";";
	if (_properties.hasOwnProperty(_key+"-italic") && _properties[_key+"-italic"] == "on") style += "font-style:italic;";
	else style += "font-style:normal;";
	if (_properties.hasOwnProperty(_key+"-underline") && _properties[_key+"-underline"] == "on") style += "text-decoration:underline;";
	else style += "text-decoration:none;";
	if (_properties.hasOwnProperty(_key+"-align") && _properties[_key+"-align"] != "") style += "text-align:"+_properties[_key+"-align"]+";";
	return {"style" : style, "webfont" : webfont};
}
function lepopup_build_style_background(_properties, _key) {
	var style = "";
	var integer, hposition = "left", vposition = "top";
	var direction = "to bottom", color1 = "transparent", color2 = "transparent";
	if (_properties.hasOwnProperty(_key+"-color") && _properties[_key+"-color"] != "") color1 = _properties[_key+"-color"];
	
	if (_properties.hasOwnProperty(_key+"-gradient") && _properties[_key+"-gradient"] == "2shades") {
		style += "background-color:"+color1+"; background-image:linear-gradient(to bottom,rgba(255,255,255,.05) 0,rgba(255,255,255,.05) 50%,rgba(0,0,0,.05) 51%,rgba(0,0,0,.05) 100%);";
	} else if (_properties.hasOwnProperty(_key+"-gradient") && (_properties[_key+"-gradient"] == "horizontal" || _properties[_key+"-gradient"] == "vertical" || _properties[_key+"-gradient"] == "diagonal")) {
		if (_properties.hasOwnProperty(_key+"-color2") && _properties[_key+"-color2"] != "") color2 = _properties[_key+"-color2"];
		if (_properties[_key+"-gradient"] == "horizontal") direction = "to right";
		else if (_properties[_key+"-gradient"] == "diagonal") direction = "to bottom right";
		style += "background-image:linear-gradient("+direction+","+color1+","+color2+");";
	} else if (_properties.hasOwnProperty(_key+"-image") && _properties[_key+"-image"] != "") {
		style += "background-color:"+color1+"; background-image:url('"+_properties[_key+"-image"]+"');";
		if (_properties.hasOwnProperty(_key+"-size") && _properties[_key+"-size"] != "") style += "background-size:"+_properties[_key+"-size"]+";";
		if (_properties.hasOwnProperty(_key+"-repeat") && _properties[_key+"-repeat"] != "") style += "background-repeat:"+_properties[_key+"-repeat"]+";";
		if (_properties.hasOwnProperty(_key+"-horizontal-position") && _properties[_key+"-horizontal-position"] != "") {
			switch (_properties[_key+"-horizontal-position"]) {
				case 'center':
					hposition = "center";
					break;
				case 'right':
					hposition = "right";
					break;
				default:
					hposition = "left";
					break;
			}
		}
		if (_properties.hasOwnProperty(_key+"-vertical-position") && _properties[_key+"-vertical-position"] != "") {
			switch (_properties[_key+"-vertical-position"]) {
				case 'center':
					vposition = "center";
					break;
				case 'bottom':
					vposition = "bottom";
					break;
				default:
					vposition = "top";
					break;
			}
		}
		style += "background-position: "+hposition+" "+vposition+";";
	} else style += "background-color:"+color1+"; background-image:none;";
	return style;
}
function lepopup_build_style_border(_properties, _key) {
	var style = "";
	var integer;
	if (_properties.hasOwnProperty(_key+"-width")) {
		integer = parseInt(_properties[_key+"-width"], 10);
		if (integer >= 0 && integer <= 16) style += "border-width:"+integer+"px;";
	}
	if (_properties.hasOwnProperty(_key+"-style") && _properties[_key+"-style"] != "") style += "border-style:"+_properties[_key+"-style"]+";";
	if (_properties.hasOwnProperty(_key+"-color") && _properties[_key+"-color"] != "") style += "border-color:"+_properties[_key+"-color"]+";";
	else style += "border-color:transparent;";
	if (_properties.hasOwnProperty(_key+"-radius")) {
		if (_properties[_key+"-radius"] == "max") {
			style += "border-radius:800px;";
		} else {
			integer = parseInt(_properties[_key+"-radius"], 10);
			if (integer >= 0 && integer <= 100) style += "border-radius:"+integer+"px;";
		}
	}
	if (_properties.hasOwnProperty(_key+"-top") && _properties[_key+"-top"] != "on") style += "border-top:none !important;";
	if (_properties.hasOwnProperty(_key+"-left") && _properties[_key+"-left"] != "on") style += "border-left:none !important;";
	if (_properties.hasOwnProperty(_key+"-right") && _properties[_key+"-right"] != "on") style += "border-right:none !important;";
	if (_properties.hasOwnProperty(_key+"-bottom") && _properties[_key+"-bottom"] != "on") style += "border-bottom:none !important;";
	return style;
}
function lepopup_build_shadow(_properties, _key) {
	var style = "box-shadow:none;";
	var color = "transparent";
	var shadow_style = "regular";
	if (_properties.hasOwnProperty(_key+"-size") && _properties[_key+"-size"] != "") {
		if (_properties.hasOwnProperty(_key+"-color") && _properties[_key+"-color"] != "") color = _properties[_key+"-color"];
		if (_properties.hasOwnProperty(_key+"-style") && _properties[_key+"-style"] != "") shadow_style = _properties[_key+"-style"];
		switch (shadow_style) {
			case 'solid':
				if (_properties[_key+"-size"] == "tiny") style = "box-shadow: 1px 1px 0px 0px "+color+";";
				else if (_properties[_key+"-size"] == "small") style = "box-shadow: 2px 2px 0px 0px "+color+";";
				else if (_properties[_key+"-size"] == "medium") style = "box-shadow: 4px 4px 0px 0px "+color+";";
				else if (_properties[_key+"-size"] == "large") style = "box-shadow: 6px 6px 0px 0px "+color+";";
				else if (_properties[_key+"-size"] == "huge") style = "box-shadow: 8px 8px 0px 0px "+color+";";
				break;
			case 'inset':
				if (_properties[_key+"-size"] == "tiny") style = "box-shadow: inset 0px 0px 15px -9px "+color+";";
				else if (_properties[_key+"-size"] == "small") style = "box-shadow: inset 0px 0px 15px -8px "+color+";";
				else if (_properties[_key+"-size"] == "medium") style = "box-shadow: inset 0px 0px 15px -7px "+color+";";
				else if (_properties[_key+"-size"] == "large") style = "box-shadow: inset 0px 0px 15px -6px "+color+";";
				else if (_properties[_key+"-size"] == "huge") style = "box-shadow: inset 0px 0px 15px -5px "+color+";";
				break;
			default:
				if (_properties[_key+"-size"] == "tiny") style = "box-shadow: 1px 1px 15px -9px "+color+";";
				else if (_properties[_key+"-size"] == "small") style = "box-shadow: 1px 1px 15px -8px "+color+";";
				else if (_properties[_key+"-size"] == "medium") style = "box-shadow: 1px 1px 15px -6px "+color+";";
				else if (_properties[_key+"-size"] == "large") style = "box-shadow: 1px 1px 15px -3px "+color+";";
				else if (_properties[_key+"-size"] == "huge") style = "box-shadow: 1px 1px 15px -0px "+color+";";
				break;
		}
	}
	return style;
}
function lepopup_build_style_padding(_properties, _key) {
	var style = "";
	var integer;
	if (_properties.hasOwnProperty(_key+"-top")) {
		integer = parseInt(_properties[_key+"-top"], 10);
		if (integer >= 0 && integer <= 300) style += "padding-top:"+integer+"px;";
	}
	if (_properties.hasOwnProperty(_key+"-right")) {
		integer = parseInt(_properties[_key+"-right"], 10);
		if (integer >= 0 && integer <= 300) style += "padding-right:"+integer+"px;";
	}
	if (_properties.hasOwnProperty(_key+"-bottom")) {
		integer = parseInt(_properties[_key+"-bottom"], 10);
		if (integer >= 0 && integer <= 300) style += "padding-bottom:"+integer+"px;";
	}
	if (_properties.hasOwnProperty(_key+"-left")) {
		integer = parseInt(_properties[_key+"-left"], 10);
		if (integer >= 0 && integer <= 300) style += "padding-left:"+integer+"px;";
	}
	return style;
}

function lepopup_update_progress() {
	jQuery(".lepopup-element-progress").each(function(){
		var page = jQuery(this).closest(".lepopup-form").attr("_data-parent");
		if (typeof page != typeof undefined) {
			var html = _lepopup_update_progress(page);
			jQuery(this).html(html);
		}
	});
}
function _lepopup_update_progress(_page_id) {
	var html = "";
	var page_name = "";
	var pages = ".lepopup-pages-bar-item";
	if (lepopup_form_options["progress-confirmation-enable"] == "on") pages += ",.lepopup-pages-bar-item-confirmation";
	var total_pages = jQuery(pages).length;
	var idx = 0;
	jQuery(pages).each(function(){
		var page_id = jQuery(this).attr("data-id");
		if (page_id == _page_id) return false;
		idx++;
	});
	var page = jQuery(".lepopup-pages-bar-item[data-id='"+_page_id+"'], .lepopup-pages-bar-item-confirmation[data-id='"+_page_id+"']");
	if (jQuery(page).length > 0) {
		if (lepopup_form_options["progress-type"] == 'progress-2') {
			html = "<div class='lepopup-progress lepopup-progress-"+_page_id+"'><ul class='lepopup-progress-t2"+(lepopup_form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")+"'>";
			var i = 0;
			jQuery(pages).each(function() {
				page_name = jQuery(this).attr("data-name");
				html += "<li"+(i < idx ? " class='lepopup-progress-t2-passed'" : (i == idx ? " class='lepopup-progress-t2-active'" : ""))+" style='width:"+(Math.floor(10000/total_pages)/100)+"%;'><span>"+(i+1)+"</span>"+(lepopup_form_options["progress-label-enable"] == "on" ? "<label>"+lepopup_escape_html(page_name)+"</label>" : "")+"</li>";
				i++;
			});
			html += "</ul></div>";
		} else {
			page_name = jQuery(page).attr("data-name");
			var width = parseInt(Math.round(100*(idx+1)/total_pages), 10);
			html = "<div class='lepopup-progress lepopup-progress-"+lepopup_form_options["progress-position"]+"' id='lepopup-progress-"+_page_id+"'><div class='lepopup-progress-t1"+(lepopup_form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")+"'><div><div style='width:"+width+"%'>"+width+"%</div></div>"+(lepopup_form_options["progress-label-enable"] == "on" ? "<label>"+lepopup_escape_html(page_name)+"</label>" : "")+"</div></div>";
		}
	}
	return html;
}

function lepopup_build() {
	var adminbar_height;
	if (jQuery("#wpadminbar").length > 0) adminbar_height = parseInt(jQuery("#wpadminbar").height(), 10);
	else adminbar_height = 0;
	var text_style, style_attr, style = "";
	var webfonts = new Array();
	var width, height;
	jQuery(".lepopup-form .lepopup-elements").html("");
	jQuery(".lepopup-form .lepopup-hidden-elements").html("");
	jQuery(".lepopup-form").attr("class", jQuery(".lepopup-form").attr("class").replace(/\blepopup-form-icon-[a-z]+\b/g, ""));
	jQuery(".lepopup-form").addClass("lepopup-form-icon-"+lepopup_form_options["input-icon-position"]);

	if (lepopup_form_options["progress-type"] == 'progress-2') {
		if (lepopup_form_options.hasOwnProperty("progress-color-color1") && lepopup_form_options['progress-color-color1'] != "") style += "ul.lepopup-progress-t2,ul.lepopup-progress-t2>li>span{background-color:"+lepopup_form_options['progress-color-color1']+";}ul.lepopup-progress-t2>li>label{color:"+lepopup_form_options['progress-color-color1']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color2") && lepopup_form_options['progress-color-color2'] != "") style += "ul.lepopup-progress-t2>li.lepopup-progress-t2-active>span,ul.lepopup-progress-t2>li.lepopup-progress-t2-passed>span{background-color:"+lepopup_form_options['progress-color-color2']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color3") && lepopup_form_options['progress-color-color3'] != "") style += "ul.lepopup-progress-t2>li>span{color:"+lepopup_form_options['progress-color-color3']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color4") && lepopup_form_options['progress-color-color4'] != "") style += "ul.lepopup-progress-t2>li.lepopup-progress-t2-active>label{color:"+lepopup_form_options['progress-color-color4']+";}";
	} else {
		if (lepopup_form_options.hasOwnProperty("progress-color-color1") && lepopup_form_options['progress-color-color1'] != "") style += "div.lepopup-progress-t1>div{background-color:"+lepopup_form_options['progress-color-color1']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color2") && lepopup_form_options['progress-color-color2'] != "") style += "div.lepopup-progress-t1>div>div{background-color:"+lepopup_form_options['progress-color-color2']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color3") && lepopup_form_options['progress-color-color3'] != "") style += "div.lepopup-progress-t1>div>div{color:"+lepopup_form_options['progress-color-color3']+";}";
		if (lepopup_form_options.hasOwnProperty("progress-color-color4") && lepopup_form_options['progress-color-color4'] != "") style += "div.lepopup-progress-t1>label{color:"+lepopup_form_options['progress-color-color4']+";}";
	}
	style += ".lepopup-progress{max-width:"+lepopup_form_options["max-width-value"]+lepopup_form_options["max-width-unit"]+";}";

	text_style = lepopup_build_style_text(lepopup_form_options, "text-style");
	if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
	style_attr = text_style["style"];
	style += ".lepopup-form *, .lepopup-progress {"+style_attr+"}";

	text_style = lepopup_build_style_text(lepopup_form_options, "input-text-style");
	if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
	style_attr = text_style["style"];
	//style += ".lepopup-element div.lepopup-input div.lepopup-signature-box span i{"+style_attr+"}";
	style_attr += lepopup_build_style_background(lepopup_form_options, "input-background-style");
	style_attr += lepopup_build_style_border(lepopup_form_options, "input-border-style");
	style_attr += lepopup_build_shadow(lepopup_form_options, "input-shadow");
	style += ".lepopup-element div.lepopup-input div.lepopup-signature-box,.lepopup-element div.lepopup-input div.lepopup-signature-box,.lepopup-element div.lepopup-input div.lepopup-multiselect,.lepopup-element div.lepopup-input input[type='text'],.lepopup-element div.lepopup-input input[type='email'],.lepopup-element div.lepopup-input input[type='password'],.lepopup-element div.lepopup-input select,.lepopup-element div.lepopup-input select option,.lepopup-element div.lepopup-input textarea{"+style_attr+"}";
	style += ".lepopup-element div.lepopup-input ::placeholder{color:"+lepopup_form_options['input-text-style-color']+"; opacity: 0.9;}";
	style += ".lepopup-element div.lepopup-input div.lepopup-multiselect::-webkit-scrollbar-thumb{background-color:"+lepopup_form_options["input-border-style-color"]+";}"
	if (lepopup_form_options["input-hover-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "input-hover-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "input-hover-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "input-hover-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "input-hover-shadow");
		style += ".lepopup-element div.lepopup-input input[type='text']:hover,.lepopup-element div.lepopup-input input[type='email']:hover,.lepopup-element div.lepopup-input input[type='password']:hover,.lepopup-element div.lepopup-input select:hover,.lepopup-element div.lepopup-input select:hover option,.lepopup-element div.lepopup-input textarea:hover{"+style_attr+"}";
	}
	if (lepopup_form_options["input-focus-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "input-focus-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "input-focus-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "input-focus-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "input-focus-shadow");
		style += ".lepopup-element div.lepopup-input input[type='text']:focus,.lepopup-element div.lepopup-input input[type='email']:focus,.lepopup-element div.lepopup-input input[type='password']:focus,.lepopup-element div.lepopup-input select:focus,.lepopup-element div.lepopup-input select:focus option,.lepopup-element div.lepopup-input textarea:focus{"+style_attr+"}";
	}

	style_attr = lepopup_build_style_border(lepopup_form_options, "imageselect-border-style");
	style_attr += lepopup_build_shadow(lepopup_form_options, "imageselect-shadow");
	style += ".lepopup-element div.lepopup-input .lepopup-imageselect+label{"+style_attr+"}";
	if (lepopup_form_options["imageselect-hover-inherit"] == "off") {
		style_attr = lepopup_build_style_border(lepopup_form_options, "imageselect-hover-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "imageselect-hover-shadow");
		style += ".lepopup-element div.lepopup-input .lepopup-imageselect+label:hover{"+style_attr+"}";
	}
	if (lepopup_form_options["imageselect-selected-inherit"] == "off") {
		style_attr = lepopup_build_style_border(lepopup_form_options, "imageselect-selected-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "imageselect-selected-shadow");
		style += ".lepopup-element div.lepopup-input .lepopup-imageselect:checked+label{"+style_attr+"}";
	}
	text_style = lepopup_build_style_text(lepopup_form_options, "imageselect-text-style");
	if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
	style += ".lepopup-element div.lepopup-input .lepopup-imageselect+label span.lepopup-imageselect-label{"+text_style["style"]+"}";

	style_attr = "";
	if (lepopup_form_options["input-icon-size"] != "") {
		style_attr += "font-size:"+lepopup_form_options["input-icon-size"]+"px;";
	}
	if (lepopup_form_options["input-icon-color"] != "") {
		style_attr += "color:"+lepopup_form_options["input-icon-color"]+";";
	}
	if (lepopup_form_options["input-icon-position"] != "outside") {
		if (lepopup_form_options["input-icon-background"] != "") {
			style_attr += "background:"+lepopup_form_options["input-icon-background"]+";";
		}
		if (lepopup_form_options["input-icon-border"] != "") {
			style_attr += "border-color:"+lepopup_form_options["input-icon-border"]+";border-style:solid;";
			if (lepopup_form_options.hasOwnProperty("input-border-style-width")) {
				integer = parseInt(lepopup_form_options["input-border-style-width"], 10);
				if (integer >= 0 && integer <= 16) style_attr += "border-width:"+integer+"px;";
			}
		}
		if (lepopup_form_options.hasOwnProperty("input-border-style-radius")) {
			var integer = parseInt(lepopup_form_options["input-border-style-radius"], 10);
			if (integer >= 0 && integer <= 100) style_attr += "border-radius:"+integer+"px;";
		}
		if (lepopup_form_options["input-icon-background"] != "" || lepopup_form_options["input-icon-border"] != "") {
			style += "div.lepopup-input.lepopup-icon-left input[type='text'], div.lepopup-input.lepopup-icon-left input[type='email'],div.lepopup-input.lepopup-icon-left input[type='password'],div.lepopup-input.lepopup-icon-left textarea {padding-left: 56px !important;}";
			style += "div.lepopup-input.lepopup-icon-right input[type='text'], div.lepopup-input.lepopup-icon-right input[type='email'],div.lepopup-input.lepopup-icon-right input[type='password'],div.lepopup-input.lepopup-icon-right textarea {padding-right: 56px !important;}";
		}
	}
	if (style_attr != "") {
		style += "div.lepopup-input>i.lepopup-icon-left, div.lepopup-input>i.lepopup-icon-right {"+style_attr+"}";
	}
	
	text_style = lepopup_build_style_text(lepopup_form_options, "button-text-style");
	if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
	style_attr = text_style["style"];
	style_attr += lepopup_build_style_background(lepopup_form_options, "button-background-style");
	style_attr += lepopup_build_style_border(lepopup_form_options, "button-border-style");
	style_attr += lepopup_build_shadow(lepopup_form_options, "button-shadow");
	style += ".lepopup-element .lepopup-button{"+style_attr+"}";
	if (lepopup_form_options["button-hover-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "button-hover-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "button-hover-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "button-hover-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "button-hover-shadow");
		style += ".lepopup-element .lepopup-button:hover,.lepopup-element .lepopup-button:focus{"+style_attr+"}";
	}
	if (lepopup_form_options["button-active-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "button-active-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "button-active-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "button-active-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "button-active-shadow");
		style += ".lepopup-element .lepopup-button:active{"+style_attr+"}";
	}

	text_style = lepopup_build_style_text(lepopup_form_options, "tile-text-style");
	if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
	style_attr = text_style["style"];
	style_attr += lepopup_build_style_background(lepopup_form_options, "tile-background-style");
	style_attr += lepopup_build_style_border(lepopup_form_options, "tile-border-style");
	style_attr += lepopup_build_shadow(lepopup_form_options, "tile-shadow");
	style += ".lepopup-element input[type='checkbox'].lepopup-tile+label,.lepopup-element input[type='radio'].lepopup-tile+label{"+style_attr+"}";
	if (lepopup_form_options["tile-hover-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "tile-hover-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "tile-hover-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "tile-hover-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "tile-hover-shadow");
		style += ".lepopup-element input[type='checkbox'].lepopup-tile+label:hover,.lepopup-element input[type='radio'].lepopup-tile+label:hover{"+style_attr+"}";
	}
	if (lepopup_form_options["tile-selected-inherit"] == "off") {
		text_style = lepopup_build_style_text(lepopup_form_options, "tile-selected-text-style");
		if (text_style["webfont"] != "" && webfonts.indexOf(text_style["webfont"]) == -1) webfonts.push(text_style["webfont"]);
		style_attr = text_style["style"];
		style_attr += lepopup_build_style_background(lepopup_form_options, "tile-selected-background-style");
		style_attr += lepopup_build_style_border(lepopup_form_options, "tile-selected-border-style");
		style_attr += lepopup_build_shadow(lepopup_form_options, "tile-selected-shadow");
		style += ".lepopup-element input[type='checkbox'].lepopup-tile:checked+label,.lepopup-element input[type='radio'].lepopup-tile:checked+label{"+style_attr+"}";
	}

	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color2") && lepopup_form_options["checkbox-radio-unchecked-color-color2"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color2"]+";";
	else style_attr += "background-color:transparent;";
	style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label:after{"+style_attr+"}";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color1") && lepopup_form_options["checkbox-radio-unchecked-color-color1"] != "") style_attr += "border-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color1"]+";";
	else style_attr += "border-color:transparent;";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color3") && lepopup_form_options["checkbox-radio-unchecked-color-color3"] != "") style_attr += "color:"+lepopup_form_options["checkbox-radio-unchecked-color-color3"]+";";
	else style_attr += "color:#ccc;";
	style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-classic+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-fa-check+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl+label{"+style_attr+"}";
	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color3") && lepopup_form_options["checkbox-radio-unchecked-color-color3"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color3"]+";";
	else style_attr += "background-color:#ccc;";
	style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label:after{"+style_attr+"}";
	style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl+label:after{"+style_attr+"}";
	if (lepopup_form_options["checkbox-radio-checked-inherit"] == "off") {
		style_attr = "";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color2") && lepopup_form_options["checkbox-radio-checked-color-color2"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-checked-color-color2"]+";";
		else style_attr += "background-color:transparent;";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color1") && lepopup_form_options["checkbox-radio-checked-color-color1"] != "") style_attr += "border-color:"+lepopup_form_options["checkbox-radio-checked-color-color1"]+";";
		else style_attr += "border-color:transparent;";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color3") && lepopup_form_options["checkbox-radio-checked-color-color3"] != "") style_attr += "color:"+lepopup_form_options["checkbox-radio-checked-color-color3"]+";";
		else style_attr += "color:#ccc;";
		style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-classic:checked+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-fa-check:checked+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label,.lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label{"+style_attr+"}";
		style_attr = "";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color3") && lepopup_form_options["checkbox-radio-checked-color-color3"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-checked-color-color3"]+";";
		else style_attr += "background-color:#ccc;";
		style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label:after{"+style_attr+"}";
		style += ".lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label:after{"+style_attr+"}";
	}
	
	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color2") && lepopup_form_options["checkbox-radio-unchecked-color-color2"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color2"]+";";
	else style_attr += "background-color:transparent;";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color1") && lepopup_form_options["checkbox-radio-unchecked-color-color1"] != "") style_attr += "border-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color1"]+";";
	else style_attr += "border-color:transparent;";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color3") && lepopup_form_options["checkbox-radio-unchecked-color-color3"] != "") style_attr += "color:"+lepopup_form_options["checkbox-radio-unchecked-color-color3"]+";";
	else style_attr += "color:#ccc;";
	style += ".lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-classic+label,.lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-fa-check+label,.lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot+label{"+style_attr+"}";
	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("checkbox-radio-unchecked-color-color3") && lepopup_form_options["checkbox-radio-unchecked-color-color3"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-unchecked-color-color3"]+";";
	else style_attr += "background-color:#ccc;";
	style += ".lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label:after{"+style_attr+"}";
	if (lepopup_form_options["checkbox-radio-checked-inherit"] == "off") {
		style_attr = "";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color2") && lepopup_form_options["checkbox-radio-checked-color-color2"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-checked-color-color2"]+";";
		else style_attr += "background-color:transparent;";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color1") && lepopup_form_options["checkbox-radio-checked-color-color1"] != "") style_attr += "border-color:"+lepopup_form_options["checkbox-radio-checked-color-color1"]+";";
		else style_attr += "border-color:transparent;";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color3") && lepopup_form_options["checkbox-radio-checked-color-color3"] != "") style_attr += "color:"+lepopup_form_options["checkbox-radio-checked-color-color3"]+";";
		else style_attr += "color:#ccc;";
		style += ".lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-classic:checked+label,.lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-fa-check:checked+label,.lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label{"+style_attr+"}";
		style_attr = "";
		if (lepopup_form_options.hasOwnProperty("checkbox-radio-checked-color-color3") && lepopup_form_options["checkbox-radio-checked-color-color3"] != "") style_attr += "background-color:"+lepopup_form_options["checkbox-radio-checked-color-color3"]+";";
		else style_attr += "background-color:#ccc;";
		style += ".lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label:after{"+style_attr+"}";
	}
	
	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("multiselect-style-hover-background") && lepopup_form_options["multiselect-style-hover-background"] != "") style_attr += "background-color:"+lepopup_form_options['multiselect-style-hover-background']+";";
	if (lepopup_form_options.hasOwnProperty("multiselect-style-hover-color") && lepopup_form_options["multiselect-style-hover-color"] != "") style_attr += "color:"+lepopup_form_options['multiselect-style-hover-color']+";";
	if (style_attr != "") style += ".lepopup-element div.lepopup-input div.lepopup-multiselect>input[type='checkbox']+label:hover{"+style_attr+"}";
	style_attr = "";
	if (lepopup_form_options.hasOwnProperty("multiselect-style-selected-background") && lepopup_form_options["multiselect-style-selected-background"] != "") style_attr += "background-color:"+lepopup_form_options['multiselect-style-selected-background']+";";
	if (lepopup_form_options.hasOwnProperty("multiselect-style-selected-color") && lepopup_form_options["multiselect-style-selected-color"] != "") style_attr += "color:"+lepopup_form_options['multiselect-style-selected-color']+";";
	if (style_attr != "") style += ".lepopup-element div.lepopup-input div.lepopup-multiselect>input[type='checkbox']:checked+label{"+style_attr+"}";

	if (typeof jQuery.fn.ionRangeSlider != typeof undefined && jQuery.fn.ionRangeSlider) {
		if (lepopup_form_options.hasOwnProperty("rangeslider-color-color1") && lepopup_form_options["rangeslider-color-color1"] != "") style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-line, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-min, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-max, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-grid-pol{background-color:"+lepopup_form_options["rangeslider-color-color1"]+" !important;}";
		if (lepopup_form_options.hasOwnProperty("rangeslider-color-color2") && lepopup_form_options["rangeslider-color-color2"] != "") style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-grid-text, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-min, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-max{color:"+lepopup_form_options["rangeslider-color-color2"]+" !important;}";
		if (lepopup_form_options.hasOwnProperty("rangeslider-color-color3") && lepopup_form_options["rangeslider-color-color3"] != "") style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-bar{background-color:"+lepopup_form_options["rangeslider-color-color3"]+" !important;}";
		if (lepopup_form_options.hasOwnProperty("rangeslider-color-color4") && lepopup_form_options["rangeslider-color-color4"] != "") {
			style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to{background-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
			style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single:before, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from:before, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to:before{border-top-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
			switch(lepopup_form_options["rangeslider-skin"]) {
				case 'sharp':
					style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle:hover, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle.state_hover{background-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
					style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle > i:first-child, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle:hover > i:first-child, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle.state_hover > i:first-child{border-top-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
					break;
				case 'round':
					style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle:hover, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle.state_hover{border-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
					break;
				default:
					style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle > i:first-child, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle:hover > i:first-child, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle.state_hover > i:first-child{background-color:"+lepopup_form_options["rangeslider-color-color4"]+" !important;}";
					break;
			}
		}
		if (lepopup_form_options.hasOwnProperty("rangeslider-color-color5") && lepopup_form_options["rangeslider-color-color5"] != "") {
			style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to{color:"+lepopup_form_options["rangeslider-color-color5"]+" !important;}";
			if (lepopup_form_options["rangeslider-skin"] == "round") {
				style += ".lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle:hover, .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle.state_hover{background-color:"+lepopup_form_options["rangeslider-color-color5"]+" !important;}";
			}
		}
	}
	
	var output;
	for (var i=0; i<lepopup_form_pages.length; i++) {
		if (lepopup_form_pages[i] != null) {
			if (lepopup_is_numeric(lepopup_form_pages[i]["size-width"])) width = Math.min(parseInt(lepopup_form_pages[i]["size-width"], 10), 1200);
			else width = Math.min(720, 1200);
			if (lepopup_is_numeric(lepopup_form_pages[i]["size-height"])) height = Math.min(parseInt(lepopup_form_pages[i]["size-height"], 10), 2400);
			else height = Math.min(540, 2400);
			jQuery("#lepopup-form-"+lepopup_form_pages[i]['id']+" .lepopup-basic-frame").width(width);
			jQuery("#lepopup-form-"+lepopup_form_pages[i]['id']+" .lepopup-basic-frame").height(height);
			output = _lepopup_build_children(lepopup_form_pages[i]['id'], null);
			jQuery("#lepopup-form-"+lepopup_form_pages[i]['id']+" .lepopup-elements").append("<style>"+output["style"]+"</style>"+output["html"]);
			webfonts = webfonts.concat(output["webfonts"]);
			output = _lepopup_build_hidden_list(lepopup_form_pages[i]['id']);
			jQuery("#lepopup-form-"+lepopup_form_pages[i]['id']+" .lepopup-hidden-elements").append(output);
		}
	}

	text_style = "";
	for (var i=0; i<webfonts.length; i++) {
		text_style += "<link href='//fonts.googleapis.com/css?family="+webfonts[i].replace(" ", "+")+":100,200,300,400,500,600,700,800,900&subset=arabic,vietnamese,hebrew,thai,bengali,latin,latin-ext,cyrillic,cyrillic-ext,greek' rel='stylesheet' type='text/css'>";
	}
	jQuery(".lepopup-form-global-style").html(text_style+"<style>"+style+"</style>");

	jQuery(".lepopup-form").each(function(){
		var id = jQuery(this).attr("id");
		_lepopup_init_basic_frame("#"+id+" .lepopup-basic-frame");
		_lepopup_init_elements("#"+id+" .lepopup-elements .lepopup-element");
	});
	jQuery(".lepopup-layers").draggable({
		drag: function(event, ui) {
			var limit = jQuery(".lepopup-form-editor").width() - 320;
			if (limit >= 0 && ui.position.left > limit) {
				ui.position.left = limit;
//			} else if (ui.position.left < 0) {
//				ui.position.left = 0;
			}
		}		
	});
	jQuery(".lepopup-layers-list").sortable({
		stop: function() {
			var list = jQuery(this).closest("ul");
			var seq = 0;
			jQuery(this).find("li").each(function(){
				var idx = jQuery(this).attr("data-idx");
				if (lepopup_is_numeric(idx)) {
					idx = parseInt(idx, 10);
					if (lepopup_form_elements.length > idx && lepopup_form_elements[idx] != null) {
						lepopup_form_elements[idx]['_seq'] = seq;
						jQuery("#lepopup-element-"+idx).css({"z-index" : parseInt(500+seq, 10)});
						seq++;
					}
				}
			});
		}
	});

	_lepopup_init_elements_contextmenu(".lepopup-element, .lepopup-hidden-element");
	jQuery(".lepopup-hidden-element").on("click", function(e) {
		e.preventDefault();
		if (lepopup_element_properties_active) {
			lepopup_properties_panel_open(this);
		}
		return false;
	});
}
function _lepopup_init_basic_frame(_selector) {
	jQuery(_selector).resizable({
		grid: [10, 10],
		minHeight: 80,
		minWidth: 80,
		resize: function() {
			var page_id = jQuery(this).parent().attr("_data-parent");
			var width = parseInt(jQuery(this).outerWidth(), 10);
			var height = parseInt(jQuery(this).outerHeight(), 10);
			for (var i=0; i<lepopup_form_pages.length; i++) {
				if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] == page_id) {
					lepopup_form_pages[i]['size-width'] = width;
					lepopup_form_pages[i]['size-height'] = height;
					break;
				}
			}
			lepopup_form_changed = true;
		},
		stop: function() {
			var page_id = jQuery(this).parent().parent().attr("_data-parent");
			var width = parseInt(jQuery(this).outerWidth(), 10);
			var height = parseInt(jQuery(this).outerHeight(), 10);
			for (var i=0; i<lepopup_form_pages.length; i++) {
				if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] == page_id) {
					lepopup_form_pages[i]['size-width'] = width;
					lepopup_form_pages[i]['size-height'] = height;
						break;
				}
			}
			lepopup_form_changed = true;
		}
	});
}
function _lepopup_init_elements_contextmenu(_selector) {
	var adminbar_height;
	if (jQuery("#wpadminbar").length > 0) adminbar_height = parseInt(jQuery("#wpadminbar").height(), 10);
	else adminbar_height = 0;
	jQuery(_selector).on("contextmenu", function(e) {
		e.preventDefault();
		jQuery(".lepopup-context-menu").hide();
		lepopup_context_menu_object = this;
		jQuery(".lepopup-context-menu").css({"top" : (e.pageY - adminbar_height), "left" : e.pageX});
		jQuery(".lepopup-context-menu-multi-page").remove();
		var li_duplicate_pages = new Array();
		var li_move_pages = new Array();
		for (var i=0; i<lepopup_form_pages.length; i++) {
			if (lepopup_form_pages[i] != null && lepopup_form_pages[i]['id'] != "confirmation" && lepopup_form_pages[i]['id'] != lepopup_form_page_active) {
				li_duplicate_pages.push("<li><a href='#' onclick='return lepopup_element_duplicate(lepopup_context_menu_object, "+i+");'>"+lepopup_escape_html(lepopup_form_pages[i]["name"])+"</a></li>");
				li_move_pages.push("<li><a href='#' onclick='return lepopup_element_move(lepopup_context_menu_object, "+i+");'>"+lepopup_escape_html(lepopup_form_pages[i]["name"])+"</a></li>");
			}
		}
		if (li_duplicate_pages.length > 0) {
			jQuery(".lepopup-context-menu-last").after("<li class='lepopup-context-menu-multi-page'><a href='#' onclick='return false;'><i class='fas fa-caret-right'></i><i class='far fa-copy'></i>Duplicate to</a><ul>"+li_duplicate_pages.join("")+"</ul></li><li class='lepopup-context-menu-multi-page'><a href='#' onclick='return false;'><i class='fas fa-caret-right'></i><i class='far fa-arrow-alt-circle-right'></i>Move to</a><ul>"+li_move_pages.join("")+"</ul></li>");
		}
		jQuery(".lepopup-context-menu").removeClass("lepopup-context-menu-high-priority");
		jQuery(".lepopup-context-menu").show();
		return false;
	});
}
function _lepopup_init_elements(_selector) {
	jQuery(_selector).draggable({
		grid: [5, 5], 
		cursor: "move",
		start: function() {
		},
		drag: function() {
			var id = jQuery(this).attr("id");
			id = id.replace("lepopup-element-", "");
			var position = jQuery(this).position();
			position.left = parseInt(position.left, 10);
			position.top = parseInt(position.top, 10);
			lepopup_form_elements[id]['position-top'] = position.top;
			lepopup_form_elements[id]['position-left'] = position.left;
			if (lepopup_element_properties_active) {
				var active_id = jQuery(lepopup_element_properties_active).attr("id");
				if (active_id) {
					active_id = active_id.replace("lepopup-element-", "");
					if (id == active_id) {
						jQuery(".lepopup-properties-panel [name='lepopup-position-top']").val(position.top);
						jQuery(".lepopup-properties-panel [name='lepopup-position-left']").val(position.left);
					}
				}
			}
			lepopup_form_changed = true;
		},
		stop: function() {
			var id = jQuery(this).attr("id");
			id = id.replace("lepopup-element-", "");
			var position = jQuery(this).position();
			position.left = parseInt(position.left, 10);
			position.top = parseInt(position.top, 10);
			lepopup_form_elements[id]['position-top'] = position.top;
			lepopup_form_elements[id]['position-left'] = position.left;
			if (lepopup_element_properties_active) {
				var active_id = jQuery(lepopup_element_properties_active).attr("id");
				if (active_id) {
					active_id = active_id.replace("lepopup-element-", "");
					if (id == active_id) {
						jQuery(".lepopup-properties-panel [name='lepopup-position-top']").val(position.top);
						jQuery(".lepopup-properties-panel [name='lepopup-position-left']").val(position.left);
					}
				}
			}
			lepopup_form_changed = true;
		}
	});
	jQuery(_selector).each(function(){
		jQuery(this).resizable({
			grid: [5, 5],
			minHeight: parseInt(jQuery(this).attr("data-resizable-min-height"), 10),
			minWidth: parseInt(jQuery(this).attr("data-resizable-min-width"), 10),
			aspectRatio: jQuery(this).attr("data-resizable-ratio"),
			handles: jQuery(this).attr("data-resizable-handles"),
			start: function() {
			},
			resize: function() {
				var id = jQuery(this).attr("id");
				id = id.replace("lepopup-element-", "");
				var update_panel = false;
				if (lepopup_element_properties_active) {
					var active_id = jQuery(lepopup_element_properties_active).attr("id");
					if (active_id) {
						active_id = active_id.replace("lepopup-element-", "");
						if (id == active_id) update_panel = true;
					}
				}
				var width = parseInt(jQuery(this).outerWidth(), 10);
				if (jQuery(this).attr("data-resizable-auto-height") == "on") {
					jQuery(this).height("auto");
					lepopup_form_elements[id]['size-width'] = width;
					if (update_panel) {
						jQuery(".lepopup-properties-panel [name='lepopup-size-width']").val(width);
					}
				} else {
					var height = parseInt(jQuery(this).outerHeight(), 10);
					lepopup_form_elements[id]['size-width'] = width;
					lepopup_form_elements[id]['size-height'] = height;
					jQuery(this).find("i.lepopup-icon-left, i.lepopup-icon-right").css({"line-height" : height+"px"});
					if (update_panel) {
						jQuery(".lepopup-properties-panel [name='lepopup-size-width']").val(width);
						jQuery(".lepopup-properties-panel [name='lepopup-size-height']").val(height);
					}
				}
				if ((lepopup_form_elements[id]['type'] == 'close' && jQuery(this).hasClass('lepopup-element-close')) || (lepopup_form_elements[id]['type'] == 'fa-icon' && jQuery(this).hasClass('lepopup-element-icon'))) {
					jQuery(this).find("span").css({"font-size" : width+"px"});
				}
				if (lepopup_form_elements[id]['type'] == 'html' || lepopup_form_elements[id]['type'] == 'rectangle') {
					jQuery(this).find(".lepopup-element-html-content").css({"min-height" : height+"px"});
				}
				if (lepopup_form_elements[id]['type'] == 'video') {
					jQuery(this).find(".lepopup-element-html-content iframe, .lepopup-element-html-content video").css({"height" : height+"px", "width" : width+"px"});
				}
				lepopup_form_changed = true;
			},
			stop: function() {
				var id = jQuery(this).attr("id");
				id = id.replace("lepopup-element-", "");
				var update_panel = false;
				if (lepopup_element_properties_active) {
					var active_id = jQuery(lepopup_element_properties_active).attr("id");
					if (active_id) {
						active_id = active_id.replace("lepopup-element-", "");
						if (id == active_id) update_panel = true;
					}
				}
				var width = parseInt(jQuery(this).outerWidth(), 10);
				if (jQuery(this).attr("data-resizable-auto-height") == "on") {
					jQuery(this).height("auto");
					lepopup_form_elements[id]['size-width'] = width;
					if (update_panel) {
						jQuery(".lepopup-properties-panel [name='lepopup-size-width']").val(width);
					}
				} else {
					var height = parseInt(jQuery(this).outerHeight(), 10);
					lepopup_form_elements[id]['size-width'] = width;
					lepopup_form_elements[id]['size-height'] = height;
					jQuery(this).find("i.lepopup-icon-left, i.lepopup-icon-right").css({"line-height" : height+"px"});
					if (update_panel) {
						jQuery(".lepopup-properties-panel [name='lepopup-size-width']").val(width);
						jQuery(".lepopup-properties-panel [name='lepopup-size-height']").val(height);
					}
				}
				if ((lepopup_form_elements[id]['type'] == 'close' && jQuery(this).hasClass('lepopup-element-close')) || (lepopup_form_elements[id]['type'] == 'fa-icon' && jQuery(this).hasClass('lepopup-element-icon'))) {
					jQuery(this).find("span").css({"font-size" : width+"px"});
				}
				if (lepopup_form_elements[id]['type'] == 'html' || lepopup_form_elements[id]['type'] == 'rectangle') {
					jQuery(this).find(".lepopup-element-html-content").css({"min-height" : height+"px"});
				}
				if (lepopup_form_elements[id]['type'] == 'video') {
					jQuery(this).find(".lepopup-element-html-content iframe, .lepopup-element-html-content video").css({"height" : height+"px", "width" : width+"px"});
				}
				lepopup_form_changed = true;
			}
		});
	});
	jQuery(_selector).on("mouseenter", function(e){
		var id = jQuery(this).attr("id");
		id = id.replace("lepopup-element-", "");
		jQuery(".lepopup-layer-"+id).addClass("lepopup-layer-hovered");
	});
	jQuery(_selector).on("mouseleave", function(e){
		var id = jQuery(this).attr("id");
		id = id.replace("lepopup-element-", "");
		jQuery(".lepopup-layer-"+id).removeClass("lepopup-layer-hovered");
	});
	jQuery(_selector).on("click", function(e) {
		e.preventDefault();
		if (lepopup_element_properties_active) {
			lepopup_properties_panel_open(this);
		}
		return false;
	});
	if (typeof jQuery.fn.ionRangeSlider != typeof undefined && jQuery.fn.ionRangeSlider) jQuery(_selector).find("input.lepopup-rangeslider").ionRangeSlider();
	_lepopup_init_elements_contextmenu(_selector);
}

function lepopup_log_resize() {
	if (lepopup_record_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 1080);
		jQuery("#lepopup-record-details").height(popup_height);
		jQuery("#lepopup-record-details").width(popup_width);
		jQuery("#lepopup-record-details .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-record-details .lepopup-admin-popup-content").height(popup_height - 52);
	}
}
function lepopup_log_ready() {
	lepopup_log_resize();
	jQuery(window).resize(function() {
		lepopup_log_resize();
	});
}
function lepopup_forms_resize() {
	if (lepopup_more_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
		jQuery("#lepopup-more-using").height(popup_height);
		jQuery("#lepopup-more-using").width(popup_width);
		jQuery("#lepopup-more-using .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-more-using .lepopup-admin-popup-content").height(popup_height - 52);
	}
}
function lepopup_forms_ready() {
	jQuery("span[title], a[title]").tooltipster({
		contentAsHTML:	true,
		maxWidth:		360,
		theme:			"tooltipster-dark",
		side:			"bottom"
	});
	lepopup_forms_resize();
	jQuery(window).resize(function() {
		lepopup_forms_resize();
	});
}
function lepopup_form_resize() {
	var window_height = jQuery(window).height();
	var adminbar_height = jQuery("#wpadminbar").height();
	if (!lepopup_is_numeric(adminbar_height)) adminbar_height = 0;	
	var toolbar_height = jQuery(".lepopup-toolbar").height();
	var top_padding = 20;
	var header_height = jQuery(".lepopup-header").height();
	//var builder_height = parseInt(window_height, 10) - parseInt(adminbar_height, 10) - parseInt(header_height, 10) - parseInt(toolbar_height, 10) - parseInt(top_padding, 10);
	var builder_height = parseInt(window_height, 10);
	var toolbars_height = jQuery(".lepopup-toolbars").height();
	jQuery(".lepopup-builder").css({"min-height" : builder_height+"px"});
	jQuery(".lepopup-form").css({"min-height" : parseInt(builder_height-20, 10)+"px"});
	var builder_width = jQuery(".lepopup-builder").outerWidth();
	jQuery(".lepopup-toolbars").css({"width" : builder_width+"px"});
	if (lepopup_element_properties_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 880), 1080);
		jQuery("#lepopup-element-properties").height(popup_height);
		jQuery("#lepopup-element-properties").width(popup_width);
		jQuery("#lepopup-element-properties .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-element-properties .lepopup-admin-popup-content").height(popup_height - 104);
	}
	if (lepopup_bulk_options_object) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 880), 1080);
		jQuery("#lepopup-bulk-options").height(popup_height);
		jQuery("#lepopup-bulk-options").width(popup_width);
		jQuery("#lepopup-bulk-options .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-bulk-options .lepopup-admin-popup-content").height(popup_height - 104);
	}
	if (lepopup_record_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 1080);
		jQuery("#lepopup-record-details").height(popup_height);
		jQuery("#lepopup-record-details").width(popup_width);
		jQuery("#lepopup-record-details .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-record-details .lepopup-admin-popup-content").height(popup_height - 52);
	}
	if (lepopup_more_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
		jQuery("#lepopup-more-using").height(popup_height);
		jQuery("#lepopup-more-using").width(popup_width);
		jQuery("#lepopup-more-using .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-more-using .lepopup-admin-popup-content").height(popup_height - 52);
	}
}
function lepopup_form_ready() {
	lepopup_form_resize();
	jQuery(window).resize(function() {
		lepopup_form_resize();
	});
	jQuery(window).scroll(function(e) {
		return;
		var position = jQuery(window).scrollTop();
		var adminbar_height = jQuery("#wpadminbar").height();
		if (!lepopup_is_numeric(adminbar_height)) adminbar_height = 0;
		var offset = jQuery(".lepopup-builder").offset().top - adminbar_height;
		if (position > offset) {
			jQuery("html").addClass("lepopup-toolbars-fixed");
		} else {
			jQuery("html").removeClass("lepopup-toolbars-fixed");
		}
	});
	
	for (var i=0; i<lepopup_form_pages_raw.length; i++) {
		if (typeof lepopup_form_pages_raw[i] == 'object') {
			if (parseInt(lepopup_form_pages_raw[i]['id'], 10) > lepopup_form_last_id) lepopup_form_last_id = parseInt(lepopup_form_pages_raw[i]['id'], 10);
			lepopup_form_pages.push(lepopup_form_pages_raw[i]);
		}
	}
	
	if (lepopup_form_options.hasOwnProperty("math-expressions") && Array.isArray(lepopup_form_options["math-expressions"])) {
		for (var i=0; i<lepopup_form_options["math-expressions"].length; i++) {
			if (typeof lepopup_form_options["math-expressions"][i] == 'object') {
				if (parseInt(lepopup_form_options["math-expressions"][i]['id'], 10) > lepopup_form_last_id) lepopup_form_last_id = parseInt(lepopup_form_options["math-expressions"][i]['id'], 10);
			}
		}
	}
	if (lepopup_form_options.hasOwnProperty("payment-gateways") && Array.isArray(lepopup_form_options["payment-gateways"])) {
		for (var i=0; i<lepopup_form_options["payment-gateways"].length; i++) {
			if (typeof lepopup_form_options["payment-gateways"][i] == 'object') {
				if (parseInt(lepopup_form_options["payment-gateways"][i]['id'], 10) > lepopup_form_last_id) lepopup_form_last_id = parseInt(lepopup_form_options["payment-gateways"][i]['id'], 10);
			}
		}
	}

	var tmp;
	for (var i=0; i<lepopup_form_elements_raw.length; i++) {
		tmp = JSON.parse(lepopup_form_elements_raw[i]);
		if (typeof tmp == 'object') {
			if (parseInt(tmp['id'], 10) > lepopup_form_last_id) lepopup_form_last_id = parseInt(tmp['id'], 10);
			lepopup_form_elements.push(tmp);
		}
	}
	
	if (jQuery(".lepopup-pages-bar-item").length == 1) jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").addClass("lepopup-pages-bar-item-delete-disabled");
	else jQuery(".lepopup-pages-bar-item").find(".lepopup-pages-bar-item-delete").removeClass("lepopup-pages-bar-item-delete-disabled");
	lepopup_pages_activate(jQuery(".lepopup-pages-bar-item").first().find("label"));
	
	jQuery(".lepopup-toolbar-list>li>a[title]").tooltipster({
		maxWidth:		360,
		theme:			"tooltipster-dark lepopup-toolbar-tooltipster",
		side:			"bottom"
	});
	
	jQuery(".lepopup-toolbar-list li a").on("click", function(e) {
		e.preventDefault();
		var type = jQuery(this).parent().attr("data-type");
		if (typeof type == undefined || type == "") return false;
		if (lepopup_element_properties_active) {
			lepopup_properties_panel_close();
		}	
		if (lepopup_meta.hasOwnProperty(type)) {
			lepopup_form_last_id++;
			var element = {"type" : type, "resize" : "both", "height" : "auto", "_parent" : lepopup_form_page_active, "_seq" : lepopup_form_last_id, "id" : lepopup_form_last_id};
			for (var key in lepopup_meta[type]) {
				if (lepopup_meta[type].hasOwnProperty(key)) {
					if (lepopup_meta[type][key].hasOwnProperty('value')) {
						if (typeof lepopup_meta[type][key]['value'] == 'object') {
							for (var option_key in lepopup_meta[type][key]['value']) {
								if (lepopup_meta[type][key]['value'].hasOwnProperty(option_key)) {
									element[key+"-"+option_key] = lepopup_meta[type][key]['value'][option_key];
								}
							}
						} else element[key] = lepopup_meta[type][key]['value'];
					} else if (lepopup_meta[type][key].hasOwnProperty('values')) element[key] = lepopup_meta[type][key]['values'];
				}
			}
			lepopup_form_elements.push(element);
			lepopup_form_changed = true;
			lepopup_build();
			_lepopup_layers_sync(lepopup_form_page_active);
			if (lepopup_gettingstarted_enable == "on" && lepopup_form_elements.length <= 2 && type != "hidden") lepopup_gettingstarted("element-properties", 0);
		}
	});
	jQuery("body").append('<div class="lepopup-context-menu"><ul><li><a href="#" onclick="return lepopup_properties_panel_open(lepopup_context_menu_object);"><i class="fas fa-pencil-alt"></i>Properties</a></li><li class="lepopup-context-menu-last"><a href="#" onclick="return lepopup_element_duplicate(lepopup_context_menu_object);"><i class="far fa-copy"></i>Duplicate</a></li><li class="lepopup-context-menu-line"></li><li><a href="#" onclick="return lepopup_element_delete(lepopup_context_menu_object);"><i class="fas fa-trash-alt"></i>Delete</a></li></ul></div>');
	jQuery("body").on("click", function(e) {
		jQuery(".lepopup-context-menu").hide();
	});
	jQuery(".lepopup-fa-selector-header input").on("keyup", function(e) {
		var needle = jQuery(this).val().toLowerCase();
		if (needle == "") {
			jQuery(this).parent().parent().find(".lepopup-fa-selector-content span").show();
		} else {
			var icons = jQuery(this).parent().parent().find(".lepopup-fa-selector-content");
			jQuery(icons).find("span").each(function() {
				if (jQuery(this).attr("title").toLowerCase().indexOf(needle) === -1) jQuery(this).hide();
				else jQuery(this).show();
			});
		}
		return false;
	});
	jQuery(window).on('beforeunload', function(e){
		if (lepopup_element_properties_data_changed || lepopup_form_changed) return 'Form changed!';
		return;
	});
	jQuery(".lepopup-pages-bar-items").sortable({
		items: "li.lepopup-pages-bar-item",
		containment: "parent",
		forcePlaceholderSize: true,
		dropOnEmpty: true,
		placeholder: "lepopup-pages-bar-item-placeholder",
		start: function(event, ui) {
			jQuery(ui.helper).addClass("lepopup-pages-bar-item-helper");
			jQuery(".lepopup-context-menu").hide();
		},		
		stop: function(event, ui) {
			jQuery(".lepopup-pages-bar-item-helper").removeClass("lepopup-pages-bar-item-helper");
			lepopup_update_progress();
		}
	});
	jQuery(".lepopup-pages-bar-items, .lepopup-pages-bar-items-confirmation").disableSelection();
	jQuery(".lepopup-element").disableSelection();
	lepopup_build();
}

function lepopup_forms_status_toggle(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var form_id = jQuery(_object).attr("data-id");
	var form_status = jQuery(_object).attr("data-status");
	var form_status_label = jQuery(_object).closest("tr").find("td.column-active").html();
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	jQuery(_object).closest("tr").find("td.column-active").html("<i class='fas fa-spinner fa-spin'></i>");
	var post_data = {"action" : "lepopup-forms-status-toggle", "form-id" : form_id, "form-status" : form_status};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).html(do_label);
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).html(data.form_action);
					jQuery(_object).attr("data-status", data.form_status);
					jQuery(_object).attr("data-doing", data.form_action_doing);
					if (data.form_status == "active") jQuery(_object).closest("tr").find(".lepopup-table-list-badge-status").html("");
					else jQuery(_object).closest("tr").find(".lepopup-table-list-badge-status").html("<span class='lepopup-badge lepopup-badge-danger'>Inactive</span>");
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					jQuery(_object).closest("tr").find("td.column-active").html(form_status_label);
					lepopup_global_message_show("danger", data.message);
				} else {
					jQuery(_object).closest("tr").find("td.column-active").html(form_status_label);
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				jQuery(_object).closest("tr").find("td.column-active").html(form_status_label);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find("td.column-active").html(form_status_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_forms_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the form.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_forms_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_forms_delete(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var form_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-forms-delete", "form-id" : form_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest("tr").fadeOut(300, function(){
						jQuery(_object).closest("tr").remove();
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_forms_duplicate(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to duplicate the form.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Duplicate',
		ok_function:	function(e){
			_lepopup_forms_duplicate(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_forms_duplicate(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var form_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-forms-duplicate", "form-id" : form_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show("success", data.message);
					location.reload();
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_columns_toggle(_object) {
	var columns = {};
	var json_columns = "";
	if (typeof _object === 'string' || _object instanceof String) {
		json_columns = lepopup_read_cookie("lepopup-"+_object+"-columns");
		if (json_columns != null) {
			try {
				columns = JSON.parse(json_columns);
				if (typeof columns == "object" && !jQuery.isEmptyObject(columns)) {
					jQuery("ul.lepopup-"+_object+"-columns input").each(function(){
						var id = jQuery(this).attr("data-id");
						if (columns.hasOwnProperty(id)) {
							if (columns[id] == "on") {
								jQuery(this).prop("checked", true);
								jQuery(".lepopup-column-"+id).show();
							} else {
								jQuery(this).prop("checked", false);
								jQuery(".lepopup-column-"+id).hide();
							}
						}
					});
					lepopup_write_cookie("lepopup-"+_object+"-columns", json_columns, 365);
				}
			} catch(error) {
				console.log(error);
			}
		}
	} else {
		var columns_set = jQuery(_object).closest("ul");
		if (columns_set) {
			jQuery(columns_set).find("input").each(function(){
				var id = jQuery(this).attr("data-id");
				if (jQuery(this).is(":checked")) {
					columns[id] = "on";
					jQuery(".lepopup-column-"+id).show();
				} else {
					columns[id] = "off";
					jQuery(".lepopup-column-"+id).hide();
				}
			});
			lepopup_write_cookie("lepopup-"+jQuery(columns_set).attr("data-id")+"-columns", JSON.stringify(columns), 365);
		}
	}
	
	return false;
}
var lepopup_more_active = null;
function lepopup_more_using_open(_object) {
	jQuery("#lepopup-more-using .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
	jQuery("#lepopup-more-using").height(window_height);
	jQuery("#lepopup-more-using").width(window_width);
	jQuery("#lepopup-more-using .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-more-using .lepopup-admin-popup-content").height(window_height - 52);
	jQuery("#lepopup-more-using-overlay").fadeIn(300);
	jQuery("#lepopup-more-using").fadeIn(300);
	jQuery("#lepopup-more-using .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-more-using .lepopup-admin-popup-loading").show();
	lepopup_more_active = jQuery(_object).attr("data-id");
	var mode = jQuery(_object).attr("data-mode");
	//if (mode != "campaign" && mode != "tab") mode = "form";
	var post_data = {"action" : "lepopup-"+mode+"-using", "item-id" : lepopup_more_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-more-using .lepopup-admin-popup-content-form").html(data.html);
					jQuery("#lepopup-more-using .lepopup-admin-popup-title h3 span").html(data.form_name);
					jQuery("#lepopup-more-using .lepopup-admin-popup-loading").hide();
					jQuery("#lepopup-more-using .lepopup-admin-popup-content-form span[title]").tooltipster({
						contentAsHTML:	true,
						maxWidth:		360,
						theme:			"tooltipster-dark",
						side:			"bottom"
					});
				} else if (data.status == "ERROR") {
					lepopup_more_using_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_more_using_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_more_using_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_more_using_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}

function lepopup_more_using_close() {
	jQuery("#lepopup-more-using-overlay").fadeOut(300);
	jQuery("#lepopup-more-using").fadeOut(300);
	lepopup_more_active = null;
	setTimeout(function(){jQuery("#lepopup-more-using .lepopup-admin-popup-content-form").html("");}, 1000);
	return false;
}

var lepopup_campaign_properties_active = null;
function lepopup_campaign_properties_open(_campaign_id) {
	jQuery("#lepopup-campaign-properties .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
	jQuery("#lepopup-campaign-properties").height(window_height);
	jQuery("#lepopup-campaign-properties").width(window_width);
	jQuery("#lepopup-campaign-properties .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-campaign-properties .lepopup-admin-popup-content").height(window_height - 104);
	jQuery("#lepopup-campaign-properties-overlay").fadeIn(300);
	jQuery("#lepopup-campaign-properties").fadeIn(300);
	jQuery("#lepopup-campaign-properties .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-campaign-properties .lepopup-admin-popup-loading").show();
	lepopup_campaign_properties_active = _campaign_id;
	var post_data = {"action" : "lepopup-campaign-properties", "campaign-id" : lepopup_campaign_properties_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-campaign-properties .lepopup-admin-popup-content-form").html(data.html);
					jQuery("#lepopup-campaign-properties .lepopup-admin-popup-title h3 span").html(data.campaign_name);
					jQuery("#lepopup-campaign-properties .lepopup-admin-popup-loading").hide();
					jQuery(".lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
						contentAsHTML:	true,
						maxWidth:		360,
						theme:			"tooltipster-dark",
						side:			"bottom",
						content:		"Default",
						functionFormat: function(instance, helper, content){
							return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
						}
					});
				} else if (data.status == "ERROR") {
					lepopup_campaign_properties_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_campaign_properties_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_campaign_properties_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_campaign_properties_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}
function lepopup_campaign_properties_close() {
	jQuery("#lepopup-campaign-properties-overlay").fadeOut(300);
	jQuery("#lepopup-campaign-properties").fadeOut(300);
	lepopup_campaign_properties_active = null;
	setTimeout(function(){jQuery("#lepopup-campaign-properties .lepopup-admin-popup-content-form").html("");}, 1000);
	return false;
}
var lepopup_campaign_stats_active = null;
function lepopup_campaign_stats_open(_campaign_id) {
	if (lepopup_chart) lepopup_chart.destroy();
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 840);
	jQuery("#lepopup-campaign-stats").height(window_height);
	jQuery("#lepopup-campaign-stats").width(window_width);
	jQuery("#lepopup-campaign-stats .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-campaign-stats .lepopup-admin-popup-content").height(window_height - 52);
	jQuery("#lepopup-campaign-stats-overlay").fadeIn(300);
	jQuery("#lepopup-campaign-stats").fadeIn(300);
	jQuery("#lepopup-campaign-stats .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-campaign-stats .lepopup-admin-popup-loading").show();
	lepopup_campaign_stats_active = _campaign_id;
	var post_data = {"action" : "lepopup-campaign-stats", "campaign-id" : lepopup_campaign_stats_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					var labels = new Array();
					var impressions = new Array();
					var submits = new Array();
					var ctrs = new Array();
					for (var key in data.data) {
						if (data.data.hasOwnProperty(key)) {
							labels.push(data.data[key]["label"]);
							impressions.push(data.data[key]["impressions"]);
							submits.push(data.data[key]["submits"]);
							ctrs.push(data.data[key]["ctrs"]);
						}
					}
					lepopup_chart = new Chart("lepopup-stats", {
						type: "bar",
						data: {
							labels: labels,
							datasets: [{
								label: "Impressions",
								lineTension : 0,
								fill : false,
								data: impressions,
								backgroundColor: 'rgb(255, 99, 132)',
								borderColor: 'rgb(255, 99, 132)',
								borderWidth: 2,
								yAxisID: "left-y-axis"
							},
							{
								label: "Submits",
								lineTension : 0,
								fill : false,
								data: submits,
								backgroundColor: 'rgb(255, 159, 64)',
								borderColor: 'rgb(255, 159, 64)',
								borderWidth: 2,
								yAxisID: "left-y-axis"
							},
							{
								label: "CTR",
								lineTension : 0,
								fill : false,
								data: ctrs,
								backgroundColor: 'rgb(75, 192, 192)',
								borderColor: 'rgb(75, 192, 192)',
								borderWidth: 2,
								yAxisID: "right-y-axis"
							}
							]
						},
						options: {
							responsive: true,
							tooltips: {
								mode: 'index',
								intersect: false,
								callbacks: {
									label: function(tooltipItems, data) {
										var label = data.datasets[tooltipItems.datasetIndex]["label"]+": "+tooltipItems.yLabel.toString();
										if (tooltipItems.datasetIndex == 2) label += "%";
										return label;
									}
								}
							},
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true
									},
									position: 'left',
									id : "left-y-axis"
								},
								{
									ticks: {
										beginAtZero:true
									},
									gridLines: {
										display: false
									},
									position: 'right',
									id : "right-y-axis"
								}]
							}
						}
					});
					
					jQuery("#lepopup-campaign-stats .lepopup-admin-popup-title h3 span").html(data.campaign_name);
					jQuery("#lepopup-campaign-stats .lepopup-admin-popup-loading").hide();
				} else if (data.status == "ERROR") {
					lepopup_campaign_stats_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_campaign_stats_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_campaign_stats_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_campaign_stats_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}
function lepopup_campaign_stats_close() {
	jQuery("#lepopup-campaign-stats-overlay").fadeOut(300);
	jQuery("#lepopup-campaign-stats").fadeOut(300);
	lepopup_campaign_stats_active = null;
	setTimeout(function(){if (lepopup_chart) lepopup_chart.destroy();}, 1000);
	return false;
}

function lepopup_stats_reset(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to reset form statistics.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Reset',
		ok_function:	function(e){
			_lepopup_stats_reset(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_stats_reset(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var form_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-stats-reset", "form-id" : form_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

var lepopup_record_active = null;
function lepopup_record_details_open(_object) {
	var href;
	jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 1080);
	jQuery("#lepopup-record-details").height(window_height);
	jQuery("#lepopup-record-details").width(window_width);
	jQuery("#lepopup-record-details .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-record-details .lepopup-admin-popup-content").height(window_height - 52);
	jQuery("#lepopup-record-details-overlay").fadeIn(300);
	jQuery("#lepopup-record-details").fadeIn(300);
	jQuery("#lepopup-record-details .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-record-details .lepopup-admin-popup-loading").show();
	lepopup_record_active = jQuery(_object).attr("data-id");
	var pdf_button = jQuery("#lepopup-record-details .lepopup-admin-popup-title span.lepopup-export-pdf");
	if (pdf_button.length > 0) {
		href = jQuery(pdf_button).attr("data-url");
		href = href.replace(/{ID}/g, lepopup_record_active);
		jQuery(pdf_button).find("a").attr("href", href);
	}
	var print_button = jQuery("#lepopup-record-details .lepopup-admin-popup-title span.lepopup-print");
	if (print_button.length > 0) {
		href = jQuery(print_button).attr("data-url");
		href = href.replace(/{ID}/g, lepopup_record_active);
		jQuery(print_button).find("a").attr("href", href);
	}
	var post_data = {"action" : "lepopup-record-details", "record-id" : lepopup_record_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html(data.html);
					jQuery("#lepopup-record-details .lepopup-admin-popup-title h3 span").html(data.form_name);
					jQuery("#lepopup-record-details .lepopup-admin-popup-loading").hide();
				} else if (data.status == "ERROR") {
					lepopup_record_details_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_record_details_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_record_details_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_record_details_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}

function lepopup_record_details_close() {
	jQuery("#lepopup-record-details-overlay").fadeOut(300);
	jQuery("#lepopup-record-details").fadeOut(300);
	lepopup_record_active = null;
	setTimeout(function(){jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html("");}, 1000);
	return false;
}

function lepopup_records_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the record.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_records_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_records_delete(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var record_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-records-delete", "record-id" : record_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest("tr").fadeOut(300, function(){
						jQuery(_object).closest("tr").remove();
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_bulk_records_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete selected records.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_bulk_records_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_bulk_records_delete(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: jQuery("#lepopup-table-log input").serialize(),
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-trash");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show('success', data.message);
					location.reload(true);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-trash");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_transaction_details_open(_object) {
	jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 1080);
	jQuery("#lepopup-record-details").height(window_height);
	jQuery("#lepopup-record-details").width(window_width);
	jQuery("#lepopup-record-details .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-record-details .lepopup-admin-popup-content").height(window_height - 52);
	jQuery("#lepopup-record-details-overlay").fadeIn(300);
	jQuery("#lepopup-record-details").fadeIn(300);
	jQuery("#lepopup-record-details .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-record-details .lepopup-admin-popup-loading").show();
	lepopup_record_active = jQuery(_object).attr("data-id");
	var href;
	var pdf_button = jQuery("#lepopup-record-details .lepopup-admin-popup-title span.lepopup-export-pdf");
	if (pdf_button.length > 0) {
		href = jQuery(pdf_button).attr("data-url");
		href = href.replace(/{ID}/g, lepopup_record_active);
		jQuery(pdf_button).find("a").attr("href", href);
	}
	var print_button = jQuery("#lepopup-record-details .lepopup-admin-popup-title span.lepopup-print");
	if (print_button.length > 0) {
		href = jQuery(print_button).attr("data-url");
		href = href.replace(/{ID}/g, lepopup_record_active);
		jQuery(print_button).find("a").attr("href", href);
	}
	var post_data = {"action" : "lepopup-transaction-details", "transaction-id" : lepopup_record_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html(data.html);
					jQuery("#lepopup-record-details .lepopup-admin-popup-loading").hide();
				} else if (data.status == "ERROR") {
					lepopup_record_details_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_record_details_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_record_details_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_record_details_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}

function lepopup_transaction_details_close() {
	jQuery("#lepopup-record-details-overlay").fadeOut(300);
	jQuery("#lepopup-record-details").fadeOut(300);
	lepopup_record_active = null;
	setTimeout(function(){jQuery("#lepopup-record-details .lepopup-admin-popup-content-form").html("");}, 1000);
}

function lepopup_transactions_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the transaction.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_transactions_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_transactions_delete(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var record_id = jQuery(_object).attr("data-id");
	var doing_label = jQuery(_object).attr("data-doing");
	var do_label = jQuery(_object).html();
	jQuery(_object).html("<i class='fas fa-spinner fa-spin'></i> "+doing_label);
	jQuery(_object).closest("tr").find(".row-actions").addClass("visible");
	var post_data = {"action" : "lepopup-transactions-delete", "transaction-id" : record_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest("tr").fadeOut(300, function(){
						jQuery(_object).closest("tr").remove();
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).html(do_label);
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).closest("tr").find(".row-actions").removeClass("visible");
			jQuery(_object).html(do_label);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_bulk_transactions_delete(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete selected transactions.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			_lepopup_bulk_transactions_delete(_object);
			lepopup_dialog_close();
		}
	});
	return false;
}

function _lepopup_bulk_transactions_delete(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: jQuery("#lepopup-table-transactions input").serialize(),
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-trash");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_global_message_show('success', data.message);
					location.reload(true);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-trash");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_field_analytics_load(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-stats-button-disabled");
	var post_data = {"action" : "lepopup-field-analytics-load", "form-id" : jQuery("#lepopup-stats-form").val(), "start-date" : jQuery("#lepopup-stats-date-start").val(), "end-date" : jQuery("#lepopup-stats-date-end").val(), "period" : (jQuery("#lepopup-stats-period").is(":checked") ? "on" : "off")};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_field_analytics_build_charts(data.data);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).find("i").attr("class", "fas fa-check");
			jQuery(_object).removeClass("lepopup-stats-button-disabled");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-check");
			jQuery(_object).removeClass("lepopup-stats-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}

function lepopup_field_analytics_ready() {
	var airdatepicker = jQuery("#lepopup-stats-date-start").airdatepicker().data('airdatepicker');
	airdatepicker.destroy();
	jQuery("#lepopup-stats-date-start").airdatepicker({
		autoClose		: true,
		timepicker		: false,
		dateFormat		: 'yyyy-mm-dd',
		onShow			: function(inst, animationCompleted) {
			var max_date_string = jQuery("#lepopup-stats-date-end").val() ? jQuery("#lepopup-stats-date-end").val() : "2030-12-31";
			inst.update('maxDate', new Date(max_date_string));
		}
	});
	airdatepicker = jQuery("#lepopup-stats-date-end").airdatepicker().data('airdatepicker');
	airdatepicker.destroy();
	jQuery("#lepopup-stats-date-end").airdatepicker({
		autoClose		: true,
		timepicker		: false,
		dateFormat		: 'yyyy-mm-dd',
		onShow			: function(inst, animationCompleted) {
			var min_date_string = jQuery("#lepopup-stats-date-start").val() ? jQuery("#lepopup-stats-date-start").val() : "2018-01-01";
			inst.update('minDate', new Date(min_date_string));
		}
	});
	jQuery("#lepopup-stats-period").on("change", function(e){
		if (jQuery("#lepopup-stats-period").is(":checked")) {
			jQuery(".lepopup-stats-input-container").fadeIn(300);
		} else {
			jQuery(".lepopup-stats-input-container").fadeOut(300);
		}
	});
	
	var data = JSON.parse(jQuery("#lepopup-field-analytics-initial-data").val());
	if (jQuery("#lepopup-stats-form").val() != 0) lepopup_field_analytics_build_charts(data);
}

function lepopup_field_analytics_build_charts(_charts) {
	var colors = new Array(
		{
			backgroundColor: 'rgb(255, 99, 132, 0.7)',
			borderColor: 'rgb(255, 99, 132)',
		},
		{
			backgroundColor: 'rgba(75, 192, 192, 0.7)',
			borderColor: 'rgb(75, 192, 192)',
		},
		{
			backgroundColor: 'rgba(255, 205, 86, 0.7)',
			borderColor: 'rgb(255, 205, 86)',
		},
		{
			backgroundColor: 'rgba(54, 162, 235, 0.7)',
			borderColor: 'rgb(54, 162, 235)',
		},
		{
			backgroundColor: 'rgba(153, 102, 255, 0.7)',
			borderColor: 'rgb(153, 102, 255)',
		},
		{
			backgroundColor: 'rgba(201, 203, 207, 0.7)',
			borderColor: 'rgb(201, 203, 207)',
		}
	);
	if (_charts.length == 0) {
		jQuery(".lepopup-field-analytics-container").html("<div class='lepopup-field-analytics-noform'>No data found.</div>");
	} else {
		var column1_height = 0, column2_height = 0, height = 0, chart_html = "";
		var labels = new Array();
		var values = new Array();
		jQuery(".lepopup-field-analytics-container").html("");
		if (_charts.length > 1) jQuery(".lepopup-field-analytics-container").html("<div class='lepopup-field-analytics-columns'><div id='lepopup-field-analytics-column1'></div><div id='lepopup-field-analytics-column2'></div></div>");
		else jQuery(".lepopup-field-analytics-container").html("");
		for (var i=0; i<_charts.length; i++) {
			labels = new Array();
			values = new Array();
			for (var j=0; j<_charts[i]['chart'].length; j++) {
				if (_charts[i]['chart'][j]['label'].length > 24) labels.push(_charts[i]['chart'][j]['label'].substring(0, 20)+"...");
				else labels.push(_charts[i]['chart'][j]['label']);
				values.push(parseInt(_charts[i]['chart'][j]['value'], 10));
			}
			height = 128+24*labels.length;
			chart_html = "<div class='lepopup-field-analytics-chart-box'><canvas id='lepopup-field-"+_charts[i]["form-id"]+"-"+_charts[i]["id"]+"'></canvas></div>";
			if (_charts.length > 1) {
				if (column1_height > column2_height) {
					jQuery("#lepopup-field-analytics-column2").append(chart_html);
					column2_height += height + 32;
				} else {
					jQuery("#lepopup-field-analytics-column1").append(chart_html);
					column1_height += height + 32;
				}
			} else jQuery(".lepopup-field-analytics-container").append(chart_html);
			
			jQuery("#lepopup-field-"+_charts[i]["form-id"]+"-"+_charts[i]["id"]).height(height);
			lepopup_chart = new Chart("lepopup-field-"+_charts[i]["form-id"]+"-"+_charts[i]["id"], {
				type: "horizontalBar",
				data: {
					labels: labels,
					datasets: [{
						label: _charts[i]["title"],
						data: values,
						backgroundColor: colors[i%colors.length]["backgroundColor"],
						borderColor: colors[i%colors.length]["borderColor"],
						borderWidth: 1
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					legend: {
						display: false
					},
					title: {
						display: true,
						text: _charts[i]["title"]
					},
					scales: {
						yAxes: [{
							maxBarThickness: 32
						}],
						xAxes: [{
							ticks: {
								beginAtZero:true
							}
						}],
					}
				}
			});
			
		}
	}
}

function lepopup_stats_load(_object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-stats-button-disabled");
	var post_data = {"action" : "lepopup-stats-load", "form-id" : jQuery("#lepopup-stats-form").val(), "start-date" : jQuery("#lepopup-stats-date-start").val(), "end-date" : jQuery("#lepopup-stats-date-end").val()};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					var labels = new Array();
					var impressions = new Array();
					var submits = new Array();
					var confirmed = new Array();
					var payments = new Array();
					for (var key in data.data) {
						if (data.data.hasOwnProperty(key)) {
							labels.push(data.data[key]["label"]);
							impressions.push(data.data[key]["impressions"]);
							confirmed.push(data.data[key]["confirmed"]);
							submits.push(data.data[key]["submits"]);
							payments.push(data.data[key]["payments"]);
						}
					}
					lepopup_stats_build_chart(labels, impressions, submits, confirmed, payments);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_object).find("i").attr("class", "fas fa-check");
			jQuery(_object).removeClass("lepopup-stats-button-disabled");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-check");
			jQuery(_object).removeClass("lepopup-stats-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
	
}

function lepopup_stats_ready() {
	var airdatepicker = jQuery("#lepopup-stats-date-start").airdatepicker().data('airdatepicker');
	airdatepicker.destroy();
	jQuery("#lepopup-stats-date-start").airdatepicker({
		autoClose		: true,
		timepicker		: false,
		dateFormat		: 'yyyy-mm-dd',
		onShow			: function(inst, animationCompleted) {
			var max_date_string = jQuery("#lepopup-stats-date-end").val() ? jQuery("#lepopup-stats-date-end").val() : "2030-12-31";
			inst.update('maxDate', new Date(max_date_string));
		}
	});
	airdatepicker = jQuery("#lepopup-stats-date-end").airdatepicker().data('airdatepicker');
	airdatepicker.destroy();
	jQuery("#lepopup-stats-date-end").airdatepicker({
		autoClose		: true,
		timepicker		: false,
		dateFormat		: 'yyyy-mm-dd',
		onShow			: function(inst, animationCompleted) {
			var min_date_string = jQuery("#lepopup-stats-date-start").val() ? jQuery("#lepopup-stats-date-start").val() : "2018-01-01";
			inst.update('minDate', new Date(min_date_string));
		}
	});
	var labels = new Array();
	var impressions = new Array();
	var submits = new Array();
	var confirmed = new Array();
	var payments = new Array();
	var data = JSON.parse(jQuery("#lepopup-stats-initial-data").val());
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			labels.push(data[key]["label"]);
			impressions.push(data[key]["impressions"]);
			submits.push(data[key]["submits"]);
			confirmed.push(data[key]["confirmed"]);
			payments.push(data[key]["payments"]);
		}
	}
	lepopup_stats_build_chart(labels, impressions, submits, confirmed, payments);
}

var lepopup_chart = null;
function lepopup_stats_build_chart(_labels, _impressions, _submits, _confirmed, _payments) {
	if (lepopup_chart) lepopup_chart.destroy();
	lepopup_chart = new Chart("lepopup-stats", {
		type: "line",
		data: {
			labels: _labels,
			datasets: [{
				label: "Impressions",
				lineTension : 0,
				fill : false,
				data: _impressions,
				backgroundColor: 'rgb(255, 99, 132)',
				borderColor: 'rgb(255, 99, 132)',
				borderWidth: 2
			},
			{
				label: "Submits",
				lineTension : 0,
				fill : false,
				data: _submits,
				backgroundColor: 'rgb(255, 159, 64)',
				borderColor: 'rgb(255, 159, 64)',
				borderWidth: 2
			},
			{
				label: "Confirmed",
				lineTension : 0,
				fill : false,
				data: _confirmed,
				backgroundColor: 'rgb(75, 192, 192)',
				borderColor: 'rgb(75, 192, 192)',
				borderWidth: 2
			},
			{
				label: "Payments",
				lineTension : 0,
				fill : false,
				data: _payments,
				backgroundColor: 'rgb(204, 125, 188)',
				borderColor: 'rgb(204, 125, 188)',
				borderWidth: 2
			}
			]
		},
		options: {
			responsive: true,
			tooltips: {
				mode: 'index',
				intersect: false,
			},
/*			hover: {
				mode: 'nearest',
				intersect: true
			},*/
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero:true
					}
				}]
			}
		}
	});
}

function lepopup_record_field_empty(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to empty this field.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Empty Field'),
		ok_function:	function(e) {
			_lepopup_record_field_empty(jQuery("#lepopup-dialog .lepopup-dialog-button-ok"), _object);
		}
	});
}

function _lepopup_record_field_empty(_button, _object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var field_id = jQuery(_object).closest(".lepopup-record-details-table-value").attr("data-id");
	var record_id = jQuery(_object).closest(".lepopup-record-details").attr("data-id");
	var icon = jQuery(_button).find("i").attr("class");
	jQuery(_button).find("i").attr("class", "fas fa-spinner fa-spin");
	var post_data = {"action" : "lepopup-record-field-empty", "record-id" : record_id, "field-id" : field_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-value").text("-");
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				console.log(error);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_button).find("i").attr("class", icon);
			lepopup_sending = false;
			lepopup_dialog_close();
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_button).find("i").attr("class", icon);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
			lepopup_dialog_close();
		}
	});
	return false;
}

function lepopup_record_field_remove(_object) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__('Please confirm that you want to remove this field.')+"</div>");
			this.show();
		},
		ok_label:		lepopup_esc_html__('Remove Field'),
		ok_function:	function(e) {
			_lepopup_record_field_remove(jQuery("#lepopup-dialog .lepopup-dialog-button-ok"), _object);
		}
	});
}

function _lepopup_record_field_remove(_button, _object) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var field_id = jQuery(_object).closest(".lepopup-record-details-table-value").attr("data-id");
	var record_id = jQuery(_object).closest(".lepopup-record-details").attr("data-id");
	var icon = jQuery(_button).find("i").attr("class");
	jQuery(_button).find("i").attr("class", "fas fa-spinner fa-spin");
	var post_data = {"action" : "lepopup-record-field-remove", "record-id" : record_id, "field-id" : field_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_object).closest("tr").fadeOut(300, function() {
						jQuery(_object).closest("tr").remove();
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				console.log(error);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_button).find("i").attr("class", icon);
			lepopup_sending = false;
			lepopup_dialog_close();
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_button).find("i").attr("class", icon);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
			lepopup_dialog_close();
		}
	});
	return false;
}

function lepopup_record_field_load_editor(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var field_id = jQuery(_button).closest(".lepopup-record-details-table-value").attr("data-id");
	var record_id = jQuery(_button).closest(".lepopup-record-details").attr("data-id");
	var icon = jQuery(_button).find("i").attr("class");
	jQuery(_button).find("i").attr("class", "fas fa-spinner fa-spin");
	var post_data = {"action" : "lepopup-record-field-load-editor", "record-id" : record_id, "field-id" : field_id};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-value").fadeOut(300, function(){
						jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").html(data.html);
						jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").fadeIn(300);
					});
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				console.log(error);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_button).find("i").attr("class", icon);
			lepopup_sending = false;
			lepopup_dialog_close();
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_button).find("i").attr("class", icon);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
			lepopup_dialog_close();
		}
	});
	return false;
}

function lepopup_record_field_cancel_editor(_button) {
	jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").fadeOut(300, function(){
		jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-value").fadeIn(300);
		jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").html("");
	});
}

function lepopup_record_field_save(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var field_id = jQuery(_button).closest(".lepopup-record-details-table-value").attr("data-id");
	var record_id = jQuery(_button).closest(".lepopup-record-details").attr("data-id");
	var icon = jQuery(_button).find("i").attr("class");
	jQuery(_button).find("i").attr("class", "fas fa-spinner fa-spin");
	var post_data = {"action" : "lepopup-record-field-save", "record-id" : record_id, "field-id" : field_id, "value" : lepopup_encode64(jQuery(_button).closest(".lepopup-record-field-editor").find("textarea, input, select").serialize())};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").fadeOut(300, function(){
						jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-value").html(data.html);
						jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-value").fadeIn(300);
						jQuery(_button).closest(".lepopup-record-details-table-value").find(".lepopup-record-field-editor").html("");
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				console.log(error);
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			jQuery(_button).find("i").attr("class", icon);
			lepopup_sending = false;
			lepopup_dialog_close();
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_button).find("i").attr("class", icon);
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
			lepopup_dialog_close();
		}
	});
	return false;
}

/* Targeting - begin */
var lepopup_target_properties_active = null;
var lepopup_target_loading = false;
var lepopup_targets_deleted = [];
var lepopup_targets_save_requested = false;
function lepopup_tragets_ready() {
	jQuery(window).resize(function() {
		lepopup_targets_resize();
	});
	jQuery(".lepopup-targets-list").sortable({
		connectWith: ".lepopup-targets-list",
		items: ".lepopup-targets-list-item",
		forcePlaceholderSize: true,
		dropOnEmpty: true,
		placeholder: "lepopup-targets-list-item-placeholder",
		start: function(event, ui) {
			jQuery(".lepopup-targets-list-item-animate").removeClass("lepopup-targets-list-item-animate");
		},
		over: function(event, ui) {
			lepopup_targets_switch_noitems();
		},
		out: function(event, ui) {
			lepopup_targets_switch_noitems();
		},
		stop: function(event, ui) {
			lepopup_targets_save_list();
		}
	});
	jQuery(".lepopup-targets-list-item").disableSelection();
}
function lepopup_targets_switch_noitems() {
	if (jQuery("#lepopup-targets-list-passive .lepopup-targets-list-item-placeholder").length > 0 || jQuery("#lepopup-targets-list-passive .lepopup-targets-list-item:not(.ui-sortable-helper)").length > 0) {
		jQuery("#lepopup-targets-list-passive .lepopup-targets-noitems-message").hide();
	} else {
		jQuery("#lepopup-targets-list-passive .lepopup-targets-noitems-message").show();
	}
	if (jQuery("#lepopup-targets-list-active .lepopup-targets-list-item-placeholder").length > 0 || jQuery("#lepopup-targets-list-active .lepopup-targets-list-item:not(.ui-sortable-helper)").length > 0) {
		jQuery("#lepopup-targets-list-active .lepopup-targets-noitems-message").hide();
	} else {
		jQuery("#lepopup-targets-list-active .lepopup-targets-noitems-message").show();
	}
}
function lepopup_targets_resize() {
	if (lepopup_target_properties_active) {
		var popup_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
		var popup_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 960);
		jQuery("#lepopup-target-properties").height(popup_height);
		jQuery("#lepopup-target-properties").width(popup_width);
		jQuery("#lepopup-target-properties .lepopup-admin-popup-inner").height(popup_height);
		jQuery("#lepopup-target-properties .lepopup-admin-popup-content").height(popup_height - 104);
	}
}
function lepopup_target_delete(_event, _target_id) {
	lepopup_dialog_open({
		echo_html:		function() {
			this.html("<div class='lepopup-dialog-message'>"+lepopup_esc_html__("Please confirm that you want to delete the target.", "lepopup")+"</div>");
			this.show();
		},
		ok_label:		'Delete',
		ok_function:	function(e){
			jQuery("#lepopup-targets-list-item-"+_target_id).fadeOut(300, function() {
				jQuery("#lepopup-targets-list-item-"+_target_id).remove();
				lepopup_targets_switch_noitems();
				lepopup_targets_deleted.push(_target_id);
				lepopup_targets_save_list();
			});
			lepopup_dialog_close();
		}
	});
	return false;
}
function lepopup_targets_save_list() {
	if (lepopup_target_loading) {
		lepopup_targets_save_requested = true;
		return false;
	}
	lepopup_target_loading = true;
	lepopup_global_message_show("info", "<i class='fas fa-spinner fa-spin'></i> Saving targets...");
	clearTimeout(lepopup_global_message_timer);
	var post_data = {"action" : "lepopup-targets-save-list", "lepopup-event": jQuery("#lepopup-targets-event").val()};
	var active = [];
	jQuery("#lepopup-targets-list-active .lepopup-targets-list-item").each(function() {
		var id = jQuery(this).attr("data-id");
		active.push(parseInt(id, 10));
	});
	post_data["lepopup-targets-active"] = active.join();
	post_data["lepopup-targets-deleted"] = lepopup_targets_deleted.join();
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			lepopup_target_loading = false;
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (lepopup_targets_save_requested) {
					lepopup_targets_save_requested = false;
					lepopup_targets_save_list();
					return;
				}
				if (status == "OK") {
					lepopup_global_message_show("success", data.message);
				} else if (status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response.", "lepopup"));
				}
			} catch(error) {
				if (lepopup_targets_save_requested) {
					lepopup_targets_save_requested = false;
					lepopup_targets_save_list();
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response.", "lepopup"));
				}
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_target_loading = false;
			if (lepopup_targets_save_requested) {
				lepopup_targets_save_requested = false;
				lepopup_targets_save_list();
			} else {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response.", "lepopup"));
			}
		}
	});
	return false;
}

function lepopup_target_properties_open(_event, _target_id) {
	lepopup_target_loading = true;
	jQuery("#lepopup-target-properties .lepopup-admin-popup-content-form").html("");
	var window_height = 2*parseInt((jQuery(window).height() - 100)/2, 10);
	var window_width = Math.min(Math.max(2*parseInt((jQuery(window).width() - 300)/2, 10), 640), 960);
	jQuery("#lepopup-target-properties").height(window_height);
	jQuery("#lepopup-target-properties").width(window_width);
	jQuery("#lepopup-target-properties .lepopup-admin-popup-inner").height(window_height);
	jQuery("#lepopup-target-properties .lepopup-admin-popup-content").height(window_height - 104);
	jQuery("#lepopup-target-properties-overlay").fadeIn(300);
	jQuery("#lepopup-target-properties").fadeIn(300);
	jQuery("#lepopup-target-properties .lepopup-admin-popup-title h3 span").html("");
	jQuery("#lepopup-target-properties .lepopup-admin-popup-loading").show();
	lepopup_target_properties_active = _target_id;
	var post_data = {"action" : "lepopup-target-properties", "lepopup-event" : _event, "lepopup-id" : lepopup_target_properties_active};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			lepopup_target_loading = false;
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-target-properties .lepopup-admin-popup-content-form").html(data.html);
					jQuery("#lepopup-target-properties .lepopup-admin-popup-title h3 span").html(data.target_name);
					jQuery("#lepopup-target-properties .lepopup-admin-popup-loading").hide();
					jQuery("#lepopup-target-content-posts").scroll(function(e) {
						var content_height = jQuery(this).prop('scrollHeight');
						var position = jQuery(this).scrollTop();
						var height = jQuery(this).height();
						if (content_height - height - position < 10) {
							lepopup_target_posts_load(false);
						}
					});
					jQuery(".lepopup-target-period-date").airdatepicker({
						inline_popup	: true,
						autoClose		: false,
						timepicker		: true,
						dateFormat		: "yyyy-mm-dd",
						timeFormat		: "hh:ii",
						minDate			: new Date()
					});
					jQuery(".lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
						contentAsHTML:	true,
						maxWidth:		360,
						theme:			"tooltipster-dark",
						side:			"bottom",
						content:		"Default",
						functionFormat: function(instance, helper, content){
							return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
						}
					});
				} else if (data.status == "ERROR") {
					lepopup_target_properties_close();
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_target_properties_close();
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_target_properties_close();
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_target_loading = false;
			lepopup_target_properties_close();
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});

	return false;
}
function lepopup_target_properties_close() {
	jQuery("#lepopup-target-properties-overlay").fadeOut(300);
	jQuery("#lepopup-target-properties").fadeOut(300);
	lepopup_target_properties_active = null;
	setTimeout(function(){jQuery("#lepopup-target-properties .lepopup-admin-popup-content-form").html("");}, 1000);
	return false;
}
function lepopup_target_post_type_selected(_object) {
	if (lepopup_target_loading) return false;
	var post_type_id = jQuery(_object).val();
	if (post_type_id == 'sitewide' || post_type_id == 'homepage') {
		jQuery("#lepopup-target-content-taxonomies").slideUp(300, function(){
			jQuery("#lepopup-target-content-loading").hide();
			jQuery("#lepopup-target-content-taxonomies").html("");
		});
		jQuery("#lepopup-target-content-url-keywords").hide();
	} else if (post_type_id == '__url') {
		jQuery("#lepopup-target-content-taxonomies").slideUp(300, function(){
			jQuery("#lepopup-target-content-loading").hide();
			jQuery("#lepopup-target-content-taxonomies").html("");
		});
		jQuery("#lepopup-target-content-url-keywords").slideDown(300);
	} else {
		jQuery("#lepopup-target-content-taxonomies").slideUp(300, function(){
			jQuery("#lepopup-target-content-loading").fadeIn(300);
		});
		jQuery("#lepopup-target-content-url-keywords").hide();
		lepopup_target_loading = true;
		jQuery("#lepopup-target-post-types").addClass("lepopup-target-disabled");
		var post_data = {"action" : "lepopup-target-taxonomies", "lepopup-post-type" : post_type_id, "lepopup-event" : jQuery("#lepopup-targets-event").val()};
		jQuery.ajax({
			type	: "POST",
			url		: lepopup_ajax_handler, 
			data	: post_data,
			success	: function(return_data) {
				jQuery("#lepopup-target-content-loading").hide();
				jQuery("#lepopup-target-post-types").removeClass("lepopup-targets-disabled");
				lepopup_target_loading = false;
				try {
					var data;
					if (typeof return_data == 'object') data = return_data;
					else data = jQuery.parseJSON(return_data);
					var status = data.status;
					if (status == "OK") {
						jQuery("#lepopup-target-content-taxonomies").html(data.html);
						jQuery("#lepopup-target-content-taxonomies").fadeIn(300);
						jQuery("#lepopup-target-content-posts").scroll(function(e) {
							var content_height = jQuery(this).prop('scrollHeight');
							var position = jQuery(this).scrollTop();
							var height = jQuery(this).height();
							if (content_height - height - position < 10) {
								lepopup_target_posts_load(false);
							}
						});
						jQuery("#lepopup-target-content-taxonomies .lepopup-properties-tooltip .lepopup-tooltip-anchor").tooltipster({
							contentAsHTML:	true,
							maxWidth:		360,
							theme:			"tooltipster-dark",
							side:			"bottom",
							content:		"Default",
							functionFormat: function(instance, helper, content){
								return jQuery(helper.origin).parent().find('.lepopup-tooltip-content').html();
							}
						});
					} else if (status == "ERROR") {
						lepopup_global_message_show("danger", data.message);
					} else {
						lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
					}
				} catch(error) {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			},
			error	: function(XMLHttpRequest, textStatus, errorThrown) {
				lepopup_target_loading = false;
				jQuery("#lepopup-target-content-loading").hide();
				jQuery("#lepopup-target-post-types").removeClass("lepopup-targets-disabled");
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		});
	}
	return false;
}
function lepopup_target_taxonomy_selected(_object, _taxonomy) {
	if (lepopup_target_loading) return false;
	var load_posts = true;
	if (!jQuery(_object).is(":checked") && jQuery(_object).val() == "all") {
		load_posts = false;
	}
	if (load_posts) {
		lepopup_target_posts_load(true);
	}
	return false;
}
function lepopup_target_posts_load(_new) {
	if (lepopup_target_loading) return false;
	
	var offset = jQuery("#lepopup-target-next-offset").val();
	if (!_new && offset == -1) return false;
	if (_new) {
		jQuery("#lepopup-target-content-posts").fadeOut(300, function(){
			jQuery("#lepopup-target-content-posts").html('<div id="lepopup-target-posts-loading"><i class="fas fa-spinner fa-spin"></i></div>');
			jQuery("#lepopup-target-content-posts").fadeIn(300);
		});
	} else {
		jQuery("#lepopup-target-content-posts").append('<div id="lepopup-target-posts-loading"><i class="fas fa-spinner fa-spin"></i></div>');
	}
	lepopup_target_loading = true;
	jQuery(".lepopup-target-taxonomies").addClass("lepopup-target-disabled-all");
	jQuery("#lepopup-target-post-types").addClass("lepopup-target-disabled-all");
	if (_new) jQuery("#lepopup-target-next-offset").val(0);
	var post_data = {"action" : "lepopup-target-posts", "lepopup-post-type" : jQuery("[name='lepopup-post-type']:checked").val(), 'lepopup-offset' : jQuery("#lepopup-target-next-offset").val(), "lepopup-posts-all" : (jQuery("#lepopup-target-post-all").is(":checked") ? "on" : "off")};
	jQuery(".lepopup-target-taxonomies").find("input").each(function(){
		var name = jQuery(this).attr("name");
		if (name.indexOf("[]") > 0 && jQuery(this).is(":checked")) {
			name = name.replace("[]", "");
			if (post_data.hasOwnProperty(name)) post_data[name].push(jQuery(this).val());
			else post_data[name] = new Array(jQuery(this).val());
		}
	});
	if (jQuery("#lepopup-id").length) post_data['lepopup-id'] = jQuery("#lepopup-id").val();
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery("#lepopup-target-posts-loading").remove();
			jQuery(".lepopup-target-disabled-all").removeClass("lepopup-target-disabled-all");
			if (_new) jQuery("#lepopup-target-content-posts").html("");
			lepopup_target_loading = false;
			var data;
			try {
				var data = jQuery.parseJSON(return_data);
				var status = data.status;
				if (status == "OK") {
					jQuery("#lepopup-target-next-offset").val(data.next_offset);
					jQuery("#lepopup-target-content-posts").append(data.html);
					jQuery("#lepopup-target-content-posts").fadeIn(300);
				} else if (status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_target_loading = false;
			jQuery("#lepopup-target-posts-loading").remove();
			jQuery(".lepopup-target-disabled-all").removeClass(".lepopup-target-disabled-all");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});
	return false;
}
function lepopup_target_save(_button) {
	if (lepopup_sending) return false;
	lepopup_sending = true;
	var button_object = _button;
	jQuery(button_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(button_object).addClass("lepopup-button-disabled");
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: jQuery(".lepopup-target-properties-form").serialize(),
		success	: function(return_data) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			var data;
			try {
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					if (data.action == 'insert') {
						jQuery("#lepopup-targets-list-active").append(data.html);
						jQuery("#lepopup-targets-list-active").find(".lepopup-targets-noitems-message").hide();
					} else {
						jQuery("#lepopup-targets-list-item-"+data.id).replaceWith(data.html);
					}
					jQuery("#lepopup-targets-list-item-"+data.id).addClass("lepopup-targets-list-item-animate");
					lepopup_target_properties_close();
					lepopup_global_message_show('success', data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(button_object).find("i").attr("class", "fas fa-check");
			jQuery(button_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_sending = false;
		}
	});
	return false;
}
var lepopup_target_onscroll_offset = "";
function lepopup_target_onscroll_units_changed() {
	var lepopup_tmp;
	if (jQuery("#lepopup-onscroll-unit-percents").is(":checked")) {
		lepopup_tmp = jQuery("#lepopup-onscroll-offset").val();
		if (lepopup_target_onscroll_offset == "") lepopup_target_onscroll_offset = lepopup_tmp;
		if (lepopup_target_onscroll_offset > 100) lepopup_target_onscroll_offset = 100;
		jQuery("#lepopup-onscroll-offset").val(lepopup_target_onscroll_offset);
		lepopup_target_onscroll_offset = lepopup_tmp;
	} else {
		lepopup_tmp = jQuery("#lepopup-onscroll-offset").val();
		if (lepopup_target_onscroll_offset != "") jQuery("#lepopup-onscroll-offset").val(lepopup_target_onscroll_offset);
		lepopup_target_onscroll_offset = lepopup_tmp;
	}
}

/* Targeting - end */

function lepopup_input_sort() {
	var input_fields = new Array();
	var fields = new Array();
	for (var i=0; i<lepopup_form_pages.length; i++) {
		if (lepopup_form_pages[i] != null) {
			fields = _lepopup_input_sort(lepopup_form_pages[i]['id'], lepopup_form_pages[i]['id'], lepopup_form_pages[i]['name']);
			if (fields.length > 0) input_fields = input_fields.concat(fields);
		}
	}
	return input_fields;
}
function _lepopup_input_sort(_parent, _page_id, _page_name) {
	var input_fields = new Array();
	var fields = new Array();
	var idxs = new Array();
	var seqs = new Array();
	for (var i=0; i<lepopup_form_elements.length; i++) {
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_form_elements[i]["_parent"] == _parent) {
			idxs.push(i);
			seqs.push(parseInt(lepopup_form_elements[i]["_seq"], 10));
		}
	}
	if (idxs.length == 0) return input_fields;
	var sorted;
	for (var i=0; i<seqs.length; i++) {
		sorted = -1;
		for (var j=0; j<seqs.length-1; j++) {
			if (seqs[j] > seqs[j+1]) {
				sorted = seqs[j];
				seqs[j] = seqs[j+1];
				seqs[j+1] = sorted;
				sorted = idxs[j];
				idxs[j] = idxs[j+1];
				idxs[j+1] = sorted;
			}
		}
		if (sorted == -1) break;
	}
	for (var k=0; k<idxs.length; k++) {
		i = idxs[k];
		if (lepopup_form_elements[i] == null) continue;
		if (lepopup_toolbar_tools.hasOwnProperty(lepopup_form_elements[i]['type']) && lepopup_toolbar_tools[lepopup_form_elements[i]['type']]['type'] == 'input') {
			input_fields.push({"id" : lepopup_form_elements[i]['id'], "name" : lepopup_form_elements[i]['name'], "page-id" : _page_id, "page-name" : _page_name});
		}
	}
	return input_fields;
}

var lepopup_htmlform_connecting = false;
function lepopup_htmlform_connect(_object) {
	if (lepopup_htmlform_connecting) return false;
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-button-disabled");
	lepopup_htmlform_connecting = true;
	var post_data = {"action" : "lepopup-htmlform-connect", "html": jQuery(_object).parent().find("textarea").val()};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).find("i").attr("class", "fas fa-random");
			jQuery(_object).removeClass("lepopup-button-disabled");
			try {
				var data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					var container = jQuery(_object).closest(".lepopup-htmlform-form");
					jQuery(container).fadeOut(300, function() {
						jQuery(container).html(data.html);
						jQuery(container).fadeIn(300);
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_htmlform_connecting = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-random");
			jQuery(_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_htmlform_connecting = false;
		}
	});
	return false;
}
function lepopup_htmlform_disconnect(_object) {
	if (lepopup_htmlform_connecting) return false;
	jQuery(_object).find("i").attr("class", "fas fa-spinner fa-spin");
	jQuery(_object).addClass("lepopup-button-disabled");
	lepopup_htmlform_connecting = true;
	var post_data = {"action" : "lepopup-htmlform-disconnect", "html" : jQuery(_object).closest(".lepopup-htmlform-form").find("input[name='html']").val()};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			jQuery(_object).find("i").attr("class", "fas fa-times");
			jQuery(_object).removeClass("lepopup-button-disabled");
			try {
				var data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					var container = jQuery(_object).closest(".lepopup-htmlform-form");
					jQuery(container).fadeOut(300, function() {
						jQuery(container).html(data.html);
						jQuery(container).fadeIn(300);
					});
					lepopup_global_message_show("success", data.message);
				} else if (data.status == "ERROR") {
					lepopup_global_message_show("danger", data.message);
				} else {
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
			lepopup_htmlform_connecting = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			jQuery(_object).find("i").attr("class", "fas fa-times");
			jQuery(_object).removeClass("lepopup-button-disabled");
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			lepopup_htmlform_connecting = false;
		}
	});
	return false;
}

function lepopup_migrate() {
	if (jQuery("#lepopup-migrate-button").hasClass("lepopup-intro-button-finished")) return false;
	if (lepopup_sending == true) return false;
	lepopup_sending = true;
	jQuery("#lepopup-migrate-button").addClass("lepopup-intro-button-disabled");
	jQuery("#lepopup-migrate-button").html("<i class='lepopup-if lepopup-if-spin lepopup-if-spinner'></i> "+jQuery("#lepopup-migrate-button").attr("data-loading"));
	var post_data = {"action" : "lepopup-migrate"};
	jQuery.ajax({
		type	: "POST",
		url		: lepopup_ajax_handler, 
		data	: post_data,
		success	: function(return_data) {
			lepopup_sending = false;
			try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					jQuery("#lepopup-migrate-status-settings").html("100%");
					jQuery("#lepopup-migrate-button").addClass("lepopup-intro-button-finished");
					jQuery("#lepopup-migrate-button").html(jQuery("#lepopup-migrate-button").attr("data-done"));
					lepopup_global_message_show("success", lepopup_esc_html__("Migration completed."));
				} else if (data.status == "CONTINUE") {
					if (data["data"]["settings"] == "done") jQuery("#lepopup-migrate-status-settings").html("100%");
					else jQuery("#lepopup-migrate-status-settings").html("0%");
					if (data["data"]["popups"]["total"] > 0) jQuery("#lepopup-migrate-status-popups").html(parseInt(100*parseInt(data["data"]["popups"]["done"], 10)/parseInt(data["data"]["popups"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-popups").html("0%");
					if (data["data"]["campaigns"]["total"] > 0) jQuery("#lepopup-migrate-status-campaigns").html(parseInt(100*parseInt(data["data"]["campaigns"]["done"], 10)/parseInt(data["data"]["campaigns"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-campaigns").html("0%");
					if (data["data"]["targets"]["total"] > 0) jQuery("#lepopup-migrate-status-targets").html(parseInt(100*parseInt(data["data"]["targets"]["done"], 10)/parseInt(data["data"]["targets"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-targets").html("0%");
					if (data["data"]["records"]["total"] > 0) jQuery("#lepopup-migrate-status-records").html(parseInt(100*parseInt(data["data"]["records"]["done"], 10)/parseInt(data["data"]["records"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-records").html("0%");
					if (data["data"]["tabs"]["total"] > 0) jQuery("#lepopup-migrate-status-tabs").html(parseInt(100*parseInt(data["data"]["tabs"]["done"], 10)/parseInt(data["data"]["tabs"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-tabs").html("0%");
					if (data["data"]["downloads"]["total"] > 0) jQuery("#lepopup-migrate-status-downloads").html(parseInt(100*parseInt(data["data"]["downloads"]["done"], 10)/parseInt(data["data"]["downloads"]["total"], 10), 10)+"%");
					else jQuery("#lepopup-migrate-status-downloads").html("0%");
					lepopup_migrate();
				} else if (data.status == "ERROR") {
					jQuery("#lepopup-migrate-button").removeClass("lepopup-intro-button-disabled");
					jQuery("#lepopup-migrate-button").html(jQuery("#lepopup-migrate-button").attr("data-label"));
					lepopup_global_message_show("danger", data.message);
				} else {
					jQuery("#lepopup-migrate-button").removeClass("lepopup-intro-button-disabled");
					jQuery("#lepopup-migrate-button").html(jQuery("#lepopup-migrate-button").attr("data-label"));
					lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
				}
			} catch(error) {
				jQuery("#lepopup-migrate-button").removeClass("lepopup-intro-button-disabled");
				jQuery("#lepopup-migrate-button").html(jQuery("#lepopup-migrate-button").attr("data-label"));
				lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			lepopup_sending = false;
			jQuery("#lepopup-migrate-button").removeClass("lepopup-intro-button-disabled");
			jQuery("#lepopup-migrate-button").html(jQuery("#lepopup-migrate-button").attr("data-label"));
			lepopup_global_message_show("danger", lepopup_esc_html__("Something went wrong. We got unexpected server response."));
		}
	});
	return false;
}

var lepopup_gettingstarted_steps = {};
function lepopup_gettingstarted(_screen, _step) {
	var screen_cookie = lepopup_read_cookie("lepopup-gettingstarted-"+_screen);
	if (screen_cookie == "off") return;
	if (jQuery(".lepopup-gettingstarted-overlay").length < 1) {
		jQuery("body").append("<div class='lepopup-gettingstarted-overlay'></div>");
		jQuery(".lepopup-gettingstarted-overlay").fadeIn(1000);
	}
	if (lepopup_gettingstarted_steps.hasOwnProperty(_screen) && _step < lepopup_gettingstarted_steps[_screen].length) {
		jQuery(".lepopup-gettingstarted-highlight").removeClass("lepopup-gettingstarted-highlight");
		jQuery(".lepopup-gettingstarted-bubble").remove();
		
		jQuery(lepopup_gettingstarted_steps[_screen][_step]["selector"]).addClass("lepopup-gettingstarted-highlight");
		var html = "<div class='lepopup-gettingstarted-bubble lepopup-gettingstarted-bubble-"+lepopup_gettingstarted_steps[_screen][_step]["class"]+"' style='"+lepopup_gettingstarted_steps[_screen][_step]["style"]+"'><p>"+lepopup_gettingstarted_steps[_screen][_step]["text"]+"</p><span onclick=\"lepopup_gettingstarted('"+_screen+"', "+(_step+1)+");\">Got it!</span></div>";
		jQuery(".lepopup-gettingstarted-highlight").append(html);
		jQuery(".lepopup-gettingstarted-bubble").fadeIn(300);
	} else {
		jQuery(".lepopup-gettingstarted-overlay").fadeOut(300, function() {
			jQuery(".lepopup-gettingstarted-overlay").remove();
		});
		jQuery(".lepopup-gettingstarted-bubble").fadeOut(300, function() {
			jQuery(".lepopup-gettingstarted-bubble").remove();
		});
		jQuery(".lepopup-gettingstarted-highlight").removeClass("lepopup-gettingstarted-highlight");
		lepopup_write_cookie("lepopup-gettingstarted-"+_screen, "off", 365);
	}
}

function lepopup_email_validator_changed(_object) {
	var value = jQuery(_object).val();
	jQuery(".lepopup-email-validator-options").hide();
	jQuery(".lepopup-email-validator-"+value).fadeIn(200);
	return false;
}

function lepopup_geoip_service_changed(_object) {
	var value = jQuery(_object).val();
	jQuery(".lepopup-geoip-service-options").hide();
	jQuery(".lepopup-geoip-service-"+value).fadeIn(200);
	return false;
}

var lepopup_global_message_timer;
function lepopup_global_message_show(_type, _message) {
	clearTimeout(lepopup_global_message_timer);
	jQuery("#lepopup-global-message").fadeOut(300, function() {
		jQuery("#lepopup-global-message").attr("class", "");
		jQuery("#lepopup-global-message").addClass("lepopup-global-message-"+_type).html(_message);
		jQuery("#lepopup-global-message").fadeIn(300);
		lepopup_global_message_timer = setTimeout(function(){jQuery("#lepopup-global-message").fadeOut(300);}, 5000);
	});
}

function lepopup_escape_html(_text) {
	if (typeof _text != typeof "string") return _text;
	if (!_text) return "";
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};
	return _text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
function lepopup_is_numeric(_text) {
	return !isNaN(parseInt(_text)) && isFinite(_text);
}
function lepopup_random_string(_length) {
	var length, text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	if (typeof _length == "undefined") length = 16;
	else length = _length;
	for (var i=0; i<length; i++) text += possible.charAt(Math.floor(Math.random() * possible.length));
	return text;
}

function lepopup_utf8encode(string) {
	string = string.replace(/\x0d\x0a/g, "\x0a");
	var output = "";
	for (var n = 0; n < string.length; n++) {
		var c = string.charCodeAt(n);
		if (c < 128) {
			output += String.fromCharCode(c);
		} else if ((c > 127) && (c < 2048)) {
			output += String.fromCharCode((c >> 6) | 192);
			output += String.fromCharCode((c & 63) | 128);
		} else {
			output += String.fromCharCode((c >> 12) | 224);
			output += String.fromCharCode(((c >> 6) & 63) | 128);
			output += String.fromCharCode((c & 63) | 128);
		}
	}
	return output;
}
function lepopup_encode64(input) {
	var keyString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var output = "";
	var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;
	input = lepopup_utf8encode(input);
	while (i < input.length) {
		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);
		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;
		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}
		output = output + keyString.charAt(enc1) + keyString.charAt(enc2) + keyString.charAt(enc3) + keyString.charAt(enc4);
	}
	return output;
}
function lepopup_utf8decode(input) {
	var string = "";
	var i = 0;
	var c = 0, c1 = 0, c2 = 0;
	while ( i < input.length ) {
		c = input.charCodeAt(i);
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		} else if ((c > 191) && (c < 224)) {
			c2 = input.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = input.charCodeAt(i+1);
			c3 = input.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}
	return string;
}
function lepopup_decode64(input) {
	var keyString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;
	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	while (i < input.length) {
		enc1 = keyString.indexOf(input.charAt(i++));
		enc2 = keyString.indexOf(input.charAt(i++));
		enc3 = keyString.indexOf(input.charAt(i++));
		enc4 = keyString.indexOf(input.charAt(i++));
		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;
		output = output + String.fromCharCode(chr1);
		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}
	}
	output = lepopup_utf8decode(output);
	return output;
}
function lepopup_esc_html__(_string) {
	var string;
	if (typeof lepopup_translations == typeof {} && lepopup_translations.hasOwnProperty(_string)) {
		string = lepopup_translations[_string];
		if (string.length == 0) string = _string;
	} else string = _string;
	return lepopup_escape_html(string);
}
function lepopup_read_cookie(key) {
	var pairs = document.cookie.split("; ");
	for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
		if (pair[0] === key) return pair[1] || "";
	}
	return null;
}
function lepopup_write_cookie(key, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = key+"="+value+expires+"; path=/";
}
