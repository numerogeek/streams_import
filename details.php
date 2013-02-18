<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Module
 * 
 * Import data into Streams from various formats and mediums
 *
 * @package  PyroCMS\Addons\Modules\Streams Import
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Module_Streams_import extends Module
{

	public $version = 0.6;
	public $module_name = 'streams_import';


	public function info()
	{
		return array(
			'name'        => array(
				'en' => 'Streams Import Manager',
				'fr' => 'Streams Import Manager',
			),
			'description' => array(
				'en' => 'Manage your Streams Import !',
				'fr' => 'Manage your Streams Import !',
			),
			'frontend' 	  => true,
			'backend'     => true,
			'plugin'      => true,
			'events'      => true,
			'menu'        => 'content',
			'sections'    => array(
				'profiles' => array(
					'name'      => $this->module_name . ':title:profiles:index',
					'uri'       => 'admin/' . $this->module_name . '/profiles',
				),
				'logs' => array(
					'name'      => $this->module_name . ':title:logs:index',
					'uri'       => 'admin/' . $this->module_name . '/logs'
				)
			),
			'shortcuts' => array(
				'customers:add' => array(
					'name'   => 'profiles:button:add',
					'uri'    => 'admin/' . $this->module_name . '/profiles/create',
					'class'  => 'add'
				)
			),
		);
	}


	public function install()
	{
		$this->load->driver('Streams');
		$this->load->library('files/files');
		
		// Make the Uploads folder and store its ID in the settings table
		if ( ($folder = Files::create_folder(0, $this->module_name)) == true )
			{
				$folder_id = $folder['data']['id'];
				$this->db->insert('settings', array(
					'slug'         => $this->module_name . '_folder',
					'title'        => $this->module_name . ' Folder',
					'description'  => 'A ' . $this->module_name . ' Folder ID Holder',
					'`default`'    => '0',
					'`value`'      => $folder_id,
					'type'         => 'text',
					'`options`'    => '',
					'is_required'  => 1,
					'is_gui'       => 0,
					'module'       => $this->module_name
				));
			}



		// Add Streams - profiles
		$stream_slug = "profiles";
		if ( $this->streams->streams->add_stream('lang:' . $this->module_name . ':title:' . $stream_slug, $stream_slug, $this->module_name, $this->module_name . '_', null) == true )
		{
			$stream_id = $this->db->where('stream_namespace', $this->module_name)->where('stream_slug', $stream_slug)->limit(1)->get('data_streams')->row()->id;
			$this->db->insert('settings', array(
				'slug'         => 'sim_' . $stream_slug . '_stream_id',
				'title'        => $this->module_name . ' ' . $stream_slug . ' stream id',
				'description'  => $this->module_name . ' ' . $stream_slug . 'stream id holder',
				'`default`'    => '0',
				'`value`'      => $stream_id,
				'type'         => 'text',
				'`options`'    => '',
				'is_required'  => 1,
				'is_gui'       => 0,
				'module'       => $this->module_name
			));
			$stream_id = null;
		}
		
		// Add Fields profiles
		$field_slug = "profile_name";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 200
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => true,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}	
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'slug_field'    => 'profile_name',
					                     'space_type'	=>'_'
				                     ),
				'assign'          => 'profiles',
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}	

		$field_slug = "example_file";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'file',
				'extra'           => array('folder' => $folder_id,
									'allowed_types'=>'*'),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "delimiter";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 5,
					                     'default_value' => ','
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'instructions'    => 'lang:' . $this->module_name . ':fields:' . $field_slug . ':instructions',
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "eol";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 5,
					                     'default_value' => '\n'
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}
		
		$field_slug = "enclosure";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 5,
					                     'default_value' => ''
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "stream_identifier";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'integer',
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "unzip";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'choice',
				'extra'			  => array(
						'choice_type'=> 'dropdown',
						'choice_data'=>	"0 : No \n ".
										"1 : Yes",
						'default_value'=>'0'

					),
				'assign'          => $stream_slug,
				'instructions'	  => 'lang:' . $this->module_name . ':fields:' . $field_slug.'_instructions',
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "datasource";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'choice',
				'extra'			  => array(
						'choice_type'=> 'dropdown',
						'choice_data'=>	"0 : File",
						'default_value'=>'0'

					),
				'assign'          => $stream_slug,
				'instructions'	  => 'lang:' . $this->module_name . ':fields:' . $field_slug.'_instructions',
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "source_format";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'choice',
				'extra'			  => array(
						'choice_type'=> 'dropdown',
						'choice_data'=>	"xml : XML \n ".
										"csv : CSV \n ".
										"txt : TXT \n ".
										"json : JSON \n ".
										"rss : RSS ",
						'default_value'=>'0'

					),
				'assign'          => $stream_slug,
				'instructions'	  => 'lang:' . $this->module_name . ':fields:' . $field_slug.'_instructions',
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		//STEP 2 OF PROFILE

		$field_slug = "xml_path_loop";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 200
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'instructions'     => 'lang:' . $this->module_name . ':fields:' . $field_slug.'_instructions',
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		// Add Streams - equalities
		$stream_slug = "mapping";
		if ( $this->streams->streams->add_stream('lang:' . $this->module_name . ':title:' . $stream_slug, $stream_slug, $this->module_name, $this->module_name . '_', null) == true )
		{
			$stream_id = $this->db->where('stream_namespace', $this->module_name)->where('stream_slug', $stream_slug)->limit(1)->get('data_streams')->row()->id;
			$this->db->insert('settings', array(
				'slug'         => 'sim_' . $stream_slug . '_stream_id',
				'title'        => $this->module_name . ' ' . $stream_slug . ' stream id',
				'description'  => $this->module_name . ' ' . $stream_slug . 'stream id holder',
				'`default`'    => '0',
				'`value`'      => $stream_id,
				'type'         => 'text',
				'`options`'    => '',
				'is_required'  => 1,
				'is_gui'       => 0,
				'module'       => $this->module_name
			));
			$stream_id = null;
		}

		// Add Fields equalities
		$field_slug = "stream_field";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 200
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "entry_number";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'extra'           => array(
					                     'max_length'    => 200
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => false,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}

		$field_slug = "profile_relation_stream";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'relationship',
				'extra'           => array(
				                       'choose_stream' => Settings::get('sim_profiles_stream_id')
				                     ),
				'title_column'    => false,
				'assign'          => $stream_slug,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}
		else {
			die('error');
		}

		// Add Streams - LOG
		$stream_slug = "logs";
		if ( $this->streams->streams->add_stream('lang:' . $this->module_name . ':title:' . $stream_slug, $stream_slug, $this->module_name, $this->module_name . '_', null) == true )
		{
			$stream_id = $this->db->where('stream_namespace', $this->module_name)->where('stream_slug', $stream_slug)->limit(1)->get('data_streams')->row()->id;
			$this->db->insert('settings', array(
				'slug'         => 'sim_' . $stream_slug . '_stream_id',
				'title'        => $this->module_name . ' ' . $stream_slug . ' stream id',
				'description'  => $this->module_name . ' ' . $stream_slug . 'stream id holder',
				'`default`'    => '0',
				'`value`'      => $stream_id,
				'type'         => 'text',
				'`options`'    => '',
				'is_required'  => 1,
				'is_gui'       => 0,
				'module'       => $this->module_name
			));
			$stream_id = null;
		}
		$update_data = array(
			'view_options'=> array('created','updated','profile_rel_logs','filename')
		);
		$this->streams->streams->update_stream($stream_slug, $this->module_name, $update_data);

		
		// Add Fields logs
		$field_slug = "profile_rel_logs";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'relationship',
				'extra'           => array(
					                     'choose_stream'    => Settings::get('sim_profiles_stream_id')
				                     ),
				'assign'          => $stream_slug,
				'title_column'    => true,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}	

		// Add Fields profiles
		$field_slug = "filename";
		if ( $this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows() == null )
		{
			$field = array(
				'name'            => 'lang:' . $this->module_name . ':fields:' . $field_slug,
				'slug'            => $field_slug,
				'namespace'       => $this->module_name,
				'type'            => 'text',
				'assign'          => $stream_slug,
				'title_column'    => false,
				'required'        => true,
				'unique'          => false
			);
			$this->streams->fields->add_field($field);
		}	
		return true;
	}


	public function uninstall()
	{
		$this->load->driver('Streams');
		$this->load->library('files/files');
		
		// Delete the Uploads folder and remove its ID in the settings table
		Files::delete_folder(Settings::get($this->module_name . '_folder'));
		$this->db->delete('settings', array('module' => $this->module_name));

		// Remove Streams News
		$this->streams->utilities->remove_namespace($this->module_name);
		
		return true;
	}


	public function upgrade($old_version)
	{


		return true;
	}


	public function help()
	{
		return 'No Help Available Yet.';
	}
	
}

/* End of file details.php */