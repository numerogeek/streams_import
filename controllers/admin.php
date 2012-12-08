<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('streams_import');
	}


	public function index()
	{
		redirect('/admin/streams_import/profiles');
		//$this->template->build('admin/dashboard');
	}
	
	
	public function quick_import($page = 'index')
	{
		# validation is handled in JS
		
		if ($page == 'mapping') {
			return $this->_mapping();
		}
		if ($page == 'import') {
			return $this->_import();
		}
		
		$stream = $this->streams_import_m->get_stream_with_fields();
		# temporary hack
		$stream = $this->streams_import_m->quick_import_fields($stream);
		
		$stream->entry_form = $this->streams_import->entry_form('new');
		
		//die(print_r($stream));
		
		$this->template->set('page', $page)->set('page_title', 'Quick Import')->build('admin/quick_import/'.$page, $stream);
	}


	public function _mapping()
	{
		# validation is handled in JS
		
		$url           = $this->input->post('url');
		$source_format = $this->input->post('source_format');
		$stream_id     = $this->input->post('stream_identifier');
		
		$raw_data = file_get_contents($url);
		
		$data = $this->streams_import->process_to_array($source_format, $raw_data);
		
		$data = $this->streams_import->mapping_form($data, $stream_id);
		
		$this->template->set('page_title', 'Quick Import')->build('admin/quick_import/mapping', $data);
	}


	public function _import()
	{
		die(print_r($_POST));
		$this->template->set('page_title', 'Quick Import')->build('admin/quick_import/import');
	}
}

/* EOF */