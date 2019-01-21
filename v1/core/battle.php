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
                if (!empty($_GET['opponent']) && ($fighter == $_GET['opponent'])) {
                    # Return error
                    response(403, "Fighter and opponent must be different", NULL);
                }

                # Fighter's name to upper for query
                $fighter_upper = strtoupper(build_str($fighter));

                # Prepare query to get current fighter
                $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$fighter_upper."";

                # Get fighter stats                
                $fighter_stats = get_characters($sql_query, $conn);

                # Check fighter stats
                if (count($fighter_stats) > 0)
                {
                    # Fighter vs. specific opponent
                    if (!empty($_GET['opponent'])) {

                        # Get current opponent
                        $opponent = clean_str($conn, $_GET['opponent']);

                        # Opponent's name to upper for query
                        $opponent_upper = strtoupper(build_str($opponent));

                        # Prepare query to get current opponent
                        $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$opponent_upper."";

                        # Get opponent stats                
                        $opponent_stats = get_characters($sql_query, $conn);
                    }
                    else
                    {
                        # Get fighter level
                        $fighter_level = $fighter_stats[0]["level"];

                        # Prepare query to get first with equal/greater level
                        $sql_query = "SELECT * FROM characters WHERE upper(name) <> ".$fighter_upper." AND level >= ".$fighter_level." ORDER BY level LIMIT 1";

                        # Get first with equal/greater level
                        $first_greater_level = get_characters($sql_query, $conn);
                        
                        # Check if there's any with greater level, if not, we try with lower level
                        if (count($first_greater_level) > 0) {
                            # TODO: Prepare query to get first 10 (greater level)
                            $sql_query = "";
                        }
                        else {
                            # Prepare query to get first with lower level
                            $sql_query = "SELECT * FROM characters WHERE upper(name) <> ".$fighter_upper." AND level < ".$fighter_level." ORDER BY level LIMIT 1";

                            # Get first with equal/greater level
                            $first_lower_level = get_characters($sql_query, $conn);

                            if (count($first_lower_level) > 0) {
                                # TODO: Prepare query to get first 10 (lower level)
                                $sql_query = "";
                            }
                            else {
                                # Return error
                                # This would only happen if there's only one registered character, but just in case.
                                response(403, "Couldn't find an opponent", NULL);
                            }
                        }

                        # TODO: Run query already defined to get first 10
                        $first_ten_suitable = [];
                        # TODO: Randomly get one between them and load array on $opponent_stats
                        # TODO: Test case when there's only one character

                        response(403, "Not implemented, please specify an opponent", NULL);
                    }

                    # Check opponent stats
                    if (count($opponent_stats) > 0)
                    {
                        # TODO: Function to fight between two creeds array, and return a dict
                        response(200, "ok", $opponent_stats);
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