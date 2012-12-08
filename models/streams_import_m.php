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

	protected $_table = 'streams_import';
	public $namespace = 'streams_import';
	public $stream_slug = 'profiles';


	public function __construct()
	{
		parent::__construct();
	}


	public function get_profiles($params = array())
	{
		$defaults = array(
			'stream'    => $this->stream_slug,
			'namespace' => $this->namespace,
		);
		
		return $this->streams->entries->get_entries(array_merge($defaults, $params));
	}


	public function get_stream()
	{
		return $this->streams->stream_obj($this->stream_slug, $this->namespace);
	}
	
	
	public function get_stream_by_id($id = 0)
	{
		return $this->db->select('id, stream_namespace, stream_slug')->where('id', $id)->get('data_streams')->row();
	}


	public function get_stream_with_fields(){
		$stream         = $this->get_stream();
		$stream->fields = $this->streams_m->get_stream_fields($stream->id);

		$stream_list = $this->db->select('id, stream_namespace, stream_slug')->order_by('stream_namespace, stream_slug')->get('data_streams')->result();
		$stream->stream_dropdown['null'] = '';
		foreach ($stream_list as $single_stream)
		{
			$stream->stream_dropdown[$single_stream->id] = $single_stream->stream_namespace . ' - ' . $single_stream->stream_slug;
		}
		
		return $stream;
	}


	public function stream_field_hack($stream)
	{
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


	public function quick_import_fields($stream)
	{
		$stream = $this->stream_field_hack($stream);
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
				'field_data'       => array('choice_data'   => "json : JSON\ncsv : CSV",
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

}

/* EOF */