<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Model
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Models
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Streams_import_m extends MY_Model
{

	/**
	 * DB Table Name
	 * 
	 * @var string
	 */
	protected $_table = 'streams_import';
	
	/**
	 * Streams Namespace
	 * @var string
	 */
	public $namespace = 'streams_import';
	
	/**
	 * Stream Slug
	 * 
	 * @var string
	 */
	public $stream_slug = 'profiles';


	/**
	 * Constructor!
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Get all available profiles
	 * 
	 * @param array  $params  Pass in any params available with Streams API
	 * @return array
	 */
	public function get_profiles($params = array())
	{
		$defaults = array(
			'stream'    => $this->stream_slug,
			'namespace' => $this->namespace,
		);
		
		return $this->streams->entries->get_entries(array_merge($defaults, $params));
	}


	/**
	 * Get the Stream for SIM
	 * 
	 * @return Stream_obj
	 */
	public function get_stream()
	{
		return $this->streams->stream_obj($this->stream_slug, $this->namespace);
	}


	/**
	 * Get a Stream by ID
	 * 
	 * @param int  $id  Stream ID
	 * @return array
	 */
	public function get_stream_by_id($id = 0)
	{
		return $this->db->where('id', $id)->get('data_streams')->row();
	}


	/**
	 * Get the Profiles Stream with its fields
	 * 
	 * @return Stream_obj
	 */
	public function get_stream_with_fields(){
		$stream         = $this->get_stream();
		$stream->fields = $this->streams_m->get_stream_fields($stream->id);

		$stream_list = $this->db->select('id, stream_namespace, stream_slug')->order_by('stream_namespace, stream_slug')->get('data_streams')->result();
		$stream->stream_dropdown['null'] = '';
		foreach ($stream_list as $single_stream)
		{
			$stream->stream_dropdown[$single_stream->id] = $single_stream->stream_namespace . ' - ' . $single_stream->stream_slug;
		}
		
		return $this->stream_field_hack($stream);
	}


	/**
	 * Replaces the Streams ID field with a Dropdown
	 * 
	 * @todo: this should really be a Field Type
	 * 
	 * @param Stream_obj $stream  A stream object to apply the hack to; Has to be from `get_stream_with_field()` result
	 * @return Stream_obj
	 */
	public function stream_field_hack($stream)
	{
		// don't run twice
		if ( isset($stream->fields->stream_identifier->field_type) and $stream->fields->stream_identifier->field_type == 'choice') 
		{
			return $stream;
		}
		
		$choice_data = '';
		
		foreach ($stream->stream_dropdown as $id => $name) {
			$choice_data .= $id . ' : ' . $name . "\n";
		}
		
		# temporary hack -- act like a choice field type
		$stream->fields->stream_identifier->field_type = 'choice';
		$stream->fields->stream_identifier->field_data['choice_data'] = trim($choice_data, "\n");
		$stream->fields->stream_identifier->field_data['choice_type'] = 'dropdown';
		
		return $stream;
	}


	/**
	 * Replace default field structure with the "Quick Import" set instead
	 * 
	 * This is used to mock up since we don't store anything in the DB in Quick Import Mode
	 * 
	 * @param Stream_obj  $stream  A Stream object; auto applies Stream Field hack
	 * @return Stream_obj
	 */
	public function quick_import_fields($stream)
	{
		$faux_fields = array(
			'url'           => array(
				'assign_id'        => '138',
				'id'               => '151',
				'stream_name'      => 'lang:streams_import:title:url',
				'stream_slug'      => 'url',
				'stream_namespace' => 'streams_import',
				'stream_prefix'    => 'streams_import_',
				'about'            => null,
				'view_options'     => null,
				'title_column'     => 'url',
				'sorting'          => 'title',
				'is_hidden'        => 'no',
				'sort_order'       => '1',
				'stream_id'        => '34',
				'field_id'         => '151',
				'is_required'      => 'yes',
				'is_unique'        => 'no',
				'instructions'     => null,
				'field_name'       => 'lang:streams_import:fields:url',
				'field_slug'       => 'url',
				'field_namespace'  => 'streams_import',
				'field_type'       => 'text',
				'field_data'       => array(),
				'is_locked'        => 'no',
			),
			'source_format' => array(
				'assign_id'        => '138',
				'id'               => '151',
				'stream_name'      => 'lang:streams_import:title:source_format',
				'stream_slug'      => 'source_format',
				'stream_namespace' => 'streams_import',
				'stream_prefix'    => 'streams_import_',
				'about'            => null,
				'view_options'     => null,
				'title_column'     => 'url',
				'sorting'          => 'title',
				'is_hidden'        => 'no',
				'sort_order'       => '1',
				'stream_id'        => '34',
				'field_id'         => '151',
				'is_required'      => 'yes',
				'is_unique'        => 'no',
				'instructions'     => null,
				'field_name'       => 'lang:streams_import:fields:source_format',
				'field_slug'       => 'source_format',
				'field_namespace'  => 'streams_import',
				'field_type'       => 'choice',
				'field_data'       => array('choice_data'   => "json : JSON\ncsv : CSV", // @todo: should be a setting somewhere
				                            'choice_type'   => 'dropdown',
				                            'default_value' => 'json'
				),
				'is_locked'        => 'no',
			),
			'stream_identifier' => $stream->fields->stream_identifier
		);
		
		# overwrite the fields
		$stream->fields = new stdClass();
		
		foreach ($faux_fields as $key => $field) {
			$stream->fields->$key = (object) $field;
		}
		
		return $stream;
	}


	/**
	 * Inserts the data into the destination Stream
	 * 
	 * @param array  $data       An indexed array of rows of data to insert
	 * @param int    $stream_id  The Stream ID to insert into
	 * @param array  $map        The SOURCE => DESTINATION mapping; generated automatically
	 * @return array  Inserted data
	 */
	public function insert_stream_data($data = array(), $stream_id = 0, $map = array())
	{
		# ERROR
		if ($stream_id === 0) {
			show_error('Gimme a Stream ID bro!');
		}
		
		# generate the mapping
		if ( empty($map) ) {
			$source = $this->input->post('source');
			$destination = $this->input->post('destination');
			$include = $this->input->post('include');
			$map = array();
			
			// only included fields
			foreach ($include as $key => $value) {
				if ( $destination[$key] ) {
					$map[$source[$key]] = $destination[$key];
				}
				else {
					// somehow they submitted an invalid destination
					show_error('Sorry, you included an unmapped source field.');
				}
			}
		}
		
		# get the stream data
		$stream = $this->get_stream_by_id($stream_id);
		$stream->table = $stream->stream_prefix . $stream->stream_slug;
		
		# check for previous posts
		// @todo: check for previous posts
		
		// future insert items
		$insert = array();
		
		# format insert according to map
		foreach ($data as $i => $record) {
			$row = array(); // store the insert row here
			
			// remove unwanted data while mapping to correct column
			foreach ($map as $source_col => $dest_col) {
				// assign the actual value!
				$row[$dest_col] = $record[$source_col];
			}
			
			// make sure the required Streams fields are included
			//   NOTE: running here ensures if they didn't want to import their current matching data
			//   then we can generate a new one for them.
			foreach (array('created','created_by','ordering_count') as $col) {
				// if source data doesn't exist, it was a required column that was not included
				// so we have to generate it
				if ( ! isset($row[$col]) ) {
					switch($col) {
						case 'created_by':
							$val = $this->current_user->id; // @todo: allowed null, give option to set?
							break;
						case 'ordering_count':
							$val = $i; // @todo: should start at highest ordering count in table
							break;
						case 'created':
							$val = date('Y-m-d H:i:s', now());
							break;
					}
					$row[$col] = $val;
				}
			}
			
			// and include it to be inserted
			$insert[] = $row;
		}
		
		# now insert
		$this->db->insert_batch($stream->table, $insert);
		
		return $insert;
	}

}

/* EOF */