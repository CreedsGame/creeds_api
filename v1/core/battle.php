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
    if ($conn)
    {
        # Charset to handle unicode
        $conn->set_charset('utf8mb4');
        mysqli_set_charset($conn, 'utf8mb4');

        # Get current HTTP method
        $method = $_SERVER['REQUEST_METHOD'];

        # Only HTTP PUT supported (for now)
        if ($method == "PUT")
        {
            # Get incoming data
            parse_str(file_get_contents("php://input"), $put_vars);

            # Check for fighter
            if (!empty($put_vars['fighter']))
            {
                # Get current fighter
                $fighter = clean_str($conn, $put_vars['fighter']);

                # Check for password
                if (!empty($put_vars['password']))
                {
                    # Get password
                    $password = $put_vars['password'];

                    # Validate password
                    if (validate_password($password))
                    {
                        # Fighter vs. specific opponent
                        if (!empty($put_vars['opponent']) && ($fighter == $put_vars['opponent']))
                        {
                            # Return error
                            response(400, "Fighter and opponent must be different", NULL);
                        }

                        # Fighter's name to upper for query
                        $fighter_upper = strtoupper(build_str($fighter));

                        # Hashed password for query
                        $password_hash = build_str(string_to_hash($password));

                        # Prepare query to get current fighter
                        $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$fighter_upper." AND password = ".$password_hash."";

                        echo $sql_query;

                        # Get fighter stats                
                        $fighter_stats = get_characters($sql_query, $conn);

                        # Check fighter stats
                        if (count($fighter_stats) > 0)
                        {
                            # Fighter vs. specific opponent
                            if (!empty($put_vars['opponent']))
                            {

                                # Get current opponent
                                $opponent = clean_str($conn, $put_vars['opponent']);

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
                                if (count($first_greater_level) > 0)
                                {
                                    # Get needed level (equal/greater)
                                    $needed_level = $first_greater_level[0]["level"];
                                }
                                else
                                {
                                    # Prepare query to get first with lower level
                                    $sql_query = "SELECT * FROM characters WHERE upper(name) <> ".$fighter_upper." AND level < ".$fighter_level." ORDER BY level DESC LIMIT 1";

                                    # Get first with equal/greater level
                                    $first_lower_level = get_characters($sql_query, $conn);

                                    if (count($first_lower_level) > 0)
                                    {
                                        # Get needed level (lower)
                                        $needed_level = $first_lower_level[0]["level"];
                                    }
                                    else
                                    {
                                        # Return error (would only happen if there's only one registered character)
                                        response(400, "Couldn't find an opponent", NULL);
                                    }
                                }

                                # Prepare query to get first 20 possible opponents
                                $sql_query = "SELECT * FROM characters WHERE upper(name) <> ".$fighter_upper." AND level = ".$needed_level." LIMIT 20";

                                # Get first 10 possible opponents
                                $first_ten_suitable = get_characters($sql_query, $conn);

                                # Checking again just in case
                                if (count($first_ten_suitable) > 0)
                                {
                                    # Empty array to return
                                    $opponent_stats = [];

                                    # Select one random opponent
                                    array_push($opponent_stats, $first_ten_suitable[rand(0,count($first_ten_suitable)-1)]);
                                }
                                else
                                {
                                    # Return error (would only happen if our previously retrieved character just got deleted)
                                    response(400, "Couldn't find an opponent", NULL);
                                }
                            }

                            # Check opponent stats
                            if (count($opponent_stats) > 0)
                            {
                                # Return battle's results
                                response(200, "ok", get_battle_results($fighter_stats, $opponent_stats));
                            }
                            else
                            {
                                # Return error
                                response(400, "No stats found for opponent", NULL);
                            }
                        }
                        else
                        {
                            # Return error
                            response(400, "Invalid fighter's name or password", NULL);
                        }
                    }
                    else
                    {
                        # Return error
                        response(400, "Invalid password", NULL);
                    }
                }
                else
                {
                    # Return error
                    response(400, "Unspecified password", NULL);
                }
            }
            else
            {
                # Return error
                response(400, "Unspecified fighter", NULL);
            }
        }
        else
        {
            # Return error
            response(501, "Not implemented", NULL);
        }
    }
    else
    {
        # Return error
        response(500, "Unable to connect to the database", NULL);
    }
?>