<?php

    /*
        creeds_api - character.py
        Returns all information about character(s) matching filters

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
        if(!empty($_POST['token']))
        {
            $token = clean_str($conn, $_POST['token']);
        }

        # Validate API token
        if (validate_api_token($conn, $token))
        {

            # Get page
            $page = 0;
            if (!empty($_POST['page'])) {
                $page = (int)$_POST['page'];
            }

            # Character name direct search
            if (!empty($_POST['name']))
            {
                # Character name
                $character_name = strtoupper(build_str(clean_str($conn, $_POST['name'])));

                # Prepare query
                $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$character_name."";
            }
            else
            {
                # User's characters search
                if (!empty($_POST['user']))
                {
                    # User name
                    $user_name = strtoupper(clean_str($conn, $_POST['user']));

                    # Filter by character's level
                    if (!empty($_POST['level']))
                    {
                        # Level
                        $char_level = clean_str($conn, $_POST['level']);

                        # Prepare query
                        $sql_query = "SELECT * FROM characters WHERE upper(userId) = '".$user_name."' AND level = '".$char_level."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                    else
                    {
                        # Prepare query
                        $sql_query = "SELECT * FROM characters WHERE upper(userId) = '".$user_name."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                }
                # Get all characters (limited to page)
                else
                {
                    # Filter by character's level
                    if (!empty($_POST['level']))
                    {
                        # Level
                        $char_level = clean_str($conn, $_POST['level']);

                        # Prepare query
                        $sql_query = "SELECT * FROM characters WHERE level = '".$char_level."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                    else
                    {
                        # Prepare query
                        $sql_query = "SELECT * FROM characters ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                }
            }

            # Return characters matching query
            response(200, "ok", get_characters($sql_query, $conn));

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