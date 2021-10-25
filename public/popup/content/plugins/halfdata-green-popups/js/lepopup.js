"use strict";
var lepopup_vars = {};
var lepopup_consts = {};
var lepopup_sending = false;
var lepopup_popup_loading = false;
var lepopup_popup_active_id = null;
var lepopup_campaign_active_slug = null;
var lepopup_popup_active_page_id = null;
var lepopup_seq_pages = {};
var lepopup_signatures = {};
var lepopup_mobile = (function(a){if(/(android|bb\d+|meego).+mobile|android|ipad|playbook|silk|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))return true; else return false;})(navigator.userAgent||navigator.vendor||window.opera);
var lepopup_uploads = {};
var lepopup_sessions = {};
var lepopup_custom_events_data = {};
var lepopup_onload_displayed = false;
var lepopup_onexit_displayed = false;
var lepopup_onscroll_displayed = false;
var lepopup_onidle_displayed = false;
var lepopup_onabd_displayed = false;
var lepopup_timeout;
var lepopup_onidle_timer;
var lepopup_onidle_counter = 0;
var lepopup_forced_location = null; //linklocker
var lepopupext_open_before;
var lepopupext_close_after;
var lepopupext_submit_after;
if (typeof lepopup_preview == typeof undefined) {
	var lepopup_preview = "off";
}
if (typeof lepopup_customjs_handlers == typeof undefined) {
	var lepopup_customjs_handlers = {};
}
if (window.jQuery) {
/*
	jQuery("a").each(function() {
		var lepopup_id = jQuery(this).attr("href");
		if (lepopup_id) {
			var prefix = "#lepopup-";
			var lepopup_idx = lepopup_id.indexOf(prefix);
			if (lepopup_idx >= 0) {
				jQuery(this).on("click", function(e) {
					e.preventDefault();
					return false;
				});
			}
		}
	});
*/
	var now = new Date();
	lepopup_consts = {
		"url"			: window.location.href,
		"page-title"	: jQuery(document).find("title").text(),
		"ip" 			: "",
		"user-agent"	: navigator.userAgent,
		"date"			: now.getFullYear()+"-"+(now.getMonth()+1 < 10 ? "0"+(now.getMonth()+1) : (now.getMonth()+1))+"-"+(now.getDate() < 10 ? "0"+now.getDate() : now.getDate()),
		"time"			: (now.getHours() < 10 ? "0"+now.getHours() : now.getHours())+":"+(now.getMinutes() < 10 ? "0"+now.getMinutes() : now.getMinutes()),
		"wp-user-login"	: "",
		"wp-user-email"	: ""
	};
	jQuery(document).ready(function(){
		if (typeof lepopup_ajax_url != typeof undefined) {
			lepopup_vars["mode"] = "local";
			lepopup_vars["cookie-value"] = lepopup_cookie_value;
			lepopup_vars["ajax-url"] = lepopup_ajax_url;
			lepopup_vars["overlays"] = lepopup_overlays;
			lepopup_vars["campaigns"] = lepopup_campaigns;
			lepopup_vars["ga-tracking"] = lepopup_ga_tracking;
			lepopup_vars["abd-enabled"] = lepopup_abd_enabled;
			lepopup_vars["events-data"] = lepopup_events_data;
			if (typeof lepopup_ulp != typeof undefined && lepopup_ulp == "on") lepopup_vars["ulp-active"] = "on";
			else lepopup_vars["ulp-active"] = "off";
			if (lepopup_async_init == 'on') {
				var inline_slugs = new Array();
				var i = 0;
				jQuery(".lepopup-inline").each(function() {
					var inline_slug = jQuery(this).attr("data-slug");
					if (inline_slug) {
						jQuery(this).attr("id", "lepopup-inline-"+i);
						inline_slugs.push(inline_slug);
						i++;
					}
				});
				if (lepopup_vars["ulp-active"] == "off") {
					jQuery(".ulp-inline").each(function() {
						var inline_slug = jQuery(this).attr("data-id");
						if (inline_slug) {
							jQuery(this).attr("id", "lepopup-inline-"+i);
							inline_slugs.push(inline_slug);
							i++;
						}
					});
				}
				var post_data = {"action" : "lepopup-async-init", "inline-slugs" : inline_slugs.join(','), "content-id" : lepopup_content_id, "referrer" : document.referrer, "hostname" : window.location.hostname, "url": window.location.href};
				if (typeof lepopup_icl_language != typeof undefined) post_data['wpml-language'] = lepopup_icl_language;
				jQuery.ajax({
					url: 		lepopup_vars["ajax-url"],
					data: 		post_data,
					type: 		"POST",
					async:		true,
					success: 	function(return_data) {
						var data;
						try {
							if (typeof return_data == 'object') data = return_data;
							else data = jQuery.parseJSON(return_data);
							var status = data.status;
							if (status == "OK") {
								lepopup_vars["events-data"] = data["events-data"];
								if (data["items-html"]) {
									jQuery("body").append(data["items-html"]);
								}
								for (var i=0; i<(data["inline-forms"]).length; i++){
									jQuery("#lepopup-inline-"+i).replaceWith((data["inline-forms"])[i]);
								}
								lepopup_ready();
							}
						} catch(error) {
							console.log(error);
							lepopup_ready();
						}
					},
					error	: function(XMLHttpRequest, textStatus, errorThrown) {
						lepopup_ready();
					}
				});
			} else {
				lepopup_ready();
			}
		} else {
			lepopup_vars["mode"] = "remote";
			lepopup_vars["events-data"] = {};
			if (jQuery("#ulp-remote").length > 0) lepopup_vars["ulp-active"] = "on";
			else lepopup_vars["ulp-active"] = "off";
			if (jQuery("#lepopup-remote").length == 0 || !jQuery("#lepopup-remote").attr("data-handler")) {
				alert('Make sure that you properly included lepopup.js. Currently you did not.');
			}
			if (jQuery("#lepopup-remote").attr("data-preview") == "on") lepopup_preview = "on";
			lepopup_vars["ajax-url"] = jQuery("#lepopup-remote").attr("data-handler");
			jQuery('head').append("<style>#lepopup-ready{display:none;width:0px;height:0px;}</style>");
			var inline_slugs = new Array();
			var i = 0;
			jQuery(".lepopup-inline").each(function() {
				var inline_slug = jQuery(this).attr("data-slug");
				if (inline_slug) {
					jQuery(this).attr("id", "lepopup-inline-"+i);
					inline_slugs.push(inline_slug);
					i++;
				}
			});
			if (lepopup_vars["ulp-active"] == "off") {
				jQuery(".ulp-inline").each(function() {
					var inline_slug = jQuery(this).attr("data-id");
					if (inline_slug) {
						jQuery(this).attr("id", "lepopup-inline-"+i);
						inline_slugs.push(inline_slug);
						i++;
					}
				});
			}
			jQuery('body').append("<div id='lepopup-ready'></div>");
			jQuery.ajax({
				url		: 	lepopup_vars['ajax-url'],
				data	: 	{"action" : "lepopup-remote-init", "inline-slugs" : inline_slugs.join(','), "preview" : lepopup_preview, "hostname" : window.location.hostname},
				method	:	(lepopup_vars["mode"] == "remote" ? "get" : "post"),
				dataType:	(lepopup_vars["mode"] == "remote" ? "jsonp" : "json"),
				async	:	true,
				success	: function(return_data) {
					try {
						var data, temp;
						if (typeof return_data == 'object') data = return_data;
						else data = jQuery.parseJSON(return_data);
						if (data.status == "OK") {
							lepopup_vars["cookie-value"] = data["cookie-value"];
							lepopup_vars["overlays"] = data["overlays"];
							lepopup_vars["campaigns"] = data["campaigns"];
							lepopup_vars["ga-tracking"] = data["ga-tracking"];
							lepopup_vars["abd-enabled"] = data["adb-enabled"];
							lepopup_vars["plugins"] = data["plugins"];
							for (var i=0; i<(data["inline-forms"]).length; i++){
								jQuery("#lepopup-inline-"+i).html((data["inline-forms"])[i]);
							}
							if (typeof data["resources"]["css"] != 'undefined') {
								for (var i=0; i<(data["resources"]["css"]).length; i++){
									jQuery('head').append("<link href='"+(data["resources"]["css"])[i]+"' rel='stylesheet' type='text/css' media='all' />");
								}
							}
							if (typeof data["resources"]["js"] != 'undefined') {
								for (var i=0; i<(data["resources"]["js"]).length; i++){
									if (typeof data["resources"]["js"][i] === typeof '') {
										jQuery('body').append("<script src='"+(data["resources"]["js"])[i]+"' type='text/javascript'></script>");
									} else if (typeof data["resources"]["js"][i] === typeof {}) {
										temp = "<script type='text/javascript'";
										for (var option_key in data["resources"]["js"][i]) {
											if (data["resources"]["js"][i].hasOwnProperty(option_key)) {
												temp += " "+lepopup_escape_html(option_key)+"='"+lepopup_escape_html(data["resources"]["js"][i][option_key])+"'";
											}
										}
										temp += "></script>";
										jQuery('body').append(temp);
									}
								}
							}
							if (data.hasOwnProperty("consts")) {
								if (typeof Object.assign == "function") {
									lepopup_consts = Object.assign(lepopup_consts, data["consts"]);
								} else {
									for (var key in data["consts"]) {
										if (data["consts"].hasOwnProperty(key)) {
											lepopup_consts[key] = data["consts"][key];
										}
									}
								}
							}
							var counter = 50;
							var ready = function() {
								counter--;
								if (counter == 0) {
									console.log("Can't load style.css.");
									return;
								}
								var width =  jQuery("#lepopup-ready").width();
								if (width == 1) {
									lepopup_ready();
								} else {
									setTimeout(ready, 200);
								}
							}
							ready();
						}
					} catch(error) {
						console.log(error);
					}
				},
				error	: 	function(XMLHttpRequest, textStatus, errorThrown) {
					console.log(errorThrown);
				}
			});
		}
		jQuery(window).on('beforeunload', function(e){
			var session_length;
			if (!jQuery.isEmptyObject(lepopup_sessions)) {
				for (var form_id in lepopup_sessions) {
					session_length = jQuery(".lepopup-form-"+form_id).attr("data-session");
					if (lepopup_is_numeric(session_length) && session_length > 0) {							
						if (lepopup_sessions.hasOwnProperty(form_id)) {
							if (lepopup_sessions[form_id]["modified"] == true) {
								lepopup_write_cookie("lepopup-session-"+form_id, JSON.stringify(lepopup_sessions[form_id]["values"]), session_length);
							}
						}
					}
				}
			}
			return;
		});
	});
} else {
	alert('lepopup.js requires jQuery to be loaded. Please include jQuery library above lepopup.js. Do not use "defer" or "async" option to load jQuery.');
}

