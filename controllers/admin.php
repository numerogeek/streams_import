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
		// @todo: temporary hack
		$stream = $this->streams_import_m->quick_import_fields($stream);
		
		$stream->entry_form = $this->streams_import->entry_form('new');
		
		$this->template
			->set('page_title', lang('streams_import:title:profiles:quick_import'))
			->build('admin/quick_import/'.$page, $stream);
	}


	/**
	 * Quick Import mapping action
	 */
	public function _mapping()
	{
		# validation is handled in JS
		
		$data = array(
			'page_title'    => lang('streams_import:title:profiles:quick_import_mapping'),
			'url'           => $this->input->post('url'),
			'source_format' => $this->input->post('source_format'),
			'stream_id'     => $this->input->post('stream_identifier'),
		);
		
		$data['source_data'] = $this->streams_import->file_to_array($data['url'], $data['source_format']);
		
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
		$data['page_title'] = lang('streams_import:title:profiles:quick_import_success');
		
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