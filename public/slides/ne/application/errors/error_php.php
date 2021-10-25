<style type="text/css">
.rs_php_error {
	background-color: #fff;
	margin: 10px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
	border:1px solid #990000;
}
.rs_php_error h1 {
    color: #fff;
    background-color: #e74c3c;
    border-bottom: 1px solid #D0D0D0;
    font-size: 19px;
    font-weight: normal;
    margin: 0 0 14px 0;
    padding: 14px 15px 10px 15px;
}
.rs_php_error p,
.rs_php_error pre {
	margin: 12px 15px 12px 15px;
}
.rs_php_error pre {
    white-space: pre-line;
}
.rs_php_error h2 {
    font-size: 19px;
    font-weight: normal;
	margin: 14px 0;
    padding: 12px 15px 12px 15px;
}
.rs_php_error h3 {
    font-size: 16px;
    font-weight: normal;
	margin: 12px 0;
}
.rs_php_error ul {
	margin-bottom: 30px;
}
.rs_php_error li {
	margin-bottom: 20px;
}
</style>
<div class="rs_php_error">
	<h1>A PHP Error was encountered</h1>
	<p>Severity: <?php echo $severity; ?></p>
	<p>Message:  <?php echo $message; ?></p>
	<p>Filename: <?php echo $filepath; ?></p>
	<p>Line Number: <?php echo $line; ?></p>
    <?php
    if (class_exists('RevSliderOperations') && RevSliderOperations::getGeneralSettingsOptionValue('enable_error_backtrace', 'off') != 'off') {
        echo '<pre>';
        @debug_print_backtrace();
        echo '</pre>';
    }
    ?>
	<h2>How to fix it?</h2>
	<ul>
		<li>
			<h3>Check PHP version</h3>
			<p>Check if PHP version of your server is up to date. PHP version 5.3 or greater is required. You can check version using "<strong>phpversion()</strong>" PHP function on webserver or by executing "<strong>php --version</strong>" via SSH.</p>
		</li>
		<li>
			<h3>Check if Safe Mode disabled in PHP</h3>
			<p>
				Safe mode needs to be disabled. You can check if it is so by viewing output of "<strong>phpinfo()</strong>" PHP command on your webserver.
			</p>
		</li>
		<li>
			<h3>Check code integrity</h3>
			<p>
				Make sure all code files are uploaded to your webserver and was not modified. If some files are missing or corrupted reupload of code files can fix it.
			</p>
		</li>
	</ul>
</div>