function lepopup_ready() {
	lepopup_resize();
	jQuery(window).resize(function() {
		lepopup_resize();
	});
	var processed_forms = new Array();
	var processed_form_ids = new Array();
	
	jQuery(".lepopup-inline").each(function(){
		jQuery(this).find(".lepopup-form").each(function(){
			var id = jQuery(this).attr("data-id");
			var form_id = jQuery(this).attr("data-form-id");
			if (processed_forms.indexOf(id) >= 0) return true;
			processed_forms.push(id);
			if (processed_form_ids.indexOf(form_id) < 0) processed_form_ids.push(form_id);
		});
	});
	if (processed_form_ids.length > 0) {
		lepopup_add_impression(processed_form_ids.join(","), null);
	}
	for (var i=0; i<processed_forms.length; i++) {
		lepopup_reset_form(processed_forms[i]);
		lepopup_handle_visibility(processed_forms[i], null, true);
		jQuery(".lepopup-form-"+processed_forms[i]).each(function(){
			var page_id = jQuery(this).attr("data-page");
			if (lepopup_is_visible(processed_forms[i], page_id)) {
				jQuery(this).find(".lepopup-element[data-content]").each(function(){
					jQuery(this).find(".lepopup-element-html-content").html(lepopup_decode64(jQuery(this).attr("data-content")));
				});
				jQuery(this).show();
				return false;
			}
		});
		if (lepopup_customjs_handlers.hasOwnProperty(processed_forms[i])) {
			lepopup_customjs_handlers[processed_forms[i]].errors = {};
			if (lepopup_customjs_handlers[processed_forms[i]].hasOwnProperty("afterinit") && typeof lepopup_customjs_handlers[processed_forms[i]].afterinit == 'function') {
				try {
					lepopup_customjs_handlers[processed_forms[i]].afterinit();
				} catch(error) {
				}
			}
		}
	}
	jQuery("a").each(function() {
		var slug = jQuery(this).attr("href");
		if (slug) {
			var idx = slug.indexOf("#");
			if (idx < 0) return true;
			slug = slug.substr(idx);
			var full_hash = slug;
			slug = slug.replace("#lepopup-", "");
			if (lepopup_vars["ulp-active"] == "off") slug = slug.replace("#ulp-", "").replace("#ulpx-", "");
			if (full_hash != slug) {
// linklocker-begin
				idx = slug.indexOf(":");
				if (idx > 0) {
					var encoded_url = slug.substr(idx + 1);
					slug = slug.substr(0, idx);
					var item_slugs = slug.split("*");
					var item_slug = item_slugs[0];
					if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
					if (encoded_url.length > 0) {
						encoded_url = lepopup_decode64(encoded_url);
						if (item_slug == "") {
							jQuery(this).attr("href", encoded_url);
						} else {
							var lepopup_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
							if (lepopup_cookie == lepopup_vars['cookie-value']) {
								jQuery(this).attr("href", encoded_url);
								return;
							} else jQuery(this).addClass("lepopup-linklocker-"+item_slug);
						}
					}
				}
// linklocker-end
				jQuery(this).on("click", function(e) {
					e.preventDefault();
					var slug = jQuery(this).attr("href");
					var idx = slug.indexOf("#");
					if (idx < 0) return;
					slug = slug.substr(idx);
					slug = slug.replace("#lepopup-", "");
					if (lepopup_vars["ulp-active"] == "off") slug = slug.replace("#ulp-", "").replace("#ulpx-", "");
// linklocker-begin
					idx = slug.indexOf(":");
					if (idx > 0) {
						var encoded_url = lepopup_decode64(slug.substr(idx + 1));
						if (encoded_url.length > 0) lepopup_forced_location = encoded_url;
						slug = slug.substr(0, idx);
					}
// linklocker-end
					lepopup_popup_open(slug);
					return false;
				});
			}
		}
	});
	lepopup_mask_init("input.lepopup-mask");
	lepopup_datepicker_init("input.lepopup-date");
	lepopup_timepicker_init("input.lepopup-time");
	lepopup_signature_init("canvas.lepopup-signature");
	lepopup_rangeslider_init("input.lepopup-rangeslider");
	for (var i=0; i<processed_form_ids.length; i++) {
		lepopup_tooltips_init(".lepopup-form-"+processed_form_ids[i], processed_form_ids[i], "dark");
	}
	
	var slug = window.location.hash;
	var idx = slug.indexOf("#");
	if (idx >= 0) {
		slug = slug.substr(idx);
		var full_hash = slug;
		slug = slug.replace("#lepopup-", "");
		if (lepopup_vars["ulp-active"] == "off") slug = slug.replace("#ulp-", "").replace("#ulpx-", "");
		if (full_hash != slug && slug.length > 0) {
	// linklocker - begin
			var redirecting = false;
			idx = slug.indexOf(":");
			if (idx > 0) {
				var encoded_url = slug.substr(idx + 1);
				slug = slug.substr(0, idx);
				var item_slugs = slug.split("*");
				var item_slug = item_slugs[0];
				if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
				if (encoded_url.length > 0) {
					encoded_url = lepopup_decode64(encoded_url);
					if (item_slug == "") {
						location.href = encoded_url;
						redirecting = true;
					} else {
						var lepopup_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
						if (lepopup_cookie == lepopup_vars['cookie-value']) {
							location.href = encoded_url;
							redirecting = true;
						} else lepopup_forced_location = encoded_url;
					}
				}
			}
			if (!redirecting) {
	// linklocker - end
				lepopup_popup_open(slug);
				lepopup_onload_displayed = true;
	// linklocker - begin
			}
	// linklocker - end
		}
	}
	jQuery(document).keyup(function(e) {
		if (lepopup_popup_active_id) {
			if (jQuery(".lepopup-form-"+lepopup_popup_active_id).first().attr("data-esc") == "on") {
				if (e.keyCode == 27) lepopup_close(0);
			}
		}
		if (e.keyCode == 13) {
			if (jQuery(document.activeElement).parent().hasClass("lepopup-input")) {
				if (jQuery(document.activeElement).prop("tagName").toLowerCase() == "textarea" && !e.ctrlKey) {
					return;
				}
				var popup = jQuery(document.activeElement).closest(".lepopup-form");
				if (popup) {
					if (jQuery(popup).attr("data-enter") == "on") {
						lepopup_submit(document.activeElement);
					}
				}
			}
		}
	});
	jQuery(window).resize();
	lepopup_events_init();
	console.log("Green Popups is ready to go!");
}
function lepopup_resize() {
	_lepopup_resize_active_popup(lepopup_popup_active_page_id);
	jQuery(".lepopup-inline").each(function() {
		var device = jQuery(this).attr("data-device");
		if ((device == 'mobile' && !lepopup_mobile) || (device == 'desktop' && lepopup_mobile)) {
			jQuery(this).hide();
		} else {
			jQuery(this).find(".lepopup-form").each(function() {
				var viewport_width = Math.max(120, jQuery(this).parent().innerWidth());
				var width = parseInt(jQuery(this).attr("data-width"), 10);
				var height = parseInt(jQuery(this).attr("data-height"), 10);
				
				var scale = viewport_width/width;
				if (scale > 1) scale = 1;
				
				jQuery(this).css({
					"width" : parseInt(width*scale, 10),
					"height" : parseInt(height*scale, 10)
				});
				jQuery(this).find(".lepopup-form-inner").css({
					"transform" : "translate(-"+parseInt(width*(1-scale)/2, 10)+"px, -"+parseInt(height*(1-scale)/2, 10)+"px) scale("+scale+")"
				});
			});
		}
	});
}
function _lepopup_resize_active_popup(_page_id) {
	if (!lepopup_popup_active_id || !_page_id) return;
	var active_page = jQuery("#lepopup-popup-"+lepopup_popup_active_id+" .lepopup-form[data-page='"+_page_id+"']");
	var viewport = {
		width: Math.max(240, jQuery(window).width()),
		height: Math.max(120, jQuery(window).height())
	};
	var width = parseInt(jQuery(active_page).attr("data-width"), 10);
	var height = parseInt(jQuery(active_page).attr("data-height"), 10);
	var scale = Math.min((viewport.width-20)/width, viewport.height/height);
	if (scale > 1) scale = 1;

	var middle_position = "-50%";
	var bottom_sign = "";
//	if (lepopup_mobile) {
		scale = Math.min((viewport.width-20)/width, 1);
		if (height*scale > viewport.height) {
			jQuery(active_page).parent().addClass("lepopup-popup-fh-container");
			middle_position = "-"+height*(1-scale)/2+"px";
			bottom_sign = "-";
		} else {
			jQuery(active_page).parent().removeClass("lepopup-popup-fh-container");
		}
//	}
	var position = jQuery(active_page).attr("data-position");
	var translate = "";
	switch (position) {
		case 'top-left':
			translate = "translate(-"+width*(1-scale)/2+"px,-"+height*(1-scale)/2+"px) ";
			break;
		case 'top-right':
			translate = "translate("+width*(1-scale)/2+"px,-"+height*(1-scale)/2+"px) ";
			break;
		case 'bottom-left':
			translate = "translate(-"+width*(1-scale)/2+"px,"+bottom_sign+height*(1-scale)/2+"px) ";
			break;
		case 'bottom-right':
			translate = "translate("+width*(1-scale)/2+"px,"+bottom_sign+height*(1-scale)/2+"px) ";
			break;
		case 'top-center':
			translate = "translate(-50%,-"+height*(1-scale)/2+"px) ";
			break;
		case 'bottom-center':
			translate = "translate(-50%,"+bottom_sign+height*(1-scale)/2+"px) ";
			break;
		case 'middle-left':
			translate = "translate(-"+width*(1-scale)/2+"px,"+middle_position+") ";
			break;
		case 'middle-right':
			translate = "translate("+width*(1-scale)/2+"px,"+middle_position+") ";
			break;
		default:
			translate = "translate(-50%,"+middle_position+") ";
			break;
	}
	jQuery(active_page).css({"transform" : translate+"scale("+scale+")"});
}

function lepopup_events_init() {
	var item_slug = null, item_slugs, event_cookie;
	
	try {
		var url = new URL(document.location);
		var disable_raw = url.searchParams.get("lepopup-disable");
		if (disable_raw != null) {
			var disable_items = disable_raw.split(",");
			for (var i=0; i<disable_items.length; i++) {
				item_slug = (disable_items[i]).trim();
				if (item_slug.length > 0 && lepopup_vars["overlays"].hasOwnProperty(item_slug)) {
					lepopup_write_cookie("lepopup-submit-"+item_slug, lepopup_vars["cookie-value"], 365*24);
				}
			}
		}
	} catch(error) {
		console.log(error);
	}
	
	lepopup_vars["events-data"] = Object.assign(lepopup_vars["events-data"], lepopup_custom_events_data);
	if (lepopup_vars["abd-enabled"] == 'on') {
		if (lepopup_vars["events-data"].hasOwnProperty("onabd-item")) {
			item_slugs = lepopup_vars["events-data"]["onabd-item"].split("*");
			item_slug = item_slugs[0];
			if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
			if (item_slug != "") {
				var event_cookie = lepopup_read_cookie("lepopup-onabd-"+item_slug);
				var slug_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
				if (event_cookie != lepopup_vars["cookie-value"] && slug_cookie != lepopup_vars["cookie-value"]) {
					if (!lepopup_popup_active_id && !lepopup_onabd_displayed) {
						if (typeof window.google_ad_status == typeof undefined || window.google_ad_status != 1) {
							if (lepopup_vars["events-data"]["onabd-mode"] == "once-only") lepopup_write_cookie("lepopup-onabd-"+item_slug, lepopup_vars["cookie-value"], 365*24);
							else if (lepopup_vars["events-data"]["onabd-mode"] == "once-period") lepopup_write_cookie("lepopup-onabd-"+item_slug, lepopup_vars["cookie-value"], lepopup_vars["events-data"]["onabd-mode-period"]);
							lepopup_popup_open(item_slug);
							lepopup_onabd_displayed = true;
						}
					}
				}
			}
		}
	}
	if (lepopup_vars["events-data"].hasOwnProperty("onload-item")) {
		item_slugs = lepopup_vars["events-data"]["onload-item"].split("*");
		item_slug = item_slugs[0];
		if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
		if (item_slug != "") {
			var event_cookie = lepopup_read_cookie("lepopup-onload-"+item_slug);
			var slug_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
			if (event_cookie != lepopup_vars["cookie-value"] && slug_cookie != lepopup_vars["cookie-value"]) {
				var onload_open = function(_slug) {
					if (!lepopup_popup_active_id && !lepopup_onload_displayed) {
						if (lepopup_vars["events-data"]["onload-mode"] == "once-only") lepopup_write_cookie("lepopup-onload-"+_slug, lepopup_vars["cookie-value"], 365*24);
						else if (lepopup_vars["events-data"]["onload-mode"] == "once-period") lepopup_write_cookie("lepopup-onload-"+_slug, lepopup_vars["cookie-value"], lepopup_vars["events-data"]["onload-mode-period"]);
						lepopup_popup_open(_slug);
						lepopup_onload_displayed = true;
						if (parseInt(lepopup_vars["events-data"]["onload-mode-close-delay"], 10) > 0) {
							lepopup_timeout = setTimeout(function() {lepopup_popup_active_close(0);}, parseInt(lepopup_vars["events-data"]["onload-mode-close-delay"], 10)*1000);
						}
					}
				};
				var onload_slug = item_slug;
				if (parseInt(lepopup_vars["events-data"]["onload-mode-delay"], 10) <= 0) {
					onload_open(onload_slug);
				} else {
					setTimeout(function() {
						onload_open(onload_slug);
					}, parseInt(lepopup_vars["events-data"]["onload-mode-delay"], 10)*1000);
				}
			}
		}
	}
	if (lepopup_vars["events-data"].hasOwnProperty("onexit-item")) {
		item_slugs = lepopup_vars["events-data"]["onexit-item"].split("*");
		item_slug = item_slugs[0];
		if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
		if (item_slug != "") {
			var event_cookie = lepopup_read_cookie("lepopup-onexit-"+item_slug);
			var slug_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
			if (event_cookie != lepopup_vars["cookie-value"] && slug_cookie != lepopup_vars["cookie-value"]) {
				var onexit_slug = item_slug;
				jQuery(document).bind('mouseleave', function(e) {
					var mouseY = parseInt(e.pageY - jQuery(window).scrollTop(), 10);
					if (!lepopup_popup_active_id && !lepopup_onexit_displayed && mouseY < 20) {
						if (lepopup_vars["events-data"]["onexit-mode"] == "once-only") lepopup_write_cookie("lepopup-onexit-"+onexit_slug, lepopup_vars["cookie-value"], 365*24);
						else if (lepopup_vars["events-data"]["onexit-mode"] == "once-period") lepopup_write_cookie("lepopup-onexit-"+onexit_slug, lepopup_vars["cookie-value"], lepopup_vars["events-data"]["onexit-mode-period"]);
						lepopup_popup_open(onexit_slug);
						lepopup_onexit_displayed = true;
					}
				});
			}
		}
	}
	if (lepopup_vars["events-data"].hasOwnProperty("onscroll-item")) {
		item_slugs = lepopup_vars["events-data"]["onscroll-item"].split("*");
		item_slug = item_slugs[0];
		if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
		if (item_slug != "") {
			var event_cookie = lepopup_read_cookie("lepopup-onscroll-"+item_slug);
			var slug_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
			if (event_cookie != lepopup_vars["cookie-value"] && slug_cookie != lepopup_vars["cookie-value"]) {
				var onscroll_slug = item_slug;
				jQuery(window).scroll(function(e) {
					if (!lepopup_popup_active_id && !lepopup_onscroll_displayed) {
						var position = jQuery(window).scrollTop();
						var offset = parseInt(lepopup_vars["events-data"]["onscroll-mode-offset"], 10);
						if (lepopup_vars["events-data"]["onscroll-mode-offset"].indexOf("%") > 0) {
							if (offset > 100) offset = 100;
							offset = parseInt((jQuery(document).height() - jQuery(window).height())*offset/100, 10);
						}
						if (position > offset) {
							if (lepopup_vars["events-data"]["onscroll-mode"] == "once-only") lepopup_write_cookie("lepopup-onscroll-"+onscroll_slug, lepopup_vars["cookie-value"], 365*24);
							else if (lepopup_vars["events-data"]["onscroll-mode"] == "once-period") lepopup_write_cookie("lepopup-onscroll-"+onscroll_slug, lepopup_vars["cookie-value"], lepopup_vars["events-data"]["onscroll-mode-period"]);
							lepopup_popup_open(onscroll_slug);
							lepopup_onscroll_displayed = true;
						}
					}
				});
			}
		}
	}
	if (lepopup_vars["events-data"].hasOwnProperty("onidle-item")) {
		item_slugs = lepopup_vars["events-data"]["onidle-item"].split("*");
		item_slug = item_slugs[0];
		if (item_slugs.length > 1 && lepopup_mobile) item_slug = item_slugs[1];
		if (item_slug != "") {
			var event_cookie = lepopup_read_cookie("lepopup-onidle-"+item_slug);
			var slug_cookie = lepopup_read_cookie("lepopup-submit-"+item_slug);
			if (event_cookie != lepopup_vars["cookie-value"] && slug_cookie != lepopup_vars["cookie-value"]) {
				var onidle_slug = item_slug;
				jQuery(window).mousemove(function(event) {
					lepopup_onidle_counter = 0;
				});
				jQuery(window).click(function(event) {
					lepopup_onidle_counter = 0;
				});
				jQuery(window).keypress(function(event) {
					lepopup_onidle_counter = 0;
				});
				jQuery(window).scroll(function(event) {
					lepopup_onidle_counter = 0;
				});
				var onidle_counter_handler = function() {
					if (lepopup_onidle_counter >= lepopup_vars["events-data"]["onidle-mode-delay"]) {
						if (!lepopup_popup_active_id && !lepopup_onidle_displayed) {
							if (lepopup_vars["events-data"]["onidle-mode"] == "once-only") lepopup_write_cookie("lepopup-onidle-"+onidle_slug, lepopup_vars["cookie-value"], 365*24);
							else if (lepopup_vars["events-data"]["onidle-mode"] == "once-period") lepopup_write_cookie("lepopup-onidle-"+onidle_slug, lepopup_vars["cookie-value"], lepopup_vars["events-data"]["onidle-mode-period"]);
							lepopup_popup_open(onidle_slug);
							lepopup_onidle_displayed = true;
						}
						return;
					} else {
						lepopup_onidle_counter = lepopup_onidle_counter + 1;
					}
					lepopup_onidle_timer = setTimeout(onidle_counter_handler, 1000);
				}
				lepopup_onidle_timer = setTimeout(onidle_counter_handler, 1000);
			}
		}
	}
}

