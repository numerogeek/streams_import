<?php defined('BASEPATH') or exit('No direct script access allowed');


if (!function_exists('abrinor_homes_sim_postprocess'))
{
	function abrinor_homes_sim_postprocess($stream, $entry_id, $data)
	{
		//get the pics

		var_dump($data['photos']);
		return true; 
	}
}

?>