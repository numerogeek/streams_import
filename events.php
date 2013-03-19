<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * streams_import Events Class
 * 
 * @package			CMS
 * @subpackage    	streams_import Module
 * @category    	Events
 * @author        	Ryan Thompson - AI Web Systems, Inc.
 * @website       	http://aiwebsystems.com
 */
class Events_Streams_import {

	protected $CI;
	public $debug = true;
	public $base;
	
	public function __construct()
	{
		$this->CI =& get_instance();

		// Load up our classes
		$this->CI->load->driver('Streams');
		$this->CI->load->helper(array('streams_import/streams_import'));
		$this->CI->load->config('streams_import/streams_import_c');

		$this->debug =  $this->CI->config->item('streams_import:debug');
		/*
		* Set up our root directory to scan
		*/
		$this->base = $this->CI->config->item('streams_import:profiles_directory');
		// Register CRON module shtuff


	}


}
/* End of file events.php */