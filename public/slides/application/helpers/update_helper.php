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


if( ! function_exists('check_for_jquery_addon'))
{
    /**
     *	Checks if jQuery editor addon installed and have correct version
     *
     *	return	boolean
     */

    function check_for_jquery_addon() {
        $ci = &get_instance();
        $ci->load->library('updater');
        $result = $ci->updater->check_jquery_addon();
        return $result['success'];

    }
}

if( ! function_exists('get_jquery_addon_message'))
{
    /**
     *	Get message about jQuery addon
     *
     *	return	string
     */

    function get_jquery_addon_message() {
        $ci = &get_instance();
        $ci->load->library('updater');
        $result = $ci->updater->check_jquery_addon();
        return isset($result['message']) ? $result['message'] : '';
    }
}

if( ! function_exists('is_jquery_addon_activated')) {
    /**
     *	Checks if jQuery editor addon activated
     *
     *	return	boolean
     */

    function is_jquery_addon_activated() {
        return get_option('jquery-plugin-code', '') && get_option('jquery-plugin-code-activated', 'false') == 'true';
    }
}

if( ! function_exists('is_jquery_addon_temp_activated')) {
    /**
     *	Checks if jQuery editor addon temporary activated
     *
     *	return	boolean
     */

    function is_jquery_addon_temp_activated() {
        return get_option('jquery-plugin-code', '') && get_option('jquery-plugin-temp-active', 'false') == 'true';
    }
}

