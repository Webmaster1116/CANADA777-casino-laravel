<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class Transient_model extends CI_Model {


	/*
	 * Table name
	 *
	 */
	public $table = 'transients';

	/**
	 *	Get transient value
	 *
	 *	@param	string Handle
	 *	@return	string
	 */
	public function get_value($handle) {
		$value = false;
		$transient = $this->db->where('handle', $handle)->get($this->table)->row();
		if ($transient)
		{
			if (is_null($transient->expires) || strtotime($transient->expires) > time())
			{
				$value = json_decode($transient->value);
			}
			else
			{
				$this->delete($handle);
			}
		}
		return $value;
	}

	/**
	 *	Set transient
	 *
	 *	@param	string	Handle
	 *	@param	string	Value
	 *	@param	int		Expires
	 */
	public function set($handle, $value, $expires = NULL) {
		$value = json_encode($value);
		$transient = $this->db->where('handle', $handle)->get($this->table)->row();
		$this->db
			->set('value', $value)
			->set('expires', $expires);
		if ($transient)
		{
			$this->db
				->where('handle', $handle)
				->update($this->table);
		}
		else
		{
			$this->db
				->set('handle', $handle)
				->insert($this->table);
		}
	}

	/**
	 *	Delete transient
	 *
	 *	@param	string Handle
	 */
	public function delete($handle) {
		$this->db->where('handle', $handle)->delete($this->table);
	}

}