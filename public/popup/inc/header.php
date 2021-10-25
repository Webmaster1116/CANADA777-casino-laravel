<?php
if (!defined('UAP_CORE')) die('What are you doing here?');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title><?php echo esc_html($page['page-title']); ?></title>

	<link href="css/fontawesome-all.min.css" rel="stylesheet">
	<link href="css/jquery-ui/jquery-ui.min.css" rel="stylesheet">
	<link href="css/color-picker.min.css" rel="stylesheet">
	<link href="css/admin.css" rel="stylesheet">
<?php
	$output = array();
	do {
		$printed = false;
		foreach($styles as $slug => $style) {
			if (!in_array($slug, $output)) {
				$diff = array_diff($style['deps'], $output);
				if (empty($diff)) {
					$output[] = $slug;
					echo '
	<link id="'.$slug.'" href="'.$style['url'].'" rel="stylesheet">';
					$printed = true;
				}
			}
		}
	} while ($printed)
?>	
	<script>var wpColorPickerL10n = {"clear":"<?php echo esc_html__('Clear', 'hap'); ?>","defaultString":"<?php echo esc_html__('Default', 'hap'); ?>","pick":"<?php echo esc_html__('Select Color', 'hap'); ?>","current":"<?php echo esc_html__('Current Color', 'hap'); ?>"};</script>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/iris.min.js"></script>
	<script src="js/color-picker.min.js"></script>
	<script src="js/admin.js"></script>
<?php
	$output = array('jquery');
	do {
		$printed = false;
		foreach($scripts as $slug => $script) {
			if (!in_array($slug, $output)) {
				$diff = array_diff($script['deps'], $output);
				if (empty($diff)) {
					$output[] = $slug;
					echo '
	<script id="'.esc_html($slug).'" src="'.esc_html($script['url']).'"></script>';
					$printed = true;
				}
			}
		}
	} while ($printed)
?>	
	<script>var ajax_handler = "<?php echo esc_html(admin_url('ajax.php')); ?>";</script>
<?php
	do_action('admin_head');
?>
</head>
<body id="uap-body">
	<div class="hap-container">
		<script>jQuery('.hap-container').css('min-height', jQuery(window).height());</script>
		<div class="hap-sidebar">
			<h1><i class="fas fa-cogs"></i> <span><?php echo esc_html(get_bloginfo('name')); ?></span></h1>
			<div class="hap-sidebar-menu">
				<ul>
<?php
	if (!defined('HALFDATA_DEMO') || HALFDATA_DEMO !== true) {
?>
					<li<?php echo ($page['slug'] == 'dashboard' ? ' class="active"' : ''); ?>><a href="<?php echo esc_html($options['url']); ?>"><i class="fas fa-home"></i> <?php echo esc_html__('Dashboard', 'hap'); ?></a></li>
<?php
	}
	foreach($menu as $slug => $item) {
		$icon = 'fas fa-cog';
		echo '
					<li'.(array_key_exists('parent', $page) && $page['parent'] == $slug ? ' class="active"' : '').'><a'.(array_key_exists('submenu', $item) ? '' : ' href="'.esc_html($options['url']).'?page='.rawurlencode($slug).'"').'><i class="'.esc_html($icon).'"></i> '.esc_html($item['menu-title']).(array_key_exists('submenu', $item) ? '<span class="fas fa-chevron-down"></span>' : '').'</a>';
		if (array_key_exists('submenu', $item)) {
			echo '
						<ul>';
			foreach ($item['submenu'] as $submenu_slug => $submenu_item) {
				echo '
							<li'.($page['slug'] == $submenu_slug ? ' class="current"' : '').'><a href="'.esc_html($options['url']).'?page='.esc_html($submenu_slug).'">'.esc_html($submenu_item['menu-title']).'</a></li>';
			}
			echo '
						</ul>';
		}
		echo '</li>';
	}
?>								
				</ul>
			</div>
		</div>
		<div class="hap-content">
			<div class="hap-topbar">
				<ul>
					<li>
						<a href="#" onclick="return false;">
							<?php echo (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true ? esc_html__('Demo User', 'hap') : esc_html($options['login'])); ?>
							<span class="fas fa-angle-down"></span>
						</a>
						<ul>
<?php
	if (!defined('HALFDATA_DEMO') || HALFDATA_DEMO !== true) {
?>
							<li><a href="<?php echo esc_html($options['url']).'settings.php'; ?>"><?php echo esc_html__('Settings', 'hap'); ?></a></li>
<?php
	}
?>
							<li><a href="<?php echo esc_html(admin_url('login.php')); ?>?logout=true"><i class="fas fa-sign-out-alt pull-right"></i> <?php echo esc_html__('Log Out', 'hap'); ?></a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="hap-content-area">
				<div id="global-message-container">
					<?php echo isset($global_message) ? $global_message : ''; // Do not need to escape, global_message is html-code with already escaped text. ?>
					<?php do_action('admin_notices'); ?>
				</div>
				<div class="hap-content-box">
