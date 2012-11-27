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
         $entries = $this->ci->streams->entries->get_entries($params);
         $profile=$entries['entries'][0];	 

         

		 $data = _pre_import_csv_to_stream($file->path,$profile['delimiter'],$profile['eol'],$profile['enclosure']); //helper        



		// get the mapping

		$params = array(
            'stream'    => 'mapping',
            'namespace' => 'streams_import',
            'where'        => " profile_relation_stream = ".$profile_id
        );
         $mapping = $this->ci->streams->entries->get_entries($params);

         //get the fields
         $stream = $this->ci->streams->stream_obj($profile['stream_identifier']);
         $fields = $this->ci->streams_m->get_stream_fields($profile['stream_identifier']);
         //prepare the array.
         foreach ($fields as $field) {
            $formated_fields[$field->field_id] = $field->field_slug;
         }


        $total = count($data['entries']);
        // Build the batch
        foreach ( $data['entries'] as $entry )
        {
            // Add..
            $insert_data = array(
                'created' => date('Y-m-d H:i:s'),
                'created_by' =>  $this->ci->current_user->id,
                'ordering_count' => 0
                );
            foreach ($mapping['entries'] as $map) {

                $insert_data[$formated_fields[$map['stream_field_id']]] =$entry[$map['entry_number']];                
            }
            $batch[] = $insert_data ;

        }
       // Import them
       return batch_insert_update($stream->stream_prefix.$stream->stream_slug, $batch, array('ordering_count'));     

	}
}