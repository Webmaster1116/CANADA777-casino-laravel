<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright		Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright		Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.1.2
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * PDO Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite3_result extends CI_DB_result {

	var $pdo_results = '';
	var $pdo_index = 0;

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	function num_rows()
	{
		if ( ! $this->pdo_results ) {
			$this->pdo_results = $this->result_id->fetchAll(PDO::FETCH_ASSOC);
		}
		return sizeof($this->pdo_results);
	}

	// --------------------------------------------------------------------

	/**
	 * Number of fields in the result set
	 *
	 * @access	public
	 * @return	integer
	 */
	function num_fields()
	{
		if ( is_array($this->pdo_results) ) {
			return sizeof($this->pdo_results[$this->pdo_index]);
		} else {
			return $this->result_id->columnCount();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Field Names
	 *
	 * Generates an array of column names
	 *
	 * @access	public
	 * @return	array
	 */
	function list_fields()
	{
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data
	 *
	 * Generates an array of objects containing field meta-data
	 *
	 * @access	public
	 * @return	array
	 */
	function field_data()
	{
		if ($this->db->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Free the result
	 *
	 * @return	null
	 */
	function free_result()
	{
		if (is_object($this->result_id))
		{
			$this->result_id = FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset.  We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @access	private
	 * @return	array
	 */
	function _data_seek($n = 0)
	{
		$this->pdo_index = $n;
	}

	// --------------------------------------------------------------------

	/**
	 * Result - associative array
	 *
	 * Returns the result set as an array
	 *
	 * @access	private
	 * @return	array
	 */
	function _fetch_assoc()
	{
		if ( is_array($this->pdo_results) ) {
			$i = $this->pdo_index;
			$this->pdo_index++;
			if ( isset($this->pdo_results[$i]))
				return $this->pdo_results[$i];
			return null;
		}
		return $this->result_id->fetch(PDO::FETCH_ASSOC);
	}

	// --------------------------------------------------------------------

	/**
	 * Result - object
	 *
	 * Returns the result set as an object
	 *
	 * @access	private
	 * @return	object
	 */
	function _fetch_object()
	{
		if ( is_array($this->pdo_results) ) {
			$i = $this->pdo_index;
			$this->pdo_index++;
			if ( isset($this->pdo_results[$i])) {
				$back = new stdClass();
				foreach ( $this->pdo_results[$i] as $key => $val ) {
					$back->$key = $val;
				}
				return $back;
			}
			return null;
		}
		return $this->result_id->fetch(PDO::FETCH_OBJ);
	}

}


/* End of file pdo_result.php */
/* Location: ./system/database/drivers/pdo/pdo_result.php */