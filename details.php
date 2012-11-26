<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Streams_import extends Module {

	public $version = 0.1;
	public $module_name = "streams_import";

	public function info()
	{
		return array (
  'name' => 
  array (
    'en' => 'Streams Import Manager',
    'fr' => 'Streams Import Manager',
  ),
  'description' => 
  array (
    'en' => 'Manage your Streams Import !',
    'fr' => 'Manage your Streams Import !',
  ),
  'backend' => true,
  'plugin' => true,
  'events' => true,
  'menu' => 'profiles',  
  'sections' => array(
	'profiles' => array(
		'name' 	=> $this->module_name.':title:profiles:index',
		'uri' 	=> 'admin/'.$this->module_name.'/profiles',
		'shortcuts' => array(
			'customers:add' => array(
				'name' 	=> 'profiles:button:add',
				'uri' 	=> 'admin/'.$this->module_name.'/profiles/create',
				'class' => 'add'
				)
			)
		)
	),
);
	}

	public function install()
	{
		$this->load->driver('Streams');
		$this->load->library('files/files');


		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Add Streams - profiles
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$stream_slug = "profiles";
		if($this->streams->streams->add_stream('lang:'. $this->module_name.':title:'.$stream_slug,	$stream_slug,	 $this->module_name,	$this->module_name.'_',	null)==true)
		{
			$stream_id = $this->db->where('stream_namespace', $this->module_name)->where('stream_slug', $stream_slug)->limit(1)->get('data_streams')->row()->id;
			$this->db->insert('settings', array(
				'slug'			=> 'sim_'.$stream_slug.'_stream_id',
				'title'			=> $this->module_name.' '.$stream_slug.' stream id',
				'description'	=>  $this->module_name.' '.$stream_slug.'stream id holder',
				'`default`'		=> '0',
				'`value`'		=> $stream_id,
				'type'			=> 'text',
				'`options`'		=> '',
				'is_required'	=> 1,
				'is_gui'		=> 0,
				'module'		=> $this->module_name
			));
			$stream_id=null;
		}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Add Fields profiles
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$field_slug = "profile_name";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'text',
				'extra'				=> array(
					'max_length'		=> 200
				),
				'assign'			=> $stream_slug,
				'title_column'		=> true,
				'required'			=> true,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "example_file";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'file',
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> true,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "delimiter";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'text',
				'extra'				=> array(
					'max_length'		=> 5
				),
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> false,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "eol";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'text',
				'extra'				=> array(
					'max_length'		=> 5
				),
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> false,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "stream_identifier";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'integer',
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> true,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Add Streams - equalities
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$stream_slug = "equalities";
		if($this->streams->streams->add_stream('lang:'. $this->module_name.':title:'.$stream_slug,	$stream_slug,	 $this->module_name,	$this->module_name.'_',	null)==true)
		{
			$stream_id = $this->db->where('stream_namespace', $this->module_name)->where('stream_slug', $stream_slug)->limit(1)->get('data_streams')->row()->id;
			$this->db->insert('settings', array(
				'slug'			=> 'sim_'.$stream_slug.'_stream_id',
				'title'			=> $this->module_name.' '.$stream_slug.' stream id',
				'description'	=>  $this->module_name.' '.$stream_slug.'stream id holder',
				'`default`'		=> '0',
				'`value`'		=> $stream_id,
				'type'			=> 'text',
				'`options`'		=> '',
				'is_required'	=> 1,
				'is_gui'		=> 0,
				'module'		=> $this->module_name
			));
			$stream_id=null;
		}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Add Fields equalities
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$field_slug = "stream_field_id";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'integer',
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> false,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "entry_number";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'integer',
				'assign'			=> $stream_slug,
				'title_column'		=> false,
				'required'			=> false,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "profile";
		if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
		{
			$field = array(
				'name'				=> 'lang:'.$this->module_name.':fields:'.$field_slug,
				'slug'				=> $field_slug,
				'namespace'			=> $this->module_name,
				'type'				=> 'relationship',
				'extra'				=> array(
					'choose_stream' => Settings::get('sim_profiles_stream_id')),
				'title_column'		=> true,
				'required'			=> true,
				'unique'			=> false
			);
			$this->streams->fields->add_field($field);
		}	

		return true;	
	}

	public function uninstall()
	{
		$this->load->driver('Streams');

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Delete the Uploads folder and remove its ID in the settings table
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			$this->db->delete('settings', array('module' => $this->module_name));
				
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Remove Streams News
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->streams->utilities->remove_namespace($this->module_name);
		return true;
		
	}

	public function upgrade($old_version)
	{
		return TRUE;
	}

	public function help()
	{
	}
}
/* End of file details.php */
?>