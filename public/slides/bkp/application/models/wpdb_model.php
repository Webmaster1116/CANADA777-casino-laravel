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

class WPDB_model extends CI_Model {

	public $prefix = '';

	public function __construct() {
		include(APPPATH.'config/database'.EXT);
		$this->prefix = $db[$active_group]['dbprefix'];
	}

	/**
	 *	Get query results
	 *
	 *	@param	string	Query
	 *	@param	string	Result format
	 *	@return	array
	 */
	public function get_results($query, $mode = ARRAY_A) {
		$res = $this->db->query($query);
        if ( ! is_object($res)) {
            return false;
        }
		return $mode == ARRAY_A ? $res->result_array() : $res->result();
	}

	/**
	 *	Get query row
	 *
	 *	@param	string	Query
	 *	@param	string	Result format
	 *	@return	array
	 */
	public function get_row($query, $mode = false) {
		$res = $this->db->query($query);
		return $mode == ARRAY_A ? $res->row_array() : $res->row();
	}

	/**
	 *	Insert row
	 *
	 *	@param	string	Table name
	 *	@param	array	Data
	 *	@return	int
	 */

	public function insert($table, $data = array()) {
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	/**
	 *	Update row
	 *
	 *	@param	string	Table name
	 *	@param	array	Data
	 *	@param	array	Where
	 */

	public function update($table, $data = array(), $where) {
		return $this->db
			->set($data)
			->where($where)
			->update($table);
	}

	/**
	 *	Delete row
	 *
	 *	@param	string	Table name
	 *	@param	array	Data
	 *	@param	array	Where
	 */

	public function delete($table, $where) {
		return $this->db
			->where($where)
			->delete($table);
	}

	/**
	 *	Prepare query
	 *
	 *	@param	string	Query
	 *	@param	mixed	Args
	 *	@return	array
	 */
	public function prepare($query, $args) {
		$args = func_get_args();
		array_shift( $args );
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace( '|(?<!%)%f|' , '%F', $query ); // Force floats to be locale unaware
		$query = preg_replace( '|(?<!%)%s|', "%s", $query ); // quote the strings, avoiding escaped strings like %%s
		array_walk( $args, array( $this, 'escape_by_ref' ) );

		return @vsprintf( $query, $args );
	}

	public function escape_by_ref(&$arg) {
		if( (string)(int)$arg != $arg) $arg = $this->db->escape($arg);
	}

    public function tables() {
        return array();
    }

    /**
     *  Enable debug mode
     *
     *  @param  boolean $isDebug
     *  @return boolean
     */

    public function suppress_errors($isDebug = null) {
        $savedState = $this->db->db_debug;
        if (is_null($isDebug)) {
            $this->db->db_debug = ! $this->db->db_debug;
        } else {
            $this->db->db_debug = $isDebug;
        }
        return $savedState;
    }

    /**
     *  Run SQL query
     *
     *  @param  string  $query
     */

    public function query($query) {
        $this->db->query($query);
    }

    /**
     *  Get var from query
     *
     *  @param  string  $query
     *  @return string
     */

    public function get_var($query) {
        $res = $this->db->query($query);
        if (is_object($res)) {
            $row = $res->row_array();
            $value = reset($row);
            return $value;
        }
   }

}