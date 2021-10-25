<div class="title">
	<h1><?php echo __('Welcome to Slider Revolution jQuery Visual Editor AddOn') ?></h1>
</div>
<div class="nav">
	<ul>
		<li><a class="active system" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo __('System Check') ?></a></li>
		<li><span class="inactive database"><?php echo __('Database') ?></span></li>
		<li><span class="inactive admin"><?php echo __('Admin User') ?></span></a></li>
	</ul>
</div>

<div class="block">
	<div class="icon system"><i></i></div>
	<div class="content">

		<p><?php echo __("Here are the results of the basic requirements check. If one requirement isn't OK, please correct it and refresh this page once it is corrected.") ?></p>

		<h2><?php echo __('System configuration check') ?></h2>

		<?php if(isset($message)) :?>
			<p class="<?php echo $message_type ?>"><?php echo $message ?></p>
		<?php endif ;?>

		<!-- PHP Version -->
		<ul class="check">
			<li class="<?php if($php_version) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('PHP >= 5.3')?> (<b><?php echo phpversion() ?></b>)</li>
			<li class="<?php if($openssl) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('OpenSSL support')?> </li>
			<li class="<?php if($mysql_support) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('MySQL support')?> </li>
			<li class="<?php if($safe_mode) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('Safe Mode Off')?> </li>
			<li class="<?php if($file_uploads) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('File upload')?></li>
			<li class="<?php if($gd_lib) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('PHP GD Lib')?></li>
			<li class="<?php if($curl_lib) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo __('PHP cURL Lib')?></li>
		</ul>

		<h2><?php echo __('These folders needs to be writable') ?></h2>

		<ul class="check">
			<?php foreach($check_folders as $folder => $result) :?>
				<li class="<?php if($result) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo ROOTPATH . $folder ?></li>
			<?php endforeach ;?>
		</ul>


		<?php if ($check_files) : ?>
			<h2><?php echo __('These files needs to be writable') ?></h2>

			<ul class="check">
				<?php foreach($check_files as $file => $result) :?>
					<li class="<?php if($result) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo $file ?></li>
				<?php endforeach ;?>
			</ul>
		<?php endif; ?>

		<div class="buttons">
			<?php if ($next) :?>
				<a href="?step=database&lang=<?php echo $lang ?>" class="button yes right"><?php echo __('Next step') ?></a>
			<?php endif ;?>
		</div>

	</div>
</div>