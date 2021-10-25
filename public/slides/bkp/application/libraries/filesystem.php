<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Filesystem {

	/**
	 *	Check if file exists
	 *
	 *	@param	string	Path to file
	 *	@return boolean
	 */

	public function exists($path) {
		return file_exists($path);
	}

	/**
	 *	Read file
	 *
	 *	@param	string	Path to file
	 *	@return string
	 */

	public function get_contents($path) {
		return file_get_contents($path);
	}

	/**
	 *	Delete file
	 *
	 *	@param	string	Path to file
	 *	@param	boolean	Is recursive
	 *	@return string
	 */

	public function delete($path, $recursive = false) {
		if (is_dir($path))
		{
			$dir = opendir($path);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($path . '/' . $file) )
					{
						if ($recursive)
						{
							$this->delete($path . '/' . $file, $recursive);
						}
					}
					else
					{
						unlink($path . '/' . $file);
					}
				}
			}
			closedir($dir);
			rmdir($path);
		}
		else
		{
			unlink($path);
		}
	}
}