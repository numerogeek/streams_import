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
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		// Register CRON module shtuff
		Events::register('cron_process_test', array($this, 'import_pics'));		// cron/test
		Events::register('cron_process_test', array($this, 'import'));		// cron/test
		Events::register('cron_process_60', array($this, 'import'));		// Hourly
	}

	/*-----------------------------------------------------------------------------------------*/

	/**
	 *	Check out profile directories and import shit
	 *
	 *	params void
	 *	return void
	 */

	public function import_pics()
	{

		$this->CI->load->helper('streams_import/streams_import');
		$query=$this->CI->db->get_where('files', array('is_downloaded' => 0), $limit=10, $offset=0); //DL 10 files a time

		//donwload them all
		foreach ($query->result() as $single_file) {
				$raw = file_get_contents($single_file->src);
				$full_path="uploads/default/files/$single_file->filename";
				write_file($full_path, $raw);
				
				$info   = CI_Image_lib::get_image_properties($full_path,true); //load info
				$ext    = CI_Image_lib::explode_name($single_file->filename);  

					$query = array(
					'extension'         =>	$ext['ext'],
					'mimetype'          =>	$info['mime_type'],
					'width'             =>	$info['width'],
					'height'            =>  $info['height'],
					'filesize'          =>	filesize($full_path),
					'src'				=>  null,
					'is_downloaded'		=>	true
					);
					$this->CI->db->where('id',$single_file->id);				
					$this->CI->db->update('files',$query);


		}			

	}



	public function import()
	{

		// Load up our classes
		$this->CI->load->driver('Streams');
		$this->CI->load->helper('common_import');
		$this->CI->load->config('streams_import/streams_import_c');


		/*
		* Set up our root directory to scan
		*/

		$base = UPLOAD_PATH.'ftp/profiles/';



		/*
		 * Get and format profile (names) to look for.
		 * Compress into lowercase alpha only.
		 */

		$profiles = $this->CI->streams->entries->get_entries(
			array(
				'stream' => 'profiles',
				'namespace' => 'streams_import',
				)
			);

		foreach ($profiles['entries'] as &$profile)
		{
			$profile = array(
				'slug' => slugify($profile['profile_name'], ''),
				'id' => $profile['id'],
				);
		}



		/*
		 * Scan
		 */

		foreach ($profiles['entries'] as $profile)
		{
			$path = $base.$profile['slug'];


			// Does the folder exist?
			if (is_dir($path))
			{
				// Files to process in this directory
				$files = array();
				
				// Scan for files
				$contents = scandir($path);

				// Get files into our array
				foreach ($contents as $part)
				{
					if (substr($part, 0, 1) == '.') continue; 	// Skip .
					if (substr($part, 0, 1) == '..') continue; 	// Skip ..
					if (! strpos($part, '.')) continue; 		// Skip directories

					$files[] = $part;
				}


				// Check for the achive folder
				if (!is_dir($path.'/archive')) mkdir($path.'/archive');


				// Move the files to archive
				foreach ($files as $file)
				{

					// Try importing the file
					if(import_file($path.'/'.$file,$file,$folder_id = 18,$owner = 17,$description = ''))	// $folder_id = 18 is "import" and $owner = 17 is "service@aiwebsystems.com"
					{
						##
						## There needs to be a public import method
						##
						## Something like website.com/streams_import/import/PROFILE/FILE/SECURITY_HASH
						##

						// File ID
						$file = $this->CI->db->select()->where('filename', $file)->limit(1)->get('files')->row(0);

						// Try this
						echo file_get_contents(site_url('streams_import/import/'.$this->CI->config->item('streams_import:hash').'/'.$profile['id'].'/'.$file->id));die;
						//echo site_url('streams_import/import/'.$this->CI->config->item('streams_import:hash').'/'.$profile['id'].'/'.$file->id);die;

						// Clean that snatch like a dirty carpet
						rename($path.'/'.$file, $path.'/archive/'.now().'.'.$file);
					}
				}
			}
			else
			{
				// Who knows?
				// Maybe we'll need it next time.
				mkdir($path);
			}
		}
	}
}
/* End of file events.php */