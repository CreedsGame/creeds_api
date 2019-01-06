<?php

    /*
        creeds_api - character.py
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    header("Content-Type:application/json");
    require "../config.php";
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

            # Get page
            $page = 0;
            if(!empty($_GET['page'])) {
                $page = (int)$_GET['page'];
            }

            # Array for characters
            $characters = [];

            # Character name direct search
            if(!empty($_GET['name']))
            {
                # Character name
                $character_name = strtoupper(build_str(clean_str($conn, $_GET['name'])));

                # Prepare and run query
                $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$character_name."";
            }
            else
            {
                # User's characters search
                if(!empty($_GET['user']))
                {
                    # User name
                    $user_name = strtoupper(clean_str($conn, $_GET['user']));

                    # Filter by character's level
                    if(!empty($_GET['level']))
                    {
                        # Level
                        $char_level = clean_str($conn, $_GET['level']);

                        # Prepare and run query
                        $sql_query = "SELECT * FROM characters WHERE upper(userId) = '".$user_name."' AND level = '".$char_level."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);    
                    }
                    else
                    {
                        # Prepare and run query
                        $sql_query = "SELECT * FROM characters WHERE upper(userId) = '".$user_name."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                }
                # Get all characters (limited to page)
                else
                {
                    # Filter by character's level
                    if(!empty($_GET['level']))
                    {
                        # Level
                        $char_level = clean_str($conn, $_GET['level']);

                        # Prepare and run query
                        $sql_query = "SELECT * FROM characters WHERE level = '".$char_level."' ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                    else
                    {
                        # Prepare and run query
                        $sql_query = "SELECT * FROM characters ORDER BY creation LIMIT 10 OFFSET ".($page*10);
                    }
                }
            }

            # Return characters matching query
            response(200, "ok", get_characters($sql_query, $conn, $characters));

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

    # Execute query, push characters to array and return it
    function get_characters($sql_query, $sql_conn, $characters)
    {

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Check if there were results
        if (mysqli_num_rows($result) > 0)
        {
            # Loop thru characters
            while ($row = mysqli_fetch_assoc($result))
            {

                # Build character data
                $character = [
                    "name" => $row["name"],
                    "userId" => $row["userId"],
                    "creation" => $row["creation"],
                    "battleCount" => (int)$row["battleCount"],
                    "lastBattle" => $row["lastBattle"],
                    "level" => (int)$row["level"],
                    "experience" => (int)$row["experience"],
                    "power" => (int)$row["power"],
                    "agility" => (int)$row["agility"],
                    "speed" => (int)$row["speed"],
                    "endurance" => (int)$row["endurance"],
                    "initiative" => (int)$row["initiative"],
                    "multigrap" => (int)$row["multigrap"],
                    "counterattack" => (int)$row["counterattack"],
                    "evasion" => (int)$row["evasion"],
                    "anticipation" => (int)$row["anticipation"],
                    "blocking" => (int)$row["blocking"],
                    "armor" => (int)$row["armor"],
                    "disarm" => (int)$row["disarm"],
                    "accuracy" => (int)$row["accuracy"],
                    "correctness" => (int)$row["correctness"]
                ];

                # TODO: Add 'skills' to character, which is an array of skills.
                # TODO: Add 'pets' to character, which is an array of pets.
                # TODO: Add 'weapons' to character, which is an array of weapons.

                # Push character to characters array
                array_push($characters, $character);
            }
        }

        # Return array of JSON characters
        return $characters;
    }
?>