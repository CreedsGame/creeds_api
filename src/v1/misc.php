<?php

    /*
        creeds_api - misc.py
        Misc functions
        
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    # Build JSON HTTP response
    function response($status, $status_message, $data)
    {
        header("HTTP/1.1 ".$status);

        $response['success'] = ($status == 200);
        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['data'] = $data;

        $json_response = json_encode($response);

        die ($json_response);
    }

    # Return cleaned up string for SQL
    function clean_str($conn, $string)
    {
        return mysqli_real_escape_string($conn, $string);
    }

    # Return valid SQL string
    function build_str($string)
    {
        return "'".$string."'";
    }

    # Return a hashed string
    function string_to_hash($string)
    {
        return md5($string);
    }
?>