<?php
if( !defined( 'ABSPATH') ) exit();

$slidersHtml = array(
    'sliders' => array(),
    'buttons' => array()
);

if(!$no_sliders){

    $useSliders = $arrSliders;

    foreach($arrSliders as $slider){

        try{
            $errorMessage = '';

            $id = $slider->getID();
            $showTitle = $slider->getShowTitle();
            $title = $slider->getTitle();
            $alias = $slider->getAlias();
            $isFromPosts = $slider->isSlidesFromPosts();
            $isFromStream = $slider->isSlidesFromStream();
            $strSource = __("Gallery",'revslider');
            $preicon = "revicon-picture-1";

            $is_favorite = $slider->isFavorite();

            $shortCode = $slider->getShortcode();
            $numSlides = $slider->getNumSlidesRaw();
            $numReal = '';

            $rowClass = "";
            $slider_type = 'gallery';
            if($isFromPosts == true){
                $strSource = __('Posts','revslider');
                $preicon ="revicon-doc";
                $rowClass = "class='row_alt'";
                $numReal = $slider->getNumRealSlides();
                $slider_type = 'posts';
                //check if we are woocommerce
                if($slider->getParam("source_type","gallery") == 'woocommerce'){
                    $strSource = __('WooCommerce','revslider');
                    $preicon ="revicon-doc";
                    $rowClass = "class='row_alt'";
                    $slider_type = 'woocommerce';
                }
            }elseif($isFromStream !== false){
                $strSource = __('Social','revslider');
                $preicon ="revicon-doc";
                $rowClass = "class='row_alt'";
                switch($isFromStream){
                    case 'facebook':
                        $strSource = __('Facebook','revslider');
                        $preicon ="eg-icon-facebook";
                        $numReal = $slider->getNumRealSlides(false, 'facebook');
                        $slider_type = 'facebook';
                    break;
                    case 'twitter':
                        $strSource = __('Twitter','revslider');
                        $preicon ="eg-icon-twitter";
                        $numReal = $slider->getNumRealSlides(false, 'twitter');
                        $slider_type = 'twitter';
                    break;
                    case 'instagram':
                        $strSource = __('Instagram','revslider');
                        $preicon ="eg-icon-info";
                        $numReal = $slider->getNumRealSlides(false, 'instagram');
                        $slider_type = 'instagram';
                    break;
                    case 'flickr':
                        $strSource = __('Flickr','revslider');
                        $preicon ="eg-icon-flickr";
                        $numReal = $slider->getNumRealSlides(false, 'flickr');
                        $slider_type = 'flickr';
                    break;
                    case 'youtube':
                        $strSource = __('YouTube','revslider');
                        $preicon ="eg-icon-youtube";
                        $numReal = $slider->getNumRealSlides(false, 'youtube');
                        $slider_type = 'youtube';
                    break;
                    case 'vimeo':
                        $strSource = __('Vimeo','revslider');
                        $preicon ="eg-icon-vimeo";
                        $numReal = $slider->getNumRealSlides(false, 'vimeo');
                        $slider_type = 'vimeo';
                    break;

                }

            }

            $first_slide_image_thumb = array('url' => '', 'class' => 'mini-transparent', 'style' => '');

            if(intval($numSlides) == 0){
                $first_slide_id = 'new&slider='.$id;
            }else{
                $slides = $slider->getFirstSlideIdFromGallery();
                if(!empty($slides)){
                    $first_slide_id = $slides[key($slides)]->getID();
                    $first_slide_image_thumb = $slides[key($slides)]->get_image_attributes($slider_type);
                }else{
                    $first_slide_id = 'new&slider='.$id;
                }
            }

            $editLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER,"id=$id");

            $editSlidesLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDE,"id=$first_slide_id");

            $showTitle = RevSliderFunctions::getHtmlLink($editLink, $showTitle);

        }catch(Exception $e){
            $errorMessage = "ERROR: ".$e->getMessage();
            $strSource = "";
            $numSlides = "";
            $isFromPosts = false;
        }

        $sliderActions = array();
        $sliderActions['embed'] = '<span class="button-primary rs-embed-slider" ><i class="eg-icon-plus"></i>'. __("Embed Slider",'revslider') .'</span>';

        if (!RS_DEMO) {
            $sliderActions['export'] = '<a class="button-primary  export_slider_overview" id="export_slider_'. $id .'" href="javascript:void(0);" ><i class="revicon-export"></i>'. __("Export",'revslider') .'</a>';

            $operations = new RevSliderOperations();
            $general_settings = $operations->getGeneralSettingsValues();
            $show_dev_export = RevSliderBase::getVar($general_settings, 'show_dev_export', 'on');
            if($show_dev_export == 'on'){
                $sliderActions['export_html'] = '<a class="button-primary  export_slider_standalone" id="export_slider_standalone_'. $id .'" href="javascript:void(0);" ><i class="revicon-export"></i>'. __("Export to HTML",'revslider') .'</a>';
            }
        }

        $sliderActions['delete'] = '<a class="button-primary  button_delete_slider" id="button_delete_'. $id .'" href="javascript:void(0)"><i class="revicon-trash"></i>'. __("Delete",'revslider') .'</a>';
        $sliderActions['duplicate'] = '<a class="button-primary  button_duplicate_slider" id="button_duplicate_'. $id .'" href="javascript:void(0)"><i class="revicon-picture"></i>'. __("Duplicate",'revslider') .'</a>';
        $sliderActions['preview'] = '<div id="button_preview_'. $id .'" class="button_slider_preview button-primary revgray"><i class="revicon-search-1"></i>'. __("Preview",'revslider') .'</div>';

        $sliderActions = apply_filters( 'rev_sliders_list_actions', $sliderActions, $id );

        $slidersHtml['sliders'][$id]['slider'] = $slider;
        $slidersHtml['sliders'][$id]['html'] = '
        <li class="tls-slide tls-stype-all tls-stype-'. $slider_type .'" data-favorit="'. ($is_favorite ? 'a' : 'b') .'" data-id="'. $id .'" data-name="'. $title .'" data-type="'. $slider_type .'">
            <div class="tls-main-metas">
                                                                                                                                        
                <span class="tls-firstslideimage '. $first_slide_image_thumb['class'] .'" style="'. $first_slide_image_thumb['style'] .';'. (!empty($first_slide_image_thumb['url']) ? 'background-image:url( '. $first_slide_image_thumb['url'] .')' : '') .'"></span>
                <a href="'. $editSlidesLink .'" class="tls-grad-bg tls-bg-top"></a>
                <span class="tls-source"><i class="'.$preicon.'"></i>'.$strSource.'</span>
                <span class="tls-star"><a href="javascript:void(0);" class="rev-toogle-fav" id="reg-toggle-id-'. $id .'"><i class="eg-icon-star'. ($is_favorite ? '' : '-empty') .'"></i></a></span>
                <span class="tls-slidenr">'. $numSlides . ($numReal !== '' ? ' ('.$numReal.')' : '') .'</span>

                <span class="tls-title-wrapper">
                    <span class="tls-id">#'. $id .'<span id="slider_title_'. $id .'" class="hidden">'. $title .'</span><span class="tls-alias hidden" >'. $alias .'</span></span>
                    <span class="tls-title">'. $showTitle . (!empty($errorMessage) ? '<span class="error_message">'. $errorMessage .'</span>' : '') .'
                    </span>
                    <a class="button-primary tls-settings" href="'. $editLink .'"><i class="revicon-cog"></i></a>
                    <a class="button-primary tls-editslides" href="'. $editSlidesLink .'"><i class="revicon-pencil-1"></i></a>
                    <span class="button-primary tls-showmore"><i class="eg-icon-down-open"></i></span>

                </span>
            </div>';

        $slidersHtml['sliders'][$id]['html'] .= apply_filters('rev_sliders_list_additional_html', '', $slider);

        $slidersHtml['sliders'][$id]['html'] .= '<div class="tls-hover-metas">';
        foreach ( $sliderActions as $action ) {
            $slidersHtml['sliders'][$id]['html'] .= $action;
        }
        $slidersHtml['sliders'][$id]['html'] .= '</div>
            <div class="tls-dimmme"></div>
        </li>';
    }
}

