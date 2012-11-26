<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CSVReader Class
 * 
 * $Id: csvreader.php 54 2009-10-21 21:01:52Z Pierre-Jean $
 * 
 * Allows to retrieve a CSV file content as a two dimensional array.
 * Optionally, the first text line may contains the column names to
 * be used to retrieve fields values (default).
 * 
 * Let's consider the following CSV formatted data:
 * 
 *        "col1";"col2";"col3"
 *         "11";"12";"13"
 *         "21;"22;"2;3"
 * 
 * It's returned as follow by the parsing operation with first line
 * used to name fields:
 * 
 *         Array(
 *             [0] => Array(
 *                     [col1] => 11,
 *                     [col2] => 12,
 *                     [col3] => 13
 *             )
 *             [1] => Array(
 *                     [col1] => 21,
 *                     [col2] => 22,
 *                     [col3] => 2;3
 *             )
 *        )
 * 
 * @author        Pierre-Jean Turpeau
 * @link        http://www.codeigniter.com/wiki/CSVReader
 */
class CSVReader
{
	private $CI;

	public function  __construct()
	{
		$this->CI =& get_instance();
		
		$this->_fields				= array();	/** columns names retrieved after parsing */ 
		    
    $this->_max_row_size 	= 4096;   	/** maximum row size to be used for decoding */
	}
	
	
	/**
		* Parse a file containing CSV formatted data.
		*
		* @access    public
		* @param    	string
		* @param    	boolean
		* @param    	int / FALSE
		* @return    array
		*/
	public function parse_file($p_Filepath, $p_NamedFields = TRUE, $limit = FALSE, $_separator = ',', $_enclosure = '"')
	{
		// Initialize
		$content = false;
		
		// Make handle
		$file = fopen($p_Filepath, 'r');
		
		// If using 1st col as name / otherwise skip em
		if($p_NamedFields)
		{
			$this->_fields = fgetcsv($file, $this->_max_row_size, $_separator, $_enclosure);
		}
		
		// Make Counter
		$c = 0;
		$go = TRUE;
		
		// Loop
		while( ($row = fgetcsv($file, $this->_max_row_size, $_separator, $_enclosure)) != false && $go )
		{
			
			// Skip it if empty
			if( $row[0] != null )
			{
				// Increment
				$c++;
				
				// Stop if at the limit
				if($limit !== FALSE)
				{
					if( $c >= $limit ) $go = FALSE;
				}
								
				// If there no content
				if( !$content )
				{
					$content = array();	// Empty array
				}
				
				if( $p_NamedFields )
				{
					$items = array();
					
					// I prefer to fill the array with values of defined fields
					foreach( $this->_fields as $id => $field )
					{
						if( isset($row[$id]) )
						{
							$items[$field] = $row[$id];    
						}
					}
					$content[] = $items;
				}
				else
				{
					$content[] = $row;
				}
			}
		}
		fclose($file);
		return $content;
	}// Eof csv_to_array()
	
}