function lepopup_add_impression(_from_ids, _campaign_slug) {
	jQuery.ajax({
		url		: 	lepopup_vars['ajax-url'],
		data	: 	{"action" : "lepopup-front-add-impression", "campaign-slug" : _campaign_slug, "form-ids" : _from_ids, "hostname" : window.location.hostname},
		method	:	(lepopup_vars["mode"] == "remote" ? "get" : "post"),
		dataType:	(lepopup_vars["mode"] == "remote" ? "jsonp" : "json"),
		async	:	true,
		success	: function(return_data) {
		try {
				var data;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					if (data.hasOwnProperty("consts")) {
						if (typeof Object.assign == "function") {
							lepopup_consts = Object.assign(lepopup_consts, data["consts"]);
						} else {
							for (var key in data["consts"]) {
								if (data["consts"].hasOwnProperty(key)) {
									lepopup_consts[key] = data["consts"][key];
								}
							}
						}
						lepopup_consts_update(null, data["consts"]);
					}
				}
			} catch(error) {
				console.log(error);
			}
		},
		error	: 	function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(errorThrown);
		}
	});
}
function lepopup_datepicker_init(_set) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("airdatepicker") >= 0 && typeof jQuery.fn.airdatepicker == typeof undefined) {
		setTimeout(function(){lepopup_datepicker_init(_set);}, 1000);
	}
	if (typeof jQuery.fn.airdatepicker == typeof undefined) return;
	jQuery(_set).each(function(){
		var object = this;
		var airdatepicker = jQuery(object).airdatepicker().data('airdatepicker');
		airdatepicker.destroy();
		jQuery(object).airdatepicker({
			inline_popup	: true,
			autoClose		: true,
			timepicker		: false,
			dateFormat		: jQuery(object).attr("data-format"),
			language		: jQuery(object).attr("data-locale"),
			onSelect		: function(formattedDate, date, inst) {
				lepopup_input_changed(object);
			},
			onShow			: function(inst, animationCompleted) {
				var content;
				var min_type = jQuery(object).attr("data-min-type");
				var min_value = jQuery(object).attr("data-min-value");
				var min_date = null;
				switch(min_type) {
					case 'today':
						min_date = new Date();
						break;
					case 'yesterday':
						min_date = new Date();
						min_date.setDate(min_date.getDate() - 1);
						break;
					case 'tomorrow':
						min_date = new Date();
						min_date.setDate(min_date.getDate() + 1);
						break;
					case 'offset':
						min_date = new Date();
						min_date.setDate(min_date.getDate() + parseInt(min_value, 10));
						break;
					case 'date':
						min_date = lepopup_date(min_value, jQuery(object).attr("data-format"));
						break;
					case 'field':
						content = jQuery(object).closest(".lepopup-container");
						if (jQuery(content).find("input[name='lepopup-"+min_value+"']").length > 0) min_date = lepopup_date(jQuery(content).find("input[name='lepopup-"+min_value+"']").val(), jQuery(object).attr("data-format"));
						break;
					default:
						break;
				}
				if (min_date != null) inst.update('minDate', min_date);
				var max_type = jQuery(object).attr("data-max-type");
				var max_value = jQuery(object).attr("data-max-value");
				var max_date = null;
				switch(max_type) {
					case 'today':
						max_date = new Date();
						break;
					case 'yesterday':
						max_date = new Date();
						max_date.setDate(max_date.getDate() - 1);
						break;
					case 'tomorrow':
						max_date = new Date();
						max_date.setDate(max_date.getDate() + 1);
						break;
					case 'offset':
						max_date = new Date();
						max_date.setDate(max_date.getDate() + parseInt(max_value, 10));
						break;
					case 'date':
						max_date = lepopup_date(max_value, jQuery(object).attr("data-format"));
						break;
					case 'field':
						content = jQuery(object).closest(".lepopup-container");
						if (jQuery(content).find("input[name='lepopup-"+max_value+"']").length > 0) max_date = lepopup_date(jQuery(content).find("input[name='lepopup-"+max_value+"']").val(), jQuery(object).attr("data-format"));
						break;
					default:
						break;
				}
				if (max_date != null) inst.update('maxDate', max_date);
			}
		});
		jQuery(object).parent().find("i").on("click", function(e){
			e.preventDefault();
			var input = jQuery(this).parent().children("input");
			var airdatepicker = jQuery(input).airdatepicker().data('airdatepicker');
			airdatepicker.show();
		});
	});
}
function lepopup_rangeslider_init(_set) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("ion.rangeSlider") >= 0 && typeof jQuery.fn.ionRangeSlider == typeof undefined) {
		setTimeout(function(){lepopup_rangeslider_init(_set);}, 1000);
	}
	if (typeof jQuery.fn.ionRangeSlider == typeof undefined || !jQuery.fn.ionRangeSlider) return;
	jQuery(_set).ionRangeSlider({
		onChange: function (_data) {
			lepopup_input_error_hide(_data.input);
			lepopup_input_changed(_data.input);
		}
	});
}
function lepopup_tooltips_init(_container, _form_id, _theme) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("tooltipster") >= 0 && (typeof jQuery.fn.tooltipster == typeof undefined || !jQuery.fn.tooltipster)) {
		setTimeout(function(){lepopup_tooltips_init(_container, _form_id, _theme);}, 1000);
	}
	if (typeof jQuery.fn.tooltipster == typeof undefined || !jQuery.fn.tooltipster) return;
	var theme = jQuery(".lepopup-form-"+_form_id).attr("data-tooltip-theme");
	if (theme != "light" && theme != "dark") theme = _theme;
	jQuery(_container).find("span.lepopup-tooltip-anchor, .lepopup-input[title], .lepopup-upload-input[title]").tooltipster({
		functionFormat: function(instance, helper, content){
			return "<div class='lepopup-tooltipster-content-"+_form_id+" lepopup-tooltipster-content-"+theme+"'>"+content+"</div>";
		},
		contentAsHTML:	true,
		maxWidth:		640,
		theme:			"tooltipster-"+theme
	});
}
function lepopup_signature_init(_set) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("signature_pad") >= 0 && typeof SignaturePad == typeof undefined) {
		setTimeout(function(){lepopup_signature_init(_set);}, 1000);
	}
	if (typeof SignaturePad == typeof undefined) return;
	jQuery(_set).each(function(){
		var object = this;
		var box = jQuery(this).parent();
		var width = Math.max(box.width(), 40);
		var height = box.height();
		jQuery(this).width(width);
		jQuery(this).height(height);
		jQuery(this).attr("width", width);
		jQuery(this).attr("height", height);
		var signature_key = jQuery(this).closest(".lepopup-form").attr("data-id")+"-"+jQuery(this).closest(".lepopup-element").attr("data-id");
		var pen_color = jQuery(this).attr("data-color");
		if (typeof pen_color == typeof undefined) pen_color = "rgb(0,0,0,1);"
		lepopup_signatures[signature_key] = new SignaturePad(this, {
			penColor: pen_color,
			onBegin: function() {
				lepopup_input_error_hide(object);
			},
			onEnd: function() {
				var input = jQuery(object).closest(".lepopup-input").find("input");
				var data_url = "";
				if (!this.isEmpty()) data_url = this.toDataURL();
				jQuery(input).val(data_url);
				lepopup_input_changed(input);
			}
		});
		jQuery(this).parent().find("span").on("click", function(e){
			var input = jQuery(object).closest(".lepopup-input").find("input");
			jQuery(input).val("");
			lepopup_signatures[signature_key].clear();
			lepopup_input_changed(input);
		});
	});
}
var lepopup_in_onselect = false;
function lepopup_timepicker_init(_set) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("airdatepicker") >= 0 && typeof jQuery.fn.airdatepicker == typeof undefined) {
		setTimeout(function(){lepopup_timepicker_init(_set);}, 1000);
	}
	if (typeof jQuery.fn.airdatepicker == typeof undefined) return;
	jQuery(_set).each(function(){
		var object = this;
		var airdatepicker = jQuery(object).airdatepicker().data('airdatepicker');
		airdatepicker.destroy();
		jQuery(object).airdatepicker({
			inline_popup	: true,
			autoClose		: true,
			timepicker		: true,
			onlyTimepicker	: true,
			minutesStep		: jQuery(object).attr("data-interval"),
			timeFormat		: jQuery(object).attr("data-format"),
			language		: jQuery(object).attr("data-locale"),
			onSelect		: function(formattedDate, date, inst) {
				if (lepopup_in_onselect) return;
				lepopup_in_onselect = true;
				var content;
				var selected_time_c = date.getHours()*100+date.getMinutes();
				var min_type = jQuery(object).attr("data-min-type");
				var min_value = jQuery(object).attr("data-min-value");
				var min_time = null;
				switch(min_type) {
					case 'time':
						min_time = lepopup_time24_str(min_value, jQuery(object).attr("data-format"));
						break;
					case 'field':
						content = jQuery(object).closest(".lepopup-container");
						if (jQuery(content).find("input[name='lepopup-"+min_value+"']").length > 0) min_time = lepopup_time24_str(jQuery(content).find("input[name='lepopup-"+min_value+"']").val(), jQuery(object).attr("data-format"));
						break;
					default:
						break;
				}
				if (min_time != null) {
					if (selected_time_c < parseInt(min_time.replace(":", ""), 10)) {
						inst.selectDate(new Date(2020, 0, 1, min_time.substr(0, 2), min_time.substr(3, 2)));
						lepopup_in_onselect = false;
						return;
					}
				}
				var max_type = jQuery(object).attr("data-max-type");
				var max_value = jQuery(object).attr("data-max-value");
				var max_time = null;
				switch(max_type) {
					case 'time':
						max_time = lepopup_time24_str(max_value, jQuery(object).attr("data-format"));
						break;
					case 'field':
						content = jQuery(object).closest(".lepopup-container");
						if (jQuery(content).find("input[name='lepopup-"+max_value+"']").length > 0) max_time = lepopup_time24_str(jQuery(content).find("input[name='lepopup-"+max_value+"']").val(), jQuery(object).attr("data-format"));
						break;
					default:
						break;
				}
				if (max_time != null) {
					if (selected_time_c > parseInt(max_time.replace(":", ""), 10)) {
						inst.selectDate(new Date(2020, 0, 1, max_time.substr(0, 2), max_time.substr(3, 2)));
						lepopup_in_onselect = false;
						return;
					}
						
				}
				lepopup_in_onselect = false;
			}
		});
		jQuery(object).parent().find("i").on("click", function(e){
			e.preventDefault();
			var input = jQuery(this).parent().children("input");
			var airdatepicker = jQuery(input).airdatepicker().data('airdatepicker');
			airdatepicker.show();
		});
	});
}
function lepopup_popup_open(_slug) {
	var slug;
	var slugs = _slug.split("*");
	if (slugs.length > 1) {
		if (lepopup_mobile) slug = slugs[1];
		else slug = slugs[0];
	} else slug = _slug;
	if (slug == "") return false;
	if (lepopup_vars["campaigns"].hasOwnProperty(slug)) {
		lepopup_campaign_active_slug = slug;
		slug = lepopup_vars["campaigns"][slug][parseInt(Math.floor(Math.random()*lepopup_vars["campaigns"][slug].length), 10)];
	} else lepopup_campaign_active_slug = null;
	var overlay_color = "rgba(0,0,0,0.7)";
	var _id = null;
	if (lepopup_vars["overlays"].hasOwnProperty(slug)) {
		_id = lepopup_vars["overlays"][slug][0];
	} else return false;
	if (lepopup_popup_active_id == _id) return false;
	if (lepopup_popup_active_id) lepopup_popup_active_close();
	if (jQuery("#lepopup-popup-"+_id).length > 0) {
		if (jQuery("#lepopup-popup-"+_id+"-overlay").length > 0) {
			jQuery("#lepopup-popup-"+_id+"-overlay").attr("class", "lepopup-popup-overlay lepopup-animated lepopup-"+lepopup_vars["overlays"][slug][5]);
		} else {
			if (lepopup_vars["overlays"][slug][2] == "on") {
				if (lepopup_vars["overlays"][slug][3] != "") overlay_color = lepopup_vars["overlays"][slug][3];
				var overlay_html = "<div class='lepopup-popup-overlay' id='lepopup-popup-"+_id+"-overlay' style='background: "+overlay_color+";'></div>";
				jQuery('body').append(overlay_html);
				jQuery("#lepopup-popup-"+_id+"-overlay").fadeIn(500);
				jQuery("#lepopup-popup-"+_id+"-overlay").attr("class", "lepopup-popup-overlay lepopup-animated lepopup-"+lepopup_vars["overlays"][slug][5]);
				if (lepopup_vars["overlays"][slug][4] == "on") {
					jQuery("#lepopup-popup-"+_id+"-overlay").on("click", function(e) {
						lepopup_popup_active_close();
					});
				}
			}
		}
		lepopup_add_impression(_id, lepopup_campaign_active_slug);
		return _lepopup_popup_open(_id, true);
	} else {
		if (!lepopup_popup_loading && !lepopup_popup_active_id) {
			lepopup_popup_loading = true;
			if (lepopup_vars["overlays"][slug][2] == "on") {
				if (lepopup_vars["overlays"][slug][3] != "") overlay_color = lepopup_vars["overlays"][slug][3];
				var overlay_html = "<div class='lepopup-popup-overlay' id='lepopup-popup-"+_id+"-overlay' style='background: "+overlay_color+";'></div>";
				jQuery('body').append(overlay_html);
				jQuery("#lepopup-popup-"+_id+"-overlay").fadeIn(500);
				jQuery("#lepopup-popup-"+_id+"-overlay").attr("class", "lepopup-popup-overlay lepopup-animated lepopup-"+lepopup_vars["overlays"][slug][5]);
				if (lepopup_vars["overlays"][slug][4] == "on") {
					jQuery("#lepopup-popup-"+_id+"-overlay").on("click", function(e) {
						lepopup_popup_loading = false;
						jQuery(".lepopup-popup-loader").hide();
						jQuery(".lepopup-popup-loader").remove();
						if (jQuery("#lepopup-popup-"+_id).length == 0) {
							jQuery("#lepopup-popup-"+_id+"-overlay").fadeOut(300);
						} else {
							lepopup_popup_active_close();
						}
					});
				}
			}
			var loader = "<style>#lepopup-popup-"+_id+"-loader .lepopup-popup-loader-triple-spinner {border-top-color:"+lepopup_vars["overlays"][slug][6]+"} #lepopup-popup-"+_id+"-loader .lepopup-popup-loader-triple-spinner::before {border-top-color:"+lepopup_vars["overlays"][slug][7]+"} #lepopup-popup-"+_id+"-loader .lepopup-popup-loader-triple-spinner::after {border-top-color:"+lepopup_vars["overlays"][slug][8]+"}</style><div id='lepopup-popup-"+_id+"-loader' class='lepopup-popup-loader lepopup-popup-loader-"+lepopup_vars["overlays"][slug][1]+"'><div class='lepopup-popup-loader-container'><div class='lepopup-popup-loader-triple-spinner'></div></div></div>";
			jQuery('body').append(loader);
			
			var style = jQuery(".lepopup-form-"+_id).length > 0 ? "off" : "on";
			jQuery.ajax({
				url: 		lepopup_vars['ajax-url'],
				method:		(lepopup_vars["mode"] == "remote" ? "get" : "post"),
				dataType:	(lepopup_vars["mode"] == "remote" ? "jsonp" : "json"),
				async:		true,
				data: 		{"action" : "lepopup-front-popup-load", "form-slug" : slug, "form-style" : style, "hostname" : window.location.hostname, "preview" : lepopup_preview},
				success: 	function(return_data) {
					jQuery(".lepopup-popup-loader").hide();
					jQuery(".lepopup-popup-loader").remove();
					var data;
					try {
						if (typeof return_data == 'object') data = return_data;
						else data = jQuery.parseJSON(return_data);
						if (data.status == "OK") {
							if (!lepopup_popup_loading) return false;
							//jQuery("#lepopup-popup-"+_id+"-overlay").html(data.html);
							jQuery("body").append(data.html);
							lepopup_mask_init("#lepopup-popup-"+_id+" input.lepopup-mask");
							lepopup_add_impression(_id, lepopup_campaign_active_slug);
							_lepopup_popup_open(_id, false);
						} else {
							if (data.hasOwnProperty("message")) {
								lepopup_global_message_show("danger", data.message);
							}
							jQuery(".lepopup-popup-loader").hide();
							jQuery(".lepopup-popup-loader").remove();
							//jQuery("#lepopup-popup-"+_id+"-overlay").off("click");
							jQuery("#lepopup-popup-"+_id+"-overlay").fadeOut(300);
						}
					} catch(error) {
						console.log(error);
						jQuery(".lepopup-popup-loader").hide();
						jQuery(".lepopup-popup-loader").remove();
						//jQuery("#lepopup-popup-"+_id+"-overlay").off("click");
						jQuery("#lepopup-popup-"+_id+"-overlay").fadeOut(300);
					}
					lepopup_popup_loading = false;
				},
				error: 		function(XMLHttpRequest, textStatus, errorThrown) {
					console.log(errorThrown);
					jQuery(".lepopup-popup-loader").hide();
					jQuery(".lepopup-popup-loader").remove();
					//jQuery("#lepopup-popup-"+_id+"-overlay").off("click");
					jQuery("#lepopup-popup-"+_id+"-overlay").fadeOut(300);
					lepopup_popup_loading = false;
				}
			});
		}
	}
	return false;
}