if( ! function_exists('dbDelta'))
{
    /**
     *  Execute DB updates
     *
     *  @param  string  $queries
     *  @param  boolean $execute
     *  @return array
     */

    function dbDelta( $queries = '', $execute = true ) {
        global $wpdb;

        if ( in_array( $queries, array( '', 'all', 'blog', 'global', 'ms_global' ), true ) )
            $queries = wp_get_db_schema( $queries );

        // Separate individual queries into an array
        if ( !is_array($queries) ) {
            $queries = explode( ';', $queries );
            $queries = array_filter( $queries );
        }


        /**
         * Filter the dbDelta SQL queries.
         *
         * @since 3.3.0
         *
         * @param array $queries An array of dbDelta SQL queries.
         */
        $queries = apply_filters( 'dbdelta_queries', $queries );

        $cqueries = array(); // Creation Queries
        $iqueries = array(); // Insertion Queries
        $for_update = array();

        // Create a tablename index for an array ($cqueries) of queries
        foreach($queries as $qry) {
            if ( preg_match( "|CREATE TABLE ([^ ]*)|", $qry, $matches ) ) {
                $cqueries[ trim( $matches[1], '`' ) ] = $qry;
                $for_update[$matches[1]] = 'Created table '.$matches[1];
            } elseif ( preg_match( "|CREATE DATABASE ([^ ]*)|", $qry, $matches ) ) {
                array_unshift( $cqueries, $qry );
            } elseif ( preg_match( "|INSERT INTO ([^ ]*)|", $qry, $matches ) ) {
                $iqueries[] = $qry;
            } elseif ( preg_match( "|UPDATE ([^ ]*)|", $qry, $matches ) ) {
                $iqueries[] = $qry;
            } else {
                // Unrecognized query type
            }
        }

        /**
         * Filter the dbDelta SQL queries for creating tables and/or databases.
         *
         * Queries filterable via this hook contain "CREATE TABLE" or "CREATE DATABASE".
         *
         * @since 3.3.0
         *
         * @param array $cqueries An array of dbDelta create SQL queries.
         */
        $cqueries = apply_filters( 'dbdelta_create_queries', $cqueries );

        /**
         * Filter the dbDelta SQL queries for inserting or updating.
         *
         * Queries filterable via this hook contain "INSERT INTO" or "UPDATE".
         *
         * @since 3.3.0
         *
         * @param array $iqueries An array of dbDelta insert or update SQL queries.
         */
        $iqueries = apply_filters( 'dbdelta_insert_queries', $iqueries );

        $global_tables = $wpdb->tables( 'global' );

        foreach ( $cqueries as $table => $qry ) {
            // Upgrade global tables only for the main site. Don't upgrade at all if conditions are not optimal.
            if ( in_array( $table, $global_tables ) && ! wp_should_upgrade_global_tables() ) {
                unset( $cqueries[ $table ], $for_update[ $table ] );
                continue;
            }

            // Fetch the table column structure from the database
            $suppress = $wpdb->suppress_errors();
            $searchTables = $wpdb->get_results("SHOW TABLES LIKE '{$table}';");
            $tablefields = $searchTables ? $wpdb->get_results("DESCRIBE {$table};") : false;
            $wpdb->suppress_errors( $suppress );

            if ( ! $tablefields )
                continue;

            // Clear the field and index arrays.
            $cfields = $indices = array();

            // Get all of the field names in the query from between the parentheses.
            preg_match("|\((.*)\)|ms", $qry, $match2);
            $qryline = trim($match2[1]);

            // Separate field lines into an array.
            $flds = explode("\n", $qryline);


            // For every field line specified in the query.
            foreach ($flds as $fld) {

                // Extract the field name.
                preg_match("|^([^ ]*)|", trim($fld), $fvals);
                $fieldname = trim( $fvals[1], '`' );

                // Verify the found field name.
                $validfield = true;
                switch (strtolower($fieldname)) {
                    case '':
                    case 'primary':
                    case 'index':
                    case 'fulltext':
                    case 'unique':
                    case 'key':
                        $validfield = false;
                        $indices[] = trim(trim($fld), ", \n");
                        break;
                }
                $fld = trim($fld);

                // If it's a valid field, add it to the field array.
                if ($validfield) {
                    $cfields[strtolower($fieldname)] = trim($fld, ", \n");
                }
            }

            // For every field in the table.
            foreach ($tablefields as $tablefield) {

                $tablefield = (object) $tablefield;

                // If the table field exists in the field array ...
                if (array_key_exists(strtolower($tablefield->Field), $cfields)) {

                    // Get the field type from the query.
                    preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
                    $fieldtype = $matches[1];

                    // Is actual field type different from the field type in query?
                    if ($tablefield->Type != $fieldtype) {
                        // Add a query to change the column type
                        $_query = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[strtolower($tablefield->Field)];
                        $cqueries[] = str_replace(" {$tablefield->Field} ", " `{$tablefield->Field}` ", $_query);
                        $for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
                    }

                    // Get the default value from the array
                    // todo: Remove this?
                    //echo "{$cfields[strtolower($tablefield->Field)]}<br>";
                    if (preg_match("| DEFAULT '(.*?)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
                        $default_value = $matches[1];
                        if ($tablefield->Default != $default_value) {
                            // Add a query to change the column's default value
                            $cqueries[] = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";
                            $for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
                        }
                    }

                    // Remove the field from the array (so it's not added).
                    unset($cfields[strtolower($tablefield->Field)]);
                } else {
                    // This field exists in the table, but not in the creation queries?
                }
            }

            // For every remaining field specified for the table.
            foreach ($cfields as $fieldname => $fielddef) {
                // Push a query line into $cqueries that adds the field to that table.
                $cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
                $for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
            }

            // Index stuff goes here. Fetch the table index structure from the database.
            $tableindices = $wpdb->get_results("SHOW INDEX FROM {$table};");

            if ($tableindices) {
                // Clear the index array.
                $index_ary = array();

                // For every index in the table.
                foreach ($tableindices as $tableindex) {

                    $tableindex = (object) $tableindex;

                    // Add the index to the index data array.
                    $keyname = $tableindex->Key_name;
                    $index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
                    $index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
                }

                // For each actual index in the index array.
                foreach ($index_ary as $index_name => $index_data) {

                    // Build a create string to compare to the query.
                    $index_string = '';
                    if ($index_name == 'PRIMARY') {
                        $index_string .= 'PRIMARY ';
                    } elseif ( $index_data['unique'] ) {
                        $index_string .= 'UNIQUE ';
                    }
                    $index_string .= 'KEY ';
                    if ($index_name != 'PRIMARY') {
                        $index_string .= $index_name;
                    }
                    $index_columns = '';

                    // For each column in the index.
                    foreach ($index_data['columns'] as $column_data) {
                        if ($index_columns != '') $index_columns .= ',';

                        // Add the field to the column list string.
                        $index_columns .= $column_data['fieldname'];
                        if ($column_data['subpart'] != '') {
                            $index_columns .= '('.$column_data['subpart'].')';
                        }
                    }

                    // The alternative index string doesn't care about subparts
                    $alt_index_columns = preg_replace( '/\([^)]*\)/', '', $index_columns );

                    // Add the column list to the index create string.
                    $index_strings = array(
                        "$index_string ($index_columns)",
                        "$index_string ($alt_index_columns)",
                    );

                    foreach( $index_strings as $index_string ) {
                        if ( ! ( ( $aindex = array_search( $index_string, $indices ) ) === false ) ) {
                            unset( $indices[ $aindex ] );
                            break;
                            // todo: Remove this?
                            //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
                        }
                    }
                    // todo: Remove this?
                    //else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br /><b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
                }
            }

            // For every remaining index specified for the table.
            foreach ( (array) $indices as $index ) {
                // Push a query line into $cqueries that adds the index to that table.
                $cqueries[] = "ALTER TABLE {$table} ADD $index";
                $for_update[] = 'Added index ' . $table . ' ' . $index;
            }

            // Remove the original table creation query from processing.
            unset( $cqueries[ $table ], $for_update[ $table ] );
        }

        $allqueries = array_merge($cqueries, $iqueries);
        if ($execute) {
            foreach ($allqueries as $query) {
                $wpdb->query($query);
            }
        }

        return $for_update;
    }
}

// For compatibility

if( ! function_exists('wp_should_upgrade_global_tables')) {
    function wp_should_upgrade_global_tables() {
        return false;
    }
}

if( ! function_exists('wp_get_db_schema')) {
    function wp_get_db_schema($queries = array()) {
        return $queries;
    }
}