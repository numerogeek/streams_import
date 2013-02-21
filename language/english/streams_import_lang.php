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
	'streams_import:title:profiles:index'                   => 'Profiles',
	'streams_import:title:profiles:create'                  => 'Create a new profile',
	'streams_import:title:profiles:edit'                    => 'Edit the profile',
	'streams_import:title:logs:index'               	  	=> 'Logs',
	'streams_import:title:profiles:quick_import_success'    => 'Quick Import: Success',

	# messages
	'streams_import:messages:profiles:create:success'       => 'New profile created with success.',
	'streams_import:messages:profiles:create:failure'       => 'Fail in creation of the profile.',
	'streams_import:messages:profiles:edit:success'         => 'Profile edited with success',
	'streams_import:messages:profiles:edit:failure'         => 'Fail in editing the profile',
	'streams_import:messages:profiles:delete:success'       => 'Profile deleted with success',
	'streams_import:messages:profiles:delete:failure'       => 'Fail in deleting the profile',
	'streams_import:messages:import:success'                => 'Import Done. Yeah.',
	'streams_import:messages:import:failure'                => 'Import fail. Sorry about that :(',
	'streams_import:messages:mapping:save:success'          => 'Mapping saved',
	'streams_import:messages:mapping:save:failure'          => 'Mapping Failed',
	'streams_import:messages:logs:delete:success'          	=> 'Logs deleted succesfully',
	'streams_import:messages:logs:delete:failure'          	=> 'Logs could not be deleted succesfully',

	# fields
	'streams_import:fields:profile_name'                    => 'Profile Name',
	'streams_import:fields:profile'                         => 'Profile ID',
	'streams_import:fields:entry_number'                    => 'Entry',
	'streams_import:fields:stream_field'                	 => 'Stream Field ID',
	'streams_import:fields:equalities'                      => 'Associations',
	'streams_import:fields:stream_identifier'               => 'Stream',
	'streams_import:fields:eol'                             => 'EOL',
	'streams_import:fields:delimiter'                       => 'Delimiter',
	'streams_import:fields:delimiter:instructions'          => '',
	'streams_import:fields:example_file'                    => 'Example File',
	'streams_import:fields:enclosure'                       => 'Enclosure',
	'streams_import:fields:delimiter:instructions'          => '',
	'streams_import:fields:namespace_stream_slug'           => 'Concerned Namespace/Streams Slug',
	'streams_import:fields:include_all'                     => 'Include All Fields?',
	'streams_import:fields:include_one'                     => 'Include This Field?',
	'streams_import:fields:url'                             => 'URL',
	'streams_import:fields:url_instructions'                => '',
	'streams_import:fields:source_format'                   => 'Source Format',
	'streams_import:fields:source_format_instructions'      => 'The format of the source data',
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
	'streams_import:fields:xml_path_loop'      				=> 'Loop Path',
	'streams_import:fields:xml_path_loop_instructions'		=> 'The file will be converted into an array. You have to provide the node where the system have to loop to import. <a  onClick="window.open(\'/admin/streams_import/profiles/raw_data/'.$ci->uri->segment(5).'\', \'NOM\', \'scrollbars=yes,width=550,height=600\')"target=\'_blank\'>See the raw data</a>',
	'streams_import:fields:filename'						=> 'File\'s name/ID',
	'streams_import:fields:profile_rel_logs'				=> 'Profile',
	'streams_import:fields:profile_slug'					=> 'Profile Slug',

	# buttons
	'profiles:button:add'                                   => 'Add a profile',
	'streams_import:button:quick_import'                    => 'Quick Import',
	'streams_import:button:edit-mapping'                    => 'Edit mapping',
	'streams_import:button:cancel'                          => 'Cancel',
	'streams_import:button:save'                            => 'Save',
	'streams_import:button:run'                             => 'Run !',
	'streams_import:button_next'                            => 'Next',

	#tabs
	'streams_import:tabs:source_connection'                  => 'File settings',
	'streams_import:tabs:general'                            => 'General',

	# misc.
	'streams_import:title_import_csv'                       => 'Run import.',
	'streams_import:misc_example_csv'                       => 'Choose a file and click next.  (XML / TXT or XML)',
	'streams_import:misc_csv_file'                          => 'Pick a file',
	'streams_import:misc_instructions_csv_file'             => 'or upload one using Files Manager.',

);