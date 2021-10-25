<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="//www.w3.org/1999/xhtml" class="ie8 wp-toolbar"  lang="en-US">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="//www.w3.org/1999/xhtml" class="wp-toolbar"  lang="en-US">
<!--<![endif]-->
<head>
	<title><?php echo __('Revolution Slider'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php foreach ($cssIncludes as $_css) : ?>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo force_ssl($_css) .'?v='. RevSliderGlobals::SLIDER_REVISION; ?>" />
	<?php endforeach; ?>
	<?php foreach ($jsIncludes as $_js) : ?>
		<script type="text/javascript" src="<?php echo force_ssl($_js) .'?v='. RevSliderGlobals::SLIDER_REVISION; ?>"></script>
	<?php endforeach; ?>
	<script type='text/javascript'>
        /* <![CDATA[ */
        var $ = jQuery;
    	var g_revNonce = "<?php echo wp_create_nonce(); ?>";
    	var g_uniteDirPlugin = "revslider";
		var wpColorPickerL10n = {"clear":"Clear","defaultString":"Default","pick":"Select Color","current":"Current Color"};
        var g_urlEditAccount = '<?php echo site_url('c=admin&m=edit_account'); ?>';
        var g_urlMediaGallery = '<?php echo site_url('c=media&t=[type]'); ?>';
        var g_urlMediaUpload = '<?php echo site_url('c=media&m=index&f=upload_file&t=[type]'); ?>';
        var g_urlMediaAjax = '<?php echo site_url('c=media&m=index&f=ajax_list&t=[type]'); ?>';
        <?php if ($localizeScripts) : ?>
			<?php foreach ($localizeScripts as $localizeScript) :  ?>
				var <?php echo $localizeScript['var']; ?> = <?php echo json_encode($localizeScript['lang']); ?>;
			<?php endforeach; ?>
        <?php endif; ?>
		/* ]]> */
	</script>
	<?php if ($inlineStyles) : ?>
		<?php foreach ($inlineStyles as $_style) : echo $_style; endforeach; ?>
	<?php endif; ?>
	<?php echo $adminHead; ?>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url(); ?>assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url(); ?>assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo base_url(); ?>assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>
<body class="wp-admin wp-core-ui <?php echo htmlspecialchars(apply_filters( 'visual_editor_body_classes_filter', '')); ?>">