<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * We some security so we can call the importer on the front end
 */
$config['streams_import:hash']                      = 'a874de040c2439d710d1b8e6c7bd59a0';
$config['streams_import:unzip_folder']              = "uploads/default/files/temp_unzip2/";
$config['streams_import:archives_folder']           = "uploads/default/files/archives/";
$config['streams_import:anomalies_folder']          = "uploads/default/files/anomalies/";
$config['streams_import:profiles_directory']        = "uploads/default/ftp/profiles/web_users/";
$config['streams_import:break_value']               = "{{do_no_insert}}";
$config['streams_import:debug']                     = true;