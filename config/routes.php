<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Streams Import Routes
 *
 * @package  PyroCMS\Addons\Modules\Streams Import\Config
 * @author   PyroCMS Community
 * @website  https://github.com/bergeo-fr/streams_import
 */
$route['streams_import/admin/profiles(/:any)?']     = 'admin_profiles$1';
$route['streams_import/admin/logs(/:any)?']         = 'admin_logs$1';
$route['streams_import/admin(/:any)?'] 				= 'admin_profiles$1';

// Front
$route['streams_import(/:any)?']		 			= 'import$1';