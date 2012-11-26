<?php
if(!function_exists('_pre_import_plain'))
{
	function _pre_import_plain($file,$delimiter,$eol=false)
	{

	    $ci =& get_instance();
	    // Get the file
	   // $file = $ci->db->select()->where('id', $file)->limit(1)->get('files')->row(0);
	   // echo $ci->db->last_query();

	    // Handle it
	    $handle = fopen($ci->parser->parse_string($file, null, true), 'r');
	    // Get it
	    if ($handle) {
	    	if(!$eol)
	    	{
	       		 ini_set('auto_detect_line_endings',TRUE);
	       	}
	       	 ini_set('auto_detect_line_endings',TRUE);
	       // ini_set(memory_limit, "1000M");
	        
	        while ( ($line = fgetcsv($handle,$length = 0,$delimiter)) !== false)
	        {
	            $data['entries'][] = $line;
	        }
	        
	        fclose($handle);
	        return $data;
	    }
	    else
	    {
	        // Die
	        die('File not found...');

	    	return false;
	    }
	}
}
?>