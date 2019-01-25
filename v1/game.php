<?php

    /*
        creeds_api - misc.py
        API and game related functions
        
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    # Validate API token
    # TODO: maybe validate if token belongs to current user, receiving user as param
    function validate_api_token($sql_conn, $token)
    {
        # Check for empty token
        if ($token == "")
        {
            return false;
        }

        # SQL token
        $sql_token = build_str($token);

        # Prepare and run query
        $sql_query = "SELECT * FROM api_tokens WHERE token = ".$sql_token." ORDER BY token";

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Check if there were results
        if (mysqli_num_rows($result) > 0)
        {
            # TODO: Update count and lastUsage?
            return true;
        }
        else
        {
            return false;
        }
    }

    # Get token's user and return it
    function get_token_user($sql_conn, $token)
    {
        # Check for empty token
        if ($token == "")
        {
            return NULL;
        }

        # SQL token
        $sql_token = build_str($token);

        # Prepare and run query
        $sql_query = "SELECT * FROM api_tokens WHERE token = ".$sql_token." ORDER BY token";

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Check if there were results
        if (mysqli_num_rows($result) == 1)
        {
            # Get row
            $row = mysqli_fetch_assoc($result);

            # Return user
            return $row["userId"];
        }
        else
        {
            return NULL;
        }
    }

    # Validate character name
    function validate_character_name($name)
    {
        # Check for empty name
        if ($name == "")
        {
            return false;
        }

        # Check string length
        if (strlen($name) > 20)
        {
            return false;
        }

        # Regex for alphanumeric only
        preg_match('/^[a-zA-Z0-9_]*$/', $name, $matches);

        # Check for matches
        if (count($matches) <= 0)
        {
            return false;
        }

        # Return OK
        return true;
    }

    # Check if character's name already exists
    function check_character_name($sql_conn, $name)
    {
        # Character name
        $character_name = strtoupper(build_str(clean_str($sql_conn, $name)));

        # Prepare query
        $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$character_name."";

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Check if there were results
        if (mysqli_num_rows($result) > 0)
        {
            # Return error
            return false;
        }
        else {
            # Return OK
            return true;
        }
    }

    # Run a battle between two creeds and return its result
    function get_battle_results($fighter_stats, $opponent_stats)
    {
        # Empty result
        $battle_result = ["boom! (TODO)"];

        # Return battle result
        return $battle_result;
    }

    # Create a random character and return it
    function create_character($sql_conn, $character_name, $user)
    {
        # Array for characters
        $characters = [];

        # Default values for character
        # TODO: Maybe randomize some of those stats
        $name = build_str(clean_str($sql_conn, $character_name));
        $userId = build_str(clean_str($sql_conn, $user));
        $battleCount = 0;
        $level = 1;
        $experience = 0;
        #################
        $strength = 2;
        $endurance = 2;
        $agility = 2;
        #################
        $evasion = 1;
        $initiative = 1;
        $blocking = 1;

        # Prepare and run query
        $sql_query = "INSERT INTO characters(name, userId, battleCount, level, experience, strength, endurance, agility, evasion, initiative, blocking) VALUES({$name}, {$userId}, {$battleCount}, {$level}, {$experience}, {$strength}, {$endurance}, {$agility}, {$evasion}, {$initiative}, {$blocking})";

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Commit changes
        mysqli_query($sql_conn, "COMMIT");

        # Build character data
        $character = [
            "name" => $character_name,
            "userId" => $user,
            "creation" => "",
            "battleCount" => $battleCount,
            "lastBattle" => null,
            "level" => $level,
            "experience" => $experience,
            ##########################################
            "strength" => $strength,
            "endurance" => $endurance,
            "agility" => $agility,
            ##########################################
            "evasion" => $evasion,
            "initiative" => $initiative,
            "blocking" => $blocking
        ];

        # Push character to characters array
        array_push($characters, $character);

        # Return new character data
        return $characters;
    }

    # Execute query, push characters to array and return it
    function get_characters($sql_query, $sql_conn)
    {
        # Array for characters
        $characters = [];

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
                    ##########################################
                    "strength" => (int)$row["strength"],
                    "endurance" => (int)$row["endurance"],
                    "agility" => (int)$row["agility"],
                    ##########################################
                    "evasion" => (int)$row["evasion"],
                    "initiative" => (int)$row["initiative"],
                    "blocking" => (int)$row["blocking"]
                ];

                # Push character to characters array
                array_push($characters, $character);
            }
        }

        # Return array of JSON characters
        return $characters;
    }

    # Execute query, push tokens to array and return it
    function get_tokens($sql_query, $sql_conn)
    {
        # Array for tokens
        $tokens = [];

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Check if there were results
        if (mysqli_num_rows($result) > 0)
        {
            # Loop thru tokens
            while ($row = mysqli_fetch_assoc($result))
            {
                # Build token data
                $token = [
                    "token" => $row["token"],
                    "count" => (int)$row["count"],
                    "lastUsage" => $row["lastUsage"]
                ];

                # Push token to tokens array
                array_push($tokens, $token);
            }
        }

        # Return array of JSON tokens
        return $tokens;
    }
?>