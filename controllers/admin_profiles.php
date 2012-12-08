<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin_profiles extends Admin_Controller
{

	protected $section = 'profiles';
	public $stream_slug;
	public $namespace;


	public function __construct()
	{
		parent::__construct();

		// Load Everything
		$this->load->library('streams_import');
		
		$this->stream_slug = $this->streams_import->stream_slug;
		$this->namespace = $this->streams_import->namespace;
	}


	public function index()
	{
		$profiles = $this->streams_import_m->get_profiles();

		$this->template
			->set('entries', $profiles)
			->set('section', $this->section)
			->set('namespace', $this->namespace)
			->set('title', lang($this->namespace . ':title:' . $this->section . ':index'));

		$this->template->build('admin/index');
	}


	public function create()
	{
		$this->streams_import->entry_form('new');

		// Build the template 
		$this->template->build('admin/profiles/create', $data);
	}


	public function mapping($id)
	{

		if ( $this->input->post() )
		{
			// We delete all existing data and we will overwrite everything
			$this->db->where('profile_relation_stream', $this->input->post('profileID'));
			$this->db->delete('streams_import_mapping');

			//Go generate and save the mapping
			$source = $this->input->post('source');
			$dest   = $this->input->post('destination');
			for ($i = 0; $i < $this->input->post('counter'); $i++)
			{

				$insert_data[] = array(
					'stream_field_id'         => $dest[$i],
					'entry_number'            => $source[$i],
					'ordering_count'          => $i,
					'created'                 => date('Y-m-d H:i:s'),
					'created_by'              => $this->current_user->id,
					'profile_relation_stream' => $this->input->post('profileID')
				);

				//var_dump($insert_data);
			}
			// Import them
			batch_insert_update('streams_import_mapping', $insert_data, array('ordering_count'));
			$this->session->set_flashdata('success', lang('streams_import:messages:mapping:save:success'));
			redirect('/admin/streams_import/profiles');

			die();
		}

		$params          = array(
			'stream'       => $this->stream_slug,
			'namespace'    => $this->namespace,
			'where'        => ' id = ' . $id
		);
		$request_entry   = $this->streams->entries->get_entries($params);
		$current_profile = $request_entry['entries'][0];
		//now get the stream of the profile
		$data->fields = $this->streams_m->get_stream_fields($current_profile['stream_identifier']);


		$data->field_count = count((array) $data->fields);

		// Feed the field dropdown
		foreach ($data->fields as $field)
		{
			$data->field_dropdown[$field->field_id] = $this->fields->translate_label($field->field_name);
		}
		
			$data->field_dropdown['id'] = 'id';
			$data->field_dropdown['created'] = 'created';
			$data->field_dropdown['updated'] = 'updated';
			$data->field_dropdown['created_by'] = 'created_by';

		// Feed the entry dropdown
		// $file_content = _pre_import_plain($current_profile['example_file']['file'],$current_profile['delimiter'],$current_profile['eol']);
		//$handle = fopen($current_profile['example_file']['file'], 'r');
		//$handle=stream_get_contents($handle);
		$file_content = _pre_import_csv_to_stream($current_profile['example_file']['file'], $current_profile['delimiter'], $current_profile['eol'], $current_profile['enclosure']);


		$data->csv_dropdown = $file_content['entries'][0];

		$this->template->build('admin/profiles/mapping', $data);
		//var_dump( $file_content);
	}


	public function edit($id)
	{

		$extra = array(
			'title'           => lang($this->namespace . ':title:' . $this->section . ':edit'),
			'success_message' => lang($this->namespace . ':messages:' . $this->section . ':edit:success'),
			'failure_message' => lang($this->namespace . ':messages:' . $this->section . ':edit:error'),
			'return'          => 'admin/' . $this->namespace . '/' . $this->section
		);

		echo $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'edit', $entry = $id, $view_override = true, $extra, $skips = array());
	}


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


	public function run($profile_id)
	{

		if ( isset($_POST['file_id']) )
		{
			if ( ! $this->streams_import->process_import($profile_id, $_POST['file_id']) )
			{
				$this->session->set_flashdata('error', lang('streams_import:messages:import:failure'));
			}
			else
			{
				$this->session->set_flashdata('success', lang('streams_import:messages:import:success'));
			}
			
			redirect('admin/' . $this->namespace . '/' . $this->section);
		}

		$files = $this->db->select('id, name')->where_in('extension', array('.csv', '.txt', '.xml'))->get('files');
		// Choose a file
		foreach ($files->result() as $row)
		{
			$data['files'][$row->id] = $row->name;
		}

		$data["profile_id"] = $profile_id;


		$this->template->build('admin/choose_csv', $data);
	}


}

/* EOF */