$slidersHtml['buttons']['addnewslider'] = '
    <li class="tls-slide tls-addnewslider">
		<a href="'.$addNewLink.'">
			<span class="tls-main-metas">
				<span class="tls-new-icon-wrapper">
					<span class="slider_list_add_buttons add_new_slider_icon"></span>
				</span>
				<span class="tls-title-wrapper">			
					<span class="tls-title">'. __("New Slider", "revslider") . '</span>					
				</span>
			</span>
		</a>
	</li>';

$slidersHtml['buttons']['addnewslidertemplate'] = '
    <li class="tls-slide tls-addnewslider">
		<a href="javascript:void(0);" id="' . (get_option('revslider-valid', 'false') == 'true' ? 'button_import_template_slider' : 'regsiter-to-access-store-none') . '">
			<span class="tls-main-metas">
				<span class="tls-new-icon-wrapper add_new_template_icon_wrapper">
					<i class="slider_list_add_buttons add_new_template_icon"></i>
				</span>
				<span class="tls-title-wrapper">			
					<span class="tls-title">'. __("Add Slider From Template", "revslider") .'</span>					
				</span>
			</span>
		</a>
	</li>';

if(!RevSliderFunctionsWP::isAdminUser() && apply_filters('revslider_restrict_role', true)){ }else{
    $slidersHtml['buttons']['importslider'] = '
    <li class="tls-slide tls-addnewslider">
        <a href="javascript:void(0);" id="button_import_slider">
				<span class="tls-main-metas">
					<span class="tls-new-icon-wrapper">
						<i class="slider_list_add_buttons  add_new_import_icon"></i>
					</span>
					<span class="tls-title-wrapper">			
						<span class="tls-title">'. __("Import Slider", "revslider") .'</span>					
					</span>
				</span>
        </a>
    </li>';
}

