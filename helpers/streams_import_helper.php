<?php

/**
 * Streams Import Helper
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Helpers
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
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

if ( !function_exists('get_current_value') )
{
	function get_current_value($profile_id, $field, $mode = 1) //mode is for 1:DESTINATION / 2:SOURCE VALUE..
	{
		$ci     =& get_instance();
		$object = $ci->db->get_where('streams_import_mapping', array(
			'profile_relation_stream'=> $profile_id,
			'stream_field'         	=> $field
		))->row();
		if ( empty($object))
		{	
			if($mode==1):
				//do we have at least one entry for this profile ? 
					$total = $ci->db->get_where('streams_import_mapping', array(
							'profile_relation_stream'=> $profile_id
						))->num_rows();
					if($total>0):
						return null;
					else:
						return  $field;
					endif;
			else:
				return null;
			endif;

		}
		switch ($mode)
		{
			case 1 :
				return $object->stream_field;
				break;
			case 2:
				return $object->entry_number;
				break;
		}
	}
}

if ( !function_exists('get_values_between_brackets') )
{
	function get_values_between_brackets($field) //return value between [] [] of a string
	{
		//Get the text between [] into array if there's more than 1 ! 
		$pattern="#\[(.*?)\]#";
		preg_match_all($pattern,$field,$matches);
		return $matches[1];
	}
}

if ( !function_exists('get_fileid_by_profileid') )
{
	function get_fileid_by_profileid($profile_id) //return value between [] [] of a string
	{
		$ci     =& get_instance();
		$profile = $ci->db->select()->where('id', $profile_id)->limit(1)->get('streams_import_profiles')->row(0);
		return $profile->example_file; 
	}
}

if ( !function_exists('create_slug') )
{
	function create_slug($name)
	{
		$name = convert_accented_characters($name);

		return strtolower(preg_replace('/-+/', '_', preg_replace('/[^a-zA-Z0-9]/', '_', $name)));
	}
}

/* EOF */