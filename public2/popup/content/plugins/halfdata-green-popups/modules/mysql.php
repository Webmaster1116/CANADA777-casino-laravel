<?php
/* MySQL integration for Green Forms */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_mysql_class {
	var $default_parameters = array(
		"current" => "on",
		"host" => "",
		"port" => "",
		"username" => "",
		"password" => "",
		"database" => "",
		"table" => "",
		"table-id" => "",
		"columns" => array()
	);
	
	var $forbidden_tables = array(
		'posts',
		'comments',
		'links',
		'options',
		'postmeta',
		'terms',
		'term_taxonomy',
		'term_relationships',
		'termmeta',
		'commentmeta',
		'users',
		'usermeta',
		'blogs',
		'blogmeta',
		'signups',
		'site',
		'sitemeta',
		'sitecategories',
		'registration_log',
		'blog_versions',
		'sessions',
		'plugins'
	);
	function __construct() {
		if (is_admin()) {
			add_filter('lepopup_providers', array(&$this, 'providers'), 10, 1);
			add_action('wp_ajax_lepopup-mysql-settings-html', array(&$this, "admin_settings_html"));
			add_action('wp_ajax_lepopup-mysql-table', array(&$this, "admin_tables"));
			add_action('wp_ajax_lepopup-mysql-columns', array(&$this, "admin_columns_html"));
		}
		add_filter('lepopup_integrations_do_mysql', array(&$this, 'front_submit'), 10, 2);
	}
	
	function providers($_providers) {
		if (!array_key_exists("mysql", $_providers)) $_providers["mysql"] = esc_html__('MySQL', 'lepopup');
		return $_providers;
	}
	
	function admin_settings_html() {
		global $wpdb, $lepopup;
		if (current_user_can('manage_options')) {
			if (array_key_exists('data', $_REQUEST)) {
				$data = json_decode(base64_decode(trim(stripslashes($_REQUEST['data']))), true);
				if (is_array($data)) $data = array_merge($this->default_parameters, $data);
				else $data = $this->default_parameters;
			} else $data = $this->default_parameters;
			$checkbox_id = $lepopup->random_string();
			$html = '
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Current connection', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enable this option if you want to use current MySQL-connection (MySQL-server and database).', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input class="lepopup-checkbox-toggle" type="checkbox" value="off" id="current-'.esc_html($checkbox_id).'" name="current"'.($data['current'] == 'on' ? ' checked="checked"' : '').' onchange="jQuery(this).is(\':checked\') ? jQuery(this).closest(\'.lepopup-integrations-content\').find(\'.lepopup-mysql-credentials\').fadeOut(300) : jQuery(this).closest(\'.lepopup-integrations-content\').find(\'.lepopup-mysql-credentials\').fadeIn(300);" /><label for="current-'.esc_html($checkbox_id).'"></label>
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-mysql-credentials"'.($data['current'] == 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Hostname', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your MySQL server hostname.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="host" value="'.esc_html($data['host']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-mysql-credentials"'.($data['current'] == 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Port', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your MySQL server port. Leave it empty if you do not know the port or it is standard 3306.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="port" value="'.esc_html($data['port']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-mysql-credentials"'.($data['current'] == 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Username', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter your MySQL server username.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="username" value="'.esc_html($data['username']).'" />
					<label class="lepopup-integrations-description">'.esc_html__('The username must have sufficient privileges to access MySQL-server and database.', 'lepopup').'</label>
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-mysql-credentials"'.($data['current'] == 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Password', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter password for MySQL server user.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="password" value="'.esc_html($data['password']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item lepopup-mysql-credentials"'.($data['current'] == 'on' ? ' style="display:none;"' : '').'>
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Database', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Enter MySQL database name.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<input type="text" name="database" value="'.esc_html($data['database']).'" />
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Table', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Select desired Table.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-group lepopup-integrations-ajax-options">
						<input type="text" name="table" value="'.esc_html($data['table']).'" data-deps="current,host,port,username,password,database" readonly="readonly" data-default-error="'.esc_html__('Error establishing a database connection.', 'lepopup').'" />
						<input type="hidden" name="table-id" value="'.esc_html($data['table-id']).'" />
					</div>
				</div>
			</div>
			<div class="lepopup-properties-item">
				<div class="lepopup-properties-label">
					<label>'.esc_html__('Columns', 'lepopup').'</label>
				</div>
				<div class="lepopup-properties-tooltip">
					<i class="fas fa-question-circle lepopup-tooltip-anchor"></i>
					<div class="lepopup-tooltip-content">'.esc_html__('Map form fields to MySQL table columns.', 'lepopup').'</div>
				</div>
				<div class="lepopup-properties-content">
					<div class="lepopup-properties-pure lepopup-integrations-ajax-inline">
						<table>';
			foreach ($data['columns'] as $field => $value) {
				$html .= '
							<tr>
								<th>'.esc_html($field).'</th>
								<td>
									<div class="lepopup-input-shortcode-selector">
										<input type="text" name="columns['.esc_html($field).']" value="'.esc_html($value).'" data-empty="on" class="widefat" />
										<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
									</div>
								</td>
							</tr>';
			}
			$html .= '
						</table>
					</div>
					<a class="lepopup-button lepopup-button-small" onclick="return lepopup_integrations_ajax_inline_html(this);" data-inline="columns" data-deps="current,host,port,username,password,database,table-id" data-default-error="'.esc_html__('Error establishing a database connection.', 'lepopup').'"><i class="fas fa-download"></i><label>'.esc_html__('Load Table Columns', 'lepopup').'</label></a>
				</div>
			</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $html;
			echo json_encode($return_object);
		}
		exit;
	}
	
	function admin_tables() {
		global $wpdb, $lepopup;
		$tables = array();
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || ($deps['current'] != 'on' && (!array_key_exists('host', $deps) || empty($deps['host']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('database', $deps) || empty($deps['database'])))) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid MySQL credentials.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			
			foreach($this->forbidden_tables as $key => $table) {
				$this->forbidden_tables[$key] = $wpdb->prefix.$table;
			}
			
			if ($deps['current'] == 'on') {
				$rows = $wpdb->get_results('SHOW TABLES', ARRAY_N);
				foreach ($rows as $record) {
					if (is_array($record)) {
						if (!in_array($record[0], $this->forbidden_tables)) {
							if (substr($record[0], 0, strlen($wpdb->prefix.'lepopup_')) != $wpdb->prefix.'lepopup_') $tables[$record[0]] = $record[0];
						}
					}
				}
			} else {
				$wpdb_ext = null;
				if (defined('UAP_CORE') && class_exists("ICDB")) {
					try {
						$wpdb_ext = new ICDB($deps['host'], $deps['port'], $deps['database'], $deps['username'], $deps['password'], $wpdb->prefix);
					} catch (Exception $e) {
						$wpdb_ext = null;
					}
				} else {
					$wpdb_ext = new wpdb($deps['username'], $deps['password'], $deps['database'], $deps['host'].(!empty($deps['port']) ? ':'.$deps['port'] : ''));
					if (!$wpdb_ext->ready) $wpdb_ext = null;
				}
				if (!empty($wpdb_ext)) {
					$rows = $wpdb_ext->get_results('SHOW TABLES', ARRAY_N);
					foreach ($rows as $record) {
						if (is_array($record)) {
							if (!in_array($record[0], $this->forbidden_tables)) {
								if (substr($record[0], 0, strlen($wpdb->prefix.'lepopup_')) != $wpdb->prefix.'lepopup_') $tables[$record[0]] = $record[0];
							}
						}
					}
				} else {
					$return_object = array('status' => 'ERROR', 'message' => esc_html__('Error establishing a database connection.', 'lepopup'));
					echo json_encode($return_object);
					exit;
				}
			}
			
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['items'] = $tables;
			echo json_encode($return_object);
		}
		exit;
	}

	function admin_columns_html() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (array_key_exists('deps', $_REQUEST)) {
				$deps = json_decode(base64_decode(trim(stripslashes($_REQUEST['deps']))), true);
				if (!is_array($deps)) $deps = null;
			} else $deps = null;
			if (!is_array($deps) || ($deps['current'] != 'on' && (!array_key_exists('host', $deps) || empty($deps['host']) || !array_key_exists('username', $deps) || empty($deps['username']) || !array_key_exists('database', $deps) || empty($deps['database']))) || !array_key_exists('table-id', $deps) || empty($deps['table-id'])) {
				$return_object = array('status' => 'ERROR', 'message' => esc_html__('Invalid MySQL credentials or Table ID.', 'lepopup'));
				echo json_encode($return_object);
				exit;
			}
			if ($deps['current'] == 'on') $credentials = null;
			else $credentials = array('host' => $deps['host'], 'port' => $deps['port'], 'username' => $deps['username'], 'password' => $deps['password'], 'database' => $deps['database']);
			$return_object = $this->get_columns_html($credentials, $deps['table-id'], $this->default_parameters['columns']);
			echo json_encode($return_object);
		}
		exit;
	}

	function get_columns_html($_credentials, $_table, $_columns) {
		global $wpdb, $lepopup;

		foreach($this->forbidden_tables as $key => $table) {
			$this->forbidden_tables[$key] = $wpdb->prefix.$table;
		}
		$columns = array();
		if (empty($_credentials)) {
			$columns = $wpdb->get_results('SHOW COLUMNS FROM '.$_table, ARRAY_A);
		} else {
			$wpdb_ext = null;
			if (defined('UAP_CORE') && class_exists("ICDB")) {
				try {
					$wpdb_ext = new ICDB($_credentials['host'], $_credentials['port'], $_credentials['database'], $_credentials['username'], $_credentials['password'], $wpdb->prefix);
				} catch (Exception $e) {
					$wpdb_ext = null;
				}
			} else {
				$wpdb_ext = new wpdb($_credentials['username'], $_credentials['password'], $_credentials['database'], $_credentials['host'].(!empty($_credentials['port']) ? ':'.$_credentials['port'] : ''));
				if (!$wpdb_ext->ready) $wpdb_ext = null;
			}
			if (!empty($wpdb_ext)) {
				$columns = $wpdb_ext->get_results('SHOW COLUMNS FROM '.$_table, ARRAY_A);
			} else {
				return array('status' => 'ERROR', 'message' => esc_html__('Error establishing a database connection.', 'lepopup'));
			}
		}
		if (!empty($columns) && is_array($columns)) {
			$fields_html = '
			<table>';
			foreach ($columns as $column) {
				if (is_array($column)) {
					if (array_key_exists('Field', $column)) {
						if (array_key_exists('Extra', $column) && strpos($column['Extra'], 'auto_increment') !== false) continue;
						$fields_html .= '
				<tr>
					<th>'.esc_html($column['Field']).'</th>
					<td>
						<div class="lepopup-input-shortcode-selector">
							<input type="text" name="columns['.esc_html($column['Field']).']" value="'.esc_html(array_key_exists($column['Field'], $_columns) ? $_columns[$column['Field']] : '').'" data-empty="on" class="widefat" />
							<div class="lepopup-shortcode-selector" onmouseover="lepopup_shortcode_selector_set(this)";><span><i class="fas fa-code"></i></span></div>
						</div>
					</td>
				</tr>';
					}
				}
			}
			$fields_html .= '
			</table>';
		} else {
			return array('status' => 'ERROR', 'message' => esc_html__('Can not get columns.', 'lepopup'));
		}
		return array('status' => 'OK', 'html' => $fields_html);
	}

	function front_submit($_result, $_data) {
		global $wpdb, $lepopup;
		$data = array_merge($this->default_parameters, $_data);
		if (($data['current'] != 'on' && (empty($data['host']) || empty($data['username']) || empty($data['database']))) || empty($data['table-id'])) return $_result;
		if (empty($data['columns']) || !is_array($data['columns'])) return $_result;

		$fields = array();
		$values = array();
		foreach($data['columns'] as $field => $value) {
			$field = trim($field);
			if (!empty($field)) {
				$fields[] = esc_sql($field);
				$values[] = "'".esc_sql($value)."'";
			}
		}
		if (empty($fields)) return $_result;
		$sql = "INSERT INTO ".esc_sql($data['table-id'])." (`".implode('`, `', $fields)."`) VALUES (".implode(', ', $values).")";
		if ($data['current'] == 'on') {
			$wpdb->query($sql);
		} else {
			$wpdb_ext = null;
			if (defined('UAP_CORE') && class_exists("ICDB")) {
				try {
					$wpdb_ext = new ICDB($data['host'], $data['port'], $data['database'], $data['username'], $data['password'], $wpdb->prefix);
				} catch (Exception $e) {
					$wpdb_ext = null;
				}
			} else {
				$wpdb_ext = new wpdb($data['username'], $data['password'], $data['database'], $data['host'].(!empty($data['port']) ? ':'.$data['port'] : ''));
				if (!$wpdb_ext->ready) $wpdb_ext = null;
			}
			if (!empty($wpdb_ext)) {
				$wpdb_ext->query($sql);
			}
		}
		return $_result;
	}
}
$lepopup_mysql = new lepopup_mysql_class();
?>