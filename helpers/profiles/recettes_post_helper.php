<?php defined('BASEPATH') or exit('No direct script access allowed');


if (!function_exists('recettes_homes_sim_postprocess'))
{
	function recettes_homes_sim_postprocess($stream_id, $entry_id, $data)
	{
		//GET images

		//images got the following template : 
		var_dump($data);

		return true; 
	}
}

?>