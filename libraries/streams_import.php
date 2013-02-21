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
	public $temp_path;


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
		
		$this->ci->load->config('streams_import_c');
        $this->temp_path = $this->ci->config->item('streams_import:unzip_folder');

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
		// Get the file example if file_id is not set
		if ($file_id ==0 )
		{
			$file_id=get_fileid_by_profileid($profile_id);
		}
		//Log that shit !
		$log_id= $this->register_logs($file_id, $profile_id);

		$profile = $this->ci->streams_import_m->get_profile_object($profile_id);
		
		// _helper.php' Preproccess and post process
		$this->ci->load->helper('profiles/'.$profile->profile_slug . '_pre');
		$this->ci->load->helper('profiles/'.$profile->profile_slug . '_post');


		$data_array = $this->file_to_array($file_id, $profile);

		// get the mapping
		$params  = array(
			'stream'       => 'mapping',
			'namespace'    => $this->namespace,
			'where'        => " profile_relation_stream = " . $profile_id
		);
		$mapping = $this->ci->streams->entries->get_entries($params);


		// Debug
		//print_r($mapping);die;


		//get the fields
		$stream = $this->ci->streams->stream_obj($profile->stream_identifier);
		$fields = $this->ci->streams_m->get_stream_fields($profile->stream_identifier);
		
		//prepare the array.
		foreach ($fields as $field)
		{
			$formated_fields[$field->field_id] = $field->field_slug; // ex : $formated_fields[59] = 'name'
			//we save the folder field type because the update will be screwed if we don't unset it in the entry_data
			if($field->field_type == 'folder')
			{
				$fields_folder[]=$field->field_slug;
			}
	}
		
		
		

		// Soemtimes, the entries are not in the root of the array. You can deal with it in editing the profil and adding the path to the entries where we shoudl loop


		if ($profile->xml_path_loop)
		{
			//Get the text between [] into array if there's more than 1 ! 
			$matches = get_values_between_brackets($profile->xml_path_loop);

			//we parse the array until we got the xpath as the root
			foreach ($matches as $key) {
				//echo $key;			
				$data_array = $data_array["$key"];
			}
		}


		// Detect and get the top level of our data_array => DOESNT WORK.
		//$data_array = $this->array_to_top_level($data_array);

		// Debug
		//print_r($data_array);die;


		// Build the batch
		foreach ($data_array as $entry)
		{
			foreach ($mapping['entries'] as $map)
			{

				// Build the function name
				$preprocess=$profile->profile_slug .'_'.$map['stream_field'].'_sim_preprocess';
				
				// Process the value
				//if the entry_number is 'preprocess' then we call the preprocessor with the full entry set because nodata is passed out and they should be hardcoded
				$processed_value = ($map['entry_number']!='preprocess')?$preprocess($entry[$map['entry_number']]):$preprocess($entry);
				$insert_data[$map['stream_field']] = (empty($processed_value))?null:$processed_value;

				// Debug
				//print_r($insert_data);die;
			}

			//Ok now : INSERT OR UPDATE ? 
			//Check if the profile fields "unique_keys" is setted up
			if(!empty($profile->unique_keys))
			{
				//Oh... so you want to update ? explode the keys ! 
				$keys = explode(',', str_replace(' ', '', $profile->unique_keys));
				foreach ($keys as $key) {
					# code...
					$this->ci->db->where($key,$insert_data[$key]);
				}

				$update = $this->ci->db->limit(1)->get($stream->stream_namespace.'_'.$stream->stream_slug)->row();
			}


			if(!empty($update->id))
			{
				 $skips = array();
				//unset the folder fields.
				foreach ($fields_folder as $slug) {
					# code...
					unset($insert_data[$slug]);
					$skips[] = $slug;
				}
				//var_dump($insert_data);

				$this->ci->streams->entries->update_entry($update->id,$insert_data, $stream->stream_slug, $stream->stream_namespace,$skips, $extra = array());
			}
			elseif (($entry_id = $this->ci->streams->entries->insert_entry($insert_data, $stream->stream_slug, $stream->stream_namespace, $skips = array(), $extra = array())) === false)
			{
				continue;
			}

			//call the post process function
			$post_process=$profile->profile_slug .'_'.$stream->stream_slug.'_'.'sim_postprocess';
			
			$post_process($stream, $entry_id, $entry);
			//die();
		}
		$this->register_logs($file_id, $profile_id,$log_id);
		return true;
	}


	/**
	 * Process a source format into DB ready array
	 *
	 * @param string  $path    The URL or file path to file (runs `file_get_contents()` on it)
	 * @param string  $format  The format name: json | csv | etc...
	 * @return array
	 */
	public function file_to_array($file_id,$profile = null)
	{

		// Get the file
		$file = $this->ci->db->select()->where('id', $file_id)->limit(1)->get('files')->row(0);

		//if the profile param is numeric, then get the profile object :) that's the way I am ! 
		if (is_numeric($profile))
		{
			$profile = $this->streams_import_m->get_profile_object($profile);
		}

		//load and set the helper name 
		$this->ci->load->helper('profiles/'.$profile->profile_slug.'_pre');
		$decode_content = $profile->profile_slug .'_content_decode_sim_preprocess';

		if (strtoupper($profile->unzip) == 'YES')
		{
			//first we empty the temps folder
			delete_files($this->temp_path);

			//get the file
			if($this->ci->unzip->extract('uploads/default/files/'.$file->filename, $this->temp_path))
			{
				$directory_list	= directory_map($this->temp_path);
				
				//now we search for our file
				foreach ($directory_list as $key => $value) {
					if (strtolower(array_pop(explode('.', $value))) == strtolower($profile->source_format))
					{
						$path		= $this->temp_path.$value;

					}
				}
			}
		}
		else {

			$path = str_replace('{{ url:site }}', site_url(), $file->path);
		}
		# get the file contents
		try
		{
			if ($profile->source_format == 'CSV')
			{
				$content = fopen($path, 'r');
			}
			else
			{
				$content = file_get_contents($path);
			}
		}
		catch (Exception $e) {
			show_error('We\'re having trouble getting that file. Please make sure you have entered the correct path or URL');
		}
		
		// JSON
		if ($profile->source_format == 'JSON') {
			$data = json_decode($content, true);
		}
		
		// CSV
		if ($profile->source_format == 'CSV') {
			$this->ci->load->library('streams_import/CSVReader', null, 'csv');
			//get the profile setting

			if (!empty($profile))
			{
				$_separator = (!empty($profile->delimiter))?$profile->delimiter:',';
				$_enclosure =  (!empty($profile->enclosure))?$profile->enclosure:'"';
			}

			$data = $this->ci->csv->parse_file($path, $p_NamedFields = true, $limit = false, $_separator, $_enclosure);
		}	

		// XML
		if ($profile->source_format == 'XML') {

  			//XML to Array magic method ! 
  			//	$xml = simplexml_load_string($file);
			//	$json = json_encode($xml);
			//	$array = json_decode($json,TRUE);
			
			//$data = (array) simplexml_load_file($content, 'SimpleXMLElement', LIBXML_NOCDATA);	

			$data_raw= $this->ci->format->factory($decode_content($content), 'xml')	;
			$data=$data_raw->to_array();


		}
		
		# Validate data or ERROR
		// no data?
		if ( ! isset($data) ) {
			show_error('Sorry, this format is not supported.');
		}

		// Debug
		//print_r($data);die;

		return $data;
	}


	/**
	 * Formats the data to an array
	 *
	 * @param mixed $data
	 * @return array 
	 */
	public function post_process_array($data = null, $key =null, $array = array())
	{
		// Arry-ize that filthy bitch
		$data = (array) $data;

		
		// Debug
		//print_r($data);die;


		// Loop through and process
		foreach ($data as $key => $value)
		{
			/*
			 * Can we safely say that
			 * this is our "top" level?
			 *
			 * - We'll determine this by evaluating
			 *   the first three values if they exist.
			 * - If they are all arrays, we're probably 
			 *   in the entry level and need to continue INward
			 */

			$evaluate = array_values((array) $data);
			
			// Debug
			//print_r($data);die;

			if (
				is_array($value)
				 and isset($evaluate[0]) and is_array($evaluate[0])
				  and isset($evaluate[1]) and is_array($evaluate[1])
				   and isset($evaluate[2]) and is_array($evaluate[2])
				)
			{
				return $this->post_process_array($value,$key, $array);
			}
			else
			{
				foreach ($data as $k=>&$v)
				{
					$v = $k;
				}

				// Debug
				//print_r($data);die;

				return $data;
			}
		}
	}


	/**
	 * Array to top level
	 *
	 * @param mixed $data
	 * @return array 
	 */
	public function array_to_top_level($data = null, $key = false, $level = array())
	{
		// Arry-ize that filthy bitch
		$data = (array) $data;
		
		// What did we have last time?
		$last = $level;
		
		// Save this level for next iteration
		if ( $key ) $level = $level[$key]; else $level = $data;


		// Loop through and process that whore
		foreach ($data as $key => $value)
		{
			/*
			 * Can we safely say that
			 * this is our "top" level?
			 *
			 * - We'll determine this by evaluating
			 *   the first three values if they exist.
			 * - If they are all arrays, we're probably 
			 *   in the entry level and need to continue INward
			 */

			$evaluate = array_values((array) $data);
			
			// Debug
			//print_r($data);die;

			if (
				is_array($value)
				 and isset($evaluate[0]) and is_array($evaluate[0])
				  and isset($evaluate[1]) and is_array($evaluate[1])
				   and isset($evaluate[2]) and is_array($evaluate[2])
				)
			{
				return $this->array_to_top_level($value,$key, $level);
			}
			else
			{
				return $last;	// My brain.. is fried..
			}
		}
	}

	public function register_logs($filename, $profile_id, $id=null)
	{

		if (!is_null($id)){
			$skips = array('filename','profile_rel_logs');
			$this->ci->streams->entries->update_entry($id, $entry_data = array(), 'logs', 'streams_import',$skips, $extra = array());
		}
		else{

		$entry_data = array(
			'filename'			=>	$filename,
			'profile_rel_logs'		=>	$profile_id,
			);
		return $this->ci->streams->entries->insert_entry($entry_data, 'logs', 'streams_import', $skips = array(), $extra = array());
		}

	}

	
}

/* EOF */