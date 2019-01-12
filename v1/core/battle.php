<?php

    /*
        creeds_api - battle.py
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    header("Content-Type:application/json");
    require "../config.php";
    require "../game.php";
    require "../misc.php";

    # Create connection
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    # Check connection
    if ($conn) {

        # Charset to handle unicode
        $conn->set_charset('utf8mb4');
        mysqli_set_charset($conn, 'utf8mb4');

        # Get API token
        $token = "";
        if(!empty($_GET['token']))
        {
            $token = clean_str($conn, $_GET['token']);
        }

        # Validate API token
        # TODO: maybe validate if token belongs to current user
        if (validate_api_token($conn, $token))
        {
            # TODO!
            response(200, "ok", NULL);
        }
        else
        {
            # Return error
            response(403, "Unauthorized or invalid API token", NULL);
        }
    }
    else
    {
        # Return error
        response(500, "Unable to connect to the database", NULL);
    }
?>