<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Admin Profiles Controller
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Controllers
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Admin_profiles extends Admin_Controller
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
		$this->load->helper(array('folder',
                                  'file',
                                  'directory',
                                  'streams_import'
                             ));
		
		$this->stream_slug = $this->streams_import->stream_slug;
		$this->namespace = $this->streams_import->namespace;

	}


	/**
	 * Default Admin Home, Profiles list screen
	 */
	public function index()
	{

		$profiles = $this->streams_import_m->get_profiles();

		$this->template
			->set('entries', $profiles)
			->set('section', $this->section)
			->set('namespace', $this->namespace)
			->set('title', lang($this->namespace . ':title:' . $this->section . ':index'))
			->build('admin/index');
	}


	/**
	 * Create a new Profile
	 */
	public function create()
	{

		if ( $this->input->post() )
		{

			//helpers
			$stream = $this->input->post('stream_identifier');
			
			$slug_profile = create_slug($this->input->post('profile_name'));
			$fieldlist=$this->get_stream_fields_list( $stream);

			//we create a Pre helper file and an Post helper file for the new profile. 
			//In the pre helper, we have a specific function for each field of the stream.

            write_file($this->helpers_dir.'/' . strtolower($slug_profile) . '_pre_helper.php', '<?php ' . $this->load->view('templates/pre_process', array('fields' => $fieldlist, 'slug_profile'=>$slug_profile), true) . "\n?>");
            
            $stream_obj = $this->streams->stream_obj($stream);
            
            write_file($this->helpers_dir.'/' . strtolower($slug_profile) . '_post_helper.php', '<?php ' . $this->load->view('templates/post_process', array('stream_obj' => $stream_obj, 'slug_profile'=>$slug_profile), true) . "\n?>");
		}

		// Get stream 
		$stream       = $this->streams->stream_obj($this->stream_slug, $this->namespace);
		$data->fields = $this->streams_m->get_stream_fields($stream->id);

		//we get the list of all the Streams existing in this Pyro Instance
		$data->stream_dropdown = $this->get_stream_dropdown_list();

		// Processing the POST data    
		$extra = array(
			'title'           => lang($this->namespace . ':title:' . $this->section . ':create'),
			'success_message' => lang($this->namespace . ':messages:' . $this->section . ':create:success'),
			'failure_message' => lang($this->namespace . ':messages:' . $this->section . ':create:error'),
			'return'          => 'admin/' . $this->namespace . '/' . $this->section . '/create_profile_step2/-id-'
		);

		// Skip these for now, this will be in step 2
		$skip = array('ftp_host','login','password','url','xml_path_loop');

		$this->streams->cp->entry_form($this->section, $this->namespace, 'new', null, false, $extra, $skip);

		// Build the template 
		$this->template->set('page_title',lang($this->namespace . ':title:' . $this->section . ':create'))
						->build('admin/profiles/create', $data);
	}

	public function create_profile_step2($id_profile)
	{
		// Processing the POST data    
		$extra = array(
			'title'           => lang($this->namespace . ':title:' . $this->section . ':create'),
			'success_message' => lang($this->namespace . ':messages:' . $this->section . ':create:success'),
			'failure_message' => lang($this->namespace . ':messages:' . $this->section . ':create:error'),
			'return'          => 'admin/' . $this->namespace . '/' . $this->section . '/mapping/-id-'
		);
			$skip = array('profile_name','example_file','eol','delimiter','enclosure','stream_identifier','unzip','datasource','source_format','profile_slug');
			$this->streams->cp->entry_form($this->section, $this->namespace, 'edit', $id_profile, true, $extra,$skip);
	}

	/**
	 * Profile Mapping screen
	 * 
	 * @param $id  Profile ID
	 */
	public function mapping($id)
	{
		if ( $this->input->post() )
		{
			// We delete all existing data for this mapping, and we will overwrite everything
			$this->db->where('profile_relation_stream', $this->input->post('profileID'))
			->delete('streams_import_mapping');

			//Go generate and save the mapping
			$source = $this->input->post('source');
			$dest   = $this->input->post('destination');

			for ($i = 0; $i < $this->input->post('counter'); $i++)
			{
				if (isset($dest[$i])&&!(empty($dest[$i]))) {
					# code...
				
				$insert_data[] = array(
					'stream_field'         => $dest[$i],
					'entry_number'            => $source[$i],
					'ordering_count'          => $i,
					'created'                 => date('Y-m-d H:i:s'),
					'created_by'              => $this->current_user->id,
					'profile_relation_stream' => $this->input->post('profileID')
				);
				}
				//var_dump($insert_data);
			}

			// Import them
			batch_insert_update('streams_import_mapping', $insert_data, array('ordering_count'));
			$this->session->set_flashdata('success', lang('streams_import:messages:mapping:save:success'));
			redirect('/admin/streams_import/profiles');
		}

		// get the profile we're editing.
		$current_profile   = $this->streams->entries->get_entry($id,$this->stream_slug, $this->namespace, TRUE);

		//get the stream's fields we want import into
		$data->fields = $this->get_stream_fields_list($current_profile->stream_identifier);
		$data->field_count = count((array) $data->fields);	//number of fields we have for this stream.

		//Go for the magic ! Convert the example file to a PHP array.
		$data_array=$this->streams_import->file_to_array(get_fileid_by_profileid($current_profile->id), $current_profile->source_format['key'],$current_profile->unzip['key']);


		//	if we work with XML and path loop is define, so we can load the node to loop
		if ($current_profile->source_format['key']=='xml' && $current_profile->xml_path_loop)
		{
			$matches = get_values_between_brackets($current_profile->xml_path_loop);

			//we fetch the array until we got the xpath as the root
			foreach ($matches as $key) {			
				$data_array = $data_array["$key"];
			}

		}

		//we put there the null value.
		$data_array[0][null] = $this->config->item('dropdown_choose_null');

		//processing the output if the array
		$data->csv_dropdown = $this->streams_import->post_process_array($data_array[0]);


		$this->template->build('admin/profiles/mapping',$data);
	}


	/**
	 * Edit Profile screen
	 * 
	 * @param $id  Profile ID
	 */
	public function edit($id)
	{

		$extra = array(
			'title'           => lang($this->namespace . ':title:' . $this->section . ':edit'),
			'success_message' => lang($this->namespace . ':messages:' . $this->section . ':edit:success'),
			'failure_message' => lang($this->namespace . ':messages:' . $this->section . ':edit:error'),
			'return'          => 'admin/' . $this->namespace . '/' . $this->section
		);

		// Assign tabs
		$this->_tabs = array(
			array(
				'title' 	=> lang($this->namespace. ':tabs:' .'general'  ),
				'id'		=> 'recipients',
				'fields'	=> array('profile_name','example_file','eol','delimiter','enclosure','stream_identifier','unzip','datasource','source_format'),
				),
			array(
				'title' 	=> lang($this->namespace. ':tabs:' .'source_connection'  ),
				'id'		=> 'general',
				'fields'	=> array('ftp_host','login','password','url','xml_path_loop'),
				));

		echo $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'edit', $entry = $id, $view_override = true, $extra, $skips = array(),$this->_tabs);
	}


	/**
	 * Delete Profile screen
	 * 
	 * @param $id  Profile ID
	 */
	public function delete($id)
	{
		if ( $this->streams->entries->delete_entry($id, $this->section, $this->namespace) )
		{
			$this->session->set_flashdata('success', lang($this->namespace . ':messages:' . $this->section . ':delete:success'));
		}
		else
		{
			$this->session->set_flashdata('error', lang($this->namespace . ':messages:' . $this->section . ':delete:failure'));
		}
		
		redirect('admin/' . $this->namespace . '/' . $this->section);
	}


	/**
	 * Run an import against a Profile
	 * 
	 * @param $profile_id  Profile ID
	 */
	public function run($profile_id)
	{
		//if we do have a file to import
		if ( isset($_POST['file_id']) )
		{
			//try to run the import 
			if ( ! $this->streams_import->process_import($profile_id, $_POST['file_id']) )
			{
				$this->session->set_flashdata('error', lang('streams_import:messages:import:failure'));
			}
			else
			{
				$this->session->set_flashdata('success', lang('streams_import:messages:import:success'));
			}
			//well redirect to admin profiles
			redirect('admin/' . $this->namespace . '/' . $this->section);
		}

		$files = $this->db->select('id, name')->where_in('extension', array('.csv', '.txt', '.xml', '.zip'))->get('files');
		
		// Choose a file
		foreach ($files->result() as $row)
		{
			$data['files'][$row->id] = $row->name;
		}

		$data["profile_id"] = $profile_id;


		$this->template->build('admin/choose_csv', $data);
	}

		/**
	 * Create a dropdown array that can be used
	 * to choose an appropriate stream. These are
	 * separated by namespace.
	 *
	 * @access 	private
	 * @return 	array
	 */
	private function get_stream_dropdown_list()
	{
		$choices = array();

		// Now get our streams and add them
		// under their namespace
		$streams = $this->db
							->where('stream_namespace !=', 'users')
							->select('id, stream_name, stream_namespace')->get(STREAMS_TABLE)->result();
		
		foreach ($streams as $stream)
		{
			if ($stream->stream_namespace)
			{
				$choices[ucfirst($stream->stream_namespace)][$stream->id] = $stream->stream_name;
			}
		}

		return $choices;
	}

	private function get_stream_fields_list($stream_id)
	{
		$fields = $this->streams_m->get_stream_fields($stream_id);
		// Feed the field dropdown
		foreach ($fields as $field)
		{
			$fieldlist[$field->field_slug] = $this->fields->translate_label($field->field_name);
		}
		$fieldlist['id'] = 'id';
		$fieldlist['created'] = 'created';
		$fieldlist['updated'] = 'updated';
		$fieldlist['created_by'] = 'created_by';
		$fieldlist['ordering_count'] = 'ordering_count';
		$fieldlist[null] = $this->config->item('dropdown_choose_null');

		return $fieldlist;
	}





}

/* EOF */