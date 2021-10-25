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

/**
 *	Get image url by id and size
 *
 *	@param	int		Image id
 *	@param	string	Size type
 *	@return string
 */
if( ! function_exists('wp_get_attachment_image_src'))
{
	function wp_get_attachment_image_src($attachment_id, $size='thumbnail')
	{
		if ( $image = image_downsize($attachment_id, $size) )
			return $image;
		else
			return false;
	}
}

if( ! function_exists('image_downsize')) {

    /**
     *	Resize image by id and preset size
     *
     *	@param	int     $id
     *	@param	string  $size
     *	@return mixed
     */
	function image_downsize($id, $size = 'medium') {
	    
		$ci = &get_instance();
		$ci->load->model('image_model', 'Image');

		$imageUrl = $ci->Image->getUrl($id);
		if ( ! $imageUrl) {
			return false;
		}

		if ($size == 'full') {

			if ( ! file_exists(FCPATH . RS_IMAGE_PATH . '/' . $imageUrl) || ! $size = getimagesize(FCPATH . RS_IMAGE_PATH . '/' . $imageUrl)){
				return false;
			}

			$resultUrl = base_url() . RS_IMAGE_PATH . '/' . $imageUrl;
			$width = $size[0];
			$height = $size[1];

		} else {

            $sizes = $ci->config->item('rs_image_sizes');
            $targetSize = isset($sizes[$size]) ? $sizes[$size] : reset($sizes);
            $width = $targetSize['width'];
            $height = $targetSize['height'];

			$resultUrl = image_resize(FCPATH . RS_IMAGE_PATH . '/' . $imageUrl, $width, $height);
		}

		return array( $resultUrl, $width, $height );
	}
}

/**
 *	Resize image
 *
 *	@param	string	Image url
 *	@param	int		Width
 *	@param	int		Height
 *	@param	boolean	Is crop
 *	@param	boolean	Is single
 *	@param	boolean	Is upscale
 *	@return string
 */
if( ! function_exists('image_resize')) {

	function image_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false) {

		$ci = &get_instance();

		$arrImagePath = explode('/', $url);
		$imageFile = array_pop($arrImagePath);

		$thumbUrl = RS_THUMB_PATH . '/' . $width . 'x' . $height . '_' . $imageFile;
		if ( ! file_exists(FCPATH . $thumbUrl) || ! getimagesize(FCPATH . $thumbUrl))
		{
			$ci->load->library('image_moo');
			$ci->image_moo->load( image_url_to_path($url) );
			if ($crop)
			{
				$ci->image_moo->resize_crop($width, $height);
			}
			else
			{
				$ci->image_moo->resize($width, $height);
			}
			$ci->image_moo->save(FCPATH . $thumbUrl, true);
			if ($ci->image_moo->errors) {
				return false;
			}
		}

		return base_url() . $thumbUrl;
	}
    
}

/**
 *	Alias for Resize Image
 */
if( ! function_exists('rev_aq_resize'))
{
	function rev_aq_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false)
	{
		return image_resize($url, $width, $height, $crop, $single, $upscale);
	}
}


/**
 *	Insert new image
 *
 *	@param	array	Data
 *	@param	string	Image
 *	@return	int		Id
 */
if( ! function_exists('wp_insert_attachment'))
{
	function wp_insert_attachment($data, $image) {
		$ci = &get_instance();
		$ci->load->model('image_model', 'Image');
		return $ci->Image->insert($image);
	}
}


/**
 *	Get image path by id
 *
 *	@param	int	$attachment_id
 *	@return	string
 */
if( ! function_exists('get_attached_file'))
{
	function get_attached_file($attachment_id) {
		$ci = &get_instance();
		$ci->load->model('image_model', 'Image');

		$imageUrl = $ci->Image->getUrl($attachment_id);

		if ( ! $imageUrl)
		{
			return false;
		}

		return FCPATH . 'media/' . $imageUrl;
	}
}


/**
 *	Get image id by url
 *
 *	@param	string	$url
 *	@return	int
 */
if( ! function_exists('get_image_id_by_url'))
{
	function get_image_id_by_url($url) {
		$ci = &get_instance();
		$ci->load->model('image_model', 'Image');

		$id = $ci->Image->getId($url);

		if ( ! $id)
		{
			return false;
		}

		return $id;
	}
}

if( ! function_exists('attachment_url_to_postid'))
{
	function attachment_url_to_postid($url) {
		return get_image_id_by_url($url);
	}
}

if( ! function_exists('image_url_to_path')) {

	/**
	 *	Convert image url to path
	 *
	 *	@param	string
	 *	@return	string
	 */

	function image_url_to_path($url) {
        $baseUrl = base_url() . RS_IMAGE_PATH . '/';
        $basePath = FCPATH . RS_IMAGE_PATH . '/';
        if (strpos($url, $baseUrl) === false && strpos($url, $basePath) === false) {
            return $url;
        }
        $image = str_replace(array($baseUrl, $basePath), '', $url);
        $path = $basePath . $image;
		return $path;
	}

}

/**
 *	For compatiblity
 */
if( ! function_exists('wp_generate_attachment_metadata'))
{
	function wp_generate_attachment_metadata() {
		return FALSE;
	}
}
if( ! function_exists('wp_update_attachment_metadata'))
{
	function wp_update_attachment_metadata() {
		return FALSE;
	}
}
if( ! function_exists('wp_get_attachment_metadata'))
{
	function wp_get_attachment_metadata() {
		return FALSE;
	}
}
if( ! function_exists('get_intermediate_image_sizes'))
{
	function get_intermediate_image_sizes() {
		return array();
	}
}
if( ! function_exists('get_the_title'))
{
	function get_the_title($id) {
		return '';
	}
}
