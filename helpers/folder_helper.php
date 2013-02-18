<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('create_dir'))
{
    function create_dir($path, $permisions = 0777)
    {
        if (!file_exists($path))
        {
            mkdir($path, $permisions);
        }
        $path = (substr($path, -1) == '/' ? $path : $path.'/');
        write_file($path.'index.html', '');
        return true;
    }
}