function _lepopup_popup_open(_id, _overlay) {
	if (jQuery("#lepopup-popup-"+_id).length == 0) return false;
	if (typeof lepopupext_open_before == 'function') {
		lepopupext_open_before(_id);
	}
	lepopup_popup_active_id = _id;
	if (_overlay) {
		jQuery("#lepopup-popup-"+_id+"-overlay").fadeIn(300);
	}
	
	var form = jQuery("#lepopup-popup-"+_id).children(".lepopup-form").first();
	var form_id = jQuery(form).attr("data-id");
	var visible_page = null;
	lepopup_reset_form(form_id);
	jQuery(".lepopup-form-"+form_id).hide();
	jQuery(".lepopup-form-"+form_id).each(function(){
		var page_id = jQuery(this).attr("data-page");
		if (lepopup_is_visible(form_id, page_id)) {
			_lepopup_popup_page_open(page_id);
			visible_page = this;
			return false;
		}
	});
	lepopup_datepicker_init("#lepopup-popup-"+_id+" input.lepopup-date");
	lepopup_timepicker_init("#lepopup-popup-"+_id+" input.lepopup-time");
	jQuery("#lepopup-popup-"+_id).show();
	lepopup_signature_init("#lepopup-popup-"+_id+" canvas.lepopup-signature");
	lepopup_rangeslider_init("#lepopup-popup-"+_id+" input.lepopup-rangeslider");
	if (lepopup_mobile) {
		jQuery("#lepopup-popup-"+_id+"-overlay").css({"padding-right" : "0px"});
	}
	lepopup_handle_visibility(form_id, null, true);
	//lepopup_resize();
	if (lepopup_customjs_handlers.hasOwnProperty(form_id)) {
		lepopup_customjs_handlers[form_id].errors = {};
		if (lepopup_customjs_handlers[form_id].hasOwnProperty("afterinit") && typeof lepopup_customjs_handlers[form_id].afterinit == 'function') {
			try {
				lepopup_customjs_handlers[form_id].afterinit();
			} catch(error) {
			}
		}
	}
	return false;
}
function _lepopup_popup_page_open(_page_id) {
	if (!lepopup_popup_active_id || lepopup_popup_active_page_id == _page_id) return;
	if (lepopup_popup_active_page_id && lepopup_popup_active_page_id != _page_id) _lepopup_popup_page_close(lepopup_popup_active_page_id);
	
	var active_page = jQuery("#lepopup-popup-"+lepopup_popup_active_id+" .lepopup-form[data-page='"+_page_id+"']");
	if (active_page.length == 0) return;
	lepopup_popup_active_page_id = _page_id;

	_lepopup_resize_active_popup(lepopup_popup_active_page_id);
	
	jQuery(active_page).removeClass("lepopup-form-page-closed");

	jQuery(active_page).find(".lepopup-element").each(function(){
		var left = jQuery(this).attr("data-left");
		var top = jQuery(this).attr("data-top");
		var animation_in = jQuery(this).attr("data-animation-in");
		var animation_out = jQuery(this).attr("data-animation-out");
		jQuery(this).css({
			"left": parseInt(left, 10)+"px",
			"top": parseInt(top, 10)+"px"
		});
		var content = jQuery(this).attr("data-content");
		if (content) {
			jQuery(this).find(".lepopup-element-html-content").html(lepopup_decode64(content));
		}
		jQuery(this).removeClass("lepopup-"+animation_out);
		jQuery(this).addClass("lepopup-animated lepopup-"+animation_in);
	});
	jQuery(active_page).show();
}

function lepopup_popup_active_close(_cookie_lifetime) {
	clearTimeout(lepopup_timeout);
	if (!lepopup_popup_active_id || !lepopup_popup_active_page_id) return;
	_lepopup_popup_page_close(lepopup_popup_active_page_id);
	var id = lepopup_popup_active_id;
	lepopup_sending = false;
	lepopup_popup_active_id = null;
	lepopup_campaign_active_slug = null;
	lepopup_forced_location = null;
	var form_uid = jQuery(".lepopup-form-"+id).first().attr("data-id");
	if (parseInt(_cookie_lifetime, 10) > 0) {
		var form_slug = jQuery(".lepopup-form-"+id).first().attr("data-slug");
		if (form_slug) lepopup_write_cookie("lepopup-submit-"+form_slug, lepopup_vars["cookie-value"], parseInt(_cookie_lifetime, 10)*24);
	}
	setTimeout(function() {
		jQuery("#lepopup-popup-"+id+"-overlay").attr("class", "lepopup-popup-overlay");
		jQuery("#lepopup-popup-"+id+"-overlay").fadeOut(300);
		jQuery("#lepopup-popup-"+id).removeClass("lepopup-popup-fh-container");
		jQuery("#lepopup-popup-"+id).hide();
		if (typeof lepopupext_close_after == 'function') { 
			lepopupext_close_after(id);
		}
		if (lepopup_customjs_handlers.hasOwnProperty(form_uid)) {
			lepopup_customjs_handlers[form_uid].errors = {};
			if (lepopup_customjs_handlers[form_uid].hasOwnProperty("afterclose") && typeof lepopup_customjs_handlers[form_uid].afterclose == 'function') {
				try {
					lepopup_customjs_handlers[form_uid].afterclose();
				} catch(error) {
				}
			}
		}
	}, 500);
	return false;
}

function lepopup_close(_cookie_lifetime) {
	lepopup_popup_active_close(_cookie_lifetime)
	return false;
}
function _lepopup_close(_id) {
	lepopup_sending = false;
	lepopup_popup_active_id = null;
	
	jQuery("#lepopup-popup-"+_id).fadeOut(300, function() {
		jQuery("#lepopup-popup-"+_id+"-overlay").fadeOut(300);
		jQuery("#lepopup-popup-"+_id).find(".lepopup-popup").hide();
	});
	return false;
}
function _lepopup_popup_page_close(_page_id) {
	if (!lepopup_popup_active_id || lepopup_popup_active_page_id != _page_id) return;
	lepopup_popup_active_page_id = null;
	var active_page = jQuery("#lepopup-popup-"+lepopup_popup_active_id+" .lepopup-form[data-page='"+_page_id+"']");
	if (active_page.length == 0) return;
	jQuery(active_page).find(".lepopup-element-error").remove();
	jQuery(active_page).addClass("lepopup-form-page-closed");
	jQuery(active_page).find(".lepopup-element").each(function(){
		var left = jQuery(this).attr("data-left");
		var top = jQuery(this).attr("data-top");
		var animation_in = jQuery(this).attr("data-animation-in");
		var animation_out = jQuery(this).attr("data-animation-out");
		jQuery(this).css({
			"left": parseInt(left, 10)+"px",
			"top": parseInt(top, 10)+"px"
		});
		jQuery(this).removeClass("lepopup-"+animation_in);
		jQuery(this).addClass("lepopup-animated lepopup-"+animation_out);
		var content = jQuery(this).attr("data-content");
		if (content) {
			jQuery(this).find(".lepopup-element-html-content").html("");
		}
	});
	setTimeout(function() {
		jQuery(active_page).hide();
	}, 500);
}


