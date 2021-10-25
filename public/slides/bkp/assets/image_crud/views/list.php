<div id="fine-uploader"></div>
<div class="clear"></div>
<div id='ajax-list'>
	<div class='photos-crud'>
	    <?php foreach($photos as $photo_num => $photo) : ?>
			<div id="photos_<?php echo $photo->$primary_key; ?>">
                <div class='photo-box'
                    data-id="<?php echo $photo->$primary_key; ?>"
                    data-url="<?php echo $photo->image_url?>"
                    data-width="<?php echo isset($photo->width) ? $photo->width : ''; ?>"
                    data-height="<?php echo isset($photo->height) ? $photo->height : ''; ?>" >
					<img src='<?php echo $photo->thumbnail_url?>' title='<?php echo $photo->file_name; ?>' width='270' height='220' class='basic-image' />
                    <?php if ($photo->type != 'image') : ?>
                        <div class="photo-file-name"><?php echo $photo->file_name; ?></div>
                    <?php endif; ?>
                    <div class="gallery-actions">
                        <a href="#" class="insert-button"><i class="eg-icon-plus-circled"></i><?php _e('Insert'); ?></a>
                        <?php if ($photo->type == 'image') : ?>
                            <a href="<?php echo $photo->image_url?>" class="color-box" rel="color-box"><i class="revicon-search-1"></i><?php _e('View'); ?></a>
                        <?php endif; ?>
                        <?php if(!$unset_delete){?>
                            <a href="<?php echo $photo->delete_url?>" class="delete-anchor"><i class="revicon-trash"></i><?php _e('Delete'); ?></a>
                        <?php }?>
                    </div>
				</div>
			</div>
	    <?php endforeach; ?>
        <div class="clear"></div>
    </div>
</div>