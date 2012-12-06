<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();


		$this->lang->load('streams_import');
	}


	public function index()
	{
		redirect('/admin/streams_import/profiles');
		//$this->template->build('admin/dashboard');
	}
}

/* EOF */