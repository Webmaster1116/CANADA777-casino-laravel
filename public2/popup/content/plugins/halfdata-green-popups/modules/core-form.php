<?php
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class lepopup_form {
	var $form_options, $form_pages, $form_elements, $form_inputs, $form_logic, $form_dependencies, $id = null, $name, $slug;
	var $cache_html = null, $cache_style = null, $cache_uids = array(), $cache_time = null;
	var $form_data = array(), $form_info = array(), $form_extra = array();
	var $record_id = 0;
	function __construct($_id, $_include_deleted = false, $_include_passive = false) {
		global $lepopup, $wpdb;
		if (is_numeric($_id)) {
			$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE ".(!$_include_deleted ? "deleted = '0' AND " : "").(!$_include_passive ? "active = '1' AND " : "")."id = '".intval($_id)."'", ARRAY_A);
		} else {
			$form_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_forms WHERE ".(!$_include_deleted ? "deleted = '0' AND " : "").(!$_include_passive ? "active = '1' AND " : "")."slug = '".esc_sql($_id)."'", ARRAY_A);
		}
		
		if (empty($form_details)) return;
		
		$this->id = $form_details['id'];
		$this->name = $form_details['name'];
		$this->slug = $form_details['slug'];
		$this->cache_html = $form_details['cache_html'];
		$this->cache_style = $form_details['cache_style'];
		$this->cache_uids = json_decode($form_details['cache_uids'], true);
		$this->cache_time = $form_details['cache_time'];
		
		$default_form_options = $lepopup->default_form_options();
		$this->form_options = json_decode($form_details['options'], true);
		if (!empty($this->form_options)) $this->form_options = array_merge($default_form_options, $this->form_options);
		else $this->form_options = $default_form_options;

		$this->form_pages = json_decode($form_details['pages'], true);
		$default_page_options = $lepopup->default_form_options("page");
		if (is_array($this->form_pages)) {
			foreach($this->form_pages as $key => $form_page) {
				if (is_array($form_page)) {
					$this->form_pages[$key] = array_merge($default_page_options, $form_page);
				} else unset($this->form_pages[$key]);
			}
			$this->form_pages = array_values($this->form_pages);
		} else $this->form_pages = array();
		
		$this->form_elements = json_decode($form_details['elements'], true);
		if (is_array($this->form_elements)) {
			foreach($this->form_elements as $key => $form_element_raw) {
				$element_options = json_decode($form_element_raw, true);
				if (is_array($element_options) && array_key_exists('type', $element_options)) {
					$default_element_options = $lepopup->default_form_options($element_options['type']);
					$element_options = array_merge($default_element_options, $element_options);
					$this->form_elements[$key] = $element_options;
				} else unset($this->form_elements[$key]);
			}
			$this->form_elements = array_values($this->form_elements);
		} else $this->form_elements = array(); 
		
		$this->form_inputs = array();
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (array_key_exists($this->form_elements[$i]['type'], $lepopup->toolbar_tools) && $lepopup->toolbar_tools[$this->form_elements[$i]['type']]['type'] == 'input') {
				$this->form_inputs[] = $this->form_elements[$i]['id'];
			}
		}
		$this->form_logic = array();
		$this->form_dependencies = array();
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (array_key_exists('logic-enable', $this->form_elements[$i]) && $this->form_elements[$i]['logic-enable'] == 'on' && array_key_exists('logic', $this->form_elements[$i]) && is_array($this->form_elements[$i]['logic']) && array_key_exists('rules', $this->form_elements[$i]['logic']) && is_array($this->form_elements[$i]['logic']['rules'])) {
				$logic = array(
					'action' => $this->form_elements[$i]['logic']['action'],
					'operator' => $this->form_elements[$i]['logic']['operator'],
					'rules' => array()
				);
				foreach($this->form_elements[$i]['logic']['rules'] as $rule) {
					if (in_array($rule['field'], $this->form_inputs)) {
						$logic['rules'][] = $rule;
						if (!array_key_exists($rule['field'], $this->form_dependencies) || !is_array($this->form_dependencies[$rule['field']]) || !in_array($this->form_elements[$i]['id'], $this->form_dependencies[$rule['field']])) $this->form_dependencies[$rule['field']][] = $this->form_elements[$i]['id'];
					}
				}
				if (!empty($logic['rules'])) {
					$this->form_logic[$this->form_elements[$i]['id']] = $logic;
				}
			}
		}
		for ($i=0; $i<sizeof($this->form_pages); $i++) {
			if (array_key_exists('logic-enable', $this->form_pages[$i]) && $this->form_pages[$i]['logic-enable'] == 'on' && array_key_exists('logic', $this->form_pages[$i]) && is_array($this->form_pages[$i]['logic']) && array_key_exists('rules', $this->form_pages[$i]['logic']) && is_array($this->form_pages[$i]['logic']['rules'])) {
				$logic = array(
					'action' => $this->form_pages[$i]['logic']['action'],
					'operator' => $this->form_pages[$i]['logic']['operator'],
					'rules' => array()
				);
				foreach($this->form_pages[$i]['logic']['rules'] as $rule) {
					if (in_array($rule['field'], $this->form_inputs)) {
						$logic['rules'][] = $rule;
					}
				}
				if (!empty($logic['rules'])) {
					$this->form_logic[$this->form_pages[$i]['id']] = $logic;
				}
			}
		}
		if (array_key_exists('confirmations', $this->form_options) && is_array($this->form_options['confirmations'])) {
			for ($i=0; $i<sizeof($this->form_options['confirmations']); $i++) {
				if (array_key_exists('logic-enable', $this->form_options['confirmations'][$i]) && $this->form_options['confirmations'][$i]['logic-enable'] == 'on' && array_key_exists('logic', $this->form_options['confirmations'][$i]) && is_array($this->form_options['confirmations'][$i]['logic']) && array_key_exists('rules', $this->form_options['confirmations'][$i]['logic']) && is_array($this->form_options['confirmations'][$i]['logic']['rules'])) {
					$logic = array(
						'action' => $this->form_options['confirmations'][$i]['logic']['action'],
						'operator' => $this->form_options['confirmations'][$i]['logic']['operator'],
						'rules' => array()
					);
					foreach($this->form_options['confirmations'][$i]['logic']['rules'] as $rule) {
						if (in_array($rule['field'], $this->form_inputs)) {
							$logic['rules'][] = $rule;
						}
					}
					if (!empty($logic['rules'])) {
						$this->form_logic['confirmation-'.$i] = $logic;
					}
				}
			}
		}
		if (array_key_exists('notifications', $this->form_options) && is_array($this->form_options['notifications'])) {
			for ($i=0; $i<sizeof($this->form_options['notifications']); $i++) {
				if (array_key_exists('logic-enable', $this->form_options['notifications'][$i]) && $this->form_options['notifications'][$i]['logic-enable'] == 'on' && array_key_exists('logic', $this->form_options['notifications'][$i]) && is_array($this->form_options['notifications'][$i]['logic']) && array_key_exists('rules', $this->form_options['notifications'][$i]['logic']) && is_array($this->form_options['notifications'][$i]['logic']['rules'])) {
					$logic = array(
						'action' => $this->form_options['notifications'][$i]['logic']['action'],
						'operator' => $this->form_options['notifications'][$i]['logic']['operator'],
						'rules' => array()
					);
					foreach($this->form_options['notifications'][$i]['logic']['rules'] as $rule) {
						if (in_array($rule['field'], $this->form_inputs)) {
							$logic['rules'][] = $rule;
						}
					}
					if (!empty($logic['rules'])) {
						$this->form_logic['notification-'.$i] = $logic;
					}
				}
			}
		}
		if (array_key_exists('integrations', $this->form_options) && is_array($this->form_options['integrations'])) {
			for ($i=0; $i<sizeof($this->form_options['integrations']); $i++) {
				if (array_key_exists('logic-enable', $this->form_options['integrations'][$i]) && $this->form_options['integrations'][$i]['logic-enable'] == 'on' && array_key_exists('logic', $this->form_options['integrations'][$i]) && is_array($this->form_options['integrations'][$i]['logic']) && array_key_exists('rules', $this->form_options['integrations'][$i]['logic']) && is_array($this->form_options['integrations'][$i]['logic']['rules'])) {
					$logic = array(
						'action' => $this->form_options['integrations'][$i]['logic']['action'],
						'operator' => $this->form_options['integrations'][$i]['logic']['operator'],
						'rules' => array()
					);
					foreach($this->form_options['integrations'][$i]['logic']['rules'] as $rule) {
						if (in_array($rule['field'], $this->form_inputs)) {
							$logic['rules'][] = $rule;
						}
					}
					if (!empty($logic['rules'])) {
						$this->form_logic['integration-'.$i] = $logic;
					}
				}
			}
		}
	}

	public function replace_shortcodes($_object, $_addons = array(), $_urlencode = false, $_friendly_options = false) {
		global $lepopup, $wpdb;
		if (is_array($_object)) {
			foreach ($_object as $key => $value) {
				$_object[$key] = $this->replace_shortcodes($value, $_addons, $_urlencode, $_friendly_options); // UF-checked
			}
			return $_object;
		} else {
			if (!class_exists('matex')) {
				include_once(dirname(dirname(__FILE__)).'/libs/matex.php');
			}
			$matex = new matex();
			$data = array(
				'{{ip}}' => $_SERVER['REMOTE_ADDR'],
				'{{user-agent}}' => $_SERVER['HTTP_USER_AGENT'],
				'{{date}}' => date("Y-m-d", time()+3600*$lepopup->gmt_offset),
				'{{time}}' => date("H:i", time()+3600*$lepopup->gmt_offset),
				'{{record-id}}' => $this->record_id,
				'{{wp-user-login}}' => '',
				'{{wp-user-email}}' => '',
				'{{form-name}}' => $this->form_options['name'],
				'{{global-from-email}}' => $lepopup->options['from-email'],
				'{{global-from-name}}' =>  $lepopup->options['from-name']
			);
			if (array_key_exists('HTTP_REFERER', $_SERVER)) $data['{{url}}'] = $_SERVER['HTTP_REFERER'];
			if (is_user_logged_in()) {
				$current_user = wp_get_current_user();
				$data['{{wp-user-login}}'] = $current_user->user_login;
				$data['{{wp-user-email}}'] = $current_user->user_email;
			}
			$data = array_merge($data, $_addons);
			foreach ($this->form_info as $key => $value) {
				$data['{{'.$key.'}}'] = $value;
			}
			
			$file_elements = array();
			$signature_elements = array();
			$rangeslider_elements = array();
			$date_elements = array();
			$option_idxs = array();
			foreach ($this->form_elements as $idx => $form_element) {
				if ($form_element['type'] == 'file') {
					$file_elements[] = $form_element['id'];
				} else if ($form_element['type'] == 'signature') {
					$signature_elements[] = $form_element['id'];
				} else if ($form_element['type'] == 'rangeslider') {
					$rangeslider_elements[] = $form_element['id'];
				} else if ($form_element['type'] == 'date') {
					$date_elements[] = $form_element['id'];
				} else if (in_array($form_element['type'], array('select', 'radio', 'checkbox', 'multiselect', 'imageselect', 'tile')) && $_friendly_options) {
					$option_idxs[$form_element['id']] = $idx;
				}
			}
			$upload_dir = wp_upload_dir();
			preg_match_all('/{{(\d+)(|.+?)}}/' , $_object, $matches);
			for ($j=0; $j<sizeof($matches[0]); $j++) {
				if (!empty($matches[0][$j]) && !empty($matches[1][$j])) {
					if (array_key_exists($matches[1][$j], $this->form_data)) {
						if (in_array($matches[1][$j], $file_elements)) {
							$esc_array = array();
							foreach ((array)$this->form_data[$matches[1][$j]] as $array_value) {
								$esc_array[] = esc_sql($array_value);
							}
							$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $esc_array)."')", ARRAY_A);
							$filenames = array();
							foreach($uploads as $upload_details) {
								if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename']) && is_file($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename'])) {
									$filenames[] = $upload_details['filename_original'];
								} else {
									$filenames[] = $upload_details['filename_original'].' ('.esc_html__('file deleted', 'lepopup').')';
								}
							}
							$data[$matches[0][$j]] = implode(', ', $filenames);
						} else if (in_array($matches[1][$j], $signature_elements)) {
							if (!empty($this->form_data[$matches[1][$j]])) $data[$matches[0][$j]] = esc_html__('Signed', 'lepopup');
							else $data[$matches[0][$j]] = esc_html__('Not signed', 'lepopup');
						} else if (in_array($matches[1][$j], $rangeslider_elements)) {
							$data[$matches[0][$j]] = str_replace(':', ' ... ', $this->form_data[$matches[1][$j]]);
						} else if (array_key_exists($matches[1][$j], $option_idxs)) {
							$esc_array = array();
							$idx = $option_idxs[$matches[1][$j]];
							foreach ((array)$this->form_data[$matches[1][$j]] as $value) {
								$added = false;
								foreach($this->form_elements[$idx]['options'] as $option) {
									if ($option['value'] == $value && $option['value'] != $option['label']) {
										$added = true;
										$esc_array[] = $option['label'].' ('.$option['value'].')';
									}
								}
								if (!$added) $esc_array[] = $value;
							}
							$data[$matches[0][$j]] = implode(', ', $esc_array);
						} else if (is_array($this->form_data[$matches[1][$j]])) {
							$data[$matches[0][$j]] = implode(', ', $this->form_data[$matches[1][$j]]);
						} else {
							$data[$matches[0][$j]] = $this->form_data[$matches[1][$j]];
						}
					} else {
						if (array_key_exists('math-expressions', $this->form_options) && sizeof($this->form_options['math-expressions']) > 0) {
							foreach ($this->form_options['math-expressions'] as $math_expression) {
								if ($math_expression['id'] == $matches[1][$j]) {
									$argument_data = array();
									preg_match_all('/{{(\d+)(|.+?)}}/', $math_expression['expression'], $argument_matches);
									for ($k=0; $k<sizeof($argument_matches[0]); $k++) {
										if (!empty($argument_matches[0][$k]) && !empty($argument_matches[1][$k])) {
											if (array_key_exists($argument_matches[1][$k], $this->form_data)) {
												$replacement = '0';
												if ($this->is_element_visible($argument_matches[1][$k])) {
													if (in_array($argument_matches[1][$k], $file_elements)) {
														$esc_array = array();
														foreach ((array)$this->form_data[$argument_matches[1][$k]] as $array_value) {
															$esc_array[] = esc_sql($array_value);
														}
														$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $esc_array)."')", ARRAY_A);
														if ($uploads) $replacement = sizeof($uploads);
													} else if (in_array($argument_matches[1][$k], $date_elements)) {
														$date = $lepopup->validate_date($this->form_data[$argument_matches[1][$k]], $this->form_options['datetime-args-date-format']);
														$ref_date = DateTime::createFromFormat("Y-m-d", "2000-01-01");
														if ($date !== false) {
															$interval = date_diff($ref_date, $date);
															$replacement = $interval->format('%r%a');															
														} else {
															$replacement = 'error';
														}
													} else if (is_array($this->form_data[$argument_matches[1][$k]])) {
														$replacement = 0;
														foreach ($this->form_data[$argument_matches[1][$k]] as $var_value_tmp) {
															$var_value = $lepopup->extract_number($var_value_tmp);
															if (is_numeric($var_value)) {
																$replacement += floatval($var_value);
															} else {
																$replacement = 'error';
															}
														}
													} else {
														$var_value = $lepopup->extract_number($this->form_data[$argument_matches[1][$k]]);
														if (is_numeric($var_value)) $replacement = $var_value;
													}
												}
												$argument_data[$argument_matches[0][$k]] = $replacement;
											}
										}
									}
									$expression = strtr($math_expression['expression'], $argument_data);
									try {
										$value = $matex->execute($expression);
										$value = number_format($value, $math_expression['decimal-digits'], '.', '');
									} catch (Exception $e) {
										$value = $math_expression['default'];
									}
									$data[$matches[0][$j]] = $value;
									break;
								}
							}
						}
					}
				}
			}
			if ($_urlencode) {
				foreach ($data as $key => $value) {
					$data[$key] = rawurlencode($value);
				}
			}
			$object = strtr($_object, $data);
			$object = apply_filters("lepopup_replace_shortcodes", $object, $_addons, $_urlencode, $_friendly_options);
			return $object;
		}
	}
	
	public function save_data() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$fields = array();
		foreach ($this->form_data as $field_id => $field_value) {
			if ($this->is_element_visible($field_id)) {
				$fields[$field_id] = $field_value;
			}
		}
		foreach ($this->form_elements as $form_element) {
			if (array_key_exists('save', $form_element) && $form_element['save'] == 'off' && array_key_exists($form_element['id'], $fields)) unset($fields[$form_element['id']]);
		}
		$field_keys = array_keys($fields);
		$unique_keys = '';
		$all_uploads = array();
		foreach ($this->form_elements as $form_element) {
			if ($form_element['type'] == 'file') {
				$str_ids = array();
				foreach((array)$fields[$form_element['id']] as $key => $file_str_id) {
					 $file_str_id = esc_sql(trim($file_str_id));
					 if (!empty($file_str_id)) $str_ids[] = $file_str_id;
				}
				if (!empty($str_ids)) {
					$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND upload_id != '' AND status = '".esc_sql(LEPOPUP_UPLOAD_STATUS_OK)."' AND str_id IN ('".implode("', '", $str_ids)."')", ARRAY_A);
					$fields[$form_element['id']] = array();
					foreach ($uploads as $upload_details) {
						$fields[$form_element['id']][] = $upload_details['id'];
					}
					$this->form_data[$form_element['id']] = $fields[$form_element['id']];
					$all_uploads = array_merge($all_uploads, $fields[$form_element['id']]);
					$esc_array = array();
					foreach ((array)$fields[$form_element['id']] as $array_value) {
						$esc_array[] = esc_sql($array_value);
					}
					$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET form_id = '".esc_sql($this->id)."', element_id = '".esc_sql($form_element['id'])."', upload_id = '', str_id = '' WHERE id IN ('".implode("', '", $esc_array)."')");
				}
			}
			if (array_key_exists('id', $form_element) && in_array($form_element['id'], $field_keys)) {
				if (array_key_exists('validators', $form_element) && is_array($form_element['validators'])) {
					foreach($form_element['validators'] as $validator) {
						if ($validator['type'] == 'prevent-duplicates') {
							foreach((array)$fields[$form_element['id']] as $value) {
								$unique_keys .= '{'.$form_element['id'].':'.$value.'}';
							}
							break;
						}
					}
				}
			}
		}
		$str_id = $lepopup->random_string(24);
		if ($this->form_options['double-enable'] == 'on') $status = LEPOPUP_RECORD_STATUS_UNCONFIRMED;
		else $status = LEPOPUP_RECORD_STATUS_NONE;
		$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_records (form_id, personal_data_keys, unique_keys, fields, info, extra, status, str_id, amount, currency, created, deleted) VALUES (
			'".esc_sql($this->id)."','','".esc_sql($unique_keys)."','".esc_sql(json_encode($fields))."','".esc_sql(json_encode($this->form_info))."','".esc_sql(json_encode($this->form_extra))."','".$status."','".esc_sql($str_id)."','0','USD','".esc_sql(time())."','0')");
		$record_id = $wpdb->insert_id;
		$this->record_id = $record_id;
		
		$datestamp = date('Ymd', time()+3600*$lepopup->gmt_offset);
		$timestamp = date('h', time()+3600*$lepopup->gmt_offset);
		
		foreach($fields as $field_id => $field_value) {
			if (is_array($field_value)) {
				foreach($field_value as $option) {
					$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_fieldvalues (form_id, record_id, field_id, value, datestamp, deleted) VALUES (
						'".esc_sql($this->id)."','".esc_sql($record_id)."','".esc_sql($field_id)."','".esc_sql($option)."','".esc_sql($datestamp)."','0')");
				}
			} else {
				$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_fieldvalues (form_id, record_id, field_id, value, datestamp, deleted) VALUES (
					'".esc_sql($this->id)."','".esc_sql($record_id)."','".esc_sql($field_id)."','".esc_sql($field_value)."','".esc_sql($datestamp)."','0')");
			}
		}
		
		if (!empty($all_uploads)) {
			$file_num = 1;
			
			$esc_array = array();
			foreach ((array)$all_uploads as $array_value) {
				$esc_array[] = esc_sql($array_value);
			}
			$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $esc_array)."') ORDER BY element_id ASC", ARRAY_A);
			foreach($uploads as $upload_details) {
				$ext = pathinfo($upload_details['filename'], PATHINFO_EXTENSION);
				$ext = strtolower($ext);
				$filename = $record_id.'-'.$upload_details['element_id'].'-'.$file_num.(!empty($ext) ? '.'.$ext : '');
				$upload_dir = wp_upload_dir();
				rename($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename'], $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$filename);
				$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET record_id = '".esc_sql($record_id)."', filename = '".esc_sql($filename)."' WHERE id = '".esc_sql($upload_details['id'])."'");
				$file_num++;
			}
		}
		$stats_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_stats WHERE form_id = '".esc_sql($this->id)."' AND datestamp = '".esc_sql($datestamp)."' AND timestamp = '".esc_sql($timestamp)."'", ARRAY_A);
		if (!empty($stats_details)) {
			$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_stats SET submits = submits + 1 WHERE id = '".esc_sql($stats_details['id'])."'");
		} else {
			$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_stats (form_id, impressions, submits, confirmed, payments, datestamp, timestamp, deleted) VALUES ('".esc_sql($this->id)."', '0', '1', '0', '0', '".esc_sql($datestamp)."', '".esc_sql($timestamp)."', '0')");
		}
		return array('str-id' => $str_id, 'id' => $record_id);
	}
	
	function update_extra() {
		global $wpdb;
		if (empty($this->id) || $this->record_id == 0) return false;
		$this->form_extra = apply_filters("lepopup_form_extra", $this->form_extra);
		$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_records SET extra = '".esc_sql(json_encode($this->form_extra))."' WHERE id = '".esc_sql($this->record_id)."'");
	}
	
	public function get_confirmation() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$confirmation = array();
		if (array_key_exists('confirmations', $this->form_options) && is_array($this->form_options['confirmations'])) {
			for ($i=0; $i<sizeof($this->form_options['confirmations']); $i++) {
				if ($this->is_element_visible('confirmation-'.$i)) {
					$confirmation = $this->form_options['confirmations'][$i];
					break;
				}
			}
		}
		return $confirmation;
	}

	public function get_notifications() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$notifications = array();
		if (array_key_exists('notifications', $this->form_options) && is_array($this->form_options['notifications'])) {
			for ($i=0; $i<sizeof($this->form_options['notifications']); $i++) {
				if ($this->is_element_visible('notification-'.$i)) {
					$notifications[] = $this->form_options['notifications'][$i];
				}
			}
		}
		return $notifications;
	}

	public function get_integrations() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$integrations = array();
		if (array_key_exists('integrations', $this->form_options) && is_array($this->form_options['integrations'])) {
			for ($i=0; $i<sizeof($this->form_options['integrations']); $i++) {
				if ($this->is_element_visible('integration-'.$i)) {
					$integrations[] = $this->form_options['integrations'][$i];
				}
			}
		}
		return $integrations;
	}

	public function get_payment_gateway($_id) {
		global $lepopup, $wpdb;
		if (empty($_id)) return false;
		if (empty($this->id)) return false;
		$payment_gateway = null;
		if (array_key_exists('payment-gateways', $this->form_options) && is_array($this->form_options['payment-gateways'])) {
			for ($i=0; $i<sizeof($this->form_options['payment-gateways']); $i++) {
				if ($this->form_options['payment-gateways'][$i]['id'] == $_id) {
					$payment_gateway = $this->form_options['payment-gateways'][$i];
					break;
				}
			}
		}
		return $payment_gateway;
	}
	
	public function is_element_visible($_element_id) {
		global $lepopup;
		$logic_rules = array();
		if (array_key_exists($_element_id, $this->form_logic)) {
			for ($i=0; $i<sizeof($this->form_logic[$_element_id]['rules']); $i++) {
				$field_values = (array)$this->form_data[$this->form_logic[$_element_id]['rules'][$i]['field']];
				$bool_value = false;
				switch($this->form_logic[$_element_id]['rules'][$i]['rule']) {
					case 'is':
						if (in_array($this->form_logic[$_element_id]['rules'][$i]['token'], $field_values)) $logic_rules[] = true;
						else $logic_rules[] = false;
						break;
					case 'is-not':
						if (!in_array($this->form_logic[$_element_id]['rules'][$i]['token'], $field_values)) $logic_rules[] = true;
						else $logic_rules[] = false;
						break;
					case 'is-empty':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (!empty($field_values[$j])) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = !$bool_value;
						break;
					case 'is-not-empty':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (!empty($field_values[$j])) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					case 'is-greater':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (floatval($field_values[$j]) > floatval($this->form_logic[$_element_id]['rules'][$i]['token'])) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					case 'is-less':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (floatval($field_values[$j]) < floatval($this->form_logic[$_element_id]['rules'][$i]['token'])) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					case 'contains':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (!empty($this->form_logic[$_element_id]['rules'][$i]['token']) && strpos($field_values[$j], $this->form_logic[$_element_id]['rules'][$i]['token']) !== false) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					case 'starts-with':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (!empty($this->form_logic[$_element_id]['rules'][$i]['token']) && substr($field_values[$j], 0, strlen($this->form_logic[$_element_id]['rules'][$i]['token'])) == $this->form_logic[$_element_id]['rules'][$i]['token']) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					case 'ends-with':
						for ($j=0; $j<sizeof($field_values); $j++) {
							if (!empty($this->form_logic[$_element_id]['rules'][$i]['token']) && substr($field_values[$j], strlen($field_values[$j]) - strlen($this->form_logic[$_element_id]['rules'][$i]['token'])) == $this->form_logic[$_element_id]['rules'][$i]['token']) {
								$bool_value = true;
								break;
							}
						}
						$logic_rules[] = $bool_value;
						break;
					default:
						break;
				}
			}
			$bool_value = false;
			if ($this->form_logic[$_element_id]['operator'] == "and") {
				if (!in_array(false, $logic_rules)) $bool_value = true;
			} else {
				if (in_array(true, $logic_rules)) $bool_value = true;
			}
			if ($this->form_logic[$_element_id]['action'] == 'hide') $bool_value = !$bool_value;
			
			if (!$bool_value) return false;
		} else $bool_value = true;
		foreach ($this->form_elements as $form_element) {
			if ($form_element["id"] === $_element_id && array_key_exists("_parent", $form_element)) {
				$bool_value = $bool_value && $this->is_element_visible($form_element["_parent"]);
				break;
			}
		}
		
		return $bool_value;
	}

	
	public function is_page_visible($_page_id) {
		return $this->is_element_visible($_page_id);
	}

	public function get_next_page_id($_page_id) {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$next_page_id = null;
		$current_found = false;
		foreach ($this->form_pages as $key => $page) {
			if ($current_found) {
				if ($this->is_page_visible($page['id'])) {
					$next_page_id = $page['id'];
					break;
				}
			}
			if ($page['id'] == $_page_id) $current_found = true;
		}
		if (!$current_found) return false;
		if (empty($next_page_id)) return true;
		return $next_page_id;
	}
	
	protected function _get_children_ids($_parent) {
		global $lepopup;
		$children = array();
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (empty($this->form_elements[$i])) continue;
			if ($this->form_elements[$i]['_parent'] == $_parent) {
				if ($this->form_elements[$i]['type'] == 'columns') $children = array_merge($children, $this->_get_children_ids($this->form_elements[$i]['id']));
				else $children[] = $this->form_elements[$i]['id'];
			}
		}
		return $children;
	}
	
	public function get_pages() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$pages = array();
		for ($i=0; $i<sizeof($this->form_pages); $i++) {
			if (!empty($this->form_pages[$i]) && is_array($this->form_pages[$i])) {
				$pages[$this->form_pages[$i]['id']] = $this->_get_children_ids($this->form_pages[$i]['id']);
			}
		}
		return $pages;
	}
		
	protected function _filter_value($_value, $_filters) {
		if (!is_array($_filters) || empty($_filters)) return $_value;
		$values = array();
		if (is_array($_value)) $values = $_value;
		else $values[] = $_value;
		foreach ($values as $key => $value) {
			foreach ($_filters as $filter) {
				switch($filter['type']) {
					case 'trim':
						$value = trim($value);
						break;
					case 'alpha':
						if ($filter['properties']['whitespace-allowed'] == 'on') $value = preg_replace('/[^\p{L}\s]/u', '', $value);
						else $value = preg_replace('/[^\p{L}]/u', '', $value);
						break;
					case 'alphanumeric':
						if ($filter['properties']['whitespace-allowed'] == 'on') $value = preg_replace('/[^\p{L}0-9\s]/u', '', $value);
						else $value = preg_replace('/[^\p{L}0-9]/u', '', $value);
						break;
					case 'digits':
						if ($filter['properties']['whitespace-allowed'] == 'on') $value = preg_replace('/[^0-9\s]/', '', $value);
						else $value = preg_replace('/[^0-9]/', '', $value);
						break;
					case 'regex':
						$value_tmp = preg_replace($filter['properties']['pattern'], '', $value);
						if ($value_tmp != null) $value = $value_tmp;
						break;
					case 'strip-tags':
						$value = strip_tags($value, $filter['properties']['tags-allowed']);
						break;
					default:
						break;
				}
			}
			$values[$key] = $value;
		}
		if (is_array($_value)) return $values;
		else return $values[0];
	}
		
	public function set_form_data($_form_data) {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$this->form_data = array();
		foreach ($this->form_elements as $form_element) {
			if (!array_key_exists($form_element['type'], $lepopup->toolbar_tools)) continue;
			switch($form_element['type']) {
				case 'text':
				case 'password':
				case 'email':
				case 'textarea':
				case 'select':
				case 'checkbox':
				case 'imageselect':
				case 'radio':
				case 'tile':
				case 'multiselect':
				case 'date':
				case 'time':
				case 'file':
				case 'hidden':
				case 'star-rating':
				case 'signature':
				case 'rangeslider':
				case 'number':
				case 'numspinner':
					if (array_key_exists('lepopup-'.$form_element['id'], $_form_data)) {
						if (array_key_exists('filters', $form_element))	$this->form_data[$form_element['id']] = $this->_filter_value($_form_data['lepopup-'.$form_element['id']], $form_element['filters']);
						else $this->form_data[$form_element['id']] = $_form_data['lepopup-'.$form_element['id']];
					} else $this->form_data[$form_element['id']] = null;
					break;
				default:
					break;
			}
		}
	}

	public function set_form_info($_form_info = array()) {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		
		$this->form_info = array(
			'page-title' => array_key_exists('page-title', $_REQUEST) ? $_REQUEST['page-title'] : '',
			'url' => $_SERVER['HTTP_REFERER'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'user-agent' => $_SERVER['HTTP_USER_AGENT']
		);
		if (array_key_exists('misc-save-ip', $this->form_options) && $this->form_options['misc-save-ip'] != 'on') $this->form_info['ip'] = '';
		if (array_key_exists('misc-save-user-agent', $this->form_options) && $this->form_options['misc-save-user-agent'] != 'on') $this->form_info['user-agent'] = '';
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$this->form_info['wp-user-login'] = $current_user->user_login;
			$this->form_info['wp-user-email'] = $current_user->user_email;
		}
	}

	protected function _validate_value($_value, $_validators, $_field_id = null) {
		global $lepopup, $wpdb;
		if (!is_array($_validators) || empty($_validators)) return null;
		$values = array();
		if (is_array($_value)) $values = $_value;
		else $values[] = $_value;
		foreach ($values as $key => $value) {
			foreach ($_validators as $validator) {
				$match = true;
				$old = array('{value}');
				$new = array($value);
				switch($validator['type']) {
					case 'alpha':
						if ($validator['properties']['whitespace-allowed'] == 'on') $match = !preg_match('/[^\p{L}\s]/u', $value);
						else $match = !preg_match('/[^\p{L}]/u', $value);
						break;
					case 'alphanumeric':
						if ($validator['properties']['whitespace-allowed'] == 'on') $match = !preg_match('/[^\p{L}0-9\s]/u', $value);
						else $match = !preg_match('/[^\p{L}0-9]/u', $value);
						break;
					case 'date':
						if (!empty($value)) $match = $lepopup->validate_date($value, $this->form_options['datetime-args-date-format']);
						else $match = true;
						break;
					case 'digits':
						if ($validator['properties']['whitespace-allowed'] == 'on') $match = !preg_match('/[^0-9\s]/', $value);
						else $match = !preg_match('/[^0-9]/', $value);
						break;
					case 'email':
						$match = $lepopup->validate_email($value, true) || empty($value);
						break;
					case 'iban':
						$match = $lepopup->validate_iban($value, true) || empty($value);
						break;
					case 'equal':
						if (strlen($value) > 0) {
							$match = ($validator['properties']['token'] == $value);
							$old[] = '{token}';
							$new[] = $validator['properties']['token'];
						} else $match = true;
						break;
					case 'equal-field':
						if (strlen($value) > 0) {
							$match = !array_key_exists($validator['properties']['token'], $this->form_data) || (array_key_exists($validator['properties']['token'], $this->form_data) && $this->form_data[$validator['properties']['token']] == $value);
//							$old[] = '{token}';
//							$new[] = $validator['properties']['token'];
						} else $match = true;
						break;
					case 'greater':
						$match = (floatval($validator['properties']['min']) < floatval($value)) || empty($value);
						$old[] = '{min}';
						$new[] = $validator['properties']['min'];
						break;
					case 'in-array':
						$tokens = explode("\n", $validator['properties']['values']);
						foreach ($tokens as $tkey => $tvalue) $tokens[$tkey] = strtolower(trim($tvalue));
						$tokens = array_unique($tokens);
						if ($validator['properties']['invert'] == 'on')	$match = !in_array(strtolower($value), $tokens);
						else $match = in_array(strtolower($value), $tokens);
						break;
					case 'length':
						if (strlen($value) > 0) {
							$match = (strlen($value) >= $validator['properties']['min'] && strlen($value) <= $validator['properties']['max']);
							$old = array_merge($old, array('{min}', '{max}'));
							$new = array_merge($new, array($validator['properties']['min'], $validator['properties']['max']));
						} else $match = true;
						break;
					case 'less':
						$match = (floatval($validator['properties']['max']) > floatval($value)) || empty($value);
						$old[] = '{max}';
						$new[] = $validator['properties']['max'];
						break;
					case 'prevent-duplicates':
						$record_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE deleted = '0' AND form_id = '".esc_sql($this->id)."' AND unique_keys LIKE '%{".esc_sql($wpdb->esc_like($_field_id.':'.$value))."}%'", ARRAY_A);
						if (empty($record_details)) $match = true;
						else $match = false;
						break;
					case 'regex':
						if ($validator['properties']['invert'] == 'on') $match = preg_match($validator['properties']['pattern'], $value);
						else $match = !preg_match($validator['properties']['pattern'], $value);
						break;
					case 'time':
						if (!empty($value)) $match = $lepopup->validate_time($value, $this->form_options['datetime-args-time-format']);
						else $match = true;
						break;
					case 'url':
						$match = preg_match('~^((http(s)?://)|(//))[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $value) || empty($value);
						break;
					default:
						break;
				}
				if (!$match) {
					$message = empty($validator['properties']['error']) ? $lepopup->validators_meta[$validator['type']]['properties']['error']['value'] : $validator['properties']['error'];
					return str_replace($old, $new, $message);
				}
			}
		}
		return null;
	}
	
	public function validate_form_data() {
		global $lepopup, $wpdb;
		if (empty($this->id)) return false;
		$errors = array();
		foreach ($this->form_elements as $form_element) {
			$element_error = null;
			if (!$this->is_element_visible($form_element['id'])) continue;
			if (!array_key_exists($form_element['type'], $lepopup->toolbar_tools)) continue;
			switch($form_element['type']) {
				case 'text':
				case 'password':
				case 'email':
				case 'textarea':
				case 'select':
				case 'checkbox':
				case 'imageselect':
				case 'radio':
				case 'tile':
				case 'multiselect':
				case 'date':
				case 'time':
				case 'file':
				case 'hidden':
				case 'star-rating':
				case 'signature':
				case 'number':
				case 'numspinner':
					if (array_key_exists($form_element['id'], $this->form_data)) $value = $this->form_data[$form_element['id']];
					else $value = null;
					if (array_key_exists('required', $form_element) && $form_element['required'] == "on" && empty($value) && $value != '0') $errors[$form_element['id']] = $form_element['required-error'];
					else if (array_key_exists('validators', $form_element)) {
						$element_error = $this->_validate_value($value, $form_element['validators'], $form_element['id']);
						if (!empty($element_error)) $errors[$form_element['id']] = $element_error;
					}
					break;
				case 'rangeslider':
					if (array_key_exists($form_element['id'], $this->form_data)) $values = explode(':', $this->form_data[$form_element['id']]);
					else $values = array(null);
					if (array_key_exists('validators', $form_element)) {
						foreach($values as $value) {
							$element_error = $this->_validate_value($value, $form_element['validators'], $form_element['id']);
							if (!empty($element_error)) {
								$errors[$form_element['id']] = $element_error;
								break;
							}
						}
					}
					break;
				default:
					break;
			}
			if ($form_element['type'] == 'password' && empty($element_error)) {
				if ($form_element['capital-mandatory'] == "on") {
					$temp = trim(preg_replace('/[^A-Z]/', '', $this->form_data[$form_element['id']]));
					if (empty($temp)) $errors[$form_element['id']] = $form_element['capital-mandatory-error'];
				}
				if ($form_element['digit-mandatory'] == "on") {
					$temp = trim(preg_replace('/[^0-9]/', '', $this->form_data[$form_element['id']]));
					if (empty($temp)) $errors[$form_element['id']] = $form_element['digit-mandatory-error'];
				}
				if ($form_element['special-mandatory'] == "on") {
					$temp = trim(preg_replace('/[^a-zA-Z0-9]/', '', $this->form_data[$form_element['id']]));
					$temp2 = trim($this->form_data[$form_element['id']]);
					if ($temp == $temp2) $errors[$form_element['id']] = $form_element['special-mandatory-error'];
				}
				if (strlen($this->form_data[$form_element['id']]) < $form_element['min-length']) $errors[$form_element['id']] = $form_element['min-length-error'];
			} else if ($form_element['type'] == 'date' && empty($element_error)) {
				$date = $lepopup->validate_date($this->form_data[$form_element['id']], $this->form_options['datetime-args-date-format']);
				if ($date) {
					$ref_date = null;
					switch ($form_element['min-date-type']) {
						case 'yesterday':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset-2*3600*24).' 00:00');
							break;
						case 'today':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset-1*3600*24).' 00:00');
							break;
						case 'tomorrow':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset).' 00:00');
							break;
						case 'offset':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset+(intval($form_element['min-date-offset'])-1)*3600*24).' 00:00');
							break;
						case 'date':
							$ref_date = $lepopup->validate_date($form_element['min-date-date'], $this->form_options['datetime-args-date-format']);
							break;
						case 'field':
							if (array_key_exists($form_element['min-date-field'], $this->form_data)) {
								$ref_date = $lepopup->validate_date($this->form_data[$form_element['min-date-field']], $this->form_options['datetime-args-date-format']);
							}
							break;
						default:
							break;
					}
					if (!empty($ref_date) && $ref_date > $date) {
						$errors[$form_element['id']] = str_replace('{value}', $this->form_data[$form_element['id']], $form_element['min-date-error']);
					}
					$ref_date = null;
					switch ($form_element['max-date-type']) {
						case 'yesterday':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset).' 23:59');
							break;
						case 'today':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset+1*3600*24).' 23:59');
							break;
						case 'tomorrow':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset+2*3600*24).' 23:59');
							break;
						case 'offset':
							$ref_date = new DateTime(date('Y-m-d', time()+3600*$lepopup->gmt_offset+(intval($form_element['max-date-offset'])+1)*3600*24).' 00:00');
							break;
						case 'date':
							$ref_date = $lepopup->validate_date($form_element['max-date-date'], $this->form_options['datetime-args-date-format']);
							break;
						case 'field':
							if (array_key_exists($form_element['max-date-field'], $this->form_data)) {
								$ref_date = $lepopup->validate_date($this->form_data[$form_element['max-date-field']], $this->form_options['datetime-args-date-format']);
							}
							break;
						default:
							break;
					}
					if (!empty($ref_date) && $ref_date < $date) {
						$errors[$form_element['id']] = str_replace('{value}', $this->form_data[$form_element['id']], $form_element['max-date-error']);
					}
				}
			} else if ($form_element['type'] == 'time' && empty($element_error)) {
				$time = $lepopup->validate_time($this->form_data[$form_element['id']], $this->form_options['datetime-args-time-format']);
				if ($time) {
					$ref_time = null;
					switch ($form_element['min-time-type']) {
						case 'time':
							$ref_time = $lepopup->validate_time($form_element['min-time-time'], $this->form_options['datetime-args-time-format']);
							break;
						case 'field':
							if (array_key_exists($form_element['min-time-field'], $this->form_data)) {
								$ref_time = $lepopup->validate_time($this->form_data[$form_element['min-time-field']], $this->form_options['datetime-args-time-format']);
							}
							break;
						default:
							break;
					}
					if (!empty($ref_time) && $ref_time > $time) {
						$errors[$form_element['id']] = str_replace('{value}', $this->form_data[$form_element['id']], $form_element['min-time-error']);
					}
					$ref_time = null;
					switch ($form_element['max-time-type']) {
						case 'time':
							$ref_time = $lepopup->validate_time($form_element['max-time-time'], $this->form_options['datetime-args-time-format']);
							break;
						case 'field':
							if (array_key_exists($form_element['max-time-field'], $this->form_data)) {
								$ref_time = $lepopup->validate_time($this->form_data[$form_element['max-time-field']], $this->form_options['datetime-args-time-format']);
							}
							break;
						default:
							break;
					}
					if (!empty($ref_time) && $ref_time < $time) {
						$errors[$form_element['id']] = str_replace('{value}', $this->form_data[$form_element['id']], $form_element['max-time-error']);
					}
				}
			} else if ($form_element['type'] == 'signature' && empty($element_error)) {
				if (!empty($this->form_data[$form_element['id']])) {
					if (substr($this->form_data[$form_element['id']], 0, strlen('data:image/png;base64,')) != 'data:image/png;base64,') $errors[$form_element['id']] = esc_html__('Invalid signature image 1.', 'lepopup');
					else {
						try {
							$data = base64_decode(substr($this->form_data[$form_element['id']], strlen('data:image/png;base64,')));
							if ($data === false) $errors[$form_element['id']] = esc_html__('Invalid signature image 3.', 'lepopup');
							else {
								$image = imagecreatefromstring($data);
								if ($image === false) $errors[$form_element['id']] = esc_html__('Invalid signature image 4.', 'lepopup');
								else {
									$width = imagesx($image);
									$height = imagesy($image);
									if ($width === false || $height === false || $width > 1200 || $height > 600) $errors[$form_element['id']] = esc_html__('Invalid signature image size.', 'lepopup');
								}
							}
						} catch (Exception $e) {
							$errors[$form_element['id']] = esc_html__('Invalid signature image 2.', 'lepopup');
						}
					}
				}
			}
		}
		if (!empty($errors)) return $errors;
		return array();
	}

	public function do_integrations($_action) {
		global $lepopup, $wpdb;
		$integrations_data = array();
		$integrations = $this->get_integrations();
		if (is_array($integrations)) {
			foreach ($integrations as $integration) {
				if ($integration['enabled'] == 'on' && $integration['action'] == $_action) {
					$data = $this->replace_shortcodes($integration['data']); // UF-checked
					$data['form-id'] = $this->id;
					$data['form-name'] = $this->name;
					$integrations_data = apply_filters('lepopup_integrations_do_'.$integration['provider'], $integrations_data, $data);
				}
			}
		}
		return $integrations_data;
	}
	
	public function do_notifications($_action, $_args = array()) {
		global $lepopup, $wpdb;
		$notifications = $this->get_notifications();
		$upload_dir = wp_upload_dir();
		if (is_array($notifications)) {
			foreach ($notifications as $notification) {
				if ($notification['enabled'] == 'on' && $notification['action'] == $_action) {
					$to_raw = explode(',', $this->replace_shortcodes($notification['recipient-email'])); // UF-checked
					$to = array();
					foreach ($to_raw as $email) {
						$email = trim($email);
						if (!empty($email) && preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $email)) $to[] = $email;
					}
					if (!empty($to)) {
						$attachments = array();
						if (is_array($notification['attachments']) && !empty($notification['attachments'])) {
							foreach ($notification['attachments'] as $attachment) {
								switch($attachment['source']) {
									case 'media-library':
										if (!empty($attachment['token'])) {
											$media_raw = explode('|', $attachment['token'], 2);
											if (sizeof($media_raw) == 2) {
												$filename = get_attached_file(intval($media_raw[0]));
												if (!empty($filename) && file_exists($filename) && is_file($filename)) {
													$attachments[] = $filename;
												}
											}
										}
										break;
									case 'form-element':
										if (!empty($attachment['token']) && array_key_exists($attachment['token'], $this->form_data) && is_array($this->form_data[$attachment['token']])) {
											$esc_array = array();
											foreach($this->form_data[$attachment['token']] as $array_value) {
												$esc_array[] = esc_sql($array_value);
											}
											$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $esc_array)."')", ARRAY_A);
											foreach($uploads as $upload_details) {
												if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename']) && is_file($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename'])) {
													$attachments[] = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename'];
												}
											}
										}
										break;
									case 'file':
										if (!empty($attachment['token']) && file_exists($attachment['token']) && is_file($attachment['token'])) {
											$attachments[] = $attachment['token'];
										}
									default:
										break;
								}
							}
						}
						$attachments = array_unique($attachments);
						$subject = $this->replace_shortcodes($notification['subject'], array(), false, true); // UF-checked
						$message = $this->replace_shortcodes($notification['message'], array(), false, true); // UF-checked
						if (strpos(strtolower($message), '<html') === false) $message = str_replace(array("\n", "\r"), array("<br />", ""), $message);
						if (strpos($message, '{{form-data}}') !== false) {
							$fields_meta = array();
							$form_elements = $this->input_fields_sort();
							foreach($form_elements as $form_element) {
								if (is_array($form_element) && array_key_exists('name', $form_element)) {
									$fields_meta[$form_element['id']] = $form_element;
								}
							}
							$fields = array();
							foreach ($this->form_data as $field_id => $field_value) {
								if ($this->is_element_visible($field_id)) {
									$fields[$field_id] = $field_value;
								}
							}
							$html = '
							<div class="lepopup-record-details">';
							if (sizeof($fields) > 0) {
								$html .= '
								<h3 style="padding: 0 0.8em; margin: 10px 0; color: #fff; display: inline-block; background: #444; line-height: 2.0em; font-weight: 400; vertical-align: middle;">'.(!empty($lepopup->advanced_options['label-form-values']) ? esc_html($lepopup->advanced_options['label-form-values']) : esc_html__('Form Values', 'lepopup')).'</h3>
								<table style="width: 100%;">';
								$upload_dir = wp_upload_dir();
								$current_page_id = 0;
								foreach ($fields_meta as $id => $field_meta) {
									if (!array_key_exists($id, $fields)) continue;
									if (sizeof($this->form_pages) > 2 && $current_page_id != $field_meta['page-id']) {
										$html .= '
								</table>
								<h4 style="padding: 0 0.8em; margin: 10px 0; color: #fff; display: inline-block; background: #999; line-height: 2.0em; font-weight: 400; vertical-align: middle;">'.esc_html($field_meta['page-name']).'</h4>
								<table style="width: 100%;">';
										$current_page_id = $field_meta['page-id'];
									}
									
									$values = $fields[$id];
									if ($field_meta['type'] == 'file') {
										if (!empty($values)) {
											$esc_array = array();
											foreach((array)$values as $array_value) {
												$esc_array[] = esc_sql($array_value);
											}
											$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $esc_array)."')", ARRAY_A);
											$values = array();
											foreach($uploads as $upload_details) {
												if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename']) && is_file($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$this->id.'/'.$upload_details['filename'])) {
													$values[] = '<a href="'.admin_url('admin.php').'?page=lepopup&lepopup-action=download&id='.$upload_details['id'].'" target="_blank">'.esc_html($upload_details['filename_original']).'</a>';
												} else {
													$values[] = esc_html($upload_details['filename_original']).' ('.esc_html__('file deleted', 'lepopup').')';
												}
											}
											if (!empty($values)) $value = implode("<br />", $values);
											else $value = '-';
										} else $value = '-';
									} else if ($field_meta['type'] == 'signature') {
										if (substr($values, 0, strlen('data:image/png;base64,')) != 'data:image/png;base64,') $value = '-';
										else $value = esc_html__('Signed', 'lepopup');
									} else if ($field_meta['type'] == 'rangeslider') {
										$value = esc_html(str_replace(':', ' ... ', $values));
									} else if (is_array($values)) {
										foreach ($values as $key => $values_value) {
											$values_value = trim($values_value);
											if ($values_value == "") $values[$key] = "-";
											else $values[$key] = esc_html($values_value);
										}
										$value = implode("<br />", $values);
									} else if ($values != "") {
										if ($field_meta['type'] == 'textarea') {
											$value_strings = explode("\n", $values);
											foreach ($value_strings as $key => $values_value) {
												$value_strings[$key] = esc_html(trim($values_value));
											}
											$value = implode("<br />", $value_strings);
										} else $value = esc_html($values);
									} else $value = "-";	
									$html .= '
									<tr><td style="width: 33%; font-weight: 700; font-size: 15px; vertical-align: top;">'.esc_html($field_meta['name']).'</td><td style="font-size: 15px;">'.$value.'</td></tr>';
								}
								$html .= '
								</table>';
								if (array_key_exists('payment-status', $_args)) {
										$html .= '
									<h3 style="padding: 0 0.8em; margin: 10px 0; color: #fff; display: inline-block; background: #444; line-height: 2.0em; font-weight: 400; vertical-align: middle;">'.(!empty($lepopup->advanced_options['label-payment']) ? esc_html($lepopup->advanced_options['label-payment']) : esc_html__('Payment', 'lepopup')).'</h3>
									<table style="width: 100%;">
										<tr><td style="width: 33%; font-weight: 700; font-size: 15px; vertical-align: top;">'.esc_html__('Amount', 'lepopup').'</td><td style="font-size: 15px;">'.($_args['payment-currency'] != 'BTC' ? number_format($_args['payment-amount'], 2, '.', '') : number_format($_args['payment-amount'], 8, '.', '')).' '.esc_html($_args['payment-currency']).'</td></tr>
										<tr><td style="width: 33%; font-weight: 700; font-size: 15px; vertical-align: top;">'.esc_html__('Status', 'lepopup').'</td><td style="font-size: 15px;">'.esc_html($_args['payment-status']).'</td></tr>
									</table>';
								}
								if (is_array($this->form_info) && $this->form_options['misc-email-tech-info'] == 'on') {
									$html .= '
								<h3 style="padding: 0 0.8em; margin: 10px 0; color: #fff; display: inline-block; background: #444; line-height: 2.0em; font-weight: 400; vertical-align: middle;">'.(!empty($lepopup->advanced_options['label-technical-info']) ? esc_html($lepopup->advanced_options['label-technical-info']) : esc_html__('Technical Info', 'lepopup')).'</h3>
								<table style="width: 100%;">';
									foreach($this->form_info as $info_key => $info_value) {
										$label = $lepopup->get_info_label($info_key);
										$html .= '
									<tr><td style="width: 33%; font-weight: 700; font-size: 15px; vertical-align: top;">'.esc_html($label).'</td><td style="font-size: 15px;">'.esc_html($info_value).'</td></tr>';
									}
									$html .= '
								</table>';
								}
							}
							$html .= '</div>';
							$message = str_replace('{{form-data}}', $html, $message);
						}
						$message = '<div style="font-size:15px;">'.$message.'</div>';
						$from_email = $this->replace_shortcodes($notification['from-email']); // UF-checked
						if (empty($from_email) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $from_email)) $from_email = $lepopup->options['from-email'];
						$from_name = $this->replace_shortcodes($notification['from-name'], array(), false, true); // UF-checked
						$reply_email = $this->replace_shortcodes($notification['reply-email']); // UF-checked
						if (empty($reply_email) || !preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $reply_email)) $reply_email = $from_email;
						$mail_headers = "Content-Type: text/html; charset=UTF-8\r\n";
						$mail_headers .= "From: \"".(empty($from_name) ? esc_html($from_email) : $from_name)."\" <".esc_html($from_email).">\r\n";
						$mail_headers .= "Reply-To: \"".esc_html($reply_email)."\" <".esc_html($reply_email).">\r\n";
						$mail_headers .= "X-Mailer: PHP/".phpversion()."\r\n";
						foreach($to as $to_email) {
							wp_mail($to_email, $subject, $message, $mail_headers, $attachments);
						}
					}
				}
			}
		}
	}

	function field_analytics_array($_start_date, $_end_date) {
		global $wpdb;
		$charts = array();
		foreach ($this->form_elements as $form_element) {
			if (in_array($form_element['type'], array('radio', 'checkbox', 'select', 'imageselect', 'multiselect', 'tile', 'star-rating'))) {
				$chart = array(
					'title' => $form_element['name'],
					'form-id' => intval($this->id),
					'id' => intval($form_element['id']),
					'chart' => $wpdb->get_results("SELECT COUNT(value) AS value, value AS label FROM ".$wpdb->prefix."lepopup_fieldvalues WHERE deleted = '0' AND datestamp >= '".esc_sql($_start_date->format("Ymd"))."' AND datestamp <= '".esc_sql($_end_date->format("Ymd"))."' AND form_id = '".esc_sql(intval($this->id))."' AND field_id = '".esc_sql(intval($form_element['id']))."' AND value != '' GROUP BY value", ARRAY_A)
				);
				if (!empty($chart['chart'])) $charts[] = $chart;
			}
		}
		return $charts;
	}

	function get_field_editor($_field_id, $_value = '') {
		global $lepopup, $wpdb;
		$html = '';
		if (array_key_exists($_field_id, $this->form_data)) {
			$type = 'text';
			foreach($this->form_elements as $form_element) {
				if (is_array($form_element) && array_key_exists('id', $form_element) && $form_element['id'] == $_field_id) {
					$type = $form_element['type'];
					break;
				}
			}
			if ($type == 'imageselect' || $type == 'tile') {
				if ($form_element['mode'] == 'radio') $type = 'radio';
				else $type = 'checkbox';
			}
			switch ($type) {
				case 'text':
				case 'email':
				case 'password':
				case 'hidden':
				case 'date':
				case 'time':
				case 'rangeslider':
				case 'number':
				case 'numspinner':
					$html = '<input type="text" value="'.esc_html($_value).'" name="value" />';
					break;
				case 'textarea':
					$html = '<textarea name="value">'.esc_html($_value).'</textarea>';
					break;
				case 'select':
				case 'radio':
					$options = "";
					if (array_key_exists("please-select-option", $form_element) && $form_element["please-select-option"] == "on") $options .= "<option value=''>".esc_html($form_element["please-select-text"])."</option>";
					for ($j=0; $j<sizeof($form_element["options"]); $j++) {
						$options .= "<option value='".esc_html($form_element["options"][$j]["value"])."'".($_value == $form_element["options"][$j]["value"] ? ' selected="selected"' : '').">".esc_html($form_element["options"][$j]["label"])."</option>";
					}
					$html = '<select name="value">'.$options.'</select>';
					break;
				case 'checkbox':
				case 'multiselect':
					$options = "";
					$total = 0;
					for ($j=0; $j<sizeof($form_element["options"]); $j++) {
						$id = $lepopup->random_string(16);
						$html .= "<div class='lepopup-cr-box'><input class='lepopup-checkbox lepopup-checkbox-fa-check lepopup-checkbox-medium' type='checkbox' name='value[]' id='".esc_html($id)."' value='".esc_html($form_element["options"][$j]["value"])."'".(in_array($form_element["options"][$j]["value"], (array)$_value) ? ' checked="checked"' : '')." /><label for='".esc_html($id)."'></label> &nbsp; <label for='".esc_html($id)."'>".esc_html($form_element["options"][$j]["label"])."</label></div>";
						$total++;
					}
					if ($total > 10) {
						$html = '<div class="lepopup-record-field-editor-scrollbox">'.$html.'</div>';
					}
					break;
				case "star-rating":
					$options = "";
					$id = $lepopup->random_string(16);
					for ($j=$form_element['total-stars']; $j>0; $j--) {
						$options .= "<input type='radio' name='value' id='".esc_html($id."-".$j)."' value='".esc_html($j)."'".($_value == $j ? " checked='checked'" : "")." /><label for='".esc_html($id."-".$j)."'></label>";
					}
					$html .= "<form><fieldset class='lepopup-star-rating'>".$options."</fieldset></form>";
					break;
				case 'file':
				case 'signature':
				default:
					return array('status' => 'ERROR', 'message' => esc_html__('This field can not be edited.', 'lepopup'));
					break;
			}
		} else $html = '<input type="text" value="'.esc_html($_value).'" name="value" />';
		return array('status' => 'OK', 'html' => $html);
	}

	function export_records() {
		global $lepopup, $wpdb;
		error_reporting(0);
		ob_start();
		if(!ini_get('safe_mode')) set_time_limit(0);
		ob_end_clean();
		$upload_dir = wp_upload_dir();
		$fields_meta = array();
		foreach($this->form_elements as $form_element) {
			if (is_array($form_element) && array_key_exists('name', $form_element)) {
				$fields_meta[$form_element['id']] = $form_element;
			}
		}
		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-type: application-download");
//			header("Content-Length: ".strlen($output));
			header('Content-Disposition: attachment; filename="form-records-'.$this->id.'.csv"');
			header("Content-Transfer-Encoding: binary");
		} else {
			header("Content-type: application-download");
//			header("Content-Length: ".strlen($output));
			header('Content-Disposition: attachment; filename="form-records-'.$this->id.'.csv"');
		}
		echo '"ID"';
		foreach ($this->form_inputs as $element_id) {
			echo $lepopup->options['csv-separator'].'"'.(array_key_exists($element_id, $fields_meta) ? str_replace('"', '""', $fields_meta[$element_id]['name']) : '-').'"';
		}
		echo $lepopup->options['csv-separator'].'"IP"'.$lepopup->options['csv-separator'].'"User-Agent"'.$lepopup->options['csv-separator'].'"Created"'."\r\n";
		$sql = "SELECT * FROM ".$wpdb->prefix."lepopup_records WHERE form_id = '".$this->id."' && deleted = '0' ORDER BY created ASC";
		$rows = $wpdb->get_results($sql, ARRAY_A);
		foreach ($rows as $record_details) {
			$field_values = array();
			$fields = json_decode($record_details['fields'], true);
			if (!is_array($fields)) continue;
			foreach ($fields as $id => $values) {
				if (array_key_exists($id, $fields_meta) && $fields_meta[$id]['type'] == 'file') {
					if (!empty($values)) {
						foreach ($values as $values_key => $values_value) {
							$values[$values_key] = esc_sql($values_value);
						}
						$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $values)."')", ARRAY_A);
						$values = array();
						foreach($uploads as $upload_details) {
							$values[] = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$record_details['form_id'].'/'.$upload_details['filename'];
						}
						if (!empty($values)) $value = implode("\r\n", $values);
						else $value = '-';
					} else $value = '-';
				} else if (array_key_exists($id, $fields_meta) && $fields_meta[$id]['type'] == 'signature') {
					if (substr($values, 0, strlen('data:image/png;base64,')) != 'data:image/png;base64,') $value = '-';
					else $value = 'signed';
				} else if (array_key_exists($id, $fields_meta) && $fields_meta[$id]['type'] == 'rangeslider') {
					$value = str_replace(':', ' ... ', $values);
				} else if (array_key_exists($id, $fields_meta) && in_array($fields_meta[$id]['type'], array('select', 'radio', 'checkbox', 'multiselect', 'imageselect', 'tile'))) {
					$esc_array = array();
					foreach ((array)$values as $key => $values_value) {
						$added = false;
						foreach($fields_meta[$id]['options'] as $option) {
							if ($option['value'] == $values_value && $option['value'] != $option['label']) {
								$added = true;
								$esc_array[] = $option['label'].' ('.$option['value'].')';
							}
						}
						if (!$added) $esc_array[] = $values_value;
					}
					$value = implode("\r\n", $esc_array);
				} else if (is_array($values)) {
					foreach ($values as $key => $values_value) {
						$values_value = trim($values_value);
						if ($values_value == "") $values[$key] = "-";
						else $values[$key] = $values_value;
					}
					$value = implode("\r\n", $values);
				} else if ($values != "") {
					$value = $values;
				} else $value = "-";
				$field_values[$id] = $value;
			}
			echo '"'.$record_details['id'].'"';
			foreach ($this->form_inputs as $element_id) {
				echo $lepopup->options['csv-separator'].'"'.(array_key_exists($element_id, $field_values) ? str_replace('"', '""', $field_values[$element_id]) : '-').'"';
			}
			$info = json_decode($record_details['info'], true);
			echo $lepopup->options['csv-separator'].'"'.str_replace('"', '""', $info['ip']).'"'.$lepopup->options['csv-separator'].'"'.str_replace('"', '""', $info['user-agent']).'"'.$lepopup->options['csv-separator'].'"'.$lepopup->unixtime_string($record_details['created']).'"'."\r\n";
		}
		flush();
		ob_flush();
		exit;
	}

	protected function _build_hidden($_parent) {
		global $lepopup;
		$html = '';
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (empty($this->form_elements[$i])) continue;
			if ($this->form_elements[$i]["type"] != "hidden") continue;
			if ($this->form_elements[$i]["_parent"] != $_parent) continue;
			$html .= "<input class='lepopup-hidden' type='hidden' name='lepopup-".esc_html($this->form_elements[$i]['id'])."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-default='".esc_html($this->form_elements[$i]["default"])."' value='".esc_html($this->form_elements[$i]["default"])."' />";
		}
		return $html;
	}
	
	protected function _build_children($_parent, $_parent_col) {
		global $lepopup;
		$html = '';
		$style = '';
		$webfonts = array();
		$uids = array();
		$properties = array();

		$idxs = array();
		$seqs = array();
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (empty($this->form_elements[$i])) continue;
			if ($this->form_elements[$i]["_parent"] == $_parent) {
				$idxs[] = $i;
				$seqs[] = intval($this->form_elements[$i]["_seq"]);
			}
		}
		if (empty($idxs)) return array("html" => "", "style" => "", "webfonts" => $webfonts, "uids" => array());
		for ($i=0; $i<sizeof($seqs); $i++) {
			$sorted = -1;
			for ($j=0; $j<sizeof($seqs)-1; $j++) {
				if ($seqs[$j] > $seqs[$j+1]) {
					$sorted = $seqs[$j];
					$seqs[$j] = $seqs[$j+1];
					$seqs[$j+1] = $sorted;
					$sorted = $idxs[$j];
					$idxs[$j] = $idxs[$j+1];
					$idxs[$j+1] = $sorted;
				}
			}
			if ($sorted == -1) break;
		}
		for ($k=0; $k<sizeof($idxs); $k++) {
			$i = $idxs[$k];
			$icon = "";
			$options = "";
			$extra_class = "";
			$column_label_class = "";
			$column_input_class = "";
			$zindex_base = 500;
			$properties = array();
			if (empty($this->form_elements[$i])) continue;
			if (array_key_exists('icon-left-icon', $this->form_elements[$i])) {
				if ($this->form_elements[$i]["icon-left-icon"] != "") {
					$extra_class .= " lepopup-icon-left";
					$icon .= "<i class='lepopup-icon-left ".esc_html($this->form_elements[$i]["icon-left-icon"])."'></i>";
					$options = "";
					if ($this->form_elements[$i]["icon-left-size"] != "") {
						$options .= "font-size:".$this->form_elements[$i]["icon-left-size"]."px;";
					}
					if (!empty($options)) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input>i.lepopup-icon-left{".esc_html($options)."}";
				}
			}
			if (array_key_exists('icon-right-icon', $this->form_elements[$i])) {
				if ($this->form_elements[$i]["icon-right-icon"] != "") {
					$extra_class .= " lepopup-icon-right";
					$icon .= "<i class='lepopup-icon-right ".esc_html($this->form_elements[$i]["icon-right-icon"])."'></i>";
					$options = "";
					if ($this->form_elements[$i]["icon-right-size"] != "") {
						$options .= "font-size:".$this->form_elements[$i]["icon-right-size"]."px;";
					}
					if (!empty($options)) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input>i.lepopup-icon-right{".esc_html($options)."}";
				}
			}
			if (array_key_exists($this->form_elements[$i]["type"], $lepopup->toolbar_tools)) {
				switch($this->form_elements[$i]["type"]) {
					case "button":
					case "link-button":
						$icon = "";
						$label = "";
						if (!empty($this->form_elements[$i]["label"])) $label = '<span>'.esc_html($this->form_elements[$i]["label"]).'</span>';
						else $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a.lepopup-button i {margin:0!important;}";
						if (array_key_exists("icon-left", $this->form_elements[$i]) && $this->form_elements[$i]["icon-left"] != "") $label = "<i class='lepopup-icon-left ".esc_html($this->form_elements[$i]["icon-left"])."'></i>".$label;
						if (array_key_exists("icon-right", $this->form_elements[$i]) && $this->form_elements[$i]["icon-right"] != "") $label .= "<i class='lepopup-icon-right ".esc_html($this->form_elements[$i]["icon-right"])."'></i>";
						
						$properties['style-attr'] = "";
						if (array_key_exists("colors-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-background"].";";
						if (array_key_exists("colors-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-border"].";";
						if (array_key_exists("colors-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:visited{".esc_html($properties['style-attr'])."}";

						$properties['style-attr'] = "";
						if (array_key_exists("colors-hover-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-hover-background"].";";
						if (array_key_exists("colors-hover-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-hover-border"].";";
						if (array_key_exists("colors-hover-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-hover-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:focus{".esc_html($properties['style-attr'])."}";
						
						$properties['style-attr'] = "";
						if (array_key_exists("colors-active-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-active-background"].";";
						if (array_key_exists("colors-active-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-active-border"].";";
						if (array_key_exists("colors-active-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-active-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:active{".esc_html($properties['style-attr'])."}";
						
						$properties['extra_attr'] = '';
						if ($this->form_elements[$i]["type"] == 'button') {
							if ($this->form_elements[$i]['button-type'] == 'submit') $properties['extra_attr'] = " href='#' onclick='return lepopup_submit(this);'";
							else if ($this->form_elements[$i]['button-type'] == 'next') $properties['extra_attr'] = " href='#' onclick='return lepopup_submit(this, \"next\");'";
							else if ($this->form_elements[$i]['button-type'] == 'prev') $properties['extra_attr'] = " href='#' onclick='return lepopup_submit(this, \"prev\");'";
						} else {
							$url = trim($this->form_elements[$i]['link']);
							if (empty($url) || $url == "#") $properties['extra_attr'] = " href='#'";
							else $properties['extra_attr'] = " href='".esc_html($url)."'".($this->form_elements[$i]['new-tab'] == "on" ? " target='_blank'" : "");
							$custom_onclick = '';
							if (array_key_exists('onclick', $this->form_elements[$i]) && !empty($this->form_elements[$i]['onclick'])) $custom_onclick = rtrim(str_replace("'", "`", trim($this->form_elements[$i]['onclick'])), ';').'; ';
							if ($this->form_elements[$i]['close'] == "period") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(".intval($this->form_elements[$i]['cookie-lifetime']).");".(empty($url) || $url == "#" ? 'return false;' : '')."'";
							else if ($this->form_elements[$i]['close'] == "forever") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(360);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
							else if ($this->form_elements[$i]['close'] == "single") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(0);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
							else if (!empty($custom_onclick)) $properties['extra_attr'] .= " onclick='".$custom_onclick."'";
						}
						
						if ($this->form_elements[$i]["type"] == 'button') $html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><a class='lepopup-button lepopup-button-".esc_html($this->form_options["button-active-transform"])." ".esc_html($this->form_elements[$i]["css-class"])."'".$properties['extra_attr']." data-label='".esc_html($this->form_elements[$i]["label"])."' data-loading='".esc_html($this->form_elements[$i]["label-loading"])."'>".$label."</a></div>";
						else $html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><a class='lepopup-button lepopup-button-".esc_html($this->form_options["button-active-transform"])." ".esc_html($this->form_elements[$i]["css-class"])."'".$properties['extra_attr'].">".$label."</a></div>";
						break;

					case "file":
						$label = "";
						if (!empty($this->form_elements[$i]["button-label"])) $label = '<span>'.esc_html($this->form_elements[$i]["button-label"]).'</span>';
						else $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a.lepopup-button i {margin:0!important;}";
						if (array_key_exists("icon-left", $this->form_elements[$i]) && $this->form_elements[$i]["icon-left"] != "") $label = "<i class='lepopup-icon-left ".$this->form_elements[$i]["icon-left"]."'></i>".$label;
						if (array_key_exists("icon-right", $this->form_elements[$i]) && $this->form_elements[$i]["icon-right"] != "") $label .= "<i class='lepopup-icon-right ".$this->form_elements[$i]["icon-right"]."'></i>";
						
						$properties['style-attr'] = "";
						if (array_key_exists("colors-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-background"].";";
						if (array_key_exists("colors-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-border"].";";
						if (array_key_exists("colors-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:visited{".esc_html($properties['style-attr'])."}";

						$properties['style-attr'] = "";
						if (array_key_exists("colors-hover-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-hover-background"].";";
						if (array_key_exists("colors-hover-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-hover-border"].";";
						if (array_key_exists("colors-hover-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-hover-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-hover-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:focus{".esc_html($properties['style-attr'])."}";
						
						$properties['style-attr'] = "";
						if (array_key_exists("colors-active-background", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-background"] != "") $properties['style-attr'] .= "background-color:".$this->form_elements[$i]["colors-active-background"].";";
						if (array_key_exists("colors-active-border", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-border"] != "") $properties['style-attr'] .= "border-color:".$this->form_elements[$i]["colors-active-border"].";";
						if (array_key_exists("colors-active-text", $this->form_elements[$i]) && $this->form_elements[$i]["colors-active-text"] != "") $properties['style-attr'] .= "color:".$this->form_elements[$i]["colors-active-text"].";";
						if ($properties['style-attr'] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-button:active{".esc_html($properties['style-attr'])."}";
					
						$accept_raw = explode(',', $this->form_elements[$i]['allowed-extensions']);
						$accept = array();
						foreach ($accept_raw as $extension) {
							$extension = trim(trim($extension), '.');
							if (!empty($extension)) $accept[] = '.'.strtolower($extension);
						}
						$upload_template = '
<div class="lepopup-uploader" id="%%upload-id%%">
	<a class="lepopup-button lepopup-button-'.esc_html($this->form_options["button-active-transform"]).' '.esc_html($this->form_elements[$i]["css-class"]).'" onclick="lepopup_input_error_hide(this); jQuery(this).parent().find(\'input[type=file]\').click(); return false;">'.$label.'</a>
	<div class="lepopup-uploader-engine">
		<form action="%%ajax-url%%" method="POST" enctype="multipart/form-data" target="lepopup-iframe-%%upload-id%%" onsubmit="return lepopup_uploader_start(this);" style="display: none !important; width: 0 !important; height: 0 !important;">
			<input type="hidden" value="%%upload-id%%" name="'.ini_get("session.upload_progress.name").'" />
			<input type="hidden" name="action" value="lepopup-upload" />
			<input type="hidden" name="upload-id" value="%%upload-id%%" />
			<input type="hidden" name="form-id" value="'.esc_html($this->id).'" />
			<input type="hidden" name="element-id" value="'.esc_html($this->form_elements[$i]['id']).'" />
			<input type="file" name="files[]"'.(!empty($accept) ? ' accept="'.esc_html(implode(', ', $accept)).'"' : '').' multiple="multiple" onchange="jQuery(this).parent().submit();" style="display: none !important; width: 0 !important; height: 0 !important;" />
			<input type="submit" value="Upload" style="display: none !important; width: 0 !important; height: 0 !important;" />
		</form>
		<iframe data-loading="false" id="lepopup-iframe-%%upload-id%%" name="lepopup-iframe-%%upload-id%%" src="about:blank" onload="lepopup_uploader_finish(this);" style="display: none !important; width: 0 !important; height: 0 !important;"></iframe>
	</div>
</div>';
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a.lepopup-button {height:".intval($this->form_elements[$i]['size-height'])."px !important;}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."' data-id='".esc_html($this->form_elements[$i]['id'])."' data-max-files='".intval($this->form_elements[$i]['max-files'])."' data-max-files-error='".esc_html($this->form_elements[$i]['max-files-error'])."' data-max-size='".intval($this->form_elements[$i]['max-size'])."' data-max-size-error='".esc_html($this->form_elements[$i]['max-size-error'])."' data-allowed-extensions='".esc_html(implode(',', $accept))."' data-allowed-extensions-error='".esc_html($this->form_elements[$i]['allowed-extensions-error'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-upload-input'><div class='lepopup-uploader-files'></div><div class='lepopup-uploaders'></div><input type='hidden' class='lepopup-uploader-template' value='".esc_html(base64_encode($upload_template))."' /></div></div>";
						break;
						
					case "email":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='email' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' autocomplete='".esc_html($this->form_elements[$i]["autocomplete"])."' data-default='".esc_html($this->form_elements[$i]["default"])."' value='".esc_html($this->form_elements[$i]["default"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "")." /></div></div>";
						break;
						
					case "text":
						$masked = $lepopup->options['mask-enable'] == "on" && array_key_exists("mask-mask", $this->form_elements[$i]) && !empty($this->form_elements[$i]["mask-mask"]);
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='text' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").($masked ? "lepopup-mask " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' autocomplete='".esc_html($this->form_elements[$i]["autocomplete"])."' data-default='".esc_html($this->form_elements[$i]["default"])."'".($masked ? "data-xmask='".esc_html($this->form_elements[$i]["mask-mask"])."'" : "")." value='".esc_html($this->form_elements[$i]["default"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "")." /></div></div>";
						break;

					case "number":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='text' inputmode='numeric' pattern='[0-9]*' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='lepopup-number".($this->form_elements[$i]['align'] != "" ? " lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' data-default='".esc_html($this->form_elements[$i]["number-value3"])."' data-min='".esc_html($this->form_elements[$i]["number-value1"])."' data-max='".esc_html($this->form_elements[$i]["number-value2"])."' data-decimal='".esc_html($this->form_elements[$i]["decimal"])."' data-value='".esc_html($this->form_elements[$i]["number-value3"])."' value='".esc_html($this->form_elements[$i]["number-value3"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onblur='lepopup_number_unfocused(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "")." /></div></div>";
						break;

					case "numspinner":
						$ranges = $this->_prepare_ranges($this->form_elements[$i]["number-advanced-value2"]);
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input lepopup-icon-left lepopup-icon-right".esc_html($extra_class)."'><i class='lepopup-icon-left lepopup-if lepopup-if-minus lepopup-numspinner-minus' onclick='lepopup_numspinner_dec(this);'></i><i class='lepopup-icon-right lepopup-if lepopup-if-plus lepopup-numspinner-plus' onclick='lepopup_numspinner_inc(this);'></i><input type='text' readonly='readonly' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='lepopup-number".($this->form_elements[$i]['align'] != "" ? " lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='...' data-mode='".($this->form_elements[$i]["simple-mode"] == 'on' ? 'simple' : 'advanced')."' data-default='".($this->form_elements[$i]["simple-mode"] == 'on' ? number_format($this->form_elements[$i]["number-value2"], $this->form_elements[$i]["decimal"], '.', '') : number_format($this->form_elements[$i]["number-advanced-value1"], $this->form_elements[$i]["decimal"], '.', ''))."'".($this->form_elements[$i]["simple-mode"] == 'on' ? " data-min='".esc_html($this->form_elements[$i]["number-value1"])."' data-max='".esc_html($this->form_elements[$i]["number-value3"])."'" : " data-range='".esc_html($ranges)."'")." data-step='".($this->form_elements[$i]["simple-mode"] == 'on' ? esc_html($this->form_elements[$i]["number-value4"]) : esc_html($this->form_elements[$i]["number-advanced-value3"]))."' data-decimal='".esc_html($this->form_elements[$i]["decimal"])."' data-value='".($this->form_elements[$i]["simple-mode"] == 'on' ? number_format($this->form_elements[$i]["number-value2"], $this->form_elements[$i]["decimal"], '.', '') : number_format($this->form_elements[$i]["number-advanced-value1"], $this->form_elements[$i]["decimal"], '.', ''))."' value='".($this->form_elements[$i]["simple-mode"] == 'on' ? number_format($this->form_elements[$i]["number-value2"], $this->form_elements[$i]["decimal"], '.', '') : number_format($this->form_elements[$i]["number-advanced-value1"], $this->form_elements[$i]["decimal"], '.', ''))."'".($this->form_elements[$i]["readonly"] == 'on' ? " data-readonly='on'" : " data-readonly='off'")." aria-label='".esc_html($this->form_elements[$i]["name"])."' /></div></div>";
						break;

					case "password":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."' data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='password' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' data-default='' value='' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);' /></div></div>";
						break;
						
					case "date":
						$value = '';
						$default = '';
						$offset = 0;
						if (array_key_exists("default", $this->form_elements[$i])) $default = $this->form_elements[$i]['default'];
						if (array_key_exists("default-type", $this->form_elements[$i])) {
							$default = '';
							if ($this->form_elements[$i]["default-type"] == "date") {
								$default = $this->form_elements[$i]['default-date'];
								$value = $this->form_elements[$i]['default-date'];
							} else if ($this->form_elements[$i]["default-type"] == "offset") {
								$default = $this->form_elements[$i]['default-type'];
								$offset = $this->form_elements[$i]['default-offset'];
							} else if ($this->form_elements[$i]["default-type"] != "none") {
								$default = $this->form_elements[$i]['default-type'];
							}
						}
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='text' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='lepopup-date ".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' autocomplete='".esc_html($this->form_elements[$i]["autocomplete"])."' data-default='".esc_html($default)."' data-offset='".esc_html($offset)."' value='".esc_html($value)."' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "")." data-format='".esc_html($this->form_options['datetime-args-date-format'])."' data-locale='".esc_html($this->form_options['datetime-args-locale'])."' data-min-type='".esc_html($this->form_elements[$i]['min-date-type'])."' data-min-value='".($this->form_elements[$i]['min-date-type'] == 'date' ? esc_html($this->form_elements[$i]['min-date-date']) : ($this->form_elements[$i]['min-date-type'] == 'field' ? esc_html($this->form_elements[$i]['min-date-field']) : ($this->form_elements[$i]['min-date-type'] == 'offset' ? esc_html($this->form_elements[$i]['min-date-offset']) : '')))."' data-max-type='".esc_html($this->form_elements[$i]['max-date-type'])."' data-max-value='".($this->form_elements[$i]['max-date-type'] == 'date' ? esc_html($this->form_elements[$i]['max-date-date']) : ($this->form_elements[$i]['max-date-type'] == 'field' ? esc_html($this->form_elements[$i]['max-date-field']) : ($this->form_elements[$i]['max-date-type'] == 'offset' ? esc_html($this->form_elements[$i]['max-date-offset']) : '')))."' /></div></div>";
						break;

					case "time":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<input type='text' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='lepopup-time ".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' data-default='".esc_html($this->form_elements[$i]["default"])."' value='".esc_html($this->form_elements[$i]["default"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "")." data-interval='".esc_html($this->form_elements[$i]['interval'])."' data-format='".esc_html($this->form_options['datetime-args-time-format'])."' data-locale='".esc_html($this->form_options['datetime-args-locale'])."' data-min-type='".esc_html($this->form_elements[$i]['min-time-type'])."' data-min-value='".($this->form_elements[$i]['min-time-type'] == 'time' ? esc_html($this->form_elements[$i]['min-time-time']) : ($this->form_elements[$i]['min-time-type'] == 'field' ? esc_html($this->form_elements[$i]['min-time-field']) : ''))."' data-max-type='".esc_html($this->form_elements[$i]['max-time-type'])."' data-max-value='".($this->form_elements[$i]['max-time-type'] == 'time' ? esc_html($this->form_elements[$i]['max-time-time']) : ($this->form_elements[$i]['max-time-type'] == 'field' ? esc_html($this->form_elements[$i]['max-time-field']) : ''))."' /></div></div>";
						break;
						
					case "textarea":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-icon-right {line-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$icon."<textarea name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='".($this->form_elements[$i]['align'] != "" ? "lepopup-ta-".esc_html($this->form_elements[$i]['align'])." " : "").esc_html($this->form_elements[$i]["css-class"])."' placeholder='".esc_html($this->form_elements[$i]["placeholder"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' data-default='".esc_html(base64_encode($this->form_elements[$i]["default"]))."' oninput='lepopup_input_changed(this);' onfocus='lepopup_input_error_hide(this);'".($this->form_elements[$i]["readonly"] == 'on' ? " readonly='readonly'" : "").(array_key_exists("maxlength", $this->form_elements[$i]) && intval($this->form_elements[$i]["maxlength"]) > 0 ? " maxlength='".intval($this->form_elements[$i]["maxlength"])."'" : "").">".esc_html($this->form_elements[$i]["default"])."</textarea></div></div>";
						break;

					case "signature":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input {height:auto;} .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input div.lepopup-signature-box {height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."' data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'><input type='hidden' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' value='' /><div class='lepopup-signature-box'><canvas class='lepopup-signature' data-color='".$this->form_options['input-text-style-color']."'></canvas><span><i class='lepopup-if lepopup-if-eraser'></i></span></div></div></div>";
						break;

					case "rangeslider":
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						$options = ($this->form_elements[$i]["readonly"] == "on" ? "data-from-fixed='true' data-to-fixed='true'" : "")." ".($this->form_elements[$i]["double"] == "on" ? "data-type='double'" : "data-type='single'")." ".($this->form_elements[$i]["grid-enable"] == "on" ? "data-grid='true'" : "data-grid='false'")." ".($this->form_elements[$i]["min-max-labels"] == "on" ? "data-hide-min-max='false'" : "data-hide-min-max='true'")." data-skin='".$this->form_options['rangeslider-skin']."' data-min='".$this->form_elements[$i]["range-value1"]."' data-max='".$this->form_elements[$i]["range-value2"]."' data-step='".$this->form_elements[$i]["range-value3"]."' data-from='".$this->form_elements[$i]["handle"]."' data-to='".$this->form_elements[$i]["handle2"]."' data-prefix='".$this->form_elements[$i]["prefix"]."' data-postfix='".$this->form_elements[$i]["postfix"]."' data-input-values-separator=':'";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'"." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input lepopup-rangeslider".esc_html($extra_class)."'><input type='text' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='lepopup-rangeslider ".esc_html($this->form_elements[$i]["css-class"])."' ".$options." data-default='".esc_html($this->form_elements[$i]["handle"].($this->form_elements[$i]["double"] == 'on' ? ':'.$this->form_elements[$i]["handle2"] : ''))."' value='".esc_html($this->form_elements[$i]["handle"].($this->form_elements[$i]["double"] == 'on' ? ':'.$this->form_elements[$i]["handle2"] : ''))."' aria-label='".esc_html($this->form_elements[$i]["name"])."' /></div></div>";
						break;

					case "select":
						$options = "";
						$default = "";
						if ($this->form_elements[$i]["please-select-option"] == "on") $options .= "<option value=''>".esc_html($this->form_elements[$i]["please-select-text"])."</option>";
						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") {
								$selected = " selected='selected'";
								$default = $this->form_elements[$i]["options"][$j]["value"];
							}
							$options .= "<option value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected.">".esc_html($this->form_elements[$i]["options"][$j]["label"])."</option>";
						}
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'><select name='lepopup-".esc_html($this->form_elements[$i]['id'])."' class='".esc_html($this->form_elements[$i]["css-class"])."' data-default='".esc_html($default)."' autocomplete='".esc_html($this->form_elements[$i]["autocomplete"])."' aria-label='".esc_html($this->form_elements[$i]["name"])."' onchange='lepopup_input_changed(this);' onclick='lepopup_input_error_hide(this);'>".$options."</select></div></div>";
						break;

					case "checkbox":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						$properties['checkbox-size'] = $this->form_options['checkbox-radio-style-size'];
						if (empty($this->form_elements[$i]['checkbox-style-position'])) $properties['checkbox-position'] = $this->form_options['checkbox-radio-style-position'];
						else $properties['checkbox-position'] = $this->form_elements[$i]['checkbox-style-position'];
						if (empty($this->form_elements[$i]['checkbox-style-align'])) $properties['checkbox-align'] = $this->form_options['checkbox-radio-style-align'];
						else $properties['checkbox-align'] = $this->form_elements[$i]['checkbox-style-align'];
						if (empty($this->form_elements[$i]['checkbox-style-layout'])) $properties['checkbox-layout'] = $this->form_options['checkbox-radio-style-layout'];
						else $properties['checkbox-layout'] = $this->form_elements[$i]['checkbox-style-layout'];
						$extra_class .= " lepopup-cr-layout-".$properties['checkbox-layout']." lepopup-cr-layout-".$properties['checkbox-align'];
						
						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") $selected = " checked='checked'";
							$option = "<div class='lepopup-cr-box'><input class='lepopup-checkbox lepopup-checkbox-".esc_html($this->form_options["checkbox-view"])." lepopup-checkbox-".esc_html($properties["checkbox-size"])."' type='checkbox' name='lepopup-".esc_html($this->form_elements[$i]['id'])."[]' id='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected." data-default='".(empty($selected) ? 'off' : 'on')."' onchange='lepopup_input_changed(this);' /><label for='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'></label></div>";
							if ($properties['checkbox-position'] == "left") $option .= "<div class='lepopup-cr-label lepopup-ta-".esc_html($properties['checkbox-align'])."'><label for='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".$this->form_elements[$i]["options"][$j]["label"]."</label></div>";
							else $option = "<div class='lepopup-cr-label lepopup-ta-".esc_html($properties['checkbox-align'])."'><label for='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".$this->form_elements[$i]["options"][$j]["label"]."</label></div>".$option;
							$options .= "<div class='lepopup-cr-container lepopup-cr-container-".esc_html($properties["checkbox-size"])." lepopup-cr-container-".esc_html($properties["checkbox-position"])."'>".$option."</div>";
						}
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$options."</div></div>";
						break;

					case "imageselect":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						$properties["image-width"] = intval($this->form_elements[$i]['image-style-width']);
						if ($properties["image-width"] <= 0 || $properties["image-width"] >= 600) $properties["image-width"] = 120;
						$properties["image-height"] = intval($this->form_elements[$i]['image-style-height']);
						if ($properties["image-height"] <= 0 || $properties["image-height"] >= 600) $properties["image-height"] = 120;

						$properties["label-height"] = intval($this->form_elements[$i]['label-height']);
						if ($properties["label-height"] <= 0 || $properties["label-height"] >= 200 || $this->form_elements[$i]['label-enable'] != 'on') $properties["label-height"] = 0;
						
						if ($this->form_options['imageselect-selected-scale'] == 'on') {
							$scale = min(floatval(($properties["image-width"]+8)/$properties["image-width"]), floatval(($properties["image-height"]+8)/$properties["image-height"]));
							$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-imageselect:checked+label {transform: scale(".number_format($scale, 2, '.', '').");}";
						}
						$extra_class .= ' lepopup-ta-'.$this->form_options['imageselect-style-align'].' lepopup-imageselect-'.$this->form_options['imageselect-style-effect'];
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-imageselect+label {width:".esc_html($properties["image-width"])."px;height:".esc_html($properties["image-height"]+$properties["label-height"])."px;}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input .lepopup-imageselect+label  span.lepopup-imageselect-image {height:".esc_html($properties["image-height"])."px;background-size:".esc_html($this->form_elements[$i]['image-style-size']).";}";

						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") $selected = " checked='checked'";
							$properties['image-label'] = "";
							if ($properties["label-height"] > 0) {
								$properties['image-label'] = "<span class='lepopup-imageselect-label'>".esc_html($this->form_elements[$i]["options"][$j]["label"])."</span>";
							}
							$options .= "<input class='lepopup-imageselect' type='".esc_html($this->form_elements[$i]['mode'])."' name='lepopup-".esc_html($this->form_elements[$i]['id']).($this->form_elements[$i]['mode'] == 'checkbox' ? "[]" : "")."' id='".esc_html("lepopup-imageselect-".$id."-".$i."-".$j)."' value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected." data-default='".(empty($selected) ? 'off' : 'on')."' onchange='lepopup_input_changed(this);' /><label for='".esc_html("lepopup-imageselect-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'><span class='lepopup-imageselect-image' style='background-image: url(".esc_html($this->form_elements[$i]["options"][$j]["image"]).");'></span>".$properties['image-label']."</label>";
						}
						if ($this->form_elements[$i]['mode'] == 'radio') $options = '<form>'.$options.'</form>';

						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'>".$options."</div></div>";
						break;

					case "multiselect":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-multiselect {height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						if (!empty($this->form_elements[$i]['align'])) $properties['align'] = $this->form_elements[$i]['align'];
						else if (!empty($this->form_options['multiselect-style-align'])) $properties['align'] = $this->form_options['multiselect-style-align'];
						else $properties['align'] = 'left';
						$options = "";
						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") $selected = " checked='checked'";
							$options .= "<input type='checkbox' name='lepopup-".esc_html($this->form_elements[$i]['id'])."[]' id='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected." data-default='".(empty($selected) ? 'off' : 'on')."' onchange='lepopup_multiselect_changed(this);' /><label for='".esc_html("lepopup-checkbox-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".esc_html($this->form_elements[$i]["options"][$j]["label"])."</label>";
						}
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'><div class='lepopup-input'><div class='lepopup-multiselect lepopup-ta-".esc_html($properties["align"])."' data-max-allowed='".(intval($this->form_elements[$i]['max-allowed']) > 0 ? intval($this->form_elements[$i]['max-allowed']) : '0')."'>".$options."</div></div></div>";
						break;
						
					case "radio":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						$properties['radio-size'] = $this->form_options['checkbox-radio-style-size'];
						if (empty($this->form_elements[$i]['radio-style-position'])) $properties['radio-position'] = $this->form_options['checkbox-radio-style-position'];
						else $properties['radio-position'] = $this->form_elements[$i]['radio-style-position'];
						if (empty($this->form_elements[$i]['radio-style-align'])) $properties['radio-align'] = $this->form_options['checkbox-radio-style-align'];
						else $properties['radio-align'] = $this->form_elements[$i]['radio-style-align'];
						if (empty($this->form_elements[$i]['radio-style-layout'])) $properties['radio-layout'] = $this->form_options['checkbox-radio-style-layout'];
						else $properties['radio-layout'] = $this->form_elements[$i]['radio-style-layout'];
						$extra_class .= " lepopup-cr-layout-".$properties['radio-layout']." lepopup-cr-layout-".$properties['radio-align'];
						
						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") $selected = " checked='checked'";
							$option = "<div class='lepopup-cr-box'><input class='lepopup-radio lepopup-radio-".esc_html($this->form_options["radio-view"])." lepopup-radio-".esc_html($properties["radio-size"])."' type='radio' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' id='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected." data-default='".(empty($selected) ? 'off' : 'on')."' onchange='lepopup_input_changed(this);' /><label for='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'></label></div>";
							if ($properties['radio-position'] == "left") $option .= "<div class='lepopup-cr-label lepopup-ta-".esc_html($properties['radio-align'])."'><label for='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".$this->form_elements[$i]["options"][$j]["label"]."</label></div>";
							else $option = "<div class='lepopup-cr-label lepopup-ta-".esc_html($properties['radio-align'])."'><label for='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".$this->form_elements[$i]["options"][$j]["label"]."</label></div>".$option;
							$options .= "<div class='lepopup-cr-container lepopup-cr-container-".esc_html($properties["radio-size"])." lepopup-cr-container-".esc_html($properties["radio-position"])."'>".$option."</div>";
						}
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'><form>".$options."</form></div></div>";
						break;

					case "tile":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						if (array_key_exists("tile-style-size", $this->form_elements[$i]) && $this->form_elements[$i]['tile-style-size'] != "") $properties['size'] = $this->form_elements[$i]['tile-style-size'];
						else $properties['size'] = $this->form_options['tile-style-size'];
						if (array_key_exists("tile-style-width", $this->form_elements[$i]) && $this->form_elements[$i]['tile-style-width'] != "") $properties['width'] = $this->form_elements[$i]['tile-style-width'];
						else $properties['width'] = $this->form_options['tile-style-width'];
						if (array_key_exists("tile-style-position", $this->form_elements[$i]) && $this->form_elements[$i]['tile-style-position'] != "") $properties['position'] = $this->form_elements[$i]['tile-style-position'];
						else $properties['position'] = $this->form_options['tile-style-position'];
						if (array_key_exists("tile-style-layout", $this->form_elements[$i]) && $this->form_elements[$i]['tile-style-layout'] != "") $properties['layout'] = $this->form_elements[$i]['tile-style-layout'];
						else $properties['layout'] = $this->form_options['tile-style-layout'];
						$extra_class .= " lepopup-tile-layout-".$properties['layout']." lepopup-tile-layout-".$properties['position']." lepopup-tile-transform-".$this->form_options['tile-selected-transform'];
						for ($j=0; $j<sizeof($this->form_elements[$i]["options"]); $j++) {
							$selected = "";
							if (array_key_exists("default", $this->form_elements[$i]["options"][$j]) && $this->form_elements[$i]["options"][$j]["default"] == "on") $selected = " checked='checked'";
							$option = "<div class='lepopup-tile-box'><input class='lepopup-tile lepopup-tile-".esc_html($properties["size"])."' type='".$this->form_elements[$i]['mode']."' name='lepopup-".esc_html($this->form_elements[$i]['id']).($this->form_elements[$i]['mode'] == 'checkbox' ? "[]" : "")."' id='".esc_html("lepopup-tile-".$id."-".$i."-".$j)."' value='".esc_html($this->form_elements[$i]["options"][$j]["value"])."'".$selected." data-default='".(empty($selected) ? 'off' : 'on')."' onchange='lepopup_input_changed(this);' /><label for='".esc_html("lepopup-tile-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'>".esc_html($this->form_elements[$i]["options"][$j]["label"])."</label></div>";
							$options .= "<div class='lepopup-tile-container lepopup-tile-".$properties["width"]."'>".$option."</div>";
						}
						if ($this->form_elements[$i]['mode'] == 'radio') $options = '<form>'.$options.'</form>';

						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input".esc_html($extra_class)."'><form>".$options."</form></div></div>";
						break;
					
					case "star-rating":
						$options = "";
						$id = $lepopup->random_string(16);
						$uids[] = $id;
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." div.lepopup-input{height:auto;line-height:1;}";
						if (!empty($this->form_elements[$i]['star-style-color-unrated'])) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-star-rating>label{color:".esc_sql($this->form_elements[$i]['star-style-color-unrated'])." !important;}";
						if (!empty($this->form_elements[$i]['star-style-color-rated'])) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-star-rating>input:checked~label, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-star-rating:not(:checked)>label:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-star-rating:not(:checked)>label:hover~label{color:".esc_html($this->form_elements[$i]['star-style-color-rated'])." !important;}";
						$options = "";
						for ($j=$this->form_elements[$i]['total-stars']; $j>0; $j--) {
							$options .= "<input type='radio' name='lepopup-".esc_html($this->form_elements[$i]['id'])."' id='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' value='".esc_html($j)."'".($this->form_elements[$i]['default'] == $j ? " checked='checked'" : "")." data-default='".($this->form_elements[$i]['default'] == $j ? 'on' : 'off')."' onchange='lepopup_input_changed(this);' /><label for='".esc_html("lepopup-radio-".$id."-".$i."-".$j)."' onclick='lepopup_input_error_hide(this);'></label>";
						}
						$extra_class = "";
						if (!empty($this->form_elements[$i]['star-style-size'])) $extra_class .= " lepopup-star-rating-".$this->form_elements[$i]['star-style-size'];
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-deps='".(array_key_exists($this->form_elements[$i]['id'], $this->form_dependencies) ? esc_html(implode(',', $this->form_dependencies[$this->form_elements[$i]['id']])) : '')."'".($this->form_elements[$i]['dynamic-default'] == 'on' ? " data-dynamic='".esc_html($this->form_elements[$i]['dynamic-parameter'])."'" : "")." data-id='".esc_html($this->form_elements[$i]['id'])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><div class='lepopup-input'><form><fieldset class='lepopup-star-rating".esc_html($extra_class)."'>".$options."</fieldset></form></div></div>";
						break;
					
					case "html":
						$data = array(
							'{{url}}' => '<span class="lepopup-const lepopup-const-url" data-id="url"></span>',
							'{{page-title}}' => '<span class="lepopup-const lepopup-const-page-title" data-id="page-title"></span>',
							'{{ip}}' => '<span class="lepopup-const lepopup-const-ip" data-id="ip"></span>',
							'{{record-id}}' => '<span class="lepopup-const lepopup-const-record-id" data-id="record-id"></span>',
							'{{user-agent}}' => '<span class="lepopup-const lepopup-const-user-agent" data-id="user-agent"></span>',
							'{{date}}' => '<span class="lepopup-const lepopup-const-date" data-id="date"></span>',
							'{{time}}' => '<span class="lepopup-const lepopup-const-time" data-id="time"></span>',
							'{{wp-user-login}}' => '<span class="lepopup-const lepopup-const-wp-user-login" data-id="wp-user-login"></span>',
							'{{wp-user-email}}' => '<span class="lepopup-const lepopup-const-wp-user-email" data-id="wp-user-email"></span>',
						);
						preg_match_all('/{{(\d+)(|.+?)}}/' , $this->form_elements[$i]["content"], $matches);
						for ($j=0; $j<sizeof($matches[0]); $j++) {
							if (!empty($matches[0][$j]) && !empty($matches[1][$j])) {
								$data[$matches[0][$j]] = '<span class="lepopup-var lepopup-var-'.esc_html($matches[1][$j]).'" data-id="'.esc_html($matches[1][$j]).'"></span>';
							}
						}
						$content = strtr($this->form_elements[$i]["content"], $data);
						$content = do_shortcode($content);
						$content = $lepopup->close_html_tags($content);
						if (defined("HALFDATA_DEMO") && HALFDATA_DEMO == true) {
							$content = preg_replace("/<script.*?>(.*)?<\/script>/im", '', $content);
						}
						$content_lower = strtolower($content);
						$base64_content = '';
						if (strpos($content_lower, '<iframe') !== false || strpos($content_lower, '<video') !== false || strpos($content_lower, '<audio') !== false) {
							$base64_content = base64_encode($content);
							$content = '';
						}
						$text_style = $this->_build_style_text($this->form_elements[$i], "text-style");
						if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
						$style_attr = $text_style["style"];
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." * {".$style_attr."}";
						$style_attr .= $this->_build_style_background($this->form_elements[$i], "background-style");
						$style_attr .= $this->_build_style_border($this->form_elements[$i], "border-style");
						$style_attr .= $this->_build_shadow($this->form_elements[$i], "shadow");
						$style_attr .= $this->_build_style_padding($this->form_elements[$i], "padding");
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." {".$style_attr."}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-element-html-content {min-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-html".($this->form_elements[$i]["scrollable"] == "on" ? " lepopup-element-html-scrollable" : "")."' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'".(!empty($base64_content) ? " data-content='".esc_html($base64_content)."'" : "")."><div class='lepopup-element-html-content'>".$content."</div></div>";
						break;

					case "video":
						$base64_content = '';
						$content = '';
						preg_match_all('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/is' , $this->form_elements[$i]["content"], $matches);
						if (is_array($matches) && is_array($matches[0]) && sizeof($matches[0])) {
							$properties['iframe-attr'] = "style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;'";
							$dom = new DomDocument();
							$dom->loadHtml($matches[0][0]);
							$iframe_html = $dom->getElementsByTagName('iframe')->item(0);
							if ($iframe_html->hasAttributes()){
								foreach ($iframe_html->attributes as $attr) {
									$name = strtolower($attr->nodeName);
									if ($name != 'style' && $name != 'width' && $name != 'height') {
										$properties['iframe-attr'] .= ' '.esc_html($name).'="'.esc_html($attr->nodeValue).'"';
									}
								}
							}
							$base64_content = base64_encode("<iframe ".$properties['iframe-attr']."></iframe>");
						} else {
							preg_match_all('/<video\b[^<]*(?:(?!<\/video>)<[^<]*)*<\/video>/is' , $this->form_elements[$i]["content"], $matches);
							if (is_array($matches) && is_array($matches[0]) && sizeof($matches[0])) {
								$properties['video-attr'] = "style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;'";
								$dom = new DomDocument();
								$dom->loadHtml($matches[0][0]);
								$video_html = $dom->getElementsByTagName('video')->item(0);
								if ($video_html->hasAttributes()){
									foreach ($video_html->attributes as $attr) {
										$name = strtolower($attr->nodeName);
										if ($name != 'style' && $name != 'width' && $name != 'height') {
											$properties['video-attr'] .= ' '.esc_html($name).'="'.esc_html($attr->nodeValue).'"';
										}
									}
								}
								$properties['video-children'] = "";
								preg_match('/<video[^>]*>(.*?)<\/video>/is', $matches[0][0], $matches2);
								if (is_array($matches2) && sizeof($matches2) > 1) $properties['video-children'] = $matches2[1];
								$base64_content = base64_encode("<video ".$properties['video-attr'].">".$properties['video-children']."</video>");
							} else {
								try {
									$url = parse_url($this->form_elements[$i]["content"]);
									if (is_array($url) && array_key_exists('host', $url)) {
										if ($url['host'] == "youtu.be" || $url['host'] == "www.youtu.be") $base64_content = base64_encode("<iframe style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;' src='https://www.youtube.com/embed".$url['path']."'></iframe>");
										else if ($url['host'] == "youtube.com" || $url['host'] == "www.youtube.com") {
											parse_str($url['query'], $url_params);
											if (is_array($url_params) && array_key_exists('v', $url_params)) $base64_content = base64_encode("<iframe style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;' src='https://www.youtube.com/embed/".$url_params['v']."'></iframe>");
										} else if ($url['host'] == "vimeo.com" || $url['host'] == "www.vimeo.com") {
											if (strlen($url['path']) > 1) {
												$video_id = substr($url['path'], 1);
												if (is_numeric($video_id)) $base64_content = base64_encode("<iframe style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;' src='https://player.vimeo.com/video".$url['path']."'></iframe>");
											}
										} else {
											$base64_content = base64_encode("<video style='height:".esc_html($this->form_elements[$i]['size-height'])."px;width:".esc_html($this->form_elements[$i]['size-width'])."px;' src='".esc_html($this->form_elements[$i]["content"])."'></video>");
										}
									}
								} catch (Exception $e) {
								}
							}
						}
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." .lepopup-element-html-content {min-height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-html' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'".(!empty($base64_content) ? " data-content='".esc_html($base64_content)."'" : "")."><div class='lepopup-element-html-content'>".$content."</div></div>";
						break;

					case "label":
						$data = array(
							'{{url}}' => '<span class="lepopup-const lepopup-const-url" data-id="url"></span>',
							'{{page-title}}' => '<span class="lepopup-const lepopup-const-page-title" data-id="page-title"></span>',
							'{{ip}}' => '<span class="lepopup-const lepopup-const-ip" data-id="ip"></span>',
							'{{record-id}}' => '<span class="lepopup-const lepopup-const-record-id" data-id="record-id"></span>',
							'{{user-agent}}' => '<span class="lepopup-const lepopup-const-user-agent" data-id="user-agent"></span>',
							'{{date}}' => '<span class="lepopup-const lepopup-const-date" data-id="date"></span>',
							'{{time}}' => '<span class="lepopup-const lepopup-const-time" data-id="time"></span>',
							'{{wp-user-login}}' => '<span class="lepopup-const lepopup-const-wp-user-login" data-id="wp-user-login"></span>',
							'{{wp-user-email}}' => '<span class="lepopup-const lepopup-const-wp-user-email" data-id="wp-user-email"></span>',
						);
						$content = esc_html($this->form_elements[$i]["content"]);
						preg_match_all('/{{(\d+)(|.+?)}}/' , $content, $matches);
						for ($j=0; $j<sizeof($matches[0]); $j++) {
							if (!empty($matches[0][$j]) && !empty($matches[1][$j])) {
								$data[$matches[0][$j]] = '<span class="lepopup-var lepopup-var-'.esc_html($matches[1][$j]).'" data-id="'.esc_html($matches[1][$j]).'"></span>';
							}
						}
						$content = strtr($content, $data);
						$text_style = $this->_build_style_text($this->form_elements[$i], "text-style");
						if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
						$style_attr = $text_style["style"];
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id']).", .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." * {".$style_attr."}";
						$properties['extra_attr'] = '';
						$custom_onclick = '';
						$url = trim($this->form_elements[$i]['link']);
						if (array_key_exists('onclick', $this->form_elements[$i]) && !empty($this->form_elements[$i]['onclick'])) $custom_onclick = rtrim(str_replace("'", "`", trim($this->form_elements[$i]['onclick'])), ';').'; ';
						if ($this->form_elements[$i]['close'] == "period") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(".intval($this->form_elements[$i]['cookie-lifetime']).");".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "forever") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(360);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "single") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(0);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if (!empty($custom_onclick)) $properties['extra_attr'] .= " onclick='".$custom_onclick."'";
						if ($url == "#" || (!empty($properties['extra_attr']) && empty($url))) $properties['extra_attr'] .= " href='#'";
						else if (!empty($url)) $properties['extra_attr'] .= " href='".esc_html($url)."'".($this->form_elements[$i]['new-tab'] == "on" ? " target='_blank'" : "");
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-html' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'>".(!empty($properties['extra_attr']) ? "<a".$properties['extra_attr'].">".$content."</a>" : $content)."</div>";
						break;

					case "rectangle":
						$style_attr = $this->_build_style_background($this->form_elements[$i], "background-style");
						$style_attr .= $this->_build_style_border($this->form_elements[$i], "border-style");
						$style_attr .= $this->_build_shadow($this->form_elements[$i], "shadow");
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." {".$style_attr."}";
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-rectangle' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'></div>";
						break;

					case "image":
						$style_attr = $this->_build_style_background($this->form_elements[$i], "image-style");
						$style_attr .= $this->_build_style_border($this->form_elements[$i], "border-style");
						$style_attr .= $this->_build_shadow($this->form_elements[$i], "shadow");
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." {".$style_attr."}";
						$properties['extra_attr'] = '';
						$custom_onclick = '';
						$url = trim($this->form_elements[$i]['link']);
						if (array_key_exists('onclick', $this->form_elements[$i]) && !empty($this->form_elements[$i]['onclick'])) $custom_onclick = rtrim(str_replace("'", "`", trim($this->form_elements[$i]['onclick'])), ';').'; ';
						if ($this->form_elements[$i]['close'] == "period") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(".intval($this->form_elements[$i]['cookie-lifetime']).");".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "forever") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(360);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "single") $properties['extra_attr'] .= " onclick='".$custom_onclick."lepopup_popup_active_close(0);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if (!empty($custom_onclick)) $properties['extra_attr'] .= " onclick='".$custom_onclick."'";
						if ($url == "#" || (!empty($properties['extra_attr']) && empty($url))) $properties['extra_attr'] .= " href='#'";
						else if (!empty($url)) $properties['extra_attr'] .= " href='".esc_html($url)."'".($this->form_elements[$i]['new-tab'] == "on" ? " target='_blank'" : "");
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-rectangle' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-height']) > 0 ? intval($this->form_elements[$i]['size-height'])."px" : "auto").";'>".(!empty($properties['extra_attr']) ? "<a class='lepopup-inherited'".$properties['extra_attr'].">&nbsp;</a>" : "")."</div>";
						break;

					case "close":
						if ($this->form_elements[$i]['colors-color3'] != "") $shadow = "text-shadow:1px 1px 1px ".esc_html($this->form_elements[$i]['colors-color3']).";";
						else $shadow = "";
						if ($this->form_elements[$i]['colors-color1'] != "") $color = "color:".esc_html($this->form_elements[$i]['colors-color1']).";";
						else $color = "";
						if ($this->form_elements[$i]['colors-color2'] != "") $color_hover = "color:".esc_html($this->form_elements[$i]['colors-color2']).";";
						else $color_hover = "";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." span {font-size:".intval($this->form_elements[$i]['size-width'])."px;".$color.$shadow."}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." span i {".$color."}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." span:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." span:hover i {".$color_hover."}";
						if ($this->form_elements[$i]['view'] == "fa-1") $icon = '<i class="lepopup-if lepopup-if-times"></i>';
						else if ($this->form_elements[$i]['view'] == "fa-2") $icon = '<i class="lepopup-if lepopup-if-cancel-circled"></i>';
						else if ($this->form_elements[$i]['view'] == "fa-3") $icon = '<i class="lepopup-if lepopup-if-cancel-circled2"></i>';
						else $icon = '×';
						if ($this->form_elements[$i]['mode'] == "period") $cookie_lifetime = intval($this->form_elements[$i]['cookie-lifetime']);
						else if ($this->form_elements[$i]['mode'] == "forever") $cookie_lifetime = 360;
						else $cookie_lifetime = 0;
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-close' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><span onclick='return lepopup_popup_active_close(".$cookie_lifetime.");'>".$icon."</span></div>";
						break;

					case "fa-icon":
						if ($this->form_elements[$i]['colors-color3'] != "") $shadow = "text-shadow:1px 1px 1px ".esc_html($this->form_elements[$i]['colors-color3']).";";
						else $shadow = "";
						if ($this->form_elements[$i]['colors-color1'] != "") $color = "color:".esc_html($this->form_elements[$i]['colors-color1']).";";
						else $color = "";
						if ($this->form_elements[$i]['colors-color2'] != "") $color_hover = "color:".esc_html($this->form_elements[$i]['colors-color2']).";";
						else $color_hover = "";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a {font-size:".intval($this->form_elements[$i]['size-width'])."px;".$color.$shadow."}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a i {".$color."}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element-".esc_html($this->form_elements[$i]['id'])." a:hover i {".$color_hover."}";

						$url = trim($this->form_elements[$i]['link']);
						if (empty($url) || $url == "#") $properties['extra_attr'] = " href='#'";
						else $properties['extra_attr'] = " href='".esc_html($url)."'".($this->form_elements[$i]['new-tab'] == "on" ? " target='_blank'" : "");
						if ($this->form_elements[$i]['close'] == "period") $properties['extra_attr'] .= " onclick='lepopup_popup_active_close(".intval($this->form_elements[$i]['cookie-lifetime']).");".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "forever") $properties['extra_attr'] .= " onclick='lepopup_popup_active_close(360);".(empty($url) || $url == "#" ? 'return false;' : '')."'";
						else if ($this->form_elements[$i]['close'] == "single") $properties['extra_attr'] .= " onclick='lepopup_popup_active_close(0);".(empty($url) || $url == "#" ? 'return false;' : '')."'";

						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-icon' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";height:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'><a".$properties['extra_attr']."><i class='".$this->form_elements[$i]['icon']."'></i></a></div>";
						break;
					
					case "progress":
						$content = "";
						$total_pages = $this->form_options["progress-confirmation-enable"] == "on" ? 0 : -1;
						$total_pages += sizeof($this->form_pages);
						$idx = 0;
						foreach ($this->form_pages as $key => $page) {
							if ($page['id'] == $this->form_elements[$i]['_parent']) {
								$idx = $key;
								break;
							}
						}
						if ($this->form_options["progress-type"] == 'progress-2') {
							$content = "<div class='lepopup-progress lepopup-progress-".esc_html($this->id)."'><ul class='lepopup-progress-t2".($this->form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")."'>";
							for($j=0; $j<$total_pages; $j++) {
								$content .= "<li".($j < $idx ? " class='lepopup-progress-t2-passed'" : ($j == $idx ? " class='lepopup-progress-t2-active'" : ""))." style='width:".number_format(floor(10000/$total_pages)/100, 2, '.', '')."%;'><span>".($j+1)."</span>".($this->form_options["progress-label-enable"] == "on" ? "<label>".esc_html($this->form_pages[$j]['name'])."</label>" : "")."</li>";
							}
							$content .= "</ul></div>";
						} else {
							$width = intval(100*($idx+1)/$total_pages);
							$content = "<div class='lepopup-progress lepopup-progress-".esc_html($this->id)."'><div class='lepopup-progress-t1".($this->form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")."'><div><div style='width:".esc_html($width)."%'>".esc_html($width)."%</div></div>".($this->form_options["progress-label-enable"] == "on" ? "<label>".esc_html($this->form_pages[$idx]['name'])."</label>" : "")."</div></div>";
						}
						$html .= "<div class='lepopup-element lepopup-element-".esc_html($this->form_elements[$i]['id'])." lepopup-element-progress' data-type='".esc_html($this->form_elements[$i]["type"])."' data-top='".intval($this->form_elements[$i]['position-top'])."' data-left='".intval($this->form_elements[$i]['position-left'])."' data-animation-in='".esc_html($this->form_elements[$i]["animation-in"])."' data-animation-out='".esc_html($this->form_elements[$i]["animation-out"])."' style='animation-duration:".intval($this->form_elements[$i]['animation-duration'])."ms;animation-delay:".intval($this->form_elements[$i]['animation-delay'])."ms;z-index:".intval($zindex_base+$seqs[$k]).";top:".intval($this->form_elements[$i]['position-top'])."px;left:".intval($this->form_elements[$i]['position-left'])."px;width:".(intval($this->form_elements[$i]['size-width']) > 0 ? intval($this->form_elements[$i]['size-width'])."px" : "auto").";'>".$content."</div>";
						break;

					default:
						break;
				}
			}
			if (array_key_exists("css", $this->form_elements[$i]) && sizeof($this->form_elements[$i]["css"]) > 0) {
				if (array_key_exists($this->form_elements[$i]["type"], $lepopup->element_properties_meta) && array_key_exists("css", $lepopup->element_properties_meta[$this->form_elements[$i]["type"]])) {
					for ($j=0; $j<sizeof($this->form_elements[$i]["css"]); $j++) {
						if (!empty($this->form_elements[$i]["css"][$j]["css"]) && !empty($this->form_elements[$i]["css"][$j]["selector"])) {
							if (array_key_exists($this->form_elements[$i]["css"][$j]["selector"], $lepopup->element_properties_meta[$this->form_elements[$i]["type"]]["css"]["selectors"])) {
								$properties["css-class"] = $lepopup->element_properties_meta[$this->form_elements[$i]["type"]]["css"]["selectors"][$this->form_elements[$i]["css"][$j]["selector"]]["front-class"];
								$properties["css-class"] = str_replace(array("{element-id}", "{form-id}"), array($this->form_elements[$i]['id'], $this->id), $properties["css-class"]);
								$style .= $properties["css-class"]."{".esc_html($this->form_elements[$i]["css"][$j]["css"])."}";
							}
						}
					}
				}
			}
		}
		return array("html" => $html, "style" => $style, "webfonts" => $webfonts, 'uids' => $uids);
	}
	protected function _prepare_ranges($_ranges) {
		$raw_ranges = explode(',', $_ranges);
		$sanitized_ranges = array();
		foreach ($raw_ranges as $range) {
			$range = trim($range);
			if (strlen($range) == 0) continue;
			$range_parts = explode('...', $range);
			if (sizeof($range_parts) == 1) {
				$range_parts[0] = trim($range_parts[0]);
				if (strlen($range_parts[0]) > 0 && is_numeric($range_parts[0])) $sanitized_ranges[] = $range_parts[0];
				else continue;
			} else if (sizeof($range_parts) == 2) {
				$range_parts[0] = trim($range_parts[0]);
				$range_parts[1] = trim($range_parts[1]);
				if (strlen($range_parts[0]) == 0) $range_parts[0] = -2147483648;
				else if (!is_numeric($range_parts[0])) continue;
				if (strlen($range_parts[1]) == 0) $range_parts[1] = 2147483647;
				else if (!is_numeric($range_parts[1])) continue;
				if ($range_parts[0] < $range_parts[1]) $sanitized_ranges[] = $range_parts[0].'...'.$range_parts[1];
				else if ($range_parts[0] > $range_parts[1]) $sanitized_ranges[] = $range_parts[1].'...'.$range_parts[0];
				else $sanitized_ranges[] = $range_parts[0];
			} else continue;
		}
		do {
			$finish = true;
			for ($i=0; $i<sizeof($sanitized_ranges)-1; $i++) {
				$range = explode('...', $sanitized_ranges[$i]);
				$val1 = $range[0];
				$range = explode('...', $sanitized_ranges[$i+1]);
				$val2 = $range[0];
				if ($val2 < $val1) {
					$val1 = $sanitized_ranges[$i];
					$sanitized_ranges[$i] = $sanitized_ranges[$i+1];
					$sanitized_ranges[$i+1] = $val1;
					$finish = false;
				}
			}
			
		} while ($finish === false);
		do {
			$finish = true;
			for ($i=0; $i<sizeof($sanitized_ranges)-1; $i++) {
				$range1 = explode('...', $sanitized_ranges[$i]);
				if (sizeof($range1) == 1) $range1[1] = $range1[0];
				$range2 = explode('...', $sanitized_ranges[$i+1]);
				if (sizeof($range2) == 1) $range2[1] = $range2[0];
				if ($range1[1] >= $range2[0]) {
					$max = max($range1[1], $range2[1]);
					if ($range1[0] == $max) $sanitized_ranges[$i+1] = $max;
					else $sanitized_ranges[$i+1] = $range1[0].'...'.$max;
					unset($sanitized_ranges[$i]);
					$finish = false;
				}
			}
			$sanitized_ranges = array_values($sanitized_ranges);
		} while ($finish === false);
		return implode(',', $sanitized_ranges);
	}
	protected function _build_style_text($_options, $_key, $_important = false) {
		global $lepopup;
		$style = "";
		if (array_key_exists($_key."-family", $_options) && $_options[$_key."-family"] != "") {
			$style .= "font-family:'".esc_html($_options[$_key."-family"])."','arial'".($_important ? " !important" : "").";";
		}
		if (array_key_exists($_key."-size", $_options)) {
			$size = intval($_options[$_key."-size"]);
			if ($size >= 8 && $size <= 256) $style .= "font-size:".esc_html($size)."px".($_important ? " !important" : "").";";
		}
		if (array_key_exists($_key."-color", $_options) && $_options[$_key."-color"] != "") $style .= "color:".esc_html($_options[$_key."-color"]).($_important ? " !important" : "").";";
		if (!array_key_exists($_key."-weight", $_options) || $_options[$_key."-weight"] == "") {
			if (array_key_exists($_key."-bold", $_options) && $_options[$_key."-bold"] == "on") $style .= "font-weight:bold".($_important ? " !important" : "").";";
			else $style .= "font-weight:normal".($_important ? " !important" : "").";";
		} else if ($_options[$_key."-weight"] != "inherit") $style .= "font-weight:".$_options[$_key."-weight"].($_important ? " !important" : "").";";
		if (array_key_exists($_key."-italic", $_options) && $_options[$_key."-italic"] == "on") $style .= "font-style:italic".($_important ? " !important" : "").";";
		else $style .= "font-style:normal".($_important ? " !important" : "").";";
		if (array_key_exists($_key."-underline", $_options) && $_options[$_key."-underline"] == "on") $style .= "text-decoration:underline".($_important ? " !important" : "").";";
		else $style .= "text-decoration:none".($_important ? " !important" : "").";";
		if (array_key_exists($_key."-align", $_options) && $_options[$_key."-align"] != "") $style .= "text-align:".esc_html($_options[$_key."-align"]).";";
		return array("style" => $style, "webfont" => $_options[$_key."-family"]);
	}
	protected function _build_style_background($_options, $_key, $_important = false) {
		global $lepopup;
		$style = "";
		$hposition = "left";
		$vposition = "top";
		$color1 = "transparent";
		$color2 = "transparent";
		$direction = "to bottom";
		if (array_key_exists($_key."-color", $_options) && $_options[$_key."-color"] != "") $color1 = $_options[$_key."-color"];
		
		if (array_key_exists($_key."-gradient", $_options) && $_options[$_key."-gradient"] == "2shades") {
			$style .= "background-color:".$color1.($_important ? " !important" : "").";background-image:linear-gradient(to bottom,rgba(255,255,255,.05) 0,rgba(255,255,255,.05) 50%,rgba(0,0,0,.05) 51%,rgba(0,0,0,.05) 100%)".($_important ? " !important" : "").";";
		} else if (array_key_exists($_key."-gradient", $_options) && ($_options[$_key."-gradient"] == "horizontal" || $_options[$_key."-gradient"] == "vertical" || $_options[$_key."-gradient"] == "diagonal")) {
			if (array_key_exists($_key."-color2", $_options) && $_options[$_key."-color2"] != "") $color2 = $_options[$_key."-color2"];
			if ($_options[$_key."-gradient"] == "horizontal") $direction = "to right";
			else if ($_options[$_key."-gradient"] == "diagonal") $direction = "to bottom right";
			$style .= "background-image:linear-gradient(".$direction.",".$color1.",".$color2.")".($_important ? " !important" : "").";";
		} else if (array_key_exists($_key."-image", $_options) && $_options[$_key."-image"] != "") {
			$style .= "background-color:".$color1.($_important ? " !important" : "").";background-image:url('".esc_html($_options[$_key."-image"])."')".($_important ? " !important" : "").";";
			if (array_key_exists($_key."-size", $_options) && $_options[$_key."-size"] != "") $style .= "background-size:".esc_html($_options[$_key."-size"]).($_important ? " !important" : "").";";
			if (array_key_exists($_key."-repeat", $_options) && $_options[$_key."-repeat"] != "") $style .= "background-repeat:".esc_html($_options[$_key."-repeat"]).($_important ? " !important" : "").";";
			if (array_key_exists($_key."-horizontal-position", $_options) && $_options[$_key."-horizontal-position"] != "") {
				switch ($_options[$_key."-horizontal-position"]) {
					case 'center':
						$hposition = "center";
						break;
					case 'right':
						$hposition = "right";
						break;
					default:
						$hposition = "left";
						break;
				}
			}
			if (array_key_exists($_key."-vertical-position", $_options) && $_options[$_key."-vertical-position"] != "") {
				switch ($_options[$_key."-vertical-position"]) {
					case 'middle':
						$vposition = "center";
						break;
					case 'bottom':
						$vposition = "bottom";
						break;
					default:
						$vposition = "top";
						break;
				}
			}
			$style .= "background-position: ".$hposition." ".$vposition.($_important ? " !important" : "").";";
		} else $style .= "background-color:".$color1.($_important ? " !important" : "").";background-image:none".($_important ? " !important" : "").";";
		return $style;
	}
	protected function _build_style_border($_options, $_key, $_important = false) {
		global $lepopup;
		$style = "";
		if (array_key_exists($_key."-width", $_options)) {
			$size = intval($_options[$_key."-width"]);
			if ($size >= 0 && $size <= 16) $style .= "border-width:".esc_html($size)."px".($_important ? " !important" : "").";";
		}
		if (array_key_exists($_key."-style", $_options) && $_options[$_key."-style"] != "") $style .= "border-style:".esc_html($_options[$_key."-style"]).($_important ? " !important" : "").";";
		if (array_key_exists($_key."-color", $_options) && $_options[$_key."-color"] != "") $style .= "border-color:".esc_html($_options[$_key."-color"]).($_important ? " !important" : "").";";
		else $style .= "border-color:transparent".($_important ? " !important" : "").";";
		if (array_key_exists($_key."-radius", $_options)) {
			if ($_options[$_key."-radius"] == 'max') {
				$style .= "border-radius:800px".($_important ? " !important" : "").";";
			} else {
				$size = intval($_options[$_key."-radius"]);
				if ($size >= 0 && $size <= 100) $style .= "border-radius:".esc_html($size)."px".($_important ? " !important" : "").";";
			}
		}
		if (array_key_exists($_key."-top", $_options) && $_options[$_key."-top"] != "on") $style .= "border-top:none !important;";
		if (array_key_exists($_key."-left", $_options) && $_options[$_key."-left"] != "on") $style .= "border-left:none !important;";
		if (array_key_exists($_key."-right", $_options) && $_options[$_key."-right"] != "on") $style .= "border-right:none !important;";
		if (array_key_exists($_key."-bottom", $_options) && $_options[$_key."-bottom"] != "on") $style .= "border-bottom:none !important;";
		return $style;
	}
	protected function _build_shadow($_options, $_key, $_important = false) {
		global $lepopup;
		$style = "box-shadow:none;";
		$color = "transparent";
		$shadow_style = "regular";
		if (array_key_exists($_key."-size", $_options) && $_options[$_key."-size"] != "") {
			if (array_key_exists($_key."-color", $_options) && $_options[$_key."-color"] != "") $color = $_options[$_key."-color"];
			if (array_key_exists($_key."-style", $_options) && $_options[$_key."-style"] != "") $shadow_style = $_options[$_key."-style"];
			switch ($shadow_style) {
				case 'solid':
					if ($_options[$_key."-size"] == "tiny") $style = "box-shadow: 1px 1px 0px 0px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "small") $style = "box-shadow: 2px 2px 0px 0px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "medium") $style = "box-shadow: 4px 4px 0px 0px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "large") $style = "box-shadow: 6px 6px 0px 0px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "huge") $style = "box-shadow: 8px 8px 0px 0px ".esc_html($color).($_important ? " !important" : "").";";
					break;
				case 'inset':
					if ($_options[$_key."-size"] == "tiny") $style = "box-shadow: inset 0px 0px 15px -9px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "small") $style = "box-shadow: inset 0px 0px 15px -8px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "medium") $style = "box-shadow: inset 0px 0px 15px -7px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "large") $style = "box-shadow: inset 0px 0px 15px -6px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "huge") $style = "box-shadow: inset 0px 0px 15px -5px ".esc_html($color).($_important ? " !important" : "").";";
					break;
				default:
					if ($_options[$_key."-size"] == "tiny") $style = "box-shadow: 1px 1px 15px -9px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "small") $style = "box-shadow: 1px 1px 15px -8px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "medium") $style = "box-shadow: 1px 1px 15px -6px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "large") $style = "box-shadow: 1px 1px 15px -3px ".esc_html($color).($_important ? " !important" : "").";";
					else if ($_options[$_key."-size"] == "huge") $style = "box-shadow: 1px 1px 15px 0px ".esc_html($color).($_important ? " !important" : "").";";
					break;
			}
		}
		return $style;
	}
	protected function _build_style_padding($_options, $_key, $_spacing = 0) {
		global $lepopup;
		$style = "";
		if (array_key_exists($_key."-top", $_options)) {
			$integer = max(intval($_options[$_key."-top"])-$_spacing, 0);
			if ($integer >= 0 && $integer <= 300) $style .= "padding-top:".esc_html($integer)."px;";
		}
		if (array_key_exists($_key."-right", $_options)) {
			$integer = max(intval($_options[$_key."-right"])-$_spacing, 0);
			if ($integer >= 0 && $integer <= 300) $style .= "padding-right:".esc_html($integer)."px;";
		}
		if (array_key_exists($_key."-bottom", $_options)) {
			$integer = max(intval($_options[$_key."-bottom"])-$_spacing, 0);
			if ($integer >= 0 && $integer <= 300) $style .= "padding-bottom:".esc_html($integer)."px;";
		}
		if (array_key_exists($_key."-left", $_options)) {
			$integer = max(intval($_options[$_key."-left"])-$_spacing, 0);
			if ($integer >= 0 && $integer <= 300) $style .= "padding-left:".esc_html($integer)."px;";
		}
		return $style;
	}

	function lepopup_build_progress($_current_page, $_uid) {
		$html = "";
		$total_pages = $this->form_options["progress-confirmation-enable"] == "on" ? 0 : -1;
		$total_pages += sizeof($this->form_pages);
		if ($this->form_options["progress-enable"] == "on" && ($this->form_pages[$_current_page]['type'] != 'page-confirmation' || $this->form_options["progress-confirmation-enable"] == "on")) {
			if ($this->form_options["progress-type"] == 'progress-2') {
				$html = "<div class='lepopup-progress lepopup-progress-".esc_html($this->form_options["progress-position"])." lepopup-progress-".esc_html($this->id)." lepopup-progress-".esc_html($_uid)."' data-page=".esc_html($this->form_pages[$_current_page]['id'])."><ul class='lepopup-progress-t2".($this->form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")."'>";
				for($i=0; $i<$total_pages; $i++) {
					$html .= "<li".($i < $_current_page ? " class='lepopup-progress-t2-passed'" : ($i == $_current_page ? " class='lepopup-progress-t2-active'" : ""))." style='width:".number_format(floor(10000/$total_pages)/100, 2, '.', '')."%;'><span>".($i+1)."</span>".($this->form_options["progress-label-enable"] == "on" ? "<label>".esc_html($this->form_pages[$i]['name'])."</label>" : "")."</li>";
				}
				$html .= "</ul></div>";
			} else {
				$width = intval(100*($_current_page+1)/$total_pages);
				$html = "<div class='lepopup-progress lepopup-progress-".esc_html($this->form_options["progress-position"])." lepopup-progress-".esc_html($this->id)." lepopup-progress-".esc_html($_uid)."' data-page=".esc_html($this->form_pages[$_current_page]['id'])."><div class='lepopup-progress-t1".($this->form_options["progress-striped"] == "on" ? " lepopup-progress-stripes" : "")."'><div><div style='width:".esc_html($width)."%'>".esc_html($width)."%</div></div>".($this->form_options["progress-label-enable"] == "on" ? "<label>".esc_html($this->form_pages[$_current_page]['name'])."</label>" : "")."</div></div>";
			}
		}
		return $html;
	}

	public function get_form_html() {
		global $wpdb, $lepopup;

		if (empty($this->id)) return false;
/*		
		$update_time = get_option('lepopup-update-time', time());
		if ($update_time < $this->cache_time && !empty($this->cache_html) && !empty($this->cache_style)) {
			$style = $this->cache_style;
			$html = $this->cache_html;
			if (is_array($this->cache_uids) && !empty($this->cache_uids)) {
				foreach ($this->cache_uids as $uid) {
					$new_uid = $lepopup->random_string(17);
					$style = str_replace($uid, $new_uid, $style);
					$html = str_replace($uid, $new_uid, $html);
				}
			}
			return array('style' => $style, 'html' => $html);
		}
*/		
		$style = '';
		$html = '';

		if ($this->form_options["progress-type"] == 'progress-2') {
			if (array_key_exists("progress-color-color1", $this->form_options) && $this->form_options["progress-color-color1"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2,.lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li>span{background-color:".esc_html($this->form_options["progress-color-color1"]).";}.lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li>label{color:".esc_html($this->form_options["progress-color-color1"]).";}";
			if (array_key_exists("progress-color-color2", $this->form_options) && $this->form_options["progress-color-color2"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li.lepopup-progress-t2-active>span,.lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li.lepopup-progress-t2-passed>span{background-color:".esc_html($this->form_options["progress-color-color2"]).";}";
			if (array_key_exists("progress-color-color3", $this->form_options) && $this->form_options["progress-color-color3"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li>span{color:".esc_html($this->form_options["progress-color-color3"]).";}";
			if (array_key_exists("progress-color-color4", $this->form_options) && $this->form_options["progress-color-color4"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." ul.lepopup-progress-t2>li.lepopup-progress-t2-active>label{color:".esc_html($this->form_options["progress-color-color4"]).";}";
		} else {
			if (array_key_exists("progress-color-color1", $this->form_options) && $this->form_options["progress-color-color1"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." div.lepopup-progress-t1>div{background-color:".esc_html($this->form_options["progress-color-color1"]).";}";
			if (array_key_exists("progress-color-color2", $this->form_options) && $this->form_options["progress-color-color2"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." div.lepopup-progress-t1>div>div{background-color:".esc_html($this->form_options["progress-color-color2"]).";}";
			if (array_key_exists("progress-color-color3", $this->form_options) && $this->form_options["progress-color-color3"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." div.lepopup-progress-t1>div>div{color:".esc_html($this->form_options["progress-color-color3"]).";}";
			if (array_key_exists("progress-color-color4", $this->form_options) && $this->form_options["progress-color-color4"] != "") $style .= ".lepopup-progress-".esc_html($this->id)." div.lepopup-progress-t1>label{color:".esc_html($this->form_options["progress-color-color4"]).";}";
		}
		
		$important = $lepopup->advanced_options['important-enable'] == 'on' ? true : false;
		
		$webfonts = array();
		$text_style = $this->_build_style_text($this->form_options, "text-style");
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style_attr = $text_style["style"];
		$style .= ".lepopup-form-".esc_html($this->id).", .lepopup-form-".esc_html($this->id)." *, .lepopup-progress-".esc_html($this->id)." {".$style_attr."}";
		
		$text_style = $this->_build_style_text($this->form_options, "input-text-style", $important);
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style_attr = $text_style["style"];
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-signature-box span i{".$style_attr."}";
		$style_attr .= $this->_build_style_background($this->form_options, "input-background-style", $important);
		$style_attr .= $this->_build_style_border($this->form_options, "input-border-style", $important);
		$style_attr .= $this->_build_shadow($this->form_options, "input-shadow", $important);
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-signature-box,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-multiselect,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='text'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='email'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='password'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select option,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input textarea{".$style_attr."}";
		if (array_key_exists("input-text-style-color", $this->form_options) && $this->form_options["input-text-style-color"] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input ::placeholder{color:".$this->form_options["input-text-style-color"]."; opacity: 0.9;} .lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input ::-ms-input-placeholder{color:".$this->form_options["input-text-style-color"]."; opacity: 0.9;}";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-multiselect::-webkit-scrollbar-thumb{background-color:".esc_html($this->form_options["input-border-style-color"]).";}";
		if ($this->form_options["input-hover-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "input-hover-text-style", $important);
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "input-hover-background-style", $important);
			$style_attr .= $this->_build_style_border($this->form_options, "input-hover-border-style", $important);
			$style_attr .= $this->_build_shadow($this->form_options, "input-hover-shadow", $important);
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='text']:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='email']:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='password']:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select:hover option,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input textarea:hover{".$style_attr."}";
		}
		if ($this->form_options["input-focus-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "input-focus-text-style", $important);
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "input-focus-background-style", $important);
			$style_attr .= $this->_build_style_border($this->form_options, "input-focus-border-style", $important);
			$style_attr .= $this->_build_shadow($this->form_options, "input-focus-shadow", $important);
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='text']:focus,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='email']:focus,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='password']:focus,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select:focus,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input select:focus option,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input textarea:focus{".$style_attr."}";
		}
		$style_attr = "";
		if ($this->form_options["input-icon-size"] != "") {
			$style_attr .= "font-size:".esc_html($this->form_options["input-icon-size"])."px;";
		}
		if ($this->form_options["input-icon-color"] != "") {
			$style_attr .= "color:".esc_html($this->form_options["input-icon-color"]).";";
		}
		if ($this->form_options['input-icon-position'] != 'outside') {
			if ($this->form_options["input-icon-background"] != "") {
				$style_attr .= "background:".esc_html($this->form_options["input-icon-background"]).";";
			}
			if ($this->form_options["input-icon-border"] != "") {
				$style_attr .= "border-color:".esc_html($this->form_options["input-icon-border"]).";border-style:solid;";
				if (array_key_exists("input-border-style-width", $this->form_options)) {
					$size = intval($this->form_options["input-border-style-width"]);
					if ($size >= 0 && $size <= 16) $style_attr .= "border-width:".esc_html($size)."px;";
				}
			}
			if (array_key_exists("input-border-style-radius", $this->form_options)) {
				$size = intval($this->form_options["input-border-style-radius"]);
				if ($size >= 0 && $size <= 100) $style_attr .= "border-radius:".esc_html($size)."px;";
			}
			if ($this->form_options["input-icon-background"] != "" || $this->form_options["input-icon-border"] != "") {
				$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-left input[type='text'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-left input[type='email'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-left input[type='password'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-left textarea {padding-left: 56px !important;}";
				$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-right input[type='text'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-right input[type='email'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-right input[type='password'],.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-icon-right textarea {padding-right: 56px !important;}";
			}
		}
		if (!empty($style_attr)) {
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input>i.lepopup-icon-left, .lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input>i.lepopup-icon-right{".$style_attr."}";
		}
		$text_style = $this->_build_style_text($this->form_options, "button-text-style");
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style_attr = $text_style["style"];
		$style_attr .= $this->_build_style_background($this->form_options, "button-background-style");
		$style_attr .= $this->_build_style_border($this->form_options, "button-border-style");
		$style_attr .= $this->_build_shadow($this->form_options, "button-shadow");
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element .lepopup-button,.lepopup-form-".esc_html($this->id)." .lepopup-element .lepopup-button:visited{".$style_attr."}";
		if ($this->form_options["button-hover-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "button-hover-text-style");
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "button-hover-background-style");
			$style_attr .= $this->_build_style_border($this->form_options, "button-hover-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "button-hover-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element .lepopup-button:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element .lepopup-button:focus{".$style_attr."}";
		}
		if ($this->form_options["button-active-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "button-active-text-style");
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "button-active-background-style");
			$style_attr .= $this->_build_style_border($this->form_options, "button-active-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "button-active-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element .lepopup-button:active{".$style_attr."}";
		}

		$style_attr = $this->_build_style_border($this->form_options, "imageselect-border-style");
		$style_attr .= $this->_build_shadow($this->form_options, "imageselect-shadow");
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input .lepopup-imageselect+label{".$style_attr."}";
		if ($this->form_options["imageselect-hover-inherit"] == "off") {
			$style_attr = $this->_build_style_border($this->form_options, "imageselect-hover-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "imageselect-hover-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input .lepopup-imageselect+label:hover{".$style_attr."}";
		}
		if ($this->form_options["imageselect-selected-inherit"] == "off") {
			$style_attr = $this->_build_style_border($this->form_options, "imageselect-selected-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "imageselect-selected-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input .lepopup-imageselect:checked+label{".$style_attr."}";
		}
		$text_style = $this->_build_style_text($this->form_options, "imageselect-text-style");
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input .lepopup-imageselect+label span.lepopup-imageselect-label{".$text_style["style"]."}";
		
		$style_attr = "";
		if (array_key_exists("checkbox-radio-unchecked-color-color2", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color2"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color2"]).";";
		else $style_attr .= "background-color:transparent;";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label:after{".$style_attr."}";
		if (array_key_exists("checkbox-radio-unchecked-color-color1", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color1"] != "") $style_attr .= "border-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color1"]).";";
		else $style_attr .= "border-color:transparent;";
		if (array_key_exists("checkbox-radio-unchecked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color3"] != "") $style_attr .= "color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color3"]).";";
		else $style_attr .= "color:#ccc;";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-classic+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-fa-check+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl+label{".$style_attr."}";
		$style_attr = "";
		if (array_key_exists("checkbox-radio-unchecked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color3"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color3"]).";";
		else $style_attr .= "color:#ccc;";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label:after{".$style_attr."}";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl+label:after{".$style_attr."}";
		if ($this->form_options["checkbox-radio-checked-inherit"] == "off") {
			$style_attr = "";
			if (array_key_exists("checkbox-radio-checked-color-color2", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color2"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-checked-color-color2"]).";";
			else $style_attr .= "background-color:transparent;";
			if (array_key_exists("checkbox-radio-checked-color-color1", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color1"] != "") $style_attr .= "border-color:".esc_html($this->form_options["checkbox-radio-checked-color-color1"]).";";
			else $style_attr .= "border-color:transparent;";
			if (array_key_exists("checkbox-radio-checked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color3"] != "") $style_attr .= "color:".esc_html($this->form_options["checkbox-radio-checked-color-color3"]).";";
			else $style_attr .= "color:#ccc;";
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-classic:checked+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-fa-check:checked+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label, .lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label{".$style_attr."}";
			$style_attr = "";
			if (array_key_exists("checkbox-radio-checked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color3"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-checked-color-color3"]).";";
			else $style_attr .= "background-color:#ccc;";
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-square:checked+label:after{".$style_attr."}";
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='checkbox'].lepopup-checkbox-tgl:checked+label:after{".$style_attr."}";
		}
		
		$style_attr = "";
		if (array_key_exists("checkbox-radio-unchecked-color-color2", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color2"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color2"]).";";
		else $style_attr .= "background-color:transparent;";
		if (array_key_exists("checkbox-radio-unchecked-color-color1", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color1"] != "") $style_attr .= "border-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color1"]).";";
		else $style_attr .= "border-color:transparent;";
		if (array_key_exists("checkbox-radio-unchecked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color3"] != "") $style_attr .= "color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color3"]).";";
		else $style_attr .= "color:#ccc;";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-classic+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-fa-check+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot+label{".$style_attr."}";
		$style_attr = "";
		if (array_key_exists("checkbox-radio-unchecked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-unchecked-color-color3"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-unchecked-color-color3"]).";";
		else $style_attr .= "background-color:#ccc;";
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label:after{".$style_attr."}";
		if ($this->form_options["checkbox-radio-checked-inherit"] == "off") {
			$style_attr = "";
			if (array_key_exists("checkbox-radio-checked-color-color2", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color2"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-checked-color-color2"]).";";
			else $style_attr .= "background-color:transparent;";
			if (array_key_exists("checkbox-radio-checked-color-color1", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color1"] != "") $style_attr .= "border-color:".esc_html($this->form_options["checkbox-radio-checked-color-color1"]).";";
			else $style_attr .= "border-color:transparent;";
			if (array_key_exists("checkbox-radio-checked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color3"] != "") $style_attr .= "color:".esc_html($this->form_options["checkbox-radio-checked-color-color3"]).";";
			else $style_attr .= "color:#ccc;";
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-classic:checked+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-fa-check:checked+label,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label{".$style_attr."}";
			$style_attr = "";
			if (array_key_exists("checkbox-radio-checked-color-color3", $this->form_options) && $this->form_options["checkbox-radio-checked-color-color3"] != "") $style_attr .= "background-color:".esc_html($this->form_options["checkbox-radio-checked-color-color3"]).";";
			else $style_attr .= "background-color:#ccc;";
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input input[type='radio'].lepopup-radio-dot:checked+label:after{".$style_attr."}";
		}

		$style_attr = "";
		if (array_key_exists("multiselect-style-hover-background", $this->form_options) && $this->form_options["multiselect-style-hover-background"] != "") $style_attr .= "background-color:".$this->form_options['multiselect-style-hover-background'].";";
		if (array_key_exists("multiselect-style-hover-color", $this->form_options) && $this->form_options["multiselect-style-hover-color"] != "") $style_attr .= "color:".$this->form_options['multiselect-style-hover-color'].";";
		if (!empty($style_attr)) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-multiselect>input[type='checkbox']+label:hover{".$style_attr."}";
		$style_attr = "";
		if (array_key_exists("multiselect-style-selected-background", $this->form_options) && $this->form_options["multiselect-style-selected-background"] != "") $style_attr .= "background-color:".$this->form_options['multiselect-style-selected-background'].";";
		if (array_key_exists("multiselect-style-selected-color", $this->form_options) && $this->form_options["multiselect-style-selected-color"] != "") $style_attr .= "color:".$this->form_options['multiselect-style-selected-color'].";";
		if (!empty($style_attr)) $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input div.lepopup-multiselect>input[type='checkbox']:checked+label{".$style_attr."}";

		if ($lepopup->options['range-slider-enable'] == 'on') {
			if (array_key_exists("rangeslider-color-color1", $this->form_options) && $this->form_options["rangeslider-color-color1"] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-line,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-min,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-max,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-grid-pol{background-color:".$this->form_options['rangeslider-color-color1']." !important;}";
			if (array_key_exists("rangeslider-color-color2", $this->form_options) && $this->form_options["rangeslider-color-color2"] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-grid-text,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-min,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-max{color:".$this->form_options["rangeslider-color-color2"]." !important;}";
			if (array_key_exists("rangeslider-color-color3", $this->form_options) && $this->form_options["rangeslider-color-color3"] != "") $style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-bar{background-color:".$this->form_options["rangeslider-color-color3"]." !important;}";
			if (array_key_exists("rangeslider-color-color4", $this->form_options) && $this->form_options["rangeslider-color-color4"] != "") {
				$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to{background-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
				//$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single:before,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from:before,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to:before{border-top-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
				switch($this->form_options["rangeslider-skin"]) {
					case 'sharp':
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle.state_hover{background-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle > i:first-child,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle:hover > i:first-child,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--sharp .irs-handle.state_hover > i:first-child{border-top-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
						break;
					case 'round':
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle.state_hover{border-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
						break;
					default:
						$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle > i:first-child,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle:hover > i:first-child,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--flat .irs-handle.state_hover > i:first-child{background-color:".$this->form_options["rangeslider-color-color4"]." !important;}";
						break;
				}
			}
			if (array_key_exists("rangeslider-color-color5", $this->form_options) && $this->form_options["rangeslider-color-color5"] != "") {
				$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-single,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-from,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs-to{color:".$this->form_options["rangeslider-color-color5"]." !important;}";
				if ($this->form_options["rangeslider-skin"] == "round") {
					$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle:hover,.lepopup-form-".esc_html($this->id)." .lepopup-element div.lepopup-input.lepopup-rangeslider .irs--round .irs-handle.state_hover{background-color:".$this->form_options["rangeslider-color-color5"]." !important;}";
				}
			}
		}

		$text_style = $this->_build_style_text($this->form_options, "tile-text-style");
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style_attr = $text_style["style"];
		$style_attr .= $this->_build_style_background($this->form_options, "tile-background-style");
		$style_attr .= $this->_build_style_border($this->form_options, "tile-border-style");
		$style_attr .= $this->_build_shadow($this->form_options, "tile-shadow");
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element input[type='checkbox'].lepopup-tile+label, .lepopup-form-".esc_html($this->id)." .lepopup-element input[type='radio'].lepopup-tile+label {".$style_attr."}";
		if ($this->form_options["tile-hover-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "tile-hover-text-style");
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "tile-hover-background-style");
			$style_attr .= $this->_build_style_border($this->form_options, "tile-hover-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "tile-hover-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element input[type='checkbox'].lepopup-tile+label:hover, .lepopup-form-".esc_html($this->id)." .lepopup-element input[type='radio'].lepopup-tile+label:hover{".$style_attr."}";
		}
		if ($this->form_options["tile-selected-inherit"] == "off") {
			$text_style = $this->_build_style_text($this->form_options, "tile-selected-text-style");
			if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
			$style_attr = $text_style["style"];
			$style_attr .= $this->_build_style_background($this->form_options, "tile-selected-background-style");
			$style_attr .= $this->_build_style_border($this->form_options, "tile-selected-border-style");
			$style_attr .= $this->_build_shadow($this->form_options, "tile-selected-shadow");
			$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element input[type='checkbox'].lepopup-tile:checked+label, .lepopup-form-".esc_html($this->id)." .lepopup-element input[type='radio'].lepopup-tile:checked+label{".$style_attr."}";
		}
		
		$text_style = $this->_build_style_text($this->form_options, "error-text-style");
		if ($text_style["webfont"] != "" && !in_array($text_style["webfont"], $webfonts)) $webfonts[] = $text_style["webfont"];
		$style_attr = $text_style["style"];
		$style_attr .= $this->_build_style_background($this->form_options, "error-background-style");
		$style .= ".lepopup-form-".esc_html($this->id)." .lepopup-element-error{".$style_attr."}";
		
		$id = $lepopup->random_string(16);
		$uids = array($id);
		$xd = $this->form_options["cross-domain"];
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (array_key_exists('type', $this->form_elements[$i]) &&  $this->form_elements[$i]['type'] == 'signature') {
				$xd = 'off';
				break;
			}
		}
		for ($i=0; $i<sizeof($this->form_pages); $i++) {
			if (!empty($this->form_pages[$i]) && is_array($this->form_pages[$i])) {
				$width = intval($this->form_pages[$i]['size-width']);
				if ($width < 64 || $width > 4096) $width = 720;
				else if ($width % 2 != 0) $width++;
				$height = intval($this->form_pages[$i]['size-height']);
				if ($height < 32 || $height > 4096) $height = 540;
				else if ($height % 2 != 0) $height++;
				$output = $this->_build_children($this->form_pages[$i]['id'], 0);
				$webfonts = array_merge($webfonts, $output['webfonts']);
				$style .= $output["style"];
				$hidden = $this->_build_hidden($this->form_pages[$i]['id']);
				$html .= '<div class="lepopup-form lepopup-form-'.esc_html($this->id).' lepopup-form-'.esc_html($id).' lepopup-form-icon-'.esc_html($this->form_options['input-icon-position']).' lepopup-form-position-'.esc_html($this->form_options["position"]).'" data-session="'.($this->form_options['session-enable'] == 'on' ? intval($this->form_options['session-length']) : '0').'" data-id="'.esc_html($id).'" data-form-id="'.esc_html($this->id).'" data-slug="'.esc_html($this->slug).'" data-title="'.esc_html($this->form_options["name"]).'" data-page="'.esc_html($this->form_pages[$i]['id']).'" data-xd="'.esc_html($xd).'" data-width="'.$width.'" data-height="'.$height.'" data-position="'.esc_html($this->form_options["position"]).'" data-esc="'.esc_html($this->form_options["esc-enable"]).'" data-enter="'.esc_html($this->form_options["enter-enable"]).'" style="display:none;width:'.$width.'px;height:'.$height.'px;" onclick="event.stopPropagation();"><div class="lepopup-form-inner" style="width:'.$width.'px;height:'.$height.'px;">'.$output["html"].$hidden.'</div></div>';
				$uids = array_merge($uids, $output['uids']);
			}
		}
		$html .= '<input type="hidden" id="lepopup-logic-'.esc_html($id).'" value="'.esc_html(json_encode($this->form_logic)).'" />';
		
		if (array_key_exists('math-expressions', $this->form_options) && !empty($this->form_options['math-expressions'])) {
			foreach ($this->form_options['math-expressions'] as $math_expression) {
				$data = array();
				$ids = array();
				preg_match_all('/{{(\d+)(|.+?)}}/' , $math_expression["expression"], $matches);
				for ($j=0; $j<sizeof($matches[0]); $j++) {
					if (!empty($matches[0][$j]) && !empty($matches[1][$j])) {
						$data[$matches[0][$j]] = '{'.$matches[1][$j].'}';
						$ids[] = $matches[1][$j];
					}
				}
				$expression = strtr($math_expression["expression"], $data);
				$html .= '<input class="lepopup-math" data-expression="'.esc_html($expression).'" data-id="'.esc_html($math_expression["id"]).'" data-ids="'.esc_html(implode(',',$ids)).'" data-decimal="'.esc_html($math_expression["decimal-digits"]).'" data-default="'.esc_html($math_expression["default"]).'" type="hidden" name="lepopup-math-'.esc_html($math_expression['id']).'" value="'.esc_html($math_expression["default"]).'" />';
			}
		}
		
		$style = '<style>'.$style.'</style>';
		
		if (!empty($webfonts)) {
			$webfonts = array_unique($webfonts);
			$esc_array = array();
			foreach($webfonts as $array_value) {
				$esc_array[] = esc_sql($array_value);
			}
			$webfonts_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_webfonts WHERE family IN ('".implode("', '", $esc_array)."') AND deleted = '0' ORDER BY family", ARRAY_A);
			if(!empty($webfonts_array)){
				$families = array();
				$subsets = array();
				foreach($webfonts_array as $webfont) {
					$families[] = str_replace(' ', '+', $webfont['family']).':'.$webfont['variants'];
					$webfont_subsets = explode(',', $webfont['subsets']);
					if (!empty($webfont_subsets) && is_array($webfont_subsets)) $subsets = array_merge($subsets, $webfont_subsets);
				}
				$subsets = array_unique($subsets);
				$query = '?family='.implode('|', $families);
				if (!empty($subsets)) $query .= '&subset='.implode(',', $subsets);
				$style = '<link href="//fonts.googleapis.com/css'.$query.'" rel="stylesheet" type="text/css">'.$style;
			}
		}
		$html .= apply_filters('lepopup_form_suffix', '', $id, $this);
/*
		$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_forms SET 
			cache_style = '".esc_sql($style)."',
			cache_html = '".esc_sql($html)."',
			cache_uids = '".esc_sql(json_encode($uids))."',
			cache_time = '".esc_sql(time())."'
			WHERE id = '".esc_sql($this->id)."'");
*/
		return array('style' => $style, 'html' => $html);
	}

	function input_fields_sort() {
		$input_fields = array();
		$fields = array();
		for ($i=0; $i<sizeof($this->form_pages); $i++) {
			if (!empty($this->form_pages[$i]) && is_array($this->form_pages[$i])) {
				$fields = $this->_lepopup_input_sort($this->form_pages[$i]['id'], 0, $this->form_pages[$i]['id'], $this->form_pages[$i]['name']);
				if (!empty($fields)) $input_fields = array_merge($input_fields, $fields);
			}
		}
		return $input_fields;
	}

	protected function _lepopup_input_sort($_parent, $_parent_col, $_page_id, $_page_name) {
		global $lepopup;
		$input_fields = array();
		$fields = array();
		$idxs = array();
		$seqs = array();
		for ($i=0; $i<sizeof($this->form_elements); $i++) {
			if (empty($this->form_elements[$i])) continue;
			if ($this->form_elements[$i]["_parent"] == $_parent) {
				$idxs[] = $i;
				$seqs[] = intval($this->form_elements[$i]["_seq"]);
			}
		}
		if (empty($idxs)) return $input_fields;
		for ($i=0; $i<sizeof($seqs); $i++) {
			$sorted = -1;
			for ($j=0; $j<sizeof($seqs)-1; $j++) {
				if ($seqs[$j] > $seqs[$j+1]) {
					$sorted = $seqs[$j];
					$seqs[$j] = $seqs[$j+1];
					$seqs[$j+1] = $sorted;
					$sorted = $idxs[$j];
					$idxs[$j] = $idxs[$j+1];
					$idxs[$j+1] = $sorted;
				}
			}
			if ($sorted == -1) break;
		}
		for ($k=0; $k<sizeof($idxs); $k++) {
			$i = $idxs[$k];
			if (empty($this->form_elements[$i])) continue;
			if (array_key_exists($this->form_elements[$i]['type'], $lepopup->toolbar_tools) && $lepopup->toolbar_tools[$this->form_elements[$i]['type']]['type'] == 'input') {
				$input_fields[] = array_merge($this->form_elements[$i], array('page-id' => $_page_id, 'page-name' => $_page_name));//array('id' => $this->form_elements[$i]['id'], 'name' => $this->form_elements[$i]['name'], 'page-id' => $_page_id, 'page-name' => $_page_name);
			} else if ($this->form_elements[$i]['type'] == "columns") {
				for ($j=0; $j<$this->form_elements[$i]['_cols']; $j++) {
					$fields = $this->_lepopup_input_sort($this->form_elements[$i]['id'], $j, $_page_id, $_page_name);
					if (!empty($fields)) $input_fields = array_merge($input_fields, $fields);

				}
			}
		}
		return $input_fields;
	}
}
?>