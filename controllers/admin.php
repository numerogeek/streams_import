<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Admin Controller
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Models
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Admin extends Admin_Controller
{

	/**
	 * Constructor!
	 * 
	 * Include the Library which includes all
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


	/**
	 * Quick Import wizard action
	 * 
	 * @param string  $page  Our current page / step
	 */
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


	/**
	 * Quick Import mapping action
	 */
	public function _mapping()
	{
		# validation is handled in JS
		
		$data = array(
			'page_title'    => 'Quick Import',
			'url'           => $this->input->post('url'),
			'source_format' => $this->input->post('source_format'),
			'stream_id'     => $this->input->post('stream_identifier'),
		);
		
		$data['raw_source_data'] = file_get_contents($data['url']);
		
		$data['source_data'] = $this->streams_import->process_to_array($data['source_format'], $data['raw_source_data']);
		
		$form = $this->streams_import->mapping_form($data['source_data'], $data['stream_id']);
		
		$data = array_merge($data, $form);
		
		# store the data to use in next step
		$this->pyrocache->write(serialize($data), 'sim_quick_import');
		
		$this->template->build('admin/quick_import/mapping', $data);
	}


	/**
	 * Quick Import importing action
	 */
	public function _import()
	{
		$data = unserialize($this->pyrocache->get('sim_quick_import'));
		
		# destroy!
		$this->pyrocache->delete('sim_quick_import');
		
		$data['inserted_rows'] = $this->streams_import_m->insert_stream_data($data['source_data'], $data['stream_id']);
		
		$data['total_inserted'] = count($data['inserted_rows']);
		
		// replace special characters for security reasons
		$data['inserted_rows'] = json_decode(htmlspecialchars(json_encode($data['inserted_rows']), ENT_NOQUOTES));
		
		$this->template->build('admin/quick_import/success', $data);
	}
}

/* EOF */