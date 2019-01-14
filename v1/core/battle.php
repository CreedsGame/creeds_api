<?php

    /*
        creeds_api - battle.py
        Starts a battle between two creeds, and returns its result
        
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
        if (!empty($_GET['token']))
        {
            $token = clean_str($conn, $_GET['token']);
        }

        # Validate API token
        if (validate_api_token($conn, $token))
        {
            # Check for fighter
            if (!empty($_GET['fighter']))
            {

                # Get current fighter
                $fighter = clean_str($conn, $_GET['fighter']);

                # Fighter vs. specific opponent
                if (!empty($_GET['opponent'])) {

                    # Get current opponent
                    $opponent = clean_str($conn, $_GET['opponent']);

                    # Characters have to be different
                    if ($fighter != $opponent)
                    {
                        # Fighter's name to upper for query
                        $fighter_upper = strtoupper(build_str($fighter));

                        # Opponent's name to upper for query
                        $opponent_upper = strtoupper(build_str($opponent));
                    }
                    else
                    {
                        # Return error
                        response(403, "Fighter and opponent must be different", NULL);
                    }

                }
                else
                {
                    # TODO: Get random opponent from database and set to $opponent and $opponent_upper
                    response(403, "Not implemented, please specify an opponent", NULL);
                }

                # Prepare query to get current fighter
                $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$fighter_upper."";

                # Get fighter stats                
                $fighter_stats = get_characters($sql_query, $conn);

                # Check fighter stats
                if (count($fighter_stats) > 0)
                {
                    # Prepare query to get current opponent
                    $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$opponent_upper."";

                    # Get opponent stats                
                    $opponent_stats = get_characters($sql_query, $conn);

                    # Check opponent stats
                    if (count($opponent_stats) > 0)
                    {
                        # TODO: Function to fight between two creeds array, and return a dict
                        response(200, "ok", $fighter_stats);
                    }
                    else
                    {
                        # Return error
                        response(403, "No stats found for opponent", NULL);
                    }
                }
                else
                {
                    # Return error
                    response(403, "No stats found for fighter", NULL);
                }

            }
            else
            {
                # Return error
                response(403, "Unspecified fighter", NULL);
            }
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