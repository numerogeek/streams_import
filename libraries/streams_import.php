<?php

class Streams_import
{

	private $ci;
	
	public $stream_slug = 'profiles';
	public $namespace = 'streams_import';


	public function  __construct()
	{
		// Curl is needed
		$this->ci =& get_instance();

		// Load our goods
		$this->ci->load->helper('streams_import');
		$this->ci->lang->load('streams_import');
		$this->ci->load->library(array('streams','form_validation', 'streams_core/Fields'));
		$this->ci->load->model(array('streams_core/streams_m','streams_import/streams_import_m'));
	}


	public function process_import($profile_id, $file_id)
	{
		// Get the file
		$file = $this->ci->db->select()->where('id', $file_id)->limit(1)->get('files')->row(0);

		//Get the profile

		$params  = array(
			'stream'       => $this->stream_slug,
			'namespace'    => $this->namespace,
			'where'        => " id = " . $profile_id
		);
		$entries = $this->ci->streams->entries->get_entries($params);
		$profile = $entries['entries'][0];


		$data = _pre_import_csv_to_stream($file->path, $profile['delimiter'], $profile['eol'], $profile['enclosure']); //helper


		// get the mapping
		$params  = array(
			'stream'       => 'mapping',
			'namespace'    => $this->namespace,
			'where'        => " profile_relation_stream = " . $profile_id
		);
		$mapping = $this->ci->streams->entries->get_entries($params);

		//get the fields
		$stream = $this->ci->streams->stream_obj($profile['stream_identifier']);
		$fields = $this->ci->streams_m->get_stream_fields($profile['stream_identifier']);
		//prepare the array.
		foreach ($fields as $field)
		{
			$formated_fields[$field->field_id] = $field->field_slug;
		}


		$total = count($data['entries']);
		// Build the batch
		foreach ($data['entries'] as $entry)
		{
			// Add..
			$insert_data = array(
				'created'        => date('Y-m-d H:i:s'),
				'created_by'     => $this->ci->current_user->id,
				'ordering_count' => 0
			);
			foreach ($mapping['entries'] as $map)
			{
				switch  ($map['stream_field_id']) {
					case "id":
						$insert_data['id'] = (int)$entry[$map['entry_number']];
					break;
					case "created_by":
						$insert_data['created_by'] = $entry[$map['entry_number']];
					break;

					case "created":
						$insert_data['created'] = $entry[$map['entry_number']];
					break;
					case "updated":
						$insert_data['updated'] = $entry[$map['entry_number']];
					break;

					default:
					$insert_data[$formated_fields[$map['stream_field_id']]] = $entry[$map['entry_number']];
					break;
				}
			}
			$batch[] = $insert_data;

		}
		
		// Import them
		return batch_insert_update($stream->stream_prefix . $stream->stream_slug, $batch, array('ordering_count'));
	}
	
	
	public function entry_form($mode = 'new') {
		// Get stream
		$stream = $this->ci->streams_import_m->get_stream_with_fields();
		
		// Processing the POST data    
		$extra = array(
			'title'           => lang($this->namespace . ':title:' . $this->stream_slug . ':create'),
			'success_message' => lang($this->namespace . ':messages:' . $this->stream_slug . ':create:success'),
			'failure_message' => lang($this->namespace . ':messages:' . $this->stream_slug . ':create:error'),
			'return'          => 'admin/' . $this->namespace . '/' . $this->stream_slug . '/mapping/-id-'
		);
		
		return $this->ci->streams->cp->entry_form($this->stream_slug, $this->namespace, $mode, null, false, $extra);
	}


	public function mapping_form($new_data = array(), $stream_id = 0)
	{
		$map = $this->automap($new_data, $stream_id);
		
		//die(print_r($map));
		return $map;
	}


	/**
	 * Maps like IDs to the same
	 * 
	 * @param array $data
	 * @param int   $stream_id
	 * @return array
	 */
	public function automap($data = array(), $stream_id = 0)
	{	
		$stream_fields = $this->ci->streams_m->get_stream_fields($stream_id);
		$source_fields = $data[0];
		$destination_fields = array(0 => '');
		$i = 0;
		
		# for each destination field
		// set stream fields as values for dropdown
		foreach ($stream_fields as &$field) {
			$destination_fields[$field->field_slug] = $field->field_name;
		}
		
		# for each source field
		// set source and destination dropdowns
		foreach ($source_fields as $field_name => $value) {
			$fields[] = array(
				'source' => form_dropdown("source[$i]", array_combine(array_keys($source_fields), array_keys($source_fields)), $field_name),
				'destination' => form_dropdown("destination[$i]", $destination_fields, $field_name)
			);
			$i++;
		}
		
		return array(
			'fields' => $fields,
			'source_fields' => $source_fields,
			'destination_fields' => $destination_fields
		);
	}


	/**
	 * Process a source format into DB ready array
	 * 
	 * @param string  $format    The format name: json | csv | etc...
	 * @param string  $raw_data  The raw data string
	 * @return array
	 */
	public function process_to_array($format, $raw_data)
	{
		if ($format == 'json') {
			$data = json_decode($raw_data, true);
		}
			
		if ($format == 'csv') {
			
		}
			
		if ( ! isset($data) ) {
			show_error('Sorry, this format is not supported.');
		}
		
		return $data;
	}
	
	
}

/* EOF */