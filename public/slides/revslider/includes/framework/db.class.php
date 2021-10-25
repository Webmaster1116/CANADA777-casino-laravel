<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RevSliderDB{
	
	private $lastRowID;
	private $ci;
	
	/**
	 * 
	 * constructor - set database object
	 */
	public function __construct(){
		$this->ci = &get_instance();
	}

	/**
	 * Get db class
	 */
	private function db() {
		return $this->ci->db;
	}

	/**
	 * 
	 * throw error
	 */
	private function throwError($message,$code=-1){
		UniteFunctionsRev::throwError($message,$code);
	}
	
	//------------------------------------------------------------
	// validate for errors
	private function checkForErrors($prefix = ""){
		
		if( $this->db()->_error_number() )
		{
			$query = $this->db()->last_query();
			$message = $this->db()->_error_message();
			
			if($prefix) $message = $prefix.' - <b>'.$message.'</b>';
            if($query) $message .=  '<br>---<br> Query: ' . esc_attr($query);
			
			$this->throwError($message);
		}
	}

	
	/**
	 *
	 * insert variables to some table
	 */
	public function insert($table,$arrItems){
		
		$this->db()->insert($table, $arrItems);
		$this->checkForErrors("Insert query error");
		
		$this->lastRowID = $this->db()->insert_id();
		
		return($this->lastRowID);
	}
	
	/**
	 * 
	 * get last insert id
	 */
	public function getLastInsertID(){
		
		$this->lastRowID = $this->db()->insert_id();
		return($this->lastRowID);			
	}
	
	
	/**
	 * 
	 * delete rows
	 */
	public function delete($table,$where){

		UniteFunctionsRev::validateNotEmpty($table,"table name");
		UniteFunctionsRev::validateNotEmpty($where,"where");
		
		$query = "delete from $table where $where";
		
		$this->db()->query($query);

		$this->checkForErrors("Delete query error");
	}


	/**
	 *
	 * run some sql query
	 */
	public function runSql($query){
		
		$this->db()->query($query);
		$this->checkForErrors("Regular query error");
	}
	
	
	/**
	 * 
     * run some sql query
     */
    public function runSqlR($query){
        global $wpdb;

        $return = $wpdb->get_results($query, ARRAY_A);

        return $return;
    }


    /**
     *
	 * insert variables to some table
	 */
	public function update($table,$arrItems,$where){

		$response = $this->db()->where($where)->update($table, $arrItems);
		//if($response === false)
		//	UniteFunctionsRev::throwError("no update action taken!");
		//$this->checkForErrors("Update query error");
		
		return $this->db()->affected_rows();
	}
	
	
	/**
	 *
	 * get data array from the database
	 * 
	 */
	public function fetch($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		
		$query = "select * from $tableName";
		if($where) $query .= " where $where";
		if($orderField) $query .= " order by $orderField";
		if($groupByField) $query .= " group by $groupByField";
		if($sqlAddon) $query .= " ".$sqlAddon;
		
		$response = $this->db()->query($query)->result_array();
		
		$this->checkForErrors("fetch");
		
		return($response);
	}
	
	/**
	 * 
	 * fetch only one item. if not found - throw error
	 */
	public function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
		if(empty($response))
			$this->throwError("Record not found");
		$record = $response[0];
		return($record);
	}
	
	/**
	 * 
     * prepare statement to avoid sql injections
	 */
    public function prepare($query, $array){
        global $wpdb;

        $query = $wpdb->prepare($query, $array);

        return($query);
	}
	
}
