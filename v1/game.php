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