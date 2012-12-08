<?php

if ( !function_exists('batch_insert_update') )
{
	function batch_insert_update($table = '', $set = NULL, $update_keys = array(), $batch = 500)
	{
		// Load CI
		$ci =& get_instance();

		// Status
		$status = true;

		// Update portion of the query
		if ( !empty($update_keys) )
		{
			foreach ($update_keys as $k=> &$key)
			{
				$key = $key . "=VALUES({$key})";
			}
			$update_keys = implode(', ', $update_keys);
		}
		else
		{
			$update_keys = "";
		}

		// Start the query string
		$sql = $ci->db->insert_string($table, $set[0]);
		$sql = substr($sql, 0, -strlen(substr($sql, strpos($sql, 'VALUES (') + 7))) . "**VALUES_SEGMENT** ON DUPLICATE KEY UPDATE ";

		// Batch this bitch - start after the first entry cause it's already in there.
		for ($i = 0, $total = count($set); $i < $total; $i += 1)
		{
			// Make the insert values part
			$temp = $ci->db->insert_string($table, $set[$i]);

			// Put in an array
			$values_array[] = substr($temp, strpos($temp, 'VALUES ') + 6);


			// Batch every $batch
			if ( count($values_array) >= $batch )
			{
				// Build a string from the values
				$values_string = implode(", ", $values_array);

				// Assemble!!
				$query = str_replace('**VALUES_SEGMENT**', $values_string, $sql) . $update_keys;

				// Run the query
				if ( !$ci->db->query($query) ) {
					$status = false;
				}

				unset($values_array);
			}
		}

		// Insert anything that is left too
		if ( !empty($values_array) )
		{
			$values_string = implode(", ", $values_array);

			// Assemble!!
			$query = str_replace('**VALUES_SEGMENT**', $values_string, $sql) . $update_keys;

			// Run the query
			if ( !$ci->db->query($query) ) {
				$status = false;
			}

			unset($values_array);
		}

		return $status;
	}
}

if ( !function_exists('_pre_import_csv_to_stream') )
{
	function _pre_import_csv_to_stream($file, $delimiter, $eol = false, $enclosure = null)
	{
		//var_dump($enclosure);
		if ( $enclosure == '' )
		{
			$enclosure = null;
		}
		$ci =& get_instance();

		// Get the file
		// $file = $ci->db->select()->where('id', $file)->limit(1)->get('files')->row(0);
		// echo $ci->db->last_query();

		// Handle it       
		$file   = $ci->parser->parse_string($file, null, true);
		$handle = fopen($file, 'r');
		// Get it
		if ( $handle )
		{
			ini_set('auto_detect_line_endings', TRUE);
			// ini_set(memory_limit, "1000M");
			$i=0;
			while (($line = fgetcsv($handle, $length = 0, $delimiter, $enclosure)) !== false)
			{
				if($i==0):
					$data['entries'][] = str_replace($enclosure, "", $line);
				else:
					$data['entries'][] = $line;
				endif;
				$i++;
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

if ( !function_exists('get_current_value') )
{
	function get_current_value($profile_id, $ordering_count, $mode = 1) //mode is for 1:DESTINATION / 2:SOURCE VALUE..
	{
		$ci     =& get_instance();
		$object = $ci->db->get_where('streams_import_mapping', array(
			'profile_relation_stream'=> $profile_id,
			'ordering_count'         => $ordering_count
		))->row();
		if ( empty($object) )
		{
			return null;
		}
		switch ($mode)
		{
			case 1 :
				return $object->stream_field_id;
				break;
			case 2:
				return $object->entry_number;
				break;
		}
	}
}

/* EOF */