function lepopup_multiselect_changed(_object) {
	var container = jQuery(_object).closest(".lepopup-multiselect");
	var max_allowed = parseInt(jQuery(container).attr("data-max-allowed"), 10);
	if (max_allowed > 0) {
		var selected = jQuery(container).find("input:checked").length;
		if (selected >= max_allowed) {
			jQuery(container).find("input:not(:checked)").attr("disabled", "disabled");
		} else {
			jQuery(container).find("input:not(:checked)").removeAttr("disabled");
		}
	}
	lepopup_input_changed(_object);
}
function lepopup_input_changed(_object) {
	var element = jQuery(_object).closest(".lepopup-element");
	var type = jQuery(element).attr("data-type");
	var element_id = jQuery(element).attr("data-id");
	var form_uid = jQuery(_object).closest(".lepopup-form").attr("data-id");
	var form_id = jQuery(".lepopup-form-"+form_uid).attr("data-form-id");
	var session_length = jQuery(".lepopup-form-"+form_uid).attr("data-session");
	var session_enable = false;
	if (lepopup_is_numeric(session_length) && session_length > 0) session_enable = true;
	
	var var_values = new Array();
	var var_value = null;
	switch(type) {
		case 'signature':
			var_value = jQuery(_object).val();
			if (var_value != "") jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).html("<img src='"+var_value+"' />");
			else jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text("");
			break;
		case 'file':
			jQuery(element).find(".lepopup-uploader-file-countable.lepopup-uploader-file-processed").each(function(){
				var_values.push(jQuery(this).attr("data-name"));
			});
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text(var_values.join(", "));
			break;
		case 'checkbox':
		case 'imageselect':
		case 'tile':
		case 'multiselect':
			jQuery(element).find("input").each(function(){
				if (jQuery(this).is(":checked")) var_values.push(jQuery(this).val());
			});
			if (session_enable) {
				lepopup_sessions[form_id]["values"][element_id] = var_values;
				lepopup_sessions[form_id]["modified"] = true;
			}
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text(var_values.join(", "));
			break;
		case 'rangeslider':
			var_value = jQuery(_object).val();
			if (session_enable) {
				lepopup_sessions[form_id]["values"][element_id] = var_value;
				lepopup_sessions[form_id]["modified"] = true;
			}
			var_value = var_value.replace(":", " ... ");
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text(var_value);
			break;
		case 'number':
			var_value = _lepopup_number_changed(_object);
			if (var_value === false) return false;
			if (session_enable) {
				lepopup_sessions[form_id]["values"][element_id] = jQuery(_object).val();
				lepopup_sessions[form_id]["modified"] = true;
			}
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text(jQuery(_object).val());
			break;
		default:
			var_value = jQuery(_object).val();
			if (session_enable) {
				lepopup_sessions[form_id]["values"][element_id] = var_value;
				lepopup_sessions[form_id]["modified"] = true;
			}
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-var-"+element_id).text(var_value);
			break;
	}
	if (lepopup_customjs_handlers.hasOwnProperty(form_uid)) {
		lepopup_customjs_handlers[form_uid].errors = {};
		if (lepopup_customjs_handlers[form_uid].hasOwnProperty("afterupdate") && typeof lepopup_customjs_handlers[form_uid].afterupdate == 'function') {
			try {
				lepopup_customjs_handlers[form_uid].afterupdate(element_id);
			} catch(error) {
			}
		}
	}
	lepopup_handle_math(form_uid);
	var dependencies = jQuery(_object).closest(".lepopup-element").attr("data-deps").split(",");
	if (dependencies.length > 0) lepopup_handle_visibility(form_uid, dependencies, true);
	return false;
}
function _lepopup_number_changed(_object) {
	var decimal = parseInt(jQuery(_object).attr("data-decimal"), 10);
	var valid_value = jQuery(_object).attr("data-value");
	var value = jQuery(_object).val();
	var caret_position = _object.selectionStart;
	var value_parts = value.split(".");
	if (value == "" || value == "-" || value == ".") {
		jQuery(_object).attr("data-value", value);
		return true;
	}
	if (isNaN(parseFloat(value)) || !isFinite(value) || value_parts.length > 2 || (decimal == 0 && value_parts.length == 2)) {
		jQuery(_object).val(valid_value);
		_object.selectionStart = valid_value.length - (value.length - caret_position);
		_object.selectionEnd = valid_value.length - (value.length - caret_position);
		return false;
	}
	if (decimal > 0 && value_parts.length == 2 && (value_parts[1]).length > decimal) {
		value_parts[1] = (value_parts[1]).substr(0, decimal);
		value = value_parts.join(".");
		jQuery(_object).val(value);
		_object.selectionStart = caret_position;
		_object.selectionEnd = caret_position;
	}
	jQuery(_object).attr("data-value", value);
	return true;
}
function lepopup_number_unfocused(_object) {
	var min = jQuery(_object).attr("data-min");
	var max = jQuery(_object).attr("data-max");
	var value = jQuery(_object).val();
	if (!isNaN(parseFloat(value)) && isFinite(value)) {
		if (!isNaN(parseFloat(min)) && isFinite(min) && parseFloat(value) < parseFloat(min)) {
			jQuery(_object).val(min);
			lepopup_input_changed(_object);
		} else if (!isNaN(parseFloat(max)) && isFinite(max) && parseFloat(value) > parseFloat(max)) {
			jQuery(_object).val(max);
			lepopup_input_changed(_object);
		}
	}
}
function lepopup_numspinner_inc(_object) {
	var temp, start, end;
	var input = jQuery(_object).parent().find("input");
	var readonly = jQuery(input).attr("data-readonly");
	if (readonly == "on") return false;
	var value = jQuery(input).attr("data-value");
	var step = jQuery(input).attr("data-step");
	if (isNaN(parseFloat(step)) || !isFinite(step) || parseFloat(step) <= 0) {
		step = 1;
	}
	var decimal = parseInt(jQuery(input).attr("data-decimal"), 10);
	var mode = jQuery(input).attr("data-mode");
	if (mode == "simple") {
		var min = jQuery(input).attr("data-min");
		var max = jQuery(input).attr("data-max");
		if (isNaN(parseFloat(value)) || !isFinite(value)) {
			if (isNaN(parseFloat(min)) || !isFinite(min)) {
				value = 0;
			} else value = parseFloat(min);
		} else value = parseFloat(value);
		value = value + parseFloat(step);
		if (!isNaN(parseFloat(max)) && isFinite(max) && value > parseFloat(max)) value = parseFloat(max);
	} else {
		var raw_ranges = jQuery(input).attr("data-range");
		if (isNaN(parseFloat(value)) || !isFinite(value)) {
			value = 0;
		} else value = parseFloat(value);
		value = value + parseFloat(step);
		if (raw_ranges.length > 0) {
			var ranges = raw_ranges.split(",");
			for (var i=0; i<ranges.length; i++) {
				temp = ranges[i].split("...");
				start = parseFloat(temp[0]);
				if (temp.length > 1) end = parseFloat(temp[1]);
				else end = start;
				if (value < start) {
					value = start;
					break;
				} else if (value <= end) {
					break;
				} else if (i == ranges.length-1) {
					value = end;
					break;
				}
			}
		}
	}	
	jQuery(input).attr("data-value", value.toFixed(decimal));
	jQuery(input).val(value.toFixed(decimal));
	lepopup_input_error_hide(input);
	lepopup_input_changed(input);
	return false;
}
function lepopup_numspinner_dec(_object) {
	var temp, start, end;
	var input = jQuery(_object).parent().find("input");
	var readonly = jQuery(input).attr("data-readonly");
	if (readonly == "on") return false;
	var value = jQuery(input).attr("data-value");
	var step = jQuery(input).attr("data-step");
	var decimal = parseInt(jQuery(input).attr("data-decimal"), 10);
	var mode = jQuery(input).attr("data-mode");
	if (mode == "simple") {
		var min = jQuery(input).attr("data-min");
		var max = jQuery(input).attr("data-max");
		if (isNaN(parseFloat(value)) || !isFinite(value)) {
			if (isNaN(parseFloat(max)) || !isFinite(max)) {
				value = 0;
			} else value = parseFloat(max);
		} else value = parseFloat(value);
		if (isNaN(parseFloat(step)) || !isFinite(step) || parseFloat(step) <= 0) {
			step = 1;
		}
		value = value - parseFloat(step);
		if (!isNaN(parseFloat(min)) && isFinite(min) && value < parseFloat(min)) value = parseFloat(min);
	} else {
		var raw_ranges = jQuery(input).attr("data-range");
		if (isNaN(parseFloat(value)) || !isFinite(value)) {
			value = 0;
		} else value = parseFloat(value);
		value = value - parseFloat(step);
		if (raw_ranges.length > 0) {
			var ranges = raw_ranges.split(",");
			for (var i=ranges.length-1; i>=0; i--) {
				temp = ranges[i].split("...");
				start = parseFloat(temp[0]);
				if (temp.length > 1) end = parseFloat(temp[1]);
				else end = start;
				if (value > end) {
					value = end;
					break;
				} else if (value >= start) {
					break;
				} else if (i == 0) {
					value = start;
					break;
				}
			}
		}
	}	
	jQuery(input).attr("data-value", value.toFixed(decimal));
	jQuery(input).val(value.toFixed(decimal));
	lepopup_input_error_hide(input);
	lepopup_input_changed(input);
	return false;
}
function lepopup_is_visible(_form_id, _element_id) {
	var field, bool_value, field_values;
	var logic_rules = new Array();
	var logic = JSON.parse(jQuery("#lepopup-logic-"+_form_id).val());
	if (!logic.hasOwnProperty(_element_id)) return true;
	for (var i=0; i<logic[_element_id]['rules'].length; i++) {
		field = jQuery(".lepopup-form-"+_form_id).find("[name='lepopup-"+logic[_element_id]['rules'][i]['field']+"']");
		if (field.length == 0) field = jQuery(".lepopup-form-"+_form_id).find("[name='lepopup-"+logic[_element_id]['rules'][i]['field']+"[]']");
		field_values = new Array()
		jQuery(field).each(function(){
			var field_type = jQuery(this).attr("type");
			if (field_type == "checkbox" || field_type == "radio" || field_type == "multiselect" || field_type == "imageselect" || field_type == "tile" || field_type == "star-rating") {
				if (jQuery(this).is(":checked")) field_values.push(jQuery(this).val());
			} else field_values.push(jQuery(this).val());
		});
		bool_value = false;
		switch(logic[_element_id]['rules'][i]['rule']) {
			case 'is':
				if (field_values.indexOf(logic[_element_id]['rules'][i]['token']) >= 0) logic_rules.push(true);
				else logic_rules.push(false);
				break;
			case 'is-not':
				if (field_values.indexOf(logic[_element_id]['rules'][i]['token']) == -1) logic_rules.push(true);
				else logic_rules.push(false);
				break;
			case 'is-empty':
				for (var j=0; j<field_values.length; j++) {
					if (field_values[j] != null && field_values[j] != "") {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(!bool_value);
				break;
			case 'is-not-empty':
				for (var j=0; j<field_values.length; j++) {
					if (field_values[j] != null && field_values[j] != "") {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			case 'is-greater':
				for (var j=0; j<field_values.length; j++) {
					if (parseFloat(field_values[j]) > parseFloat(logic[_element_id]['rules'][i]['token'])) {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			case 'is-less':
				for (var j=0; j<field_values.length; j++) {
					if (parseFloat(field_values[j]) < parseFloat(logic[_element_id]['rules'][i]['token'])) {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			case 'contains':
				for (var j=0; j<field_values.length; j++) {
					if (logic[_element_id]['rules'][i]['token'] != "" && field_values[j].indexOf(logic[_element_id]['rules'][i]['token']) >= 0) {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			case 'starts-with':
				for (var j=0; j<field_values.length; j++) {
					if (logic[_element_id]['rules'][i]['token'] != "" && field_values[j].substring(0, logic[_element_id]['rules'][i]['token'].length) === logic[_element_id]['rules'][i]['token']) {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			case 'ends-with':
				for (var j=0; j<field_values.length; j++) {
					if (logic[_element_id]['rules'][i]['token'] != "" && field_values[j].substring(field_values[j].length - logic[_element_id]['rules'][i]['token'].length) === logic[_element_id]['rules'][i]['token']) {
						bool_value = true;
						break;
					}
				}
				logic_rules.push(bool_value);
				break;
			default:
				break;
		}
	}
	bool_value = false;
	if (logic[_element_id]['operator'] == "and") {
		if (logic_rules.indexOf(false) == -1) bool_value = true;
	} else {
		if (logic_rules.indexOf(true) >= 0) bool_value = true;
	}
	if (logic[_element_id]['action'] == 'hide') bool_value = !bool_value;
			
	return bool_value;
}
function lepopup_handle_visibility(_form_id, _ids, _immediately) {
	if (jQuery("#lepopup-logic-"+_form_id).length == 0) return false;
	var logic = JSON.parse(jQuery("#lepopup-logic-"+_form_id).val());
	for (var key in logic) {
		if (logic.hasOwnProperty(key)) {
			if (Array.isArray(_ids) && _ids.indexOf(key) == -1) continue;
			if (lepopup_is_visible(_form_id, key)) {
				if (_immediately == true) jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).show();
				else {
					jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).css({"position" : "relative"});
					jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).fadeIn(200);
				}
			} else {
				if (_immediately == true) jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).hide();
				else {
					jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).css({"position" : "absolute"});
					jQuery(".lepopup-form-"+_form_id).find(".lepopup-element-"+key).fadeOut(200);
				}
			}
		}
	}
	return false;
}
function lepopup_mask_init(_set) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("jquery.mask") >= 0 && typeof jQuery.fn.mask == typeof undefined) {
		setTimeout(function(){lepopup_mask_init(_set);}, 1000);
	}
	if (typeof jQuery.fn.mask == typeof undefined) return;
	jQuery(_set).each(function(){
		var mask = jQuery(this).attr("data-xmask");
		if (mask) jQuery(this).mask(mask);
	});
}
function lepopup_submit(_object, _action) {
	var prev_page_id;
	clearTimeout(lepopup_timeout);
	var button_pressed = false;
	if (jQuery(_object).hasClass("lepopup-button")) button_pressed = true;
	var form_uid = jQuery(_object).closest(".lepopup-form").attr("data-id");
	var form_slug = jQuery(_object).closest(".lepopup-form").attr("data-slug");
	var form_id = jQuery(_object).closest(".lepopup-form").attr("data-form-id");
	var page_id = jQuery(_object).closest(".lepopup-form").attr("data-page");
	var session_length = jQuery(_object).closest(".lepopup-form").attr("data-session");
	var allowed_actions = new Array("next", "prev", "submit");
	if (typeof _action == undefined || _action == "") _action = "submit";
	else if (allowed_actions.indexOf(_action) == -1) _action = "submit";
	jQuery(".lepopup-form-"+form_uid).find(".lepopup-element-error").fadeOut(300, function(){
		jQuery(this).remove();
	});
	var is_popup = false;
	if (lepopup_popup_active_id && jQuery(_object).closest(".lepopup-form").parent().hasClass("lepopup-popup-container")) is_popup = true;
	if (_action == "prev") {
		lepopup_sending = false;
		if (lepopup_seq_pages.hasOwnProperty(form_uid) && lepopup_seq_pages[form_uid].length > 0) {
			prev_page_id = lepopup_seq_pages[form_uid][lepopup_seq_pages[form_uid].length-1];
			lepopup_seq_pages[form_uid].splice(lepopup_seq_pages[form_uid].length-1, 1);
			if (is_popup) {
				_lepopup_popup_page_open(prev_page_id);
				jQuery("#lepopup-popup-"+lepopup_popup_active_id).stop().animate({scrollTop: 0}, 300);
			} else {
				jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").fadeOut(300, function(){
					jQuery(this).find(".lepopup-element[data-content]").each(function(){
						jQuery(this).find(".lepopup-element-html-content").html("");
					});
					jQuery(".lepopup-form-"+form_uid+"[data-page='"+prev_page_id+"']").fadeIn(300).find(".lepopup-element[data-content]").each(function(){
						jQuery(this).find(".lepopup-element-html-content").html(lepopup_decode64(jQuery(this).attr("data-content")));
					});
					lepopup_resize();
				});
			}
			return false;
		} else return false;
	}

	if (button_pressed) {
		var original_icon = jQuery(_object).attr("data-original-icon");
		if (typeof original_icon === typeof undefined || original_icon === false) {
			original_icon = jQuery(_object).children("i").first().attr("class");
			if (typeof original_icon !== typeof undefined && original_icon !== false) {
				jQuery(_object).attr("data-original-icon", original_icon);
			}
		}
		jQuery(_object).children("i").first().attr("class", "lepopup-if lepopup-if-spinner lepopup-if-spin");
		jQuery(_object).find("span").text(jQuery(_object).attr("data-loading"));
	}
	
	jQuery(".lepopup-form-"+form_uid).find(".lepopup-button").addClass("lepopup-button-disabled");
	jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").children(".lepopup-confirmaton-message").slideUp(300, function(){
		jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").children(".lepopup-confirmaton-message").remove();
	});

	if (lepopup_uploads.hasOwnProperty(form_uid)) {
		var waiting_upload = false;
		for (var upload_id in lepopup_uploads[form_uid]) {
			if ((lepopup_uploads[form_uid]).hasOwnProperty(upload_id)) {
				if (lepopup_uploads[form_uid][upload_id] == "LOADING") {
					waiting_upload = true;
				}
			}
		}
		if (waiting_upload) {
			setTimeout(function(){
				lepopup_submit(_object, _action);
			}, 500);
			return false;
		}
	}
	
	if (lepopup_sending) return false;
	lepopup_sending = true;

	var all_pages = new Array();
	jQuery(".lepopup-form-"+form_uid).each(function(){
		all_pages.push(jQuery(this).attr("data-page"));
	});
	
	if (typeof SignaturePad != typeof undefined) {
		jQuery(".lepopup-form-"+form_uid).find(".lepopup-signature").each(function(){
			var element_id = jQuery(this).closest(".lepopup-element").attr("data-id");
			if (lepopup_signatures.hasOwnProperty(form_uid+"-"+element_id)) {
				var data_url = "";
				if (!(lepopup_signatures[form_uid+"-"+element_id]).isEmpty()) data_url = (lepopup_signatures[form_uid+"-"+element_id]).toDataURL();
				jQuery(this).closest(".lepopup-element").find("input").val(data_url);
			}
		});
	}

	var xd = jQuery(".lepopup-form-"+form_uid).attr("data-xd");
	if (!xd) xd = "off";

	var post_data = {"action" : "lepopup-front-"+_action, "campaign-slug" : lepopup_campaign_active_slug, "form-id" : form_id, "page-id" : page_id, "form-data" : lepopup_encode64(jQuery(".lepopup-form-"+form_uid).find("textarea, input, select").serialize()), "hostname" : window.location.hostname, "page-title" : lepopup_consts["page-title"]};
	if (lepopup_customjs_handlers.hasOwnProperty(form_uid)) {
		lepopup_customjs_handlers[form_uid].errors = {};
		if (lepopup_customjs_handlers[form_uid].hasOwnProperty("beforesubmit") && typeof lepopup_customjs_handlers[form_uid].beforesubmit == 'function') {
			try {
				lepopup_customjs_handlers[form_uid].beforesubmit();
			} catch(error) {
			}
		}
	}
	jQuery.ajax({
		url		:	lepopup_vars['ajax-url'],
		data	:	post_data,
		method	:	(lepopup_vars["mode"] == "remote" && xd == "on" ? "get" : "post"),
		dataType:	(lepopup_vars["mode"] == "remote" && xd == "on" ? "jsonp" : "json"),
		async	:	true,
		success	: function(return_data) {
			try {
				var data, temp;
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					if (lepopup_vars["overlays"].hasOwnProperty(form_slug)) {
						if (parseInt(lepopup_vars["overlays"][form_slug][9], 10) > 0) {
							lepopup_write_cookie("lepopup-submit-"+form_slug, lepopup_vars["cookie-value"], parseInt(lepopup_vars["overlays"][form_slug][9], 10)*24);
							if (lepopup_campaign_active_slug) lepopup_write_cookie("lepopup-submit-"+lepopup_campaign_active_slug, lepopup_vars["cookie-value"], parseInt(lepopup_vars["overlays"][form_slug][9], 10)*24);
						}
					} else {
						lepopup_write_cookie("lepopup-submit-"+form_slug, lepopup_vars["cookie-value"], 365*24);
						if (lepopup_campaign_active_slug) lepopup_write_cookie("lepopup-submit-"+lepopup_campaign_active_slug, lepopup_vars["cookie-value"], 365*24);
					}
					if (data.hasOwnProperty("record-id")) {
						jQuery(".lepopup-form-"+form_uid+" .lepopup-const-record-id").text(data["record-id"]);
					}
					if (lepopup_is_numeric(session_length) && session_length > 0) {
						lepopup_write_cookie("lepopup-session-"+form_id, "", 0);
						if (lepopup_sessions.hasOwnProperty(form_id)) delete lepopup_sessions[form_id];
					}
					if (data.hasOwnProperty("error")) console.log(data["error"]);
					if (typeof lepopupext_submit_after == 'function') {
						lepopupext_submit_after(form_slug);
					}
					if (lepopup_customjs_handlers.hasOwnProperty(form_uid)) {
						lepopup_customjs_handlers[form_uid].errors = {};
						if (lepopup_customjs_handlers[form_uid].hasOwnProperty("aftersubmitsuccess") && typeof lepopup_customjs_handlers[form_uid].aftersubmitsuccess == 'function') {
							try {
								lepopup_customjs_handlers[form_uid].aftersubmitsuccess();
							} catch(error) {
							}
						}
					}
					if (data.hasOwnProperty("forms")) {
						jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").append(data["forms"]);
						jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").find(".lepopup-send").trigger("click");
					}
					// linklocker - begin
					lepopup_unlock_links(form_slug);
					if (lepopup_campaign_active_slug) lepopup_unlock_links(lepopup_campaign_active_slug);
					if (lepopup_forced_location) {
						data.type = "redirect";
						data.url = lepopup_forced_location;
					}
					// linklocker - end
					switch (data.type) {
						case 'redirect':
							if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
							location.href = data.url;
							break;
						case 'payment':
							if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
							if (data.hasOwnProperty("payment-form")) {
								jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").append(data["payment-form"]);
								jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").find(".lepopup-pay").trigger("click");
							} else if (data.hasOwnProperty("payment-message")) {
								lepopup_popup_message_open(data["payment-message"]);
							} else if (data.hasOwnProperty("stripe")) {
								lepopup_stripe_checkout(data["stripe"]["public-key"], data["stripe"]["session-id"]);
							} else if (data.hasOwnProperty("payumoney")) {
								lepopup_payumoney_checkout(data["payumoney"]["request-data"]);
							}
							break;
						case 'page-redirect':
						case 'page-payment':
							if (parseInt(data.delay, 10) > 0) {
								setTimeout(function(){
									if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
									if (data.type == 'page-redirect') location.href = data.url;
									else {
										if (data.hasOwnProperty("payment-form")) {
											jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").append(data["payment-form"]);
											jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").find(".lepopup-pay").trigger("click");
										} else if (data.hasOwnProperty("payment-message")) {
											lepopup_popup_message_open(data["payment-message"]);
										} else if (data.hasOwnProperty("stripe")) {
											lepopup_stripe_checkout(data["stripe"]["public-key"], data["stripe"]["session-id"]);
										} else if (data.hasOwnProperty("payumoney")) {
											lepopup_payumoney_checkout(data["payumoney"]["request-data"]);
										}
									}
								}, 1000*parseInt(data.delay, 10));
							} else {
								if (data.type == 'page-redirect') location.href = data.url;
								else {
									if (data.hasOwnProperty("payment-form")) {
										jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").append(data["payment-form"]);
										jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").find(".lepopup-pay").trigger("click");
									} else if (data.hasOwnProperty("payment-message")) {
										lepopup_popup_message_open(data["payment-message"]);
									} else if (data.hasOwnProperty("stripe")) {
										lepopup_stripe_checkout(data["stripe"]["public-key"], data["stripe"]["session-id"]);
									} else if (data.hasOwnProperty("payumoney")) {
										lepopup_payumoney_checkout(data["payumoney"]["request-data"]);
									}
								}
							}
						case 'page':
							if (!lepopup_seq_pages.hasOwnProperty(form_uid)) lepopup_seq_pages[form_uid] = new Array();
							lepopup_seq_pages[form_uid].push(page_id);
							if (is_popup) {
								_lepopup_popup_page_open('confirmation');
								jQuery("#lepopup-popup-"+lepopup_popup_active_id).stop().animate({scrollTop: 0}, 300);
								if (parseInt(data.delay, 10) > 0) {
									setTimeout(function(){
										if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
										lepopup_popup_active_close(0);
									}, 1000*parseInt(data.delay, 10));
								}								
							} else {
								jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").fadeOut(300, function(){
									jQuery(this).find(".lepopup-element[data-content]").each(function(){
										jQuery(this).find(".lepopup-element-html-content").html("");
									});
									jQuery(".lepopup-form-"+form_uid+"[data-page='confirmation']").fadeIn(300).find(".lepopup-element[data-content]").each(function(){
										jQuery(this).find(".lepopup-element-html-content").html(lepopup_decode64(jQuery(this).attr("data-content")));
									});
									var element_top = jQuery(".lepopup-form-"+form_uid+"[data-page='confirmation']").offset().top;
									var viewport_top = jQuery(window).scrollTop();
									var viewport_bottom = viewport_top + jQuery(window).height();
									if (element_top < viewport_top || element_top > viewport_bottom) {
										jQuery('html, body').stop().animate({scrollTop: element_top-60}, 300);
									}
									lepopup_resize();
								});
							}
							break;
						case 'form':
							if (!lepopup_seq_pages.hasOwnProperty(form_uid)) lepopup_seq_pages[form_uid] = new Array();
							lepopup_seq_pages[form_uid].push(page_id);
							if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
							lepopup_popup_open(data.form);
							if (parseInt(data.delay, 10) > 0) {
								lepopup_timeout = setTimeout(function(){
									lepopup_popup_active_close(0);
								}, 1000*parseInt(data.delay, 10));
							}								
							break;
						default:
							if (!lepopup_seq_pages.hasOwnProperty(form_uid)) lepopup_seq_pages[form_uid] = new Array();
							lepopup_seq_pages[form_uid].push(page_id);
							if (data['reset-form'] == "on") lepopup_reset_form(form_uid);
							if (is_popup) {
								lepopup_popup_active_close(0);
							}
							break;
					}
					lepopup_track(form_uid, "lepopup", "submit");
				} else if (data.status == "NEXT") {
					if (!lepopup_seq_pages.hasOwnProperty(form_uid)) lepopup_seq_pages[form_uid] = new Array();
					lepopup_seq_pages[form_uid].push(page_id);
					if (is_popup) {
						_lepopup_popup_page_open(data.page);
						jQuery("#lepopup-popup-"+lepopup_popup_active_id).stop().animate({scrollTop: 0}, 300);
					} else {
						jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").fadeOut(300, function(){
							jQuery(this).find(".lepopup-element[data-content]").each(function(){
								jQuery(this).find(".lepopup-element-html-content").html("");
							});
							jQuery(".lepopup-form-"+form_uid+"[data-page='"+data.page+"']").fadeIn(300).find(".lepopup-element[data-content]").each(function(){
								jQuery(this).find(".lepopup-element-html-content").html(lepopup_decode64(jQuery(this).attr("data-content")));
							});
							
							var element_top = jQuery(".lepopup-form-"+form_uid+"[data-page='"+data.page+"']").offset().top;
							var viewport_top = jQuery(window).scrollTop();
							var viewport_bottom = viewport_top + jQuery(window).height();
							if (element_top < viewport_top || element_top > viewport_bottom) {
								jQuery('html, body').stop().animate({scrollTop: element_top-60}, 300);
							}
							lepopup_resize();
						});
					}
				} else if (data.status == "ERROR") {
					var min_index = null;
					var element_error = null, element_position = null;
					for (var id in data["errors"]) {
						if (data["errors"].hasOwnProperty(id)) {
							temp = id.split(":");
							if (all_pages.indexOf(temp[0]) >= 0) {
								if (min_index == null) min_index = all_pages.indexOf(temp[0]);
								else if (all_pages.indexOf(temp[0]) < min_index) min_index = all_pages.indexOf(temp[0]);
							}
							//jQuery(".lepopup-form-"+form_uid+"[data-page='"+temp[0]+"']").find(".lepopup-element-"+temp[1]).find(".lepopup-input").append("<div class='lepopup-element-error'><span>"+data["errors"][id]+"</span></div>");
							//jQuery(".lepopup-form-"+form_uid+"[data-page='"+temp[0]+"']").find(".lepopup-element-"+temp[1]).find(".lepopup-uploader").append("<div class='lepopup-uploader-error'><span>"+data["errors"][id]+"</span></div>");
							element_error = jQuery(".lepopup-form-"+form_uid+"[data-page='"+temp[0]+"']").find(".lepopup-element-"+temp[1]);
							element_position = {
								left: jQuery(element_error).attr("data-left"),
								top: jQuery(element_error).attr("data-top")
							};
							jQuery(".lepopup-form-"+form_uid+"[data-page='"+temp[0]+"']").find(".lepopup-form-inner").append("<div class='lepopup-element-error lepopup-element-error-"+temp[1]+"'><span>"+data["errors"][id]+"</span></div>");
							jQuery(".lepopup-form-"+form_uid+"[data-page='"+temp[0]+"']").find(".lepopup-element-error-"+temp[1]).css({"top" : parseInt(parseInt(element_position.top, 10)+parseInt(jQuery(element_error).height(), 10), 10)+"px", "left" : element_position.left+"px"});
						}
					}
					if (min_index != null && all_pages[min_index] != page_id) {
						for (var i=min_index; i<all_pages.length; i++) {
							if (lepopup_seq_pages[form_uid].indexOf(all_pages[i]) >= 0) lepopup_seq_pages[form_uid].splice(lepopup_seq_pages[form_uid].indexOf(all_pages[i]), 1);
						}
						jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"']").fadeOut(300, function(){
							jQuery(".lepopup-form-"+form_uid+"[data-page='"+all_pages[min_index]+"']").fadeIn(300);
							page_id = all_pages[min_index];
							jQuery(".lepopup-form-"+form_uid).find(".lepopup-element-error").fadeIn(300);
						});
					} else jQuery(".lepopup-form-"+form_uid).find(".lepopup-element-error").fadeIn(300);
					jQuery(".lepopup-form-"+form_uid+"[data-page='"+page_id+"'] .lepopup-element").each(function(){
						if (jQuery(this).find(".lepopup-element-error").length > 0) {
							if (is_popup) {
								jQuery("#lepopup-popup-"+lepopup_popup_active_id).stop().animate({scrollTop: 0}, 300);
								return false;
							} else {
								var element_top = jQuery(this).offset().top;
								var viewport_top = jQuery(window).scrollTop();
								var viewport_bottom = viewport_top + jQuery(window).height();
								if (element_top < viewport_top || element_top > viewport_bottom) {
									jQuery('html, body').stop().animate({scrollTop: element_top-60}, 300);
									return false;
								}
							}
						}
					});
				} else if (data.status == "FATAL") {
					
				} else {
					
				}
			} catch(error) {
				console.log(error);
			}
			if (button_pressed) {
				jQuery(_object).find("span").text(jQuery(_object).attr("data-label"));
				var original_icon = jQuery(_object).attr("data-original-icon");
				if (typeof original_icon !== typeof undefined && original_icon !== false) jQuery(_object).children("i").first().attr("class", original_icon);
			}
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-button").removeClass("lepopup-button-disabled");
			lepopup_sending = false;
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(errorThrown);
			if (button_pressed) {
				jQuery(_object).find("span").text(jQuery(_object).attr("data-label"));
				var original_icon = jQuery(_object).attr("data-original-icon");
				if (typeof original_icon !== typeof undefined && original_icon !== false) jQuery(_object).children("i").first().attr("class", original_icon);
			}
			jQuery(".lepopup-form-"+form_uid).find(".lepopup-button").removeClass("lepopup-button-disabled");
			lepopup_sending = false;
		}
	});
	return false;
}
function lepopup_popup_message_open(_html) {
	jQuery('body').append("<div class='lepopup-popup-message-overlay'></div>");
	jQuery(".lepopup-popup-message-overlay").fadeIn(300);
	jQuery('body').append("<div class='lepopup-popup-message'><div class='lepopup-popup-message-content'><span class='lepopup-popup-message-close' onclick='lepopup_popup_message_close();'><i class='lepopup-if lepopup-if-times'></i></span>"+_html+"</div></div>");
	jQuery(".lepopup-popup-message").fadeIn(300);
}
function lepopup_popup_message_close() {
	jQuery(".lepopup-popup-message").fadeOut(300, function(){
		jQuery(".lepopup-popup-message").remove();
		jQuery(".lepopup-popup-message-overlay").fadeOut(300, function(){
			jQuery(".lepopup-popup-message-overlay").remove();
		});
	});
}
function lepopup_handle_math(_form_uid) {
	if (lepopup_vars["mode"] == "remote" && lepopup_vars["plugins"].indexOf("jsep") >= 0 && typeof jsep == typeof undefined) {
		setTimeout(function(){lepopup_handle_math(_form_uid);}, 500);
	}
	jQuery(".lepopup-form-"+_form_uid).parent().find("input.lepopup-math").each(function(){
		var replacement, from_element, type, value, values, parse_tree, ref_date ;
		var id = jQuery(this).attr("data-id");
		var expression = jQuery(this).attr("data-expression");
		var var_value = jQuery(this).attr("data-default");
		var decimal_digits = parseInt(jQuery(this).attr("data-decimal"), 10);
		var ids_raw = jQuery(this).attr("data-ids");
		var ids = ids_raw.split(",");
		jQuery(this).val("");
		for (var j=0; j<ids.length; j++) {
			from_element = jQuery(".lepopup-form-"+_form_uid+" .lepopup-element-"+ids[j]);
			if (from_element.length > 0) {
				if (lepopup_is_visible(_form_uid, ids[j])) {
					type = jQuery(from_element).attr("data-type");
					switch (type) {
						case 'file':
							replacement = 0;
							jQuery(from_element).find(".lepopup-uploader-file-countable.lepopup-uploader-file-processed").each(function(){
								replacement++;
							});
							break;
						case 'date':
							value = lepopup_date(jQuery(from_element).find("input").val(), jQuery(from_element).find("input").attr("data-format"));
							ref_date = new Date(2000, 0, 1);
							if (value != null) {
								replacement = parseInt(Math.round((value-ref_date)/(1000*60*60*24)), 10);
							} else replacement = 'error';
							break;
						case 'time':
							replacement = 0;
							break;
						case 'email':
						case 'text':
						case 'number':
						case 'numspinner':
							value = lepopup_extract_number(jQuery(from_element).find("input").val());
							if (isNaN(parseFloat(value)) || !isFinite(value)) replacement = 'error';
							else replacement = parseFloat(value);
							break;
						case 'textarea':
							value = lepopup_extract_number(jQuery(from_element).find("textarea").val());
							replacement = parseFloat(value);
							break;
						case 'select':
							value = lepopup_extract_number(jQuery(from_element).find("select").val());
							if (isNaN(parseFloat(value)) || !isFinite(value)) replacement = 'error';
							else replacement = parseFloat(value);
							break;
						case 'radio':
						case 'checkbox':
						case 'imageselect':
						case 'tile':
						case 'multiselect':
						case 'star-rating':
							replacement = 0;
							jQuery(from_element).find("input").each(function(){
								if (jQuery(this).is(":checked")) {
									value = lepopup_extract_number(jQuery(this).val());
									if (isNaN(parseFloat(value)) || !isFinite(value)) {
										replacement = 'error';
										return false;
									}
									replacement += parseFloat(value);
								};
							});
							break;
						case 'rangeslider':
							value = lepopup_extract_number(jQuery(from_element).find("input").val());
							values = value.split(":");
							if (values.length == 1) {
								if (isNaN(parseFloat(value)) || !isFinite(value)) replacement = 'error';
								else replacement = parseFloat(value);
							} else if (values.length == 2) {
								if (isNaN(parseFloat(values[0])) || !isFinite(values[0]) || isNaN(parseFloat(values[1])) || !isFinite(values[1])) replacement = 'error';
								else replacement = (parseFloat(values[0]) + parseFloat(values[1]))/2;
							} else replacement = 'error';
							break;
						default:
							replacement = 0;
							break;
					}
				} else replacement = 0;
				expression = expression.split("{"+ids[j]+"}").join(replacement);
			} else {
				from_element = jQuery(".lepopup-form-"+_form_uid+" .lepopup-hidden[name='lepopup-"+ids[j]+"']");
				if (from_element.length > 0) {
					value = lepopup_extract_number(jQuery(from_element).val());
					if (isNaN(parseFloat(value)) || !isFinite(value)) replacement = 'error';
					else replacement = parseFloat(value);
					expression = expression.split("{"+ids[j]+"}").join(replacement);
				}
			}
		}
		if (typeof jsep != typeof undefined) {
			try {
				parse_tree = jsep(expression);
				if (parse_tree.type == 'Compound') value = parseFloat(expression);
				else {
					value = lepopup_jsep_calc(parse_tree);
				}
				if (value !== false) {
					jQuery(this).val(value);
					var_value = value.toFixed(decimal_digits);
				}
			} catch(error) {
			}
		}
		jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+id).text(var_value);
	});
}
function lepopup_jsep_calc(_parse_tree) {
	var left, right;
	if (typeof _parse_tree != typeof {}) {
		return false;
	} else if (_parse_tree.type == "BinaryExpression") {
		left = lepopup_jsep_calc(_parse_tree.left);
		right = lepopup_jsep_calc(_parse_tree.right);
		if (left === false || right === false) return false;
		if (_parse_tree.operator == "+") return parseFloat(left+right);
		else if (_parse_tree.operator == "-") return parseFloat(left-right);
		else if (_parse_tree.operator == "*") return parseFloat(left*right);
		else if (_parse_tree.operator == "/" && right != 0) return parseFloat(left/right);
		else return false;
	} else if (_parse_tree.type == "UnaryExpression") {
		left = 0;
		right = lepopup_jsep_calc(_parse_tree.argument);
		if (_parse_tree.operator == "+") return parseFloat(left+right);
		else if (_parse_tree.operator == "-") return parseFloat(left-right);
		else if (_parse_tree.operator == "*") return parseFloat(left*right);
		else if (_parse_tree.operator == "/" && right != 0) return parseFloat(left/right);
		else return false;
	} else if (_parse_tree.type == "Literal") {
		return parseFloat(_parse_tree.value);
	} else return false;
}
function lepopup_consts_update(_form_uid, _consts) {
	var selector = ".lepopup-const";
	if (_form_uid != null) selector = ".lepopup-form-"+_form_uid+" .lepopup-const";
	jQuery(selector).each(function(){
		var element_id = jQuery(this).attr("data-id");
		if (_consts.hasOwnProperty(element_id)) {
			jQuery(this).text(_consts[element_id]);
		}
	});
}
function lepopup_reset_form(_form_uid) {
	var input, default_value = "";
	jQuery(".lepopup-form-"+_form_uid+" .lepopup-hidden").each(function(){
		var url_parameter = null;
		var element_id = jQuery(this).attr("data-id");
		var dynamic_parameter = jQuery(this).attr("data-dynamic");
		if (typeof dynamic_parameter != "undefined" && dynamic_parameter != "") {
			url_parameter = lepopup_query_parameter(dynamic_parameter);
		}
		if (url_parameter != null) default_value = url_parameter;
		else default_value = jQuery(this).attr("data-default");
		jQuery(this).val(default_value);
		jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
	});
	lepopup_consts_update(_form_uid, lepopup_consts);
	var form_id = jQuery(".lepopup-form-"+_form_uid).attr("data-form-id");
	lepopup_sessions[form_id] = {"modified" : false, "values" : {}};
	var session_length = jQuery(".lepopup-form-"+_form_uid).attr("data-session");
	var session_enable = false;
	if (lepopup_is_numeric(session_length) && session_length > 0) {
		session_enable = true;
		try {
			var cookie_session = JSON.parse(lepopup_read_cookie("lepopup-session-"+form_id));
			if (cookie_session != null) lepopup_sessions[form_id]["values"] = cookie_session;
		} catch(error) {
		}
	}
	jQuery(".lepopup-form-"+_form_uid+" .lepopup-element").each(function(){
		var url_parameters, url_parameter = null;
		var var_values = new Array();
		var type = jQuery(this).attr("data-type");
		var temp, upload_id;
		var element_id = jQuery(this).attr("data-id");
		var dynamic_parameter = jQuery(this).attr("data-dynamic");
		if (typeof dynamic_parameter != "undefined" && dynamic_parameter != "") {
			url_parameter = lepopup_query_parameter(dynamic_parameter);
		}
		switch (type) {
			case 'file':
				jQuery(this).find(".lepopup-uploader-files").html("");
				upload_id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
				temp = lepopup_decode64(jQuery(this).find(".lepopup-uploader-template").val());
				temp = temp.replace(new RegExp("%%upload-id%%", 'g'), upload_id).replace(new RegExp("%%ajax-url%%", 'g'), lepopup_vars["ajax-url"]);
				jQuery(this).find(".lepopup-uploaders").html(temp);
				break;
			case 'date':
				input = jQuery(this).find("input");
				if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) default_value = lepopup_sessions[form_id]["values"][element_id];
				else if (url_parameter != null) default_value = url_parameter;
				else {
					default_value = jQuery(input).attr("data-default");
					switch(default_value) {
						case 'today':
							temp = new Date();
							default_value = lepopup_date_str(temp, jQuery(input).attr("data-format"));
							break;
						case 'yesterday':
							temp = new Date();
							temp.setDate(temp.getDate() - 1);
							default_value = lepopup_date_str(temp, jQuery(input).attr("data-format"));
							break;
						case 'tomorrow':
							temp = new Date();
							temp.setDate(temp.getDate() + 1);
							default_value = lepopup_date_str(temp, jQuery(input).attr("data-format"));
							break;
						case 'offset':
							temp = new Date();
							temp.setDate(temp.getDate() + parseInt(jQuery(input).attr("data-offset"), 10));
							default_value = lepopup_date_str(temp, jQuery(input).attr("data-format"));
							break;
						default:
							break;
					}
					
				}
				jQuery(input).val(default_value);
				jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
				break;
			case 'email':
			case 'text':
			case 'time':
			case 'password':
			case 'number':
			case 'numspinner':
				input = jQuery(this).find("input");
				if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) default_value = lepopup_sessions[form_id]["values"][element_id];
				else if (url_parameter != null) default_value = url_parameter;
				else default_value = jQuery(input).attr("data-default");
				jQuery(input).val(default_value);
				if (type == "numspinner") jQuery(input).attr("data-value", default_value);
				jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
				break;
			case 'textarea':
				input = jQuery(this).find("textarea");
				if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) default_value = lepopup_sessions[form_id]["values"][element_id];
				else if (url_parameter != null) default_value = url_parameter;
				else default_value = lepopup_decode64(jQuery(input).attr("data-default"));
				jQuery(input).val(default_value);
				jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
				break;
			case 'select':
				input = jQuery(this).find("select");
				if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) default_value = lepopup_sessions[form_id]["values"][element_id];
				else if (url_parameter != null) default_value = url_parameter;
				else default_value = jQuery(input).attr("data-default");
				jQuery(input).val(default_value);
				jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
				break;
			case 'checkbox':
			case 'imageselect':
			case 'tile':
			case 'radio':
			case 'multiselect':
			case 'star-rating':
				if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) {
					jQuery(this).find("input").each(function(){
						default_value = jQuery(this).val();
						if ((Array.isArray(lepopup_sessions[form_id]["values"][element_id]) && (lepopup_sessions[form_id]["values"][element_id]).indexOf(default_value) >= 0) || default_value == lepopup_sessions[form_id]["values"][element_id]) {
							jQuery(this).prop("checked", true);
							var_values.push(default_value);
						} else jQuery(this).prop("checked", false);
					});
				} else if (url_parameter != null) {
					url_parameters = url_parameter.split(",");
					jQuery(this).find("input").each(function(){
						default_value = jQuery(this).val();
						if (url_parameters.indexOf(default_value) >= 0) {
							jQuery(this).prop("checked", true);
							var_values.push(jQuery(this).val());
						} else jQuery(this).prop("checked", false);
					});
				} else {
					jQuery(this).find("input").each(function(){
						default_value = jQuery(this).attr("data-default");
						if (default_value == "on") {
							jQuery(this).prop("checked", true);
							var_values.push(jQuery(this).val());
						} else jQuery(this).prop("checked", false);
					});
				}
				jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(var_values.join(", "));
				break;
			case 'signature':
				if (typeof SignaturePad != typeof undefined) {
					if (lepopup_signatures.hasOwnProperty(_form_uid+"-"+element_id)) {
						(lepopup_signatures[_form_uid+"-"+element_id]).clear();
					}
				}
				break;
			case 'rangeslider':
				if (typeof jQuery.fn.ionRangeSlider != typeof undefined && jQuery.fn.ionRangeSlider) {
					jQuery(this).find("input").each(function(){
						if (session_enable && lepopup_sessions.hasOwnProperty(form_id) && lepopup_sessions[form_id] != null && lepopup_sessions[form_id].hasOwnProperty("values") && lepopup_sessions[form_id]["values"] != null && lepopup_sessions[form_id]["values"].hasOwnProperty(element_id)) default_value = lepopup_sessions[form_id]["values"][element_id];
						else default_value = jQuery(this).attr("data-default");
						var from_to = default_value.split(":");
						jQuery(this).attr("data-from", from_to[0]);
						if (from_to.length > 1) jQuery(this).attr("data-to", from_to[1]);
						jQuery(this).val(default_value);
						var rangeslider = jQuery(this).data("ionRangeSlider");
						if (typeof rangeslider != typeof undefined && rangeslider) {
							rangeslider.reset();
						}
						default_value = default_value.replace(":", " ... ");
						jQuery(".lepopup-form-"+_form_uid+" .lepopup-var-"+element_id).text(default_value);
					});
				}
				break;
			default:
				break;
		}
	});
	lepopup_handle_math(_form_uid);
}

