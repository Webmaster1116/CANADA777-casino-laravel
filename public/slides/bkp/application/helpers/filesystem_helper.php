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


if( ! function_exists('WP_Filesystem'))
{
	/**
	 *	Init filesystem class
	 */

	function WP_Filesystem() {

		global $wp_filesystem;

		$ci = &get_instance();
		$ci->load->library('filesystem');

		$wp_filesystem = $ci->filesystem;

		return true;
	}
}

if( ! function_exists('unzip_file'))
{
	/**
	 *	Unzip file
	 *
	 *	@param	string	$file
	 *	@param	string	$path
	 *	@return boolean
	 */

	function unzip_file($file, $path) {

        // make sure it have trailing slash
        $path = rtrim(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if ( ! wp_mkdir_p($path)) return false;

		if (class_exists('ZipArchive') && RevSliderOperations::getGeneralSettingsOptionValue('force_pclzip', 'off') == 'off') {

			$zip = new ZipArchive;
			$zipResult = $zip->open($file, ZIPARCHIVE::CREATE);
			if ($zipResult === true) {
				for($i = 0; $i < $zip->numFiles; $i++) {
					$fileName = $zip->getNameIndex($i);
					$fileInfo = pathinfo($fileName);
					if (strpos($fileName, '_') !== 0 && strpos($fileName, '.') !== 0 && strpos($fileInfo['basename'], '_') !== 0 && strpos($fileInfo['basename'], '.') !== 0) {

					    if ($fileInfo['dirname'] !== '.' && ! file_exists($path.$fileInfo['dirname'])) {
							$parts = explode('/', $fileInfo['dirname']);
							$dirPath = $path;
							foreach ($parts as $part) {
								$dirPath .= $part . DIRECTORY_SEPARATOR;
								wp_mkdir_p($dirPath);
							}
						}

						if (substr($fileName, -1) !== '/' && substr($fileName, -1) !== '\\') {
						    $targetFile = $path.str_replace('//', DIRECTORY_SEPARATOR, $fileName);
                            file_put_contents($targetFile, $zip->getFromName($fileName));
                            updatePermissions($targetFile);
						}

					}
				}
				$zip->close();
			}

		} else {

			include_once APPPATH . "libraries/pclzip.lib.php";
			$pclZip = new PclZip($file);
			$list = $pclZip->listContent();
			if ($list) {
				for ($i=0; $i<sizeof($list); $i++) {
					$fileInfo = $list[$i];
					$fileName = $fileInfo['filename'];
					if (strpos($fileName, '_') !== 0 && strpos($fileName, '.') !== 0 && strpos($fileName, '/_') === FALSE && strpos($fileName, '/.') === FALSE) {

						if ($fileInfo['folder']) {
                            if ( ! file_exists($path.$fileName)) {
                                wp_mkdir_p($path.$fileName);
                            }
						} elseif ( ! file_exists($path . dirname($fileName))) {
							$parts = explode('/', dirname($fileName));
							$dirPath = $path;
							foreach ($parts as $part) {
								$dirPath .= $part . DIRECTORY_SEPARATOR;
								wp_mkdir_p($dirPath);
							}
                        }

                        $extract = $pclZip->extract(PCLZIP_OPT_BY_INDEX, $fileInfo['index'], PCLZIP_OPT_EXTRACT_AS_STRING);
                        if ( ! $fileInfo['folder'] && $extract && isset($extract[0]['content'])) {
                            $targetFile = $path.$fileName;
                            file_put_contents($targetFile, $extract[0]['content']);
                            updatePermissions($targetFile);
                        }

					}
				}
			}
			$zipResult = count($list) !== 0;

		}
		return $zipResult;
	}
}

if( ! function_exists('recurse_move'))
{
	/**
	 * Move files recursively
	 *
	 * @param	string	$src
	 * @param	string	$dst
	 */

	function recurse_move($src, $dst) {
		$src = rtrim($src,'/\\');
		$dst = rtrim($dst,'/\\');
		$dir = opendir($src);
		wp_mkdir_p($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) )
				{
					recurse_move($src . '/' . $file,$dst . '/' . $file);
				}
				else
				{
					rename($src . '/' . $file,$dst . '/' . $file);
                    updatePermissions($dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
}
