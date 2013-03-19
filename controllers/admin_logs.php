<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Admin Profiles Controller
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Controllers
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
class Admin_logs extends Admin_Controller
{

	/**
	 * Admin Section var
	 * 
	 * @var string
	 */
	protected $section = 'logs';
	
	
	/**
	 * Stream Namespace
	 * 
	 * Auto imported from Streams Import Library
	 * 
	 * @var string
	 */
	public $namespace ='streams_import';



	/**
	 * Constructor!
	 * 
	 * Load the Library which loads all, and set some items automatically.
	 */
	public function __construct()
	{
		parent::__construct();
        $this->lang->load('streams_import');
        $this->load->library('streams_import');

	}

    public function index($offset)
    {

        $keyword = trim($this->input->post('keyword'));
        $start_date = trim($this->input->post('start_date'));
        $end_date = trim($this->input->post('end_date'));
        $profile = trim($this->input->post('profile'));

        (!empty($keyword))? $where[] = " ( log_detail like '%".$keyword."%' ) " : false;
        (!empty($start_date))? $where[] = " ( created >= '".$start_date." 00:00:00' ) " : false;
        (!empty($end_date))? $where[] = " ( created <= '".$start_date." 00:00:00' ) " : false;
        (!empty($profile))? $where[] = " ( profile_rel_logs = ".$profile." ) " : false;


        $where_str = (!empty($where))?implode(' AND ', $where):' created <='.date("Y-m-d").' ';
        //Get the logs

       // var_dump($where_str);


        $params = array(
            'namespace'=>$this->namespace,
            'stream'    =>$this->section,
            'where'     =>$where_str,
            'limit'     =>'150',
            'paginate'  =>'no',
            'pag_segment'=>4,

            );

        $results = $this->streams->entries->get_entries($params);

        $data->entries = $results['entries'];
        $data->section = $this->section;
        $data->namespace = $this->section;
        $data->title = lang($this->namespace.':title:'.$this->section.':index');

        $profiles = $this->db->select('id,profile_name')->get('streams_import_profiles');
        $data->profiles['0'] = '----';
        foreach ($profiles->result() as $profile) {
            $data->profiles[$profile->id] = $profile->profile_name;
        }

        $this->template
          ->append_js('admin/filter.js');

        $this->input->is_ajax_request() ? $this->template->set_layout(false)->build('admin/'.$this->section.'/table',$data) : $this->template->build('admin/'.$this->section.'/index',$data);


/*
        $extra = 
         array(
         'title'                => lang($this->namespace.':title:'.$this->section.':index'),
         'buttons' => array(
            array(
                'label'     => lang('global:delete'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/delete/-entry_id-',
                'confirm'   => true
            ),
            array(
                'label'     => lang('global:view'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/view/-entry_id-',
                'confirm'   => false
            ))
         );
        
        echo $this->streams->cp->entries_table($this->section, $this->namespace, $pagination = "50", $pagination_uri = "admin/streams_import/logs", $view_override = true, $extra);
  */  }

    public function create()
    {
    	//we won't create logs manually !
        redirect('admin/'.$this->namespace.'/'.$this->section);
    }

    public function edit ($id)
    {
    	//we won't edit logs manually ! 
        redirect('admin/'.$this->namespace.'/'.$this->section);
    }

    public function view ($id)
    {
        $query = $this->streams->entries->get_entry($id,$this->section, $this->namespace,false);

        echo "<pre>";
        var_dump($query);
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
/* EOF */