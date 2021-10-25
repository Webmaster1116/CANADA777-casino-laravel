
var navExport = function(action) {
    var sliderID = jQuery('#rs_breadcrumbs_slider_settings').data('id');
    if ( isNaN(sliderID) || sliderID == '' ) {
        UniteAdminRev.showInfo({type: 'warning', hideon: '', event: '', content: 'Slider ID should not be empty', hidedelay: 3});
        return;
    }
    var urlAjaxExport = ajaxurl+"&action="+g_uniteDirPlugin+"_ajax_action&client_action=" + action + "&dummy=0&nonce=" + g_revNonce;
    urlAjaxExport += "&sliderid=" + sliderID;
    location.href = urlAjaxExport;
};

jQuery(document).ready(function() {
	jQuery("#nav_button_export_slider").click(function() {
        navExport('export_slider');
    });
    jQuery("#nav_button_export_html").click(function() {
        navExport('preview_slider&only_markup=true');
    });
});