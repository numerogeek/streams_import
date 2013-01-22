<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Admin Controller
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Controllers
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Admin extends Admin_Controller
{

	/**
	 * Constructor!
	 * 
	 * 
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('streams_import');
	}


	/**
	 * Redirect our home to the Profiles page
	 */
	public function index()
	{
		redirect('/admin/streams_import/profiles');
	}

}

/* EOF */