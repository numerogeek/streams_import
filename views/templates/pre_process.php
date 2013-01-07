defined('BASEPATH') or exit('No direct script access allowed');

<?php 
foreach ($fields as $slug=>$field) {
?>
if (!function_exists('<?php echo  $slug_profile.'_'.$slug; ?>_sim_preprocess'))
{
	function <?php echo  $slug_profile.'_'.$slug; ?>_sim_preprocess($<?php echo $slug; ?>)
	{

		return $<?php echo $slug; ?> ;
	}
}

<?php
}
?>
