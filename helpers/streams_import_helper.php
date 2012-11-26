<?php
if(!function_exists('_pre_import_csv_to_stream'))
{
	function _pre_import_csv_to_stream($file,$delimiter,$eol=false,$enclosure=null)
	{
		var_dump($enclosure);
		if($enclosure=='')
		{
			$enclosure=null;
		}
	    $ci =& get_instance();

	    // Get the file
	   // $file = $ci->db->select()->where('id', $file)->limit(1)->get('files')->row(0);
	   // echo $ci->db->last_query();

	    // Handle it       
        $file=$ci->parser->parse_string($file, null, true);
        $handle = fopen($file, 'r');
	    // Get it
	    if ($handle) {
	       	 ini_set('auto_detect_line_endings',TRUE);
	       // ini_set(memory_limit, "1000M");
	        
	        while ( ($line = fgetcsv($handle,$length = 0,$delimiter,$enclosure)) !== false)
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