// update sliders / buttons array
$slidersHtml = apply_filters('rev_sliders_list_update_sliders_buttons', $slidersHtml);

//prepare original output
$slidersHtmlOutput = '<ul class="tp-list_sliders">';
foreach ( $slidersHtml['sliders'] as $slider ) {
    $slidersHtmlOutput .= $slider['html'];
}
foreach ( $slidersHtml['buttons'] as $button ) {
    $slidersHtmlOutput .= $button;
}
$slidersHtmlOutput .= '</ul>';
//here you can overwrite sliders list
echo apply_filters('rev_sliders_list_before_output', $slidersHtmlOutput, $slidersHtml);

?>
<script>
  jQuery(document).ready(function() {
  	 jQuery('.tls-showmore').click(function() {
  	 	jQuery(this).closest('.tls-slide').find('.tls-hover-metas').show();
  	 	var elements = jQuery('.tls-slide:not(.hovered) .tls-dimmme');
  	 	punchgs.TweenLite.to(elements,0.5,{autoAlpha:0.6,overwrite:"all",ease:punchgs.Power3.easeInOut});
  	 	punchgs.TweenLite.to(jQuery(this).find('.tls-dimmme'),0.3,{autoAlpha:0,overwrite:"all",ease:punchgs.Power3.easeInOut})
  	 });

  	 jQuery('.tls-slide').hover(function() {
  	 	jQuery(this).addClass("hovered");
  	 }, function() {
  	 	var elements = jQuery('.tls-slide .tls-dimmme');
  	 	punchgs.TweenLite.to(elements,0.5,{autoAlpha:0,overwrite:"auto",ease:punchgs.Power3.easeInOut});
  	 	jQuery(this).removeClass("hovered");
  	 	jQuery(this).find('.tls-hover-metas').hide();
  	 });
  });

  jQuery('#filter-sliders').on("change",function() {
  	jQuery('.tls-slide').hide();
  	jQuery('.tls-stype-'+jQuery(this).val()).show();
  	jQuery('.tls-addnewslider').show();
  });

  function sort_li(a, b){
	    return (jQuery(b).data(jQuery('#sort-sliders').val())) < (jQuery(a).data(jQuery('#sort-sliders').val())) ? 1 : -1;    
	}

  jQuery('#sort-sliders').on('change',function() {
  	jQuery(".tp-list_sliders li").sort(sort_li).appendTo('.tp-list_sliders');
  	jQuery('.tls-addnewslider').appendTo('.tp-list_sliders');
  });

  jQuery('.slider-lg-views').click(function() {
	var tls =jQuery('.tp-list_sliders'),
		t = jQuery(this);
	jQuery('.slider-lg-views').removeClass("active");
	jQuery(this).addClass("active");
	tls.removeClass("rs-listview");
	tls.removeClass("rs-gridview");
	tls.addClass(t.data('type'));
  });

</script>