defined('BASEPATH') or exit('No direct script access allowed');

//decode the content if it's needed
if (!function_exists('<?php echo  $slug_profile.'_content_decode'; ?>_sim_preprocess'))
{
    function <?php echo  $slug_profile.'_content_decode'; ?>_sim_preprocess($raw)
    {

        return $raw;
    }
}


<?php 
foreach ($fields as $slug=>$field) {
	if (isset($slug) && $slug !='')
	{
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
}
?>
