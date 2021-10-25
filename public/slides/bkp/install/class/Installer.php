<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.90
 */

// ------------------------------------------------------------------------

/**
 * Ionize Installer
 *
 * @package		Ionize
 * @subpackage	Installer
 * @category	Installer
 * @author		Ionize Dev Team
 *
 */

class Installer {

	private static $instance;
	private $template;
    private $_dbError;

	public $lang = array();
	// Default language
	public $lang_code = 'en';
	public $db;
	public $config = array();
	public $translator;
    public $load;


	// --------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Check GET language
		if (is_array($_GET) && isset($_GET['lang']) )
		{
			$this->lang_code = $_GET['lang'];
		}

		$this->template['lang'] = $this->lang_code;
		$languages = array('English');
		$this->template['languages'] = $languages;

		// Put the current URL to template (for language selection)
		$this->template['current_url'] = (isset($_GET['step'])) ? '?step='.$_GET['step'] : '?step=checkconfig';
	}


	// --------------------------------------------------------------------


	/**
	 * Returns current instance of Installer
	 *
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}


	// --------------------------------------------------------------------


	/**
	 * Checks the config settings
	 *
	 */
	function check_config() {

		$check = TRUE;

		// PHP version >= 5
		$this->template['php_version'] = version_compare(substr(phpversion(), 0, 3), '5.3', '>=');

		// MySQL support
		$this->template['mysql_support']  = function_exists('mysql_connect') || function_exists('mysqli_connect');

		// Safe Mode
		$this->template['safe_mode']  = (ini_get('safe_mode')) ? FALSE : TRUE;

		// Files upload
		$this->template['file_uploads'] = (ini_get('file_uploads')) ? TRUE : FALSE;

		// openssl Extension
        $this->template['openssl'] = extension_loaded('openssl');

        // GD lib
        $this->template['gd_lib'] = function_exists('imagecreatetruecolor');

		// cURL lib
		$this->template['curl_lib'] = function_exists('curl_version');

		// Message to user if one setting is false
		foreach($this->template as $config) {
			if ( ! $config) {
				$check = FALSE;
			}
		}

		// Check files rights
		$files = array(
			'application/config/config.php',
			'application/config/database.php',
		);

		$check_files = array();
		foreach($files as $key => $file) {
			if (file_exists(ROOTPATH . $file)) {
				$_check = is_really_writable(ROOTPATH . $file);
				$check_files[$file] = $_check;
				if ( ! $_check) {
					$check = FALSE;
				}
			}
		}
		$this->template['check_files'] = $check_files;

		// Check folders rights
		$folders = array(
            '',
			'application/config',
			'application/cache',
			'application/logs',
			'media',
			'media/thumb',
            'pages',
            'plugins',
			'revslider/public',
		);

		$check_folders = array();
		foreach($folders as $folder) {
			$_check = $this->_test_dir(ROOTPATH . $folder);
			$check_folders[$folder] = $_check;
			if ( ! $_check) {
				$check = FALSE;
			}
		}
		$this->template['check_folders'] = $check_folders;

		// Message to user if one setting is false
		if ( ! $check) {
			$this->template['next'] = false;
			$this->_send_error('check_config', __('Some base requirement are not OK.<br/>Please correct them to continue the installation.'));
		}

		// Outputs the view
		$this->output('check_config');
	}


	// --------------------------------------------------------------------


	/**
	 * Prints out the database form
	 *
	 */
	function configure_database()
	{
		if ( ! isset($_POST['action']))
		{
			$data = array('db_driver', 'db_hostname', 'db_name', 'db_username', 'db_prefix');

			$this->_feed_blank_template($data);

			$this->output('database');
		}
		else
		{
			$this->_save_database_settings();
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Prints out the user form
	 *
	 */
	function configure_user()
	{
		// Check if an Admin user already exists in the DB
		$this->template['skip'] = FALSE;

		$this->db_connect();

		$query = $this->db->get($this->db->dbprefix . 'user');

		if ($query->num_rows() > 0)
		{
			$this->template['skip'] = TRUE;
		}


		if ( ! isset($_POST['action'])) {

			// Skip TRUE and no POST = Admin user already exists
			if ($this->template['skip'] == TRUE) {
				$this->template['message_type'] = 'alert';
				$this->template['message'] = __('An administrator user already exists in the database.<br/>You can skip this step if you wish not to create or update an Admin account.');
			}

			// Prepare data
			$data = array('username', 'firstname', 'lastname', 'email');

			$this->_feed_blank_template($data);

			// Encryption key : check if one exists
			require(ROOTPATH . 'application/config/config.php');
			if ($config['encryption_key'] == '') {
				$this->template['encryption_key'] = $this->generateEncryptKey(128);
			}

			$this->output('user');

		} else {

			$this->_save_user();
			$this->db_connect();

			header("Location: ".BASEURL.'install/?step=finish&lang='.$this->template['lang'], TRUE, 302);
		}
	}





	// --------------------------------------------------------------------


	/**
	 * Saves the website default settings
	 * - Default lang
	 *
	 *
	 */
	function settings()
	{
		if ( ! isset($_POST['action']))
		{
			$this->template['lang_code'] = 'en';
			$this->template['lang_name'] = 'english';
			$this->template['admin_url'] = 'admin';

			$this->output('settings');
		}
		else
		{
			$ret = $this->_save_settings();

			if ($ret)
			{
				header("Location: ".BASEURL.'install/?step=user&lang='.$this->template['lang'], TRUE, 302);
			}
			else
			{
				$this->_send_error('settings', __('settings_error_write_rights'), $_POST);
			}
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Finish installation
	 *
	 */
	function finish()
	{
		$this->db_connect();
		$this->template['base_url'] = BASEURL;
		$this->output('finish');
	}

	// --------------------------------------------------------------------


	/**
	 * Saves database settings
	 *
	 */
	function _save_database_settings()
	{
		$fields = array('db_driver', 'db_hostname', 'db_name', 'db_username', 'db_password', 'db_prefix');

		// Post data
		$data = array();

		// Check each mandatory POST data
		foreach ($fields as $key)
		{
			if (isset($_POST[$key]))
			{
				$val = $this->input->post($key);

				// Break if $val == ''
				if ($val == '' && $key != 'db_prefix' && $key != 'db_password')
				{
					$this->_send_error('database', __('Some information is missing.<br/>Please fill all fields !'), $_POST);
				}

				if ( ! get_magic_quotes_gpc())
					$val = addslashes($val);

				$data[$key] = trim($val);
			}
		}

		// Try create and save config/config.file
		$config = array(
			'encryption_key' => $this->generateEncryptKey(128),
			'base_url' => BASEURL
		);
		if ( ! $this->_save_config_settings_to_file($config) ) {
			$this->_send_error('database', __('<b>Error :</b><br/>No write rights on <b>/application/config/config.php</b>. Please check the PHP rights on this file.'), $_POST);
		}

		// Try connect or exit
		if ( ! $this->_db_connect($data)) {
			$this->_send_error(
                'database',
                __("<b>Error:</b><br/>Connection to the database fails with the provided settings.<br/>") . $this->_dbError,
                $_POST
            );
		}

		// If database doesn't exists, create it !
		if ( ! $this->db->db_select())
		{
			// Loads CI DB Forge class
			require_once(BASEPATH.'database/DB_forge'.EXT);
			require_once(BASEPATH.'database/drivers/'.$this->db->dbdriver.'/'.$this->db->dbdriver.'_forge'.EXT);

			$class = 'CI_DB_'.$this->db->dbdriver.'_forge';

			$this->dbforge = new $class();

			if ( ! $this->dbforge->create_database($data['db_name']))
			{
				$this->_send_error('database', __('The installer cannot create the database. Check your database name or your rights'), $_POST);
			}
			else
			{
				// Put information about database creation to view
				$this->template['database_created'] = __('<b class="ex">The database was successfully created.</b>');
				$this->template['database_name'] = $data['db_name'];
			}
		}


		// Select database, save database config file and launch SQL table creation script
		// The database should exists, so try to connect
		if ( ! $this->db->db_select())
		{
			$this->_send_error('database', __("The database doesn't exist !"), $_POST);
		}
		else
		{
			// Everything's OK, save config/database.php
			if ( ! $this->_save_database_settings_to_file($data))
			{
				$this->_send_error('database', __('<b>Error :</b><br/>The file <b style=\"color:#000;\">/application/config/database.php</b> could not be written!<br/>Check your permissions.'), $_POST);
			}

			// Load database XML script
			$xml = simplexml_load_file('./database/database.xml');

			// Get tables & content
			$tables = $xml->xpath('/sql/tables/query');
			$content = $xml->xpath('/sql/content/query');

			// Create tables
			foreach ($tables as $table)
			{
				$this->db->query( str_replace('[DB_PREFIX]', $data['db_prefix'], $table) );
			}

			// Basis content insert
			foreach ($content as $sql)
			{
				$this->db->query( str_replace('[DB_PREFIX]', $data['db_prefix'], $sql) );
			}

			// Users message
			$this->template['database_installation_message'] = __('<b class="ex">The database was successfully installed.</b>');
		}

		header("Location: ".BASEURL.'install/?step=user&lang='.$this->template['lang'], TRUE, 302);
	}


	// --------------------------------------------------------------------


	/**
	 * Saves the user informations
	 *
	 */
	function _save_user()
	{
		// Load config
		include(APPPATH.'config/config.php');

		// Saves the users data
		$fields = array('username', 'firstname', 'lastname', 'email', 'password', 'password2');

		// Post data
		$data = array();

		// Check each mandatory POST data
		foreach ($fields as $key)
		{
			if (isset($_POST[$key]))
			{
				$val = $this->input->post($key);

				// Exit if $val == ''
				if ($val == '')
				{
					$this->_send_error('user', __('Please fill all fields !'), $_POST);
				}

				// Exit if username or password < 4 chars
				if (($key == 'username' OR $key == 'password') && strlen($val) < 4)
				{
					$this->_send_error('user', __('Login and Password must be at least 4 char length!'), $_POST);
				}

				$data[$key] = trim($val);
			}
		}

		// Check email
		if ( ! valid_email($data['email']) )
		{
			$this->_send_error('user', __('Email seems not to be valid. Please correct.'), $_POST);
		}

		// Check password
		if ( ! ($data['password'] == $data['password2']) )
		{
			$this->_send_error('user', __('Password and confirmation password are not equal.'), $_POST);
		}

        // Here is everything OK, we can create the user

        $salt = SaltCellar::getSalt(44, 50);

        $data['join_date'] = date('Y-m-d H:i:s');
		$data['salt'] = $salt;
		$data['password'] = PasswordStorage::create_hash($salt . $data['password']);

		unset($data['password2']);

		// DB save
		$this->db_connect();

		// Check if the user exists
		$this->db->where('username', $data['username']);
		$query = $this->db->get('user');

		if ($query->num_rows() > 0)
		{
			// updates the user
			$this->db->where('username', $data['username']);
			$this->db->update('user', $data);
		}
		else
		{
			// insert the user
			$this->db->insert('user', $data);
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Outputs the view
	 *
	 */
	function output($_view)
	{
		GLOBAL $config;
		if (!isset($this->template['next'])) {$this->template['next'] = true; }

		$this->template['version'] = RevSliderGlobals::SLIDER_REVISION;

		extract($this->template);

		include('./views/header.php');
		include('./views/' . $_view . '.php');
		include('./views/footer.php');
	}


	// --------------------------------------------------------------------


	/**
	 * Generates a random salt value.
	 *
	 * @return String	Hash value
	 *
	 **/
	function get_salt()
	{
		require('../application/config/revslider.php');
		return substr(md5(uniqid(rand(), true)), 0, $config['salt_length']);
	}


	// --------------------------------------------------------------------

	/**
	 * Connects to the DB with the database.php config file
	 *
	 */
	function db_connect()
	{
		include(APPPATH.'config/database'.EXT);

		$this->db = DB('default', true);

		$this->db->db_connect();
		$this->db->db_select();
	}

	/**
	 * Tests if a dir is writable
	 *
	 * @param	string  $dir
	 * @return	boolean
	 */

	function _test_dir($dir) {

		if ( ! file_exists($dir))
		    @mkdir($dir);

		if ( ! is_really_writable($dir) OR ! $dh = opendir($dir))
		    @chmod($dir, 0777);

		if ( ! is_really_writable($dir) OR ! $dh = opendir($dir))
		    return false;

		closedir($dh);
		return true;
	}

	/**
	 * Tests if a file is writable
	 *
	 * @param	Mixed		folder path to test
	 * @param	boolean		if true, check all directories recursively
	 *
	 * @return	boolean		true if every tested dir is writable, false if one is not writable
	 *
	 */
	function _test_file($files)
	{
		foreach ($files as $file)
		{
			if ( ! is_really_writable($file)) return false;
		}
		return true;
	}


	// --------------------------------------------------------------------


	/**
	 * Try to connect to the DB
	 *
	 */
	function _db_connect($data) {

		$connected = true;
        switch ($data['db_driver']) {
            case 'mysql' :
                if ( ! $link = @mysql_connect($data['db_hostname'], $data['db_username'], $data['db_password'])) {
                    $connected = false;
                    $this->_dbError = mysql_error();
                } else {
                    if ( ! @mysql_select_db($data['db_name'], $link)) {
                        $connected = false;
                        $this->_dbError = mysql_error();
                    }
                    mysql_close($link);
                }
            break;
            case 'mysqli' :
                if ( ! $link = @mysqli_connect($data['db_hostname'], $data['db_username'], $data['db_password'], $data['db_name'])) {
                    $connected = false;
                    $this->_dbError = mysqli_connect_error();
                }
            break;
        }

        if ( $connected) {
			//urlencode symbols that might break dsn string format
			$data = array_map('rawurlencode', $data);
            // $dsn = 'dbdriver://username:password@hostname/database';
            $dsn = $data['db_driver'].'://'.$data['db_username'].':'.$data['db_password'].'@'.$data['db_hostname'].'/'.$data['db_name'];
            $this->db = DB($dsn, true, true);
            $connected = $this->db->db_connect();
        }

		return $connected;
	}


	// --------------------------------------------------------------------


	/**
	 * Feed the templates data with blank values
	 * @param	array	Array of key to fill
	 */
	function _feed_blank_template($data)
	{
		foreach($data as $key)
		{
			$this->template[$key] = '';
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Feed the templates data with provided values
	 * @param	array	Array of key to fill
	 */
	function _feed_template($data)
	{
		foreach($data as $key => $value)
		{
			$this->template[$key] = $value;
		}
	}

	function _clean_data($data, $table)
	{
		$cleaned_data = array();

		if ( ! empty($data))
		{
			$fields = $this->db->list_fields($table);
			$fields = array_fill_keys($fields,'');
			$cleaned_data = array_intersect_key($data, $fields);
		}
		return $cleaned_data;
	}

	public function _exists($where, $table)
	{
		$query = $this->db->get_where($table, $where, FALSE);

		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}

	public function _get_default_lang()
	{
		$query = $this->db->get_where('lang', array('def' => '1'), FALSE);

		if ($query->num_rows() > 0)
			return $query->row_array();
		else
			return FALSE;

	}


	// --------------------------------------------------------------------


	/**
	 * Creates an error message and displays the submitted view
	  * @param	string	View name
	  * @param	string	Error message content
	  * @param	array	Data to feed to form. Optional.

	 */
	function _send_error($view, $msg, $data = array())
	{
		$this->template['message_type'] = 'error';
		$this->template['message'] = $msg;

		if ( !empty($data))
		{
			$this->_feed_template($data);
		}

		$this->output($view);

		exit();
	}


	// --------------------------------------------------------------------


	/**
	 * Saves database settings to config/database.php file
	 *
	 */
	function _save_database_settings_to_file($data)
	{
		$config_file = @file_get_contents(APPPATH . '/config/database.default' . EXT);
		foreach ($data as $key => $value) {
			$config_file = str_replace('[' . strtoupper($key) . ']', $value, $config_file);
		}
		return @file_put_contents(APPPATH . '/config/database' . EXT, $config_file);
	}

	/**
	 * Saves config settings to config/config.php file
	 *
	 */
	function _save_config_settings_to_file($data)
	{
		$config_file = @file_get_contents(APPPATH . '/config/config.default' . EXT);
		foreach ($data as $key => $value) {
			$config_file = str_replace('[' . strtoupper($key) . ']', $value, $config_file);
		}
		return @file_put_contents(APPPATH . '/config/config' . EXT, $config_file);
	}

	function generateEncryptKey($size=32) {
		return SaltCellar::getToken($size);
	}

	function is_installed()
	{
		if ( ! file_exists(APPPATH . '/config/config.php')) return false;
		if ( ! file_exists(APPPATH . '/config/database.php')) return false;

		$this->db_connect();
		$query = $this->db->get($this->db->dbprefix . 'user');
		if ( ! $query->num_rows()) return false;

		return true;
	}
}

function &get_instance() {
	return Installer::get_instance();
}
