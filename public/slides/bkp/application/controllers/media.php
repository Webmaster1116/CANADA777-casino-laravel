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

class Media extends RS_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('image_CRUD');
	}

	/**
	 *	Load gallery dialog
	 */
	public function index()
	{
		$image_crud = new image_CRUD();

		$image_crud->set_primary_key_field('id');
		$image_crud->set_url_field('url');
		$image_crud->set_table('images')
            ->set_ordering_field('id')
            ->set_ordering_direction('desc')
			->set_image_path( RS_IMAGE_PATH )
			->set_thumbnail_prefix( RS_THUMB_FOLDER . '/gallery' );
		
		try {
			$output = $image_crud->render();
		}
		catch (Exception $e) {
		   echo json_encode((object)array('success' => false, 'responseProperty' => $e->getMessage()));
		   die();
		}
		//$output = $image_crud->render();
		$this->load->view('media/gallery_dialog', $output);
	}

	/**
	 *	Get thumbnail
	 */
	public function image() {
		$img = $this->input->get('img');
		$w = $this->input->get('w');
		$h = $this->input->get('h');
		$t = $this->input->get('t');

		$image = wp_get_attachment_image_src($img, 'full');
		if ($image)
		{
			redirect($image[0]);
		}
	}

}