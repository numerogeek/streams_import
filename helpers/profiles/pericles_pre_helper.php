<?php defined('BASEPATH') or exit('No direct script access allowed');
$ci =& get_instance();
$ci->load->helper('visite_immo');
if (!function_exists('pericles_commercialisation_sim_preprocess'))
{
	function pericles_commercialisation_sim_preprocess($commercialisation)
	{

		return $commercialisation ;
	}
}

if (!function_exists('pericles_hometype_sim_preprocess'))
{
	function pericles_hometype_sim_preprocess($hometype)
	{

		return $hometype ;
	}
}

if (!function_exists('pericles_city_sim_preprocess'))
{
	function pericles_city_sim_preprocess($city)
	{
		$cityid = get_cityID_by_name($city);
		return $cityid ;
	}
}

if (!function_exists('pericles_hide_city_sim_preprocess'))
{
	function pericles_hide_city_sim_preprocess($hide_city)
	{

		return $hide_city ;
	}
}

if (!function_exists('pericles_city_complement_sim_preprocess'))
{
	function pericles_city_complement_sim_preprocess($city_complement)
	{

		return $city_complement ;
	}
}

if (!function_exists('pericles_reference_partner_sim_preprocess'))
{
	function pericles_reference_partner_sim_preprocess($reference_partner)
	{

		return $reference_partner ;
	}
}

if (!function_exists('pericles_gaz_emission_sim_preprocess'))
{
	function pericles_gaz_emission_sim_preprocess($gaz_emission)
	{

		return $gaz_emission ;
	}
}

if (!function_exists('pericles_energetic_class_sim_preprocess'))
{
	function pericles_energetic_class_sim_preprocess($energetic_class)
	{

		return $energetic_class ;
	}
}

if (!function_exists('pericles_description_sim_preprocess'))
{
	function pericles_description_sim_preprocess($description)
	{

		return $description ;
	}
}

if (!function_exists('pericles_description_complement_sim_preprocess'))
{
	function pericles_description_complement_sim_preprocess($description_complement)
	{

		return $description_complement ;
	}
}

if (!function_exists('pericles_price_sim_preprocess'))
{
	function pericles_price_sim_preprocess($price)
	{

		return $price ;
	}
}

if (!function_exists('pericles_display_price_sim_preprocess'))
{
	function pericles_display_price_sim_preprocess($display_price)
	{

		return $display_price ;
	}
}

if (!function_exists('pericles_surface_sim_preprocess'))
{
	function pericles_surface_sim_preprocess($surface)
	{

		return $surface ;
	}
}

if (!function_exists('pericles_room_sim_preprocess'))
{
	function pericles_room_sim_preprocess($room)
	{

		return $room ;
	}
}

if (!function_exists('pericles_author_sim_preprocess'))
{
	function pericles_author_sim_preprocess($author)
	{

		return $author ;
	}
}

if (!function_exists('pericles_siteref_sim_preprocess'))
{
	function pericles_siteref_sim_preprocess($siteref)
	{

		return $siteref ;
	}
}

if (!function_exists('pericles_external_id_sim_preprocess'))
{
	function pericles_external_id_sim_preprocess($external_id)
	{

		return $external_id ;
	}
}

if (!function_exists('pericles_test_folder_sim_preprocess'))
{
	function pericles_test_folder_sim_preprocess($test_folder)
	{

		return '--NONE--' ;
	}
}

if (!function_exists('pericles_active_sim_preprocess'))
{
	function pericles_active_sim_preprocess($active)
	{

		return $active ;
	}
}

if (!function_exists('pericles_superactive_sim_preprocess'))
{
	function pericles_superactive_sim_preprocess($superactive)
	{

		return $superactive ;
	}
}

if (!function_exists('pericles_agency_id_sim_preprocess'))
{
	function pericles_agency_id_sim_preprocess($agency_id)
	{
		$agencyid = get_agencyID_by_email($agency_id);
		return $agencyid;
	}
}

if (!function_exists('pericles_starred_sim_preprocess'))
{
	function pericles_starred_sim_preprocess($starred)
	{

		return $starred ;
	}
}

if (!function_exists('pericles_id_sim_preprocess'))
{
	function pericles_id_sim_preprocess($id)
	{

		return $id ;
	}
}

if (!function_exists('pericles_created_sim_preprocess'))
{
	function pericles_created_sim_preprocess($created)
	{

		return $created ;
	}
}

if (!function_exists('pericles_updated_sim_preprocess'))
{
	function pericles_updated_sim_preprocess($updated)
	{

		return $updated ;
	}
}

if (!function_exists('pericles_created_by_sim_preprocess'))
{
	function pericles_created_by_sim_preprocess($created_by)
	{

		return $created_by ;
	}
}

if (!function_exists('pericles_ordering_count_sim_preprocess'))
{
	function pericles_ordering_count_sim_preprocess($ordering_count)
	{

		return $ordering_count ;
	}
}


?>