<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Profiles Controller
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Controllers
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Import extends Public_Controller
{

	/**
	 * Admin Section var
	 * 
	 * @var string
	 */
	protected $section = 'profiles';
	
	/**
	 * Stream Slug
	 * 
	 * Auto imported from Streams Import Library
	 * 
	 * @var string
	 */
	public $stream_slug;
	
	/**
	 * Stream Namespace
	 * 
	 * Auto imported from Streams Import Library
	 * 
	 * @var string
	 */
	public $namespace ='streams_import';

	/**
	 * SThe directory path of the profile helper
	 * 
	 *  
	 * @var string
	 */
	protected $helpers_dir; 


	/**
	 * Constructor!
	 * 
	 * Load the Library which loads all, and set some items automatically.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->helpers_dir= ADDONPATH.'modules/streams_import/helpers/profiles';

		// load everything
		$this->load->library('streams_import');
		$this->load->library('unzip');
		$this->load->library('format');
		$this->load->config('streams_import_c');
		$this->load->helper(array('folder',
                                  'file',
                                  'directory',
                                  'streams_import'
                             ));
		
		$this->stream_slug = $this->streams_import->stream_slug;
		$this->namespace = $this->streams_import->namespace;

	}


	/**
	 * Run an import against a Profile
	 * 
	 * @param $profile_id  Profile ID
	 */
	public function index($hash, $profile_id, $file_id)
	{

		// Check the hash
		if ($hash != $this->config->item('streams_import:hash')) die('goaway');
		
		//try to run the import 
		if ( ! $this->streams_import->process_import($profile_id, $file_id) )
		{
			echo 'error';
		}
		else
		{
			echo 'success';
		}

		exit;
	}
}

/* EOF */