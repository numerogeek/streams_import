<?php defined('BASEPATH') or exit('No direct script access allowed');

$ci =& get_instance();
$ci->load->helper('visite_immo');


if (!function_exists('abrinor_commercialisation_sim_preprocess'))
{
	function abrinor_commercialisation_sim_preprocess($commercialisation)
	{

		return $commercialisation ;
	}
}

if (!function_exists('abrinor_hometype_sim_preprocess'))
{
	function abrinor_hometype_sim_preprocess($hometype)
	{

		return $hometype ;
	}
}

if (!function_exists('abrinor_city_sim_preprocess'))
{
	function abrinor_city_sim_preprocess($city)
	{
		$cityid = get_cityID_by_name($city);
		return $cityid ;
	}
}

if (!function_exists('abrinor_hide_city_sim_preprocess'))
{
	function abrinor_hide_city_sim_preprocess($hide_city)
	{

		return $hide_city ;
	}
}

if (!function_exists('abrinor_city_complement_sim_preprocess'))
{
	function abrinor_city_complement_sim_preprocess($city_complement)
	{

		return $city_complement ;
	}
}

if (!function_exists('abrinor_reference_partner_sim_preprocess'))
{
	function abrinor_reference_partner_sim_preprocess($reference_partner)
	{

		return $reference_partner ;
	}
}

if (!function_exists('abrinor_gaz_emission_sim_preprocess'))
{
	function abrinor_gaz_emission_sim_preprocess($gaz_emission)
	{

		return $gaz_emission ;
	}
}

if (!function_exists('abrinor_energetic_class_sim_preprocess'))
{
	function abrinor_energetic_class_sim_preprocess($energetic_class)
	{

		return $energetic_class ;
	}
}

if (!function_exists('abrinor_description_sim_preprocess'))
{
	function abrinor_description_sim_preprocess($description)
	{

		return $description ;
	}
}

if (!function_exists('abrinor_description_complement_sim_preprocess'))
{
	function abrinor_description_complement_sim_preprocess($description_complement)
	{

		return $description_complement ;
	}
}

if (!function_exists('abrinor_price_sim_preprocess'))
{
	function abrinor_price_sim_preprocess($price)
	{

		return $price ;
	}
}

if (!function_exists('abrinor_display_price_sim_preprocess'))
{
	function abrinor_display_price_sim_preprocess($display_price)
	{

		return $display_price ;
	}
}

if (!function_exists('abrinor_surface_sim_preprocess'))
{
	function abrinor_surface_sim_preprocess($surface)
	{

		return $surface ;
	}
}

if (!function_exists('abrinor_room_sim_preprocess'))
{
	function abrinor_room_sim_preprocess($room)
	{

		return $room ;
	}
}

if (!function_exists('abrinor_author_sim_preprocess'))
{
	function abrinor_author_sim_preprocess($author)
	{

		return $author ;
	}
}

if (!function_exists('abrinor_siteref_sim_preprocess'))
{
	function abrinor_siteref_sim_preprocess($siteref)
	{

		return $siteref ;
	}
}

if (!function_exists('abrinor_external_id_sim_preprocess'))
{
	function abrinor_external_id_sim_preprocess($external_id)
	{

		return $external_id ;
	}
}

if (!function_exists('abrinor_test_folder_sim_preprocess'))
{
	function abrinor_test_folder_sim_preprocess($test_folder)
	{

		return '--NONE--' ;
	}
}

if (!function_exists('abrinor_active_sim_preprocess'))
{
	function abrinor_active_sim_preprocess($active)
	{

		return $active ;
	}
}

if (!function_exists('abrinor_superactive_sim_preprocess'))
{
	function abrinor_superactive_sim_preprocess($superactive)
	{

		return $superactive ;
	}
}

if (!function_exists('abrinor_agency_id_sim_preprocess'))
{
	function abrinor_agency_id_sim_preprocess($agency_id)
	{
		$agencyid = get_agencyID_by_email($agency_id);
		return $agencyid;
	}
}

if (!function_exists('abrinor_starred_sim_preprocess'))
{
	function abrinor_starred_sim_preprocess($starred)
	{

		return $starred ;
	}
}

if (!function_exists('abrinor_id_sim_preprocess'))
{
	function abrinor_id_sim_preprocess($id)
	{

		return $id ;
	}
}

if (!function_exists('abrinor_created_sim_preprocess'))
{
	function abrinor_created_sim_preprocess($created)
	{

		return $created ;
	}
}

if (!function_exists('abrinor_updated_sim_preprocess'))
{
	function abrinor_updated_sim_preprocess($updated)
	{

		return $updated ;
	}
}

if (!function_exists('abrinor_created_by_sim_preprocess'))
{
	function abrinor_created_by_sim_preprocess($created_by)
	{

		return $created_by ;
	}
}

if (!function_exists('abrinor_ordering_count_sim_preprocess'))
{
	function abrinor_ordering_count_sim_preprocess($ordering_count)
	{

		return $ordering_count ;
	}
}

?>