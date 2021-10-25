
/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

var Gallery = new function(){

	var t = this;

	var isMultipleUpload = null;
	var onInsertCallback = null;
	var galleryFileType = null;

	t.open = function(title, onInsert, isMultiple, fileType) {

		onInsertCallback = onInsert;
		isMultipleUpload = isMultiple == undefined ? false : isMultiple;
		galleryFileType = fileType == undefined ? 'image' : fileType;

		jQuery('<div id="dialog_gallery"/>').load(g_urlMediaGallery.replace('[type]', galleryFileType), function() {
			initGallery();
			initUploader();
            t.updateGalleryHeight();
		}).dialog({
			title: title,
            width:jQuery(window).width(),
            height:jQuery(window).height(),
            modal: true,
            dialogClass: 'tpdialogs fullscreen-dialog-window gallery_dialog',
			create:function(event) {
                var title = jQuery(event.target).parent().find('.ui-dialog-titlebar');
                title.addClass("tp-slider-new-dialog-title");
                title.prepend('<span class="revlogo-mini" style="margin-right:15px;"></span>');
			},
            hide:{effect:"",delay:250},
			open: function(event) {
                var holder = jQuery(event.target).parent();
                setTimeout(function() {
                    holder.addClass("show");
                },200);
                jQuery('#dialog_gallery').closest('.ui-dialog').addClass("visible-fullscreen-dialog");
                t.updateGalleryHeight();
			},
            beforeClose:function(event) {
                jQuery(event.target).parent().removeClass("show");
            },
            close:function() {
                jQuery('#fine-uploader').remove();
                jQuery('.color-box').colorbox().remove();
                var ui = jQuery('#dialog_addobj').closest('.ui-dialog');
                ui.removeClass("visible-fullscreen-dialog");
                jQuery(this).empty().remove();
            }
		});
	};

	var initGallery = function() {

		// insert action

        jQuery('.insert-button').on('click', function() {
            var $photo = jQuery(this).parents('.photo-box');
            if (isMultipleUpload) {
                var arrImages = [{
                    url:$photo.data('url'),
                    id:$photo.data('id'),
                    width:$photo.data('width'),
                    height:$photo.data('height')
                }];
                onInsertCallback(arrImages);
            } else {
                onInsertCallback($photo.data('url'), $photo.data('id'), $photo.data('width'), $photo.data('height'));
            }
            jQuery('#dialog_gallery').dialog('close');
            return false;
        });

		// delete action

		jQuery('.delete-anchor').click(function(){
			if(confirm('Are you sure want to delete this image?'))
			{
				jQuery.ajax({
					url:jQuery(this).attr('href'),
					dataType: 'json',
					beforeSend: function()
					{
						jQuery('.file-upload-messages-container:first').show();
						jQuery('.file-upload-message').html("Deleting image...");
					},
					success: function(response) {
						if (response.success)
						{
							loadPhotoGallery();
						}
						else
						{
							jQuery('.qq-upload-list').append('<li class="qq-upload-fail" title="' + response.responseProperty + '"><span class="qq-upload-status-text">' + response.responseProperty + '</span></li>');
						}
					}
				});
			}
			return false;
		});

		// preview action

		jQuery('.color-box').colorbox({
			rel: 'color-box'
		});

        // TAKE CARE ABOUT SCROLL OF THE LIBRARY CONTAINER
        jQuery('#ajax-list').perfectScrollbar({wheelPropagation:false,suppressScrollX:true});
        t.updateGalleryHeight();
	};

	var initUploader = function() {

		var uploader = new qq.FineUploader({
			element: document.getElementById('fine-uploader'),
			request: {
				 endpoint: g_urlMediaUpload.replace('[type]', galleryFileType)
			},
			validation: {
				 allowedExtensions: galleryFileType == 'image'
									? ['jpeg', 'jpg', 'png', 'gif']
									: ['mp4', 'mp3' , 'webm', 'ogv']
			},
			callbacks: {
				 onComplete: function(id, fileName, responseJSON) {
					if (responseJSON.success)
					{
						loadPhotoGallery();
					}
				 }
			},
			failedUploadTextDisplay: {
				mode: 'custom',
				responseProperty: 'error'
			},
			debug: false
		});

	};

	var loadPhotoGallery = function() {

		jQuery.ajax({
			url: g_urlMediaAjax.replace('[type]', galleryFileType),
			cache: false,
			dataType: 'text',
			beforeSend: function() {
				jQuery('.file-upload-messages-container:first').show();
				jQuery('.file-upload-message').html("Loading images...");
			},
			complete: function() {
				jQuery('.file-upload-messages-container').hide();
				jQuery('.file-upload-message').html('');
			},
			success: function(data){
				jQuery('#gallery_content').html(data);
				initGallery();
				initUploader();
                t.updateGalleryHeight();
            }
		});

	};

	t.updateGalleryHeight = function() {
        var $dialog = jQuery('#dialog_gallery'),
            $results = jQuery('#ajax-list');
        if ($dialog.is(':visible')) {
            var windowHeight = jQuery(window).height(),
                titleHeight = $dialog.parent().find('.tp-slider-new-dialog-title').height(),
                uploaderHeight = jQuery('#fine-uploader', $dialog).height();
            $dialog.height(windowHeight - titleHeight);
            $results.height(windowHeight - titleHeight - uploaderHeight - 40);
        }
        $results.perfectScrollbar("update");
	}

};

jQuery(window).resize(function(){
    Gallery.updateGalleryHeight();
});