function lepopup_track(_uid, _type, _action) {
	if (lepopup_vars['ga-tracking'] == "on") {
		try {
			var title = jQuery(".lepopup-form-"+_uid).first().attr("data-title");
			if (!title) title = 'Unknown form';
			if (typeof _gaq == 'object') {
				_gaq.push(['_trackEvent', _type, _action, title, 1, false]);
			} else if (typeof _trackEvent == 'function') { 
				_trackEvent(_type, _action, title, 1, false);
			} else if (typeof __gaTracker == 'function') { 
				__gaTracker('send', 'event', _type, _action, title);
			} else if (typeof ga == 'function') {
				ga('send', 'event', _type, _action, title);
			}
		} catch(error) {
		}
	}
}

function lepopup_uploader_files_selected(_object) {
	jQuery(_object).parent().trigger("submit");
}
function lepopup_uploader_file_delete(_object) {
	var file = jQuery(_object).closest(".lepopup-uploader-file");
	var name = jQuery(file).attr("data-name");
	var upload_id = jQuery(file).attr("data-upload");
	var form_uid = jQuery(_object).closest(".lepopup-form").attr("data-id");
	jQuery(file).slideUp(200, function(){
		var temp = jQuery(file).parent();
		jQuery(file).remove();
		lepopup_input_changed(temp);
		if (jQuery(".lepopup-uploader-file-"+upload_id).length == 0) {
			lepopup_uploads[form_uid][upload_id] = 'DELETED';
			jQuery("#"+upload_id).remove();
		}
	});
	var post_data = {"action" : "lepopup-upload-delete", "upload-id" : upload_id, "name" : name, "hostname" : window.location.hostname};
	jQuery.ajax({
		url		:	lepopup_vars['ajax-url'],
		data	:	post_data,
		method	:	(lepopup_vars["mode"] == "remote" ? "get" : "post"),
		dataType:	(lepopup_vars["mode"] == "remote" ? "jsonp" : "json"),
		async	:	true
	});
}
function lepopup_uploader_start(_object) {
	var temp;
	var upload_id = jQuery(_object).closest(".lepopup-uploader").attr("id");
	var form_uid = jQuery(_object).closest(".lepopup-form").attr("data-id");
	var form_element = jQuery(_object).closest(".lepopup-element");
	var max_size = parseInt(jQuery(form_element).attr("data-max-size"), 10)*1024*1024;
	var max_files = parseInt(jQuery(form_element).attr("data-max-files"), 10);
	temp = jQuery(form_element).attr("data-allowed-extensions");
	temp = temp.toLowerCase();
	var allowed_extensions = temp.split(",");
	temp = null;
	var countable_files = jQuery(_object).closest(".lepopup-upload-input").find(".lepopup-uploader-file-countable").length;
	var size_visual, ext, html = "";
	var error = false;
	var error_message = "";
	var files = jQuery(_object).find("input[type=file]")[0].files;
	if (files.length < 1) return false;
	for (var i=0; i<files.length; i++) {
		if (countable_files + files.length > max_files) {
			error = true;
			error_message = jQuery(form_element).attr("data-max-files-error");
			break;
		}
		ext = "."+(files[i].name).split(".").pop();
		ext = ext.toLowerCase();
		if (allowed_extensions.length > 0 && allowed_extensions[0] != "" && allowed_extensions.indexOf(ext) < 0) {
			error = true;
			error_message = jQuery(form_element).attr("data-allowed-extensions-error");
			break;
		}
		if (max_size > 0 && files[i].size > max_size) {
			error = true;
			error_message = jQuery(form_element).attr("data-max-size-error");
			break;
		}
		if (files[i].size > 4*1024*1024) size_visual = Math.round(10*files[i].size/(1024*1024))/10 + " Mb";
		else if (files[i].size > 4*1024) size_visual = Math.round(10*files[i].size/1024)/10 + " Kb";
		else size_visual = files[i].size + " bytes";
		html += "<div class='lepopup-uploader-file lepopup-uploader-file-"+upload_id+" lepopup-uploader-file-countable' data-upload='"+upload_id+"' data-name='"+lepopup_escape_html(files[i].name)+"' data-size='"+files[i].size+"'><div class='lepopup-uploader-file-title'>"+lepopup_escape_html(files[i].name)+" ("+size_visual+")</div><div class='lepopup-uploader-progress'>Uploading...</div><span onclick='return lepopup_uploader_file_delete(this);'><i class='lepopup-if lepopup-if-times'></i></span></div>";
	}
	if (error) {
		jQuery(_object).closest(".lepopup-uploader").append("<div class='lepopup-uploader-error'><span>"+error_message+"</span></div>");
		jQuery(_object).closest(".lepopup-uploader").find(".lepopup-uploader-error").fadeIn(300);
		return false;
	} else {
		jQuery(_object).closest(".lepopup-uploader").find(".lepopup-button").remove();
		var new_upload_id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
		temp = lepopup_decode64(jQuery(_object).closest(".lepopup-upload-input").find(".lepopup-uploader-template").val());
		temp = temp.replace(new RegExp("%%upload-id%%", 'g'), new_upload_id).replace(new RegExp("%%ajax-url%%", 'g'), lepopup_vars["ajax-url"]);
		jQuery(_object).closest(".lepopup-uploaders").append(temp);
		jQuery(_object).closest(".lepopup-upload-input").find(".lepopup-uploader-files").append(html);
		if (!lepopup_uploads.hasOwnProperty(form_uid)) lepopup_uploads[form_uid] = {};
		lepopup_uploads[form_uid][upload_id] = 'LOADING';
		lepopup_uploader_progress(form_uid, upload_id);
	}
}
function lepopup_uploader_finish(_object) {
	var upload_id = jQuery(_object).closest(".lepopup-uploader").attr("id");
	var form_uid = jQuery(_object).closest(".lepopup-form").attr("data-id");
	if (lepopup_uploads.hasOwnProperty(form_uid) && lepopup_uploads[form_uid].hasOwnProperty(upload_id) && lepopup_uploads[form_uid][upload_id] == "LOADING") {
		lepopup_uploads[form_uid][upload_id] = "UPLOADED";
	}
}
function lepopup_uploader_progress(_form_uid, _upload_id) {
	var post_data = {"action" : "lepopup-upload-progress", "upload-id" : _upload_id, "hostname" : window.location.hostname};
	if (lepopup_uploads[_form_uid][_upload_id] == "DELETED") return;
	else if (lepopup_uploads[_form_uid][_upload_id] == "UPLOADED") post_data["last-request"] = "on";
	jQuery.ajax({
		url		:	lepopup_vars['ajax-url'],
		data	:	post_data,
		method	:	(lepopup_vars["mode"] == "remote" ? "get" : "post"),
		dataType:	(lepopup_vars["mode"] == "remote" ? "jsonp" : "json"),
		async	:	true,
		success	: function(return_data) {
			try {
				var data, file_container, field_id;
				field_id = jQuery("#"+_upload_id).closest(".lepopup-element").attr("data-id");
				if (typeof return_data == 'object') data = return_data;
				else data = jQuery.parseJSON(return_data);
				if (data.status == "OK") {
					lepopup_uploads[_form_uid][_upload_id] = 'OK';
					if (data.hasOwnProperty("result")) {
						for (var i=0; i<data["result"].length; i++) {
							file_container = jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id+"[data-name='"+lepopup_escape_html(data["result"][i]["name"])+"']");
							if (data["result"][i]["status"] == "OK") {
								jQuery(file_container).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-bar' style='width:100%;'></div>");
								jQuery(file_container).append("<input type='hidden' name='lepopup-"+field_id+"[]' value='"+lepopup_escape_html(data["result"][i]["uid"])+"' />");
							} else {
								jQuery(file_container).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-error'>"+data["result"][i]["message"]+"</div>");
								jQuery(file_container).removeClass("lepopup-uploader-file-countable");
							}
							jQuery(file_container).addClass("lepopup-uploader-file-processed");
						}
					}
					jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id).each(function(){
						if (!jQuery(this).hasClass("lepopup-uploader-file-processed")) {
							jQuery(this).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-error'>File can not be uploaded.</div>");
							jQuery(this).removeClass("lepopup-uploader-file-countable");
							jQuery(this).addClass("lepopup-uploader-file-processed");
						}
					});
					lepopup_input_changed("#"+_upload_id);
					jQuery("#"+_upload_id).remove();
				} else if (data.status == "LOADING") {
					if (data.hasOwnProperty("progress")) {
						for (var i=0; i<data["progress"].length; i++) {
							file_container = jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id+"[data-name='"+lepopup_escape_html(data["progress"][i]["name"])+"']");
							if (file_container.length > 0) {
								jQuery(file_container).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-bar' style='width:"+Math.ceil(100*parseInt(data["progress"][i]["bytes_processed"]) / parseInt(jQuery(file_container).attr("data-size"), 10))+"%;'></div>");
							}
						}
					}
					setTimeout(function(){
						lepopup_uploader_progress(_form_uid, _upload_id);
					}, 500);
				} else {
					lepopup_uploads[_form_uid][_upload_id] = 'ERROR';
					jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id).each(function(){
						if (!jQuery(this).hasClass("lepopup-uploader-file-processed")) {
							jQuery(this).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-error'>Internal Error!</div>");
							jQuery(this).removeClass("lepopup-uploader-file-countable");
							jQuery(this).addClass("lepopup-uploader-file-processed");
						}
					});
					jQuery("#"+_upload_id).remove();
				}
			} catch(error) {
				console.log(error);
				lepopup_uploads[_form_uid][_upload_id] = 'ERROR';
				jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id).each(function(){
					if (!jQuery(this).hasClass("lepopup-uploader-file-processed")) {
						jQuery(this).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-error'>Internal Error!</div>");
						jQuery(this).removeClass("lepopup-uploader-file-countable");
						jQuery(this).addClass("lepopup-uploader-file-processed");
					}
				});
				jQuery("#"+_upload_id).remove();
			}
		},
		error	: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(errorThrown);
			lepopup_uploads[_form_uid][_upload_id] = 'ERROR';
			jQuery("#"+_upload_id).closest(".lepopup-upload-input").find(".lepopup-uploader-file-"+_upload_id).each(function(){
				if (!jQuery(this).hasClass("lepopup-uploader-file-processed")) {
					jQuery(this).find(".lepopup-uploader-progress").html("<div class='lepopup-uploader-progress-error'>Internal Error!</div>");
					jQuery(this).removeClass("lepopup-uploader-file-countable");
					jQuery(this).addClass("lepopup-uploader-file-processed");
				}
			});
			jQuery("#"+_upload_id).remove();
		}
	});
}
function lepopup_input_error_hide(_object) {
	var element_id = jQuery(_object).closest(".lepopup-element").attr("data-id");
	jQuery(_object).closest(".lepopup-form").find(".lepopup-element-error-"+element_id).fadeOut(300, function(){jQuery(this).remove();});
}
function lepopup_stripe_checkout(_public_key, _session_id) {
	try {
		var stripe = Stripe(_public_key, {betas: ['checkout_beta_4']});
		stripe.redirectToCheckout({sessionId: _session_id}).then(function (result) {
			console.log(result);
		});
	} catch(error) {
		console.log(error);
	}
}
function lepopup_payumoney_checkout(_request_data) {
	try {
		if (typeof bolt != typeof undefined) {
			var handler = {
				responseHandler: function(BOLT){
					console.log("Response");
					console.log(BOLT);
				},
				catchException: function(BOLT){
					console.log("Exception");
					console.log(BOLT);
				}
			}			
			bolt.launch(_request_data, handler);
		} else console.log('No bolt');
	} catch(error) {
		console.log(error);
	}
}
// linklocker-begin
function lepopup_unlock_links(_slug) {
	jQuery(".lepopup-linklocker-"+_slug).each(function(){
		var url = jQuery(this).attr("href");
		var url_idx = url.lastIndexOf(":");
		if (url_idx > 0) {
			var url = url.substr(url_idx + 1);
			if (url.length > 0) {
				url = lepopup_decode64(url);
				jQuery(this).attr("href", url);
			}
		}
	});
}
// linklocker-end
var lepopup_global_message_timer;
function lepopup_global_message_show(_type, _message) {
	clearTimeout(lepopup_global_message_timer);
	if (jQuery("#lepopup-global-message").length == 0) jQuery("body").append("<div id='lepopup-global-message'></div>");
	jQuery("#lepopup-global-message").fadeOut(300, function() {
		jQuery("#lepopup-global-message").attr("class", "");
		jQuery("#lepopup-global-message").addClass("lepopup-global-message-"+_type).html(_message);
		jQuery("#lepopup-global-message").fadeIn(300);
		lepopup_global_message_timer = setTimeout(function(){jQuery("#lepopup-global-message").fadeOut(300);}, 5000);
	});
}
function lepopup_date(_date, _format) {
	var pattern = _format.replace('yyyy', '([0-9]{4})').replace('mm', '([0-9]{2})').replace('dd', '([0-9]{2})');
	var match = _date.match(pattern);
	if (!match || _format.length != _date.length) return null;
	var year = parseInt(_date.substr(_format.indexOf('yyyy'), 4), 10);
	var month = parseInt(_date.substr(_format.indexOf('mm'), 2), 10);
	var day = parseInt(_date.substr(_format.indexOf('dd'), 2), 10);
	var date = new Date(year, month-1, day);
	if (date.getDate() == day && date.getMonth() == month-1 && date.getFullYear() == year) return date;
	return null;
}
function lepopup_date_str(_date, _format) {
	var pattern = _format;
	var prefix = "";
	var temp = _date.getDate();
	if (temp < 9) prefix = "0";
	pattern = pattern.replace("dd", prefix+temp);
	temp = _date.getMonth() + 1;
	if (temp < 9) prefix = "0";
	else prefix = "";
	pattern = pattern.replace("mm", prefix+temp);
	temp = _date.getFullYear();
	pattern = pattern.replace("yyyy", temp);
	return pattern;
}

