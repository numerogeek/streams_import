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

		// Load up our classes
		$this->CI->load->driver('Streams');
		$this->CI->load->helper(array('common_import','streams_import/streams_import'));
		$this->CI->load->config('streams_import/streams_import_c');

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

		$query=$this->CI->db->get_where('files', array('is_downloaded' => 0), $limit=50, $offset=0); //DL 10 files a time

		//donwload them all
		foreach ($query->result() as $single_file) {
				$raw = file_get_contents($single_file->src);
				if ($raw)
				{
				
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
				}else{					
					echo "src :".$single_file->src;
					echo "<br/> no downloadé <br/>";
				}


		}			

	}


	public function purge()
	{


		//Purge
		//SELECT listings older than 30 days (updated OR created.) Limit it. It's really slow. maximum should be 50
		$sql =" SELECT id FROM default_listing_homes WHERE (updated is null and (TO_DAYS(NOW()) - TO_DAYS(created)) > 30 ) OR ( (TO_DAYS(NOW()) - TO_DAYS(updated)) >30 ) LIMIT 10"; 

		$entries = $this->CI->db->query($sql)->result();
		foreach ($entries as $entry) {
			$this->CI->streams->entries->delete_entry($entry->id, 'homes', 'listing');
		}
	}

	public function import()
	{
die();
		//Steps for automatic import
		// 1- check in the config the path for the profiles.
		// 2- Load all the profiles where "auto = true"
		// 3- check if a file is awaiting for import process
		// 4- Import it :)  







		/*
		* Set up our root directory to scan
		*/

		$base = $this->CI->config->item('streams_import:profiles_directory');

		$exit_at_the_end = false;


		/*
		 * Get and format profile (names) to look for.
		 * Compress into lowercase alpha only.
		 */

		$profiles = $this->CI->streams->entries->get_entries(
			array(
				'stream' => 'profiles',
				'namespace' => 'streams_import'
				)
			);

		/*
		 * Scan
		 */

		foreach ($profiles['entries'] as $profile)
		{
			$path = $base.$profile['profile_slug'].'/data/';


var_dump($path);
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
				$archive = $this->CI->config->item('streams_import:archives_folder').$profile['profile_slug'];

				
				if (!is_dir($this->CI->config->item('streams_import:archives_folder')))
					{ mkdir($this->CI->config->item('streams_import:archives_folder')); }				
				if (!is_dir($archive))
					{ mkdir($archive); }


				// Move the files to archive
				foreach ($files as $file)
				{
					$exit_at_the_end = true;
					// Try importing the file
					if(import_file($path.$file,$file,$folder_id = 18,$owner = 17,$description = 'file to import'))	// $folder_id = 18 is "import" and $owner = 17 is "service@aiwebsystems.com"
					{
						//echo "success ".$path.$file;
						##
						## There needs to be a public import method
						##
						## Something like website.com/streams_import/import/PROFILE/FILE/SECURITY_HASH
						##

						// File ID
						$file_id = $this->CI->db->select()->where('filename', $file)->limit(1)->get('files')->row(0);

						// Try this
						$hit = site_url('streams_import/import/'.$this->CI->config->item('streams_import:hash').'/'.$profile['id'].'/'.$file_id->id);
						//var_dump($hit);
						echo file_get_contents($hit);
						//echo site_url('streams_import/import/'.$this->CI->config->item('streams_import:hash').'/'.$profile['id'].'/'.$file->id);die;

						// Clean that snatch like a dirty carpet

						rename($path.$file, $archive.'/'.now().'.'.$file);
					}
				}
				if ($exit_at_the_end)
				{
					//We want to process one profile per time.
					die();
				}
			}
			else
			{
				//echo "ano";
				// Who knows?
				// Maybe we'll need it next time.
				rename($path.$file, $archive.'/'.$file);
			}
		}
	}
}
/* End of file events.php */