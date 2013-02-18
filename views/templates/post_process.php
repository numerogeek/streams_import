defined('BASEPATH') or exit('No direct script access allowed');


if (!function_exists('<?php echo  $slug_profile.'_'.$stream_obj->stream_slug; ?>_sim_postprocess'))
{
	function <?php echo $slug_profile.'_'.$stream_obj->stream_slug; ?>_sim_postprocess($stream, $entry_id, $data)
	{

		return true; 
	}
}
