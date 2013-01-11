<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Library
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Libraries
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Streams_import
{

	/**
	 * CI Instance
	 * 
	 * @var CI_Controller
	 */
	private $ci;

	/**
	 * Stream Slug
	 * 
	 * Master record. All other classes pull this value.
	 * 
	 * @var string
	 */
	public $stream_slug = 'profiles';
	
	/**
	 * Stream Namespace
	 * 
	 * Master record. All other classes pull this value.
	 * 
	 * @var string
	 */
	public $namespace = 'streams_import';


	/**
	 * Constructor!
	 */
	public function  __construct()
	{
		$this->ci =& get_instance();

		// load everything here
		$this->ci->load->helper('streams_import');
		$this->ci->lang->load('streams_import');
		$this->ci->load->library(array('streams','form_validation', 'streams_core/Fields','format','unzip','format'));
		$this->ci->load->model(array('streams_core/streams_m','streams_core/row_m','streams_import/streams_import_m'));
	}


	/**
	 * Process an Import
	 * 
	 * @param int  $profile_id  Profile ID
	 * @param int  $file_id     The ID of the file we are importing
	 * @return bool
	 */
	public function process_import($profile_id, $file_id = 0)
	{


		// Get the file
		if ($file_id ==0 )
		{
			get_fileid_by_profileid($profile_id);
		}

		//Get the profile

		$params  = array(
			'stream'       => $this->stream_slug,
			'namespace'    => $this->namespace,
			'where'        => " id = " . $profile_id
		);
		$entries = $this->ci->streams->entries->get_entries($params);
		$profile = $entries['entries'][0];

		//Import helper
		$profile_slug=create_slug($profile['profile_name']);
		// _helper.php'
		$this->ci->load->helper('profiles/'.$profile_slug . '_pre');



		//$data = _pre_import_csv_to_stream($file->path, $profile['delimiter'], $profile['eol'], $profile['enclosure']); //helper


		$data_array = $this->file_to_array($file_id, $profile['source_format']['key'], $profile['unzip']['key']);

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
			$formated_fields[$field->field_id] = $field->field_slug; // ex : $formated_fields[59] = 'name'
		}
//var_dump($formated_fields);

		//	if we work with XML and path loop is define, so we can load the node to loop
		if ($profile['source_format']['key']=='xml' && $profile['xml_path_loop'])
		{
			//Get the text between [] into array if there's more than 1 ! 
			$matches = get_values_between_brackets($profile['xml_path_loop']);
		}

		//we parse the array until we got the xpath as the root
		

		foreach ($matches as $key) {
			//echo $key;			
			$data_array = $data_array["$key"];
		}

		// Build the batch
		foreach ($data_array as $entry)
		{
			// Add..
			/*$insert_data = array(
				'created'        => date('Y-m-d H:i:s'),
				'created_by'     => $this->ci->current_user->id,
				'ordering_count' => 0
			);*/
			foreach ($mapping['entries'] as $map)
			{
				$matches = get_values_between_brackets($map['entry_number']);
				$value=$entry;
				foreach ($matches as $key) {
					if (!empty($key)): 
					$value = $value[$key];
					else:
					//if the matches is null then there is no value.
					$value = null;
					endif;	
				}
				
				$preprocess=$profile_slug .'_'.$map['stream_field_id'].'_sim_preprocess';
				//$preprocess($value)
				//jaap_file_for_my_home_stream_hide_city_sim_preprocess

				$insert_data[$map['stream_field_id']] = $preprocess($value); //name_of_field_sim_pre($entry[$map['entry_number']])
			}

			$this->ci->row_m->insert_entry($insert_data, $fields, $stream, $skips = array(), $extra = array());

				var_dump($insert_data);
				die();
			$batch[] = $insert_data;

		}
		
		// Import them
		die();
		return batch_insert_update($stream->stream_prefix . $stream->stream_slug, $batch, array('ordering_count'));
	}


	/**
	 * Generate the entry form and other goodies
	 * 
	 * @param string  $mode  Form mode: new | edit
	 * @return array
	 */
	public function entry_form($mode = 'new') {
		$stream = $this->ci->streams_import_m->get_stream_with_fields();
		
		return array(
			'mode'   => $mode,
			'fields' => $stream->fields,
			'stream' => $stream
		);
	}


	/**
	 * Generate the Mapping form
	 * 
	 * @param array  $data       Raw source data (after processed by `file_to_array()`)
	 * @param int    $stream_id  ID of Stream to match to
	 * @return array
	 */
	public function mapping_form($data = array(), $stream_id = 0)
	{	
		# gather our fields
		$stream_fields = (array) $this->ci->streams_m->get_stream_fields($stream_id);
		$core_fields = array();
		$source_fields = $data[0];
		$destination_fields = array();
		
		# add some required fields if necessary; for Streams support
		foreach (array('id', 'created', 'updated', 'created_by', 'ordering_count') as $col) {
			if ( ! isset($stream_fields[$col]) ) {
				$core_fields[$col] = ucwords(str_replace('_', ' ', $col));
			}
		}
		
		# for each destination field
		// format for dropdown element
		foreach ($stream_fields as &$field) {
			$destination_fields[$field->field_slug] = $field->field_name;
		}
		
		// add in some optgroups and default item
		$destination_fields = array(
			0 => '', // "Select an Option"
			'Streams Core Fields' => $core_fields,
			'Custom Fields' => $destination_fields
		);
		
		# for each source field
		// format for dropdowns
		$i = 0;
		foreach ($source_fields as $field_name => $value) {
			$fields[] = array(
				'include' => form_checkbox("include[$i]", true, true, 'class="row_checkbox" title="'.lang('streams_import:fields:include_one').'"'),
				'source' => $field_name . form_hidden("source[$i]", $field_name),
				'destination' => form_dropdown("destination[$i]", $destination_fields, $field_name)
			);
			$i++;
		}
		
		return array(
			'fields' => $fields,
			'core_fields' => $core_fields,
			'source_fields' => $source_fields,
			'destination_fields' => $destination_fields
		);
	}


	/**
	 * Process a source format into DB ready array
	 *
	 * @param string  $path    The URL or file path to file (runs `file_get_contents()` on it)
	 * @param string  $format  The format name: json | csv | etc...
	 * @return array
	 */
	public function file_to_array($file_id, $format, $unzip='0')
	{

		// Get the file
		$file = $this->ci->db->select()->where('id', $file_id)->limit(1)->get('files')->row(0);


		if ($unzip == '1')
		{
			//get the file
			$this->ci->unzip->allow(array($format));
			//if($this->ci->unzip->extract('uploads/default/files/'.$file->filename, 'uploads/default/files/temp_unzip/'))
			if($this->ci->unzip->extract('uploads/default/files/'.$file->filename, 'uploads/default/files/temp_unzip/'))
			{
				$directory_list	= directory_map('uploads/default/files/temp_unzip/');
				$path		= 'uploads/default/files/temp_unzip/'.$directory_list[0];
			}
		}
		else {

			$path = $file->path;
		}

		# get the file contents
		try {
			$content = file_get_contents($path);
		}
		catch (Exception $e) {
			show_error('We\'re having trouble getting that file. Please make sure you have entered the correct path or URL');
		}
		
		// JSON
		if ($format == 'json') {
			$data = json_decode($content, true);
		}
		
		// CSV
		if ($format == 'csv') {
			$this->ci->load->library('streams_import/CSVReader', null, 'csv');
			$data = $this->ci->csv->parse_file($path, $p_NamedFields = true, $limit = false, $_separator = ',', $_enclosure = '"');
		}	

		// XML
		if ($format == 'xml') {

  			//XML to Array magic method ! 
  			//	$xml = simplexml_load_string($file);
			//	$json = json_encode($xml);
			//	$array = json_decode($json,TRUE);
			
			//$data = (array) simplexml_load_file($content, 'SimpleXMLElement', LIBXML_NOCDATA);	
			$data_raw= $this->ci->format->factory($content, 'xml')	;
			$data=$data_raw->to_array();
			//add the key into the label :)
			//$data=$this->to_array($data);
		}
		
		# Validate data or ERROR
		// no data?
		if ( ! isset($data) ) {
			show_error('Sorry, this format is not supported.');
		}
	/*	// non-indexed array
		if ( ! isset($data[0]) ) {
			show_error('Your data needs to be an indexed array.');
		}
		*/
		return $data;
	}


		/**
	 * Formats the data to an array
	 *
	 * @param mixed $data
	 * @return array 
	 */
	public function post_process_array($data = null, $group =null)
	{

		$array = array();

		foreach ((array) $data as $key => $value)
		{
			if (is_object($value) or is_array($value))
			{
				$array[$key] = $this->post_process_array($value,$key);
			}
			else
			{
				if(is_null($group)):
				$array['['.$key.']'] = $key.'-'.$value;
				else:
					$array['['.$group.']['.$key.']'] = $key.'-'.$value;
				endif;

			}
		}

		return $array;
	}
	
	
}

/* EOF */