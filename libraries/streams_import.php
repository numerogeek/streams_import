<?php

class Streams_import
{

	private $CI;

	public function  __construct()
	{
		// Curl is needed
		$this->ci =& get_instance();

		// Load Drivers

        $this->ci->load->helper('streams_import');
        $this->ci->lang->load('streams_import');
        $this->ci->load->library('streams');
        $this->ci->load->model('streams_core/streams_m');     
        $this->ci->load->library(array('form_validation', 'streams_core/Fields'));
	}



	public function process_import($profile_id, $file_id)
	{
         // Get the file
         $file =  $this->ci->db->select()->where('id', $file_id)->limit(1)->get('files')->row(0);

		 //Get the profile


		$params = array(
            'stream'    => 'profiles',
            'namespace' => 'streams_import',
            'where'        => " id = ".$profile_id
        );
         $profile = $this->streams->entries->get_entries($params);	 

         

		 $data = _pre_import_csv_to_stream($file->path,$delimiter,$eol=false,$enclosure=null); //helper        



		// get the mapping

		$params = array(
            'stream'    => 'mapping',
            'namespace' => 'streams_import',
            'where'        => " profile_relation_stream = ".$profile_id
        );
         $mapping = $this->streams->entries->get_entries($params);

         //get the fields
        // $stream = $ci->streams->stream_obj();
         $data->fields = $ci->streams_m->get_stream_fields($profile['entries'][0]['stream_identifier']);

		// get the file entries



		//batch the insert


		//run the insert

		//go away

	}
}