function lepopup_time24_str(_time, _format) {
	var pattern = _format.replace('hh', '([0-9]{2})').replace('ii', '([0-9]{2})').replace('aa', '(am|pm)');
	var match = _time.match(pattern);
	if (!match || _format.length != _time.length) return null;
	var hours = parseInt(_time.substr(_format.indexOf('hh'), 2), 10);
	if (hours < 0 || hours > 23) return null;
	var minutes = parseInt(_time.substr(_format.indexOf('ii'), 2), 10);
	if (minutes < 0 || minutes > 59) return null;
	var ampm = null;
	if (_format.indexOf('aa') >= 0) {
		ampm = _time.substr(_format.indexOf('aa'), 2);
		ampm = ampm.toLowerCase();
	}
	if (ampm != null && hours == 12) hours = 0;
	if (ampm == "pm") hours += 12;
	return (hours < 10 ? "0" : "")+hours+":"+(minutes < 10 ? "0" : "")+minutes;
}
function lepopup_extract_number(_value) {
	if (_value == null) return "";
	var str_value = _value.toString();
	var num_value;
	var result = str_value.match(/\((.*?)\)/g);
	if (result) {
		var values = result.map(function(val){
			return val.replace(/\(|\)/g,'');
		});
		num_value = values[values.length-1].replace(",", ".");
	} else num_value = str_value.replace(",", ".");
	if (!isNaN(num_value) && !isNaN(parseFloat(num_value))) return num_value;
	return str_value;
}

function lepopup_query_parameter(_name) {
	var url = window.location.href;
	_name = _name.replace(/[\[\]]/g, '\\$&');
	var regex = new RegExp('[?&]' + _name + '(=([^&#]*)|&|#|$)'),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
function lepopup_escape_html(_text) {
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
function lepopup_read_cookie(key) {
	var pairs = document.cookie.split("; ");
	for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
		if (pair[0] === key) return pair[1] || "";
	}
	return null;
}
function lepopup_write_cookie(key, value, hours) {
	if (hours) {
		var date = new Date();
		date.setTime(date.getTime()+(hours*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "; expires=0";
	document.cookie = key+"="+value+expires+"; path=/";
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
	var c = 0, c1 = 0, c2 = 0, c3 = 0;
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

if (typeof ulp_add_event == typeof undefined) {
	var ulp_add_event = function(_event, _data) {_lepopup_add_event(_event, _data);};
}
if (typeof lepopup_add_event == typeof undefined) {
	var lepopup_add_event = function(_event, _data) {_lepopup_add_event(_event, _data);};
}
function _lepopup_add_event(_event, _data) {
//lepopup_custom_events_data	
	var events_data = {};
	if (typeof _data == 'object' && _data != null) {
		if (!_data.hasOwnProperty("item") && !_data.hasOwnProperty("item_mobile") && !_data.hasOwnProperty("popup") && !_data.hasOwnProperty("popup_mobile")) return;
		var item = "";
		if (lepopup_mobile && _data.hasOwnProperty("item_mobile")) item = _data.item_mobile;
		else if (_data.hasOwnProperty("item")) item = _data.item;
		else if (lepopup_mobile && _data.hasOwnProperty("popup_mobile")) item = _data.popup_mobile;
		else if (_data.hasOwnProperty("popup")) item = _data.popup;
		if (item == "") return;
		switch (_event) {
			case "onload":
				events_data = {
					'onload-item'				: item,
					'onload-mode'				: _data.hasOwnProperty('mode') ? _data.mode : 'every-time',
					'onload-mode-period'		: _data.hasOwnProperty('period') ? parseInt(_data.period, 10) : 24,
					'onload-mode-delay'			: _data.hasOwnProperty('delay') ? parseInt(_data.delay, 10) : 0,
					'onload-mode-close-delay'	: _data.hasOwnProperty('close_delay') ? parseInt(_data.close_delay, 10) : 0
				};
				break;
			case "onexit":
				events_data = {
					'onexit-item'				: item,
					'onexit-mode'				: _data.hasOwnProperty('mode') ? _data.mode : 'every-time',
					'onexit-mode-period'		: _data.hasOwnProperty('period') ? parseInt(_data.period, 10) : 24
				};
				break;
			case "onscroll":
				events_data = {
					'onscroll-item'				: item,
					'onscroll-mode'				: _data.hasOwnProperty('mode') ? _data.mode : 'every-time',
					'onscroll-mode-period'		: _data.hasOwnProperty('period') ? parseInt(_data.period, 10) : 24,
					'onscroll-mode-offset'		: _data.hasOwnProperty('offset') ? _data.offset.toString() : 600
				};
				break;
			case "onidle":
				events_data = {
					'onidle-item'				: item,
					'onidle-mode'				: _data.hasOwnProperty('mode') ? _data.mode : 'every-time',
					'onidle-mode-period'		: _data.hasOwnProperty('period') ? parseInt(_data.period, 10) : 24,
					'onidle-mode-delay'			: _data.hasOwnProperty('delay') ? parseInt(_data.delay, 10) : 30
				};
				break;
			case "onabd":
				events_data = {
					'onabd-item'				: item,
					'onabd-mode'				: _data.hasOwnProperty('mode') ? _data.mode : 'every-time',
					'onabd-mode-period'			: _data.hasOwnProperty('period') ? parseInt(_data.period, 10) : 24
				};
				break;
			default:
				break;
		}
		lepopup_custom_events_data = Object.assign(lepopup_custom_events_data, events_data);
	}
}