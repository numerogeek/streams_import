<?php defined('BASEPATH') or exit('No direct script access allowed');



if (!function_exists('pericles_homes_sim_postprocess'))
{
	function pericles_homes_sim_postprocess($stream, $entry_id, $data)
	{
		$ci =& get_instance();
		$ci->load->library('streams_import','streams');
		$ci->load->helper('streams_import');
		$temp_path = $ci->streams_import->temp_path;
		//GET images

		$entry = $ci->streams->entries->get_entry($entry_id, $stream->stream_slug, $stream->stream_namespace, FALSE);

		//images got the following template : 
		//var_dump($entry);

		$letters = array('a','b','c','d','e','f','g','h','i');
		$template = $data['CODE_SOCIETE'].'-'.$data['CODE_SITE'].'-'.$data['NO_ASP'].'-';

		foreach ($letters as $key) {
			$file = $template.$key.'.jpg';


			//move the file into the good director
			$source 		= $temp_path.$file;
			$destination 	= UPLOAD_PATH.'files/'.$file;

			if (file_exists($source)) // dont download if it exists already :)
			{
				copy($source,$destination);	
				
				$info   = CI_Image_lib::get_image_properties($destination,true); //load info
				$ext    = CI_Image_lib::explode_name($file);       // get extension

				$batch[] = array(
				'id'                => substr(md5(microtime()+$file), 0, 15),
				'folder_id'         =>	$entry->test_folder,
				'user_id'           =>	$ci->current_user->id,
				'type'              =>	'i',
				'name'              =>	$file,
				'filename'          =>	$file,
				'path'              =>	'{{ url:site }}files/large/'.$file,
				'description'       =>	$entry->description,
				'extension'         =>	$ext['ext'],
				'mimetype'          =>	$info['mime_type'],
				'width'             =>	$info['width'],
				'height'            =>  $info['height'],
				'filesize'          =>	filesize($destination),
				'date_added'        =>	now()
				);
			}
		}

		if (!empty($batch)) {
			# code...
			batch_insert_update('files', $batch, array('sort'));
		}
		
		return true; 
	}
}

?>