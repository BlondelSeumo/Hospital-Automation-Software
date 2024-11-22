<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('find_file_type')) {

    function find_file_type($_find)
    {
  
        $mime = get_mimes();
        foreach ($mime as $mime_key => $mime_value) {

            if (is_array($mime_value) && in_array($_find, $mime_value)) {

                return $mime_key;
            } elseif ($mime_value == $_find) {

                return $mime_key;
            }
        }
        return false;
    }


}

if (!function_exists('format_file_size')) {

function format_file_size($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
}