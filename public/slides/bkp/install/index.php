<?php

if (version_compare(phpversion(), '5.3.0', '<') === true) {
    echo
		'<div style="background-color:#fff;margin:10px;font:13px/20px normal Helvetica, Arial, sans-serif;color:#4F5155;border:1px solid #990000;">
		<h1 style="color:#fff;background-color:#e74c3c;border-bottom:1px solid #D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px;">Oops, it looks like you have an invalid PHP version.</h1>
		<p style="margin:12px 15px 12px 15px;">Revolution Slider Editor supports PHP 5.3.0 or newer.</p>
		</div>';
    exit;
}

umask(0);
$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']); 
$base_url = str_replace('/install/', '/', $base_url);

// CI base data
$system_folder = "../system";
$application_folder = '../application';

if (strpos($system_folder, '/') === FALSE)
{
	if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
		$system_folder = realpath(dirname(__FILE__)).'/'.$system_folder;
}
else
{
	$system_folder = str_replace("\\", "/", $system_folder); 
}

// CI constants
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('BASEPATH', $system_folder.'/');
define('ROOTPATH', str_replace("\\", "/", realpath(dirname($system_folder))) . '/');
define('FCPATH', '');
define('BASEURL', $base_url);
if ( ! file_exists($application_folder.'/config/config.php') || ! file_exists($application_folder.'/config/database.php'))
{
  define('ENVIRONMENT', 'install');
}
else
{
  define('ENVIRONMENT', 'production');
}
                                      
if (is_dir($application_folder))
{
	define('APPPATH', $application_folder.'/');
}
else
{
	if ($application_folder == '')
		$application_folder = 'application';

	define('APPPATH', BASEPATH.$application_folder.'/');
}

// CI classes include
require($system_folder . '/core/Utf8.php');
require($system_folder . '/core/Config.php');
require($system_folder . '/core/Loader.php');
require($system_folder . '/core/Security.php');
require($system_folder . '/core/Input.php');
require($system_folder . '/database/DB.php');
require($system_folder . '/core/Common.php');
require($application_folder . '/config/constants.php');
require($application_folder . '/config/revslider.php');

// RS classes

require_once(ROOTPATH . 'revslider/includes/globals.class.php');

// Translation classes
include_once APPPATH . "libraries/Gettext/autoloader.php";
use Gettext\Translator;

// Installer class
if (file_exists('./class/Installer.php'))
{
	require './class/Installer.php';

	$installer = new Installer();
	
    // Libraries
    include(APPPATH.'libraries/SaltCellar.php');
    include(APPPATH.'libraries/PasswordStorage.php');

	// Helpers
	require(BASEPATH.'helpers/url_helper.php');
	require(BASEPATH.'helpers/form_helper.php');
	require(BASEPATH.'helpers/file_helper.php');
	require(BASEPATH.'helpers/email_helper.php');
	require(APPPATH.'helpers/language_helper.php');
	require(APPPATH.'helpers/trace_helper.php');

	// Set translation
	$language = 'en_US';
	$translations = Gettext\Translations::fromPoFile('../revslider/languages/revslider-' . $language . '.po');
	$installer->translator = new Translator();
	$installer->translator->loadTranslations($translations);
  
	$CFG = new CI_Config();
	$SEC = new CI_Security();
	$UNI = new CI_Utf8();

	$CFG->set_item('index_page', '');
	$CFG->set_item('base_url', $base_url . 'install');
	$CFG->set_item('enable_query_strings', TRUE);

	$installer->uni = $UNI;
	$installer->load = new CI_Loader();
	$installer->config = $CFG;
	$installer->security = $SEC;
	$installer->input = new CI_Input();

	// Installer Step
	$step = 'checkconfig';
	
	if (is_array($_GET) && isset($_GET['step']))
		$step = ($_GET['step']) ? $_GET['step'] : 'checkconfig' ;

	// Check if is installed
	if ($installer->is_installed() && $step != 'finish')
	{
		header("Location: ".BASEURL.'index.php', TRUE, 302);
	}
	
	// Actions
	switch($step)
	{
		case 'checkconfig' :
			$installer->check_config();
			break;
		
		case 'database' :
			$installer->configure_database();
			break;
			 
		case 'user' :
			$installer->configure_user();
			break;
			 
		case 'finish' :
			$installer->finish();
			break;
	}
}
