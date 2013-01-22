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
	public $temp_path = 'uploads/default/files/temp_unzip/';


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

		$profile = $this->ci->streams->entries->get_entry($profile_id,$this->stream_slug, $this->namespace, TRUE);

		//Import helper
		$profile_slug=create_slug($profile->profile_name);
		
		// _helper.php' Preproccess and post process
		$this->ci->load->helper('profiles/'.$profile_slug . '_pre');
		$this->ci->load->helper('profiles/'.$profile_slug . '_post');


		$data_array = $this->file_to_array($file_id, $profile->source_format['key'], $profile->unzip['key']);

		// get the mapping
		$params  = array(
			'stream'       => 'mapping',
			'namespace'    => $this->namespace,
			'where'        => " profile_relation_stream = " . $profile_id
		);
		$mapping = $this->ci->streams->entries->get_entries($params);

		//get the fields

		$stream = $this->ci->streams->stream_obj($profile->stream_identifier);
		$fields = $this->ci->streams_m->get_stream_fields($profile->stream_identifier);
		//prepare the array.
		foreach ($fields as $field)
		{
			$formated_fields[$field->field_id] = $field->field_slug; // ex : $formated_fields[59] = 'name'
		}
		//	if we work with XML and path loop is define, so we can load the node to loop
		if ($profile->source_format['key']=='xml' && $profile->xml_path_loop)
		{
			//Get the text between [] into array if there's more than 1 ! 
			$matches = get_values_between_brackets($profile->xml_path_loop);
		}

		//we parse the array until we got the xpath as the root
		

		foreach ($matches as $key) {
			//echo $key;			
			$data_array = $data_array["$key"];
		}

		// Build the batch
		foreach ($data_array as $entry)
		{
			foreach ($mapping['entries'] as $map)
			{
				$matches = get_values_between_brackets($map['entry_number']);
				$value=$entry;
				foreach ($matches as $key) {
					if (!empty($key) and isset($value[$key])): 
					$value = $value[$key];
					else:
					//if the matches is null then there is no value.
					$value = null;
					endif;	
				}
				
				$preprocess=$profile_slug .'_'.$map['stream_field'].'_sim_preprocess';
				//$preprocess($value)
				//jaap_file_for_my_home_stream_hide_city_sim_preprocess
				$processed_value = $preprocess($value);

				$insert_data[$map['stream_field']] = (empty($processed_value))?null:$processed_value; //name_of_field_sim_pre($entry[$map['entry_number']])
			}

			$entry_id =  $this->ci->streams->entries->insert_entry($insert_data, $stream->stream_slug, $stream->stream_namespace, $skips = array(), $extra = array());

			//call the post process function
			$post_process=$profile_slug .'_'.$stream->stream_slug.'_'.'sim_postprocess';
			$post_process($stream, $entry_id, $entry);


		}

		return true;
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
			//first we empty the temps folder
			delete_files($this->temp_path);

			//get the file
			//$this->ci->unzip->allow(array($format));
			//if($this->ci->unzip->extract('uploads/default/files/'.$file->filename, 'uploads/default/files/temp_unzip/'))
			if($this->ci->unzip->extract('uploads/default/files/'.$file->filename, $this->temp_path))
			{
				$directory_list	= directory_map('uploads/default/files/temp_unzip/');
				//now we search for our file
				foreach ($directory_list as $key => $value) {
					if (strtolower(array_pop(explode('.', $value))) == strtolower($format))
					{
						$path		= 'uploads/default/files/temp_unzip/'.$value;

					}
				}
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