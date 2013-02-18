<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Language
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Lang
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
$ci =& get_instance();
$lang = array(
	# titles
	'streams_import:title:profiles:index'                   => 'Profils',
	'streams_import:title:profiles:create'                  => 'Creation d\'un profil',
	'streams_import:title:profiles:edit'                    => 'Edition d\'un profil',

	'streams_import:title:logs:index'               	  	=> 'Logs',

	# messages
	'streams_import:messages:profiles:create:success'       => 'Nouveau profil créé avec succès.',
	'streams_import:messages:profiles:create:failure'       => 'Echec dans la création du profil.',
	'streams_import:messages:profiles:edit:success'         => 'Profil édité avec succès.',
	'streams_import:messages:profiles:edit:failure'         => 'Echec dans l\'édition du profil.',
	'streams_import:messages:profiles:delete:success'       => 'Profil supprimé avec succès.',
	'streams_import:messages:profiles:delete:failure'       => 'Echec dans la suppression du profil.',
	'streams_import:messages:import:success'                => 'Import Done. Yeah.', #translate
	'streams_import:messages:import:failure'                => 'Import fail. Sorry about that :(', #translate
	'streams_import:messages:mapping:save:success'          => 'Mapping sauvegardé',
	'streams_import:messages:mapping:save:failure'          => 'Echec du mapping',
	'streams_import:messages:logs:delete:success'          	=> 'Logs supprimé avec succès',
	'streams_import:messages:logs:delete:failure'          	=> 'Le logs n\'a pu être supprimé',

	# fields
	'streams_import:fields:profile_name'                    => 'Nom du profil',
	'streams_import:fields:profile'                         => 'Profile ID', #translate
	'streams_import:fields:entry_number'                    => 'Entrée',
	'streams_import:fields:stream_field'                 => 'Stream Field ID', #translate
	'streams_import:fields:equalities'                      => 'Associations', #translate
	'streams_import:fields:stream_identifier'               => 'Stream Destination', #translate
	'streams_import:fields:eol'                             => 'Fin de ligne',
	'streams_import:fields:delimiter'                       => 'Delimiteur',
	'streams_import:fields:delimiter:instructions'          => '',
	'streams_import:fields:example_file'                    => 'Example File', #translate
	'streams_import:fields:enclosure'                       => 'Enclosure', #translate
	'streams_import:fields:namespace_stream_slug'           => 'Concerned Namespace/Streams Slug',
	'streams_import:fields:include_all'                     => 'Include All Fields?', #translate
	'streams_import:fields:include_one'                     => 'Include This Field?', #translate
	'streams_import:fields:url'                             => 'URL', #translate
	'streams_import:fields:url_instructions'                => '',
	'streams_import:fields:source_format'                   => 'Source Format', #translate
	'streams_import:fields:source_format_instructions'      => 'The format of the source data', #translate
	'streams_import:fields:unzip'                  			=> 'Unzip ?',
	'streams_import:fields:unzip_instructions'      		=> 'Check if you want to unzip first.',
	'streams_import:fields:datasource'                 		=> 'Type of the sourcefile',
	'streams_import:fields:datasource_instructions'      	=> '',
	'streams_import:fields:ftp_host'						=> 'FTP Host',
	'streams_import:fields:ftp_host_instructions'      		=> '',
	'streams_import:fields:login'							=> 'login',
	'streams_import:fields:login_instructions'      		=> '',
	'streams_import:fields:password'						=> 'password',
	'streams_import:fields:password_instructions'      		=> '',
	'streams_import:fields:xml_path_loop'      				=> 'Chemin de la boucle',
	'streams_import:fields:xml_path_loop_instructions'		=> 'Le fichier sera converti en array. Indiquez le chemin du noeud sur lequel boucler pour l\'import. <a  onClick="window.open(\'/admin/streams_import/profiles/raw_data/'.$ci->uri->segment(5).'\', \'NOM\', \'scrollbars=yes,width=550,height=600\')"target=\'_blank\'>See the raw data</a>',
	'streams_import:fields:filename'						=> 'Nom ou ID de fichier',
	'streams_import:fields:profile_rel_logs'				=> 'Profil',
	'streams_import:fields:profile_slug'					=> 'Slug de profil',

	# buttons
	'profiles:button:add'                                   => 'Ajouter un profil',
	'streams_import:button:quick_import'                    => 'Quick Import', #translate
	'streams_import:button:edit-mapping'                    => 'Editer le mapping',
	'streams_import:button:cancel'                          => 'Annuler',
	'streams_import:button:save'                            => 'Sauvegarder',
	'streams_import:button:run'                             => 'Executer',
	'streams_import:button_next'                            => 'Suivant',

	#tabs
	'streams_import:tabs:source_connection'                  => 'File settings',
	'streams_import:tabs:general'                            => 'General',

	# misc.
	'streams_import:title_import_csv'                       => 'Importer un fichier',
	'streams_import:misc_example_csv'                       => 'Un fichier XML / TXT ou XML',
	'streams_import:misc_csv_file'                          => 'Misc.', #translate
	'streams_import:misc_instructions_csv_file'             => 'Instructions', #translate

);	