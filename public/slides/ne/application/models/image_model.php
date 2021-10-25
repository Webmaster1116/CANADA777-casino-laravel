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

class Image_model extends CI_Model {

	/*
	 * Table name
	 *
	 */
	public $table = 'images';

	/**
	 *	Get image url
	 *
	 *	@param	int		Id
	 *	@return	string	Image url
	 */
	public function getUrl($id) {
		$image = $this->db->where('id', $id)->get($this->table)->row();
		return $image ? $image->url : FALSE;
	}

	/**
	 *	Get image ud by url
	 *
	 *	@param	string	Url
	 *	@return	int		Id
	 */
	public function getId($url) {
		$image = $this->db->where('url', $url)->get($this->table)->row();
		return $image ? $image->id : FALSE;
	}

	/**
	 *	Add new image
	 *
	 *	@param	string	Image
	 *	@return	int		Id
	 */
	public function insert($image) {
		$this->db->set('url', $image)->insert($this->table);
		return $this->db->insert_id();
	}

}