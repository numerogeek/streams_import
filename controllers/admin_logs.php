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

	}

    public function index($offset)
    {
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
    }

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