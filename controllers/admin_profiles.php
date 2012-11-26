<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin_profiles extends Admin_Controller
{
    protected $section = 'profiles';
    protected $stream_slug = 'profiles';
    protected $namespace = 'streams_import';
    public function __construct()
    {
        parent::__construct();

		// Load Drivers
		$this->load->driver('Streams');
        $this->lang->load('streams_import');
        $this->load->library('streams');
        $this->load->model('streams_core/streams_m');        
        $this->load->helper('streams_import');
        $this->load->library(array('form_validation', 'streams_core/Fields'));
    }

    public function index()
    {

         $params = array(
            'stream'    => $this->stream_slug,
            'namespace' => $this->namespace,
           //'where'        => $base_where
        );
         $request_entry = $this->streams->entries->get_entries($params);

		$this->template
             ->set('entries', $request_entry)                
             ->set('section', $this->section)              
             ->set('namespace', $this->namespace) 
             ->set('title', lang($this->namespace.':title:'.$this->section.':index'));

        $this->template->build('admin/index');
    }

     public function create()
    {
        // Get stream
        $stream = $this->streams->stream_obj($this->stream_slug, $this->namespace);
        $data->fields = $this->streams_m->get_stream_fields($stream->id);

        $stream_list =$this->db->select("id, stream_namespace, stream_slug")->get('data_streams')->result();
        foreach ($stream_list as $single_stream) {
                $data->stream_dropdown[$single_stream->id] = $single_stream->stream_namespace.' - '.$single_stream->stream_slug;
                # code...
            }

        // Processing the POST data    
        $extra = array('title' =>  lang($this->namespace.':title:'.$this->section.':create'),
        'success_message' => lang($this->namespace.':messages:'.$this->section.':create:success'),
        'failure_message' => lang($this->namespace.':messages:'.$this->section.':create:error'),
        'return'          => 'admin/'.$this->namespace.'/'.$this->section.'/step2/-id-'   );
        $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'new', null, $view_override = false,  $extra , $skips = array());
 
        $this->template->build('admin/profiles/create',$data); 
    }

    public function step2($id)
    {
         $params = array(
            'stream'    => $this->stream_slug,
            'namespace' => $this->namespace,
           'where'        => ' id = '.$id
        );
         $request_entry = $this->streams->entries->get_entries($params);
         $current_profile =  $request_entry ['entries'][0];

         //now get the stream of the profile
        $stream = $this->streams->stream_obj($this->stream_slug, $this->namespace);
        $data->fields = $this->streams_m->get_stream_fields($current_profile['stream_identifier']);

        $data->field_count = count($data->fields);

        //Feed the field dropdown
        foreach ($data->fields as $field) {
           $data->field_dropdown[$field->field_id]  = $this->fields->translate_label($field->field_name);
        }
        

        //Feed the entry dropdown
        $file_content = _pre_import_plain($current_profile['example_file']['file'],$current_profile['delimiter'],$current_profile['eol']);


        var_dump( $file_content);
    }

    public function edit($id)
    {

        $extra = array('title' =>  lang($this->namespace.':title:'.$this->section.':edit'),
        'success_message' => lang($this->namespace.':messages:'.$this->section.':edit:success'),
        'failure_message' => lang($this->namespace.':messages:'.$this->section.':edit:error'),
        'return'          => 'admin/'.$this->namespace.'/'.$this->section   );

        echo $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'edit', $entry = $id, $view_override = true, $extra, $skips = array());
    }


    public function delete($id)
    {

        if($this->streams->entries->delete_entry($id, $this->section, $this->namespace)){
            $this->session->set_flashdata('success', lang($this->namespace.':messages:'.$this->section.':delete:success'));
        }else{
            $this->session->set_flashdata('error', lang($this->namespace.':messages:'.$this->section.':delete:failure'));
        }
        redirect('admin/'.$this->namespace.'/'.$this->section);

    }


}
?>