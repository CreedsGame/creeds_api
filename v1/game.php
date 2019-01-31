<?php

    /*
        creeds_api - misc.py
        API and game related functions
        
        Contribute on https://github.com/CreedsGame/creeds_api
    */

    # Validate password
    function validate_password($password)
    {
        # Check for empty password
        if ($password == "")
        {
            return false;
        }

        # Check string length
        if (strlen($password) > 50)
        {
            return false;
        }

        # Return OK
        return true;
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
        if (strlen($name) > 30)
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
        # Array of players for convenience
        $players = [$fighter_stats[0], $opponent_stats[0]];

        # Starting player (using 'initiative' stat)
        $current_player = 0;
        if ($players[0]["initiative"] == $players[1]["initiative"])
        {
            # Same initiative, so get it randomly
            $current_player = rand(0,1);
        }
        else
        {
            # Check for both player's initiative
            if ($players[0]["initiative"] > $players[1]["initiative"])
            {
                # Fighter has greater initiative
                $current_player = 0;
            }
            else
            {
                # Opponent has greater initiative
                $current_player = 1;
            }
        }

        # Keep player who started
        $starting_player = $current_player;

        # Starting life of each players
        $life = [get_player_life($players[0]["level"]), get_player_life($players[1]["level"])];

        # Array of turns
        $turns = [];

        # Set modifiers (fixed values based on level difference)
        $first_difference = $players[0]["level"] - $players[1]["level"];
        $second_difference = $players[1]["level"] - $players[0]["level"];
        $modifiers = [$first_difference <= 0 ? 1 : $first_difference + 1, $second_difference <= 0 ? 1 : $second_difference + 1];

        # Keep battling until some player dies
        while ($life[0] > 0 && $life[1] > 0)
        {
            # Calculate damage dealt
            # TODO: Consider also single-handed two-handed weapons for 1 or 2 hits, shields (blocking boost) and so on
            # Damage formula: 1 hit = floor(5*sqrt(str/end*atk)*mod*rnd)

            # Critical hit chance (1/10 at the moment)
            $critical = get_critical_hit();

            # Get stats
            $str = $players[$current_player]["strength"];
            $end = $players[get_next_index($current_player)]["endurance"];
            # $agi = $players[$current_player]["agility"];
            $end = $players[$current_player]["endurance"];
            $atk = 1; # TODO
            $mod = $modifiers[$current_player];
            $rnd = get_randomness_factor();

            # Calculate final damage
            $damage = floor(5*sqrt($str/$end*$atk)*$mod*$rnd);
            $damage = $damage*($critical ? 2 : 1);

            # Reduce opponent's life
            $life[get_next_index($current_player)] = $life[get_next_index($current_player)] - $damage;

            # Life shouldn't be negative
            if ($life[get_next_index($current_player)] < 0)
            {
                $life[get_next_index($current_player)] = 0;
            }

            # Get current action
            # TODO: Determine evasion/blocking
            # Action names: "EVADED" and "BLOCKED"
            $action = $critical ? "CRITICAL HIT" : "HIT";

            # Build current turn
            $turn = [
                "action" => $action,
                "damage" => $damage,
                "executor" => $players[$current_player]["name"],
                "receiver" => $players[get_next_index($current_player)]["name"]
            ];

            # Push turn to turns array
            array_push($turns, $turn);

            # Switch players
            $current_player = get_next_index($current_player);
        }

        # Determine winner player
        if ($life[0] <= 0)
        {
            # Opponent won
            $winner_player = 1;
        }
        else
        {
            # Fighter won
            $winner_player = 0;
        }

        # Build outcome
        $outcome = [
            "fighter" => $players[0],
            "opponent" => $players[1],
            "starting" => $players[$starting_player]["name"],
            "winner" => $players[$winner_player]["name"],
            "loser" => $players[get_next_index($winner_player)]["name"],
            "life" => $life,
            "turns" => $turns
        ];

        # Return battle result
        return $outcome;
    }

    # Get next player's index (for convenience)
    function get_next_index($index)
    {
        # Switch index
        if ($index == 0)
        {
            $index = 1;
        }
        else
        {
            $index = 0;
        }

        # Return new index
        return $index;
    }

    # Randomness factor (between 0.95 and 1.05)
    function get_randomness_factor()
    {
        return rand(95, 105) / 100;
    }

    # Get critical hit chance
    function get_critical_hit()
    {
        return rand(1,10) == 10 ? true : false;
    }

    # Create a random character and return it
    function create_character($sql_conn, $character_name, $password)
    {
        # Array for characters
        $characters = [];

        # Default values for character
        # TODO: Maybe randomize some of those stats
        $name = build_str(clean_str($sql_conn, $character_name));
        $hashed_password = build_str(string_to_hash(clean_str($sql_conn, $password)));
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
        $sql_query = "INSERT INTO characters(name, password, battleCount, level, experience, strength, endurance, agility, evasion, initiative, blocking) VALUES({$name}, {$hashed_password}, {$battleCount}, {$level}, {$experience}, {$strength}, {$endurance}, {$agility}, {$evasion}, {$initiative}, {$blocking})";

        # Execute query
        $result = mysqli_query($sql_conn, $sql_query);

        # Commit changes
        mysqli_query($sql_conn, "COMMIT");

        # Build character data
        $character = [
            "name" => $character_name,
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
            "blocking" => $blocking,
            ##########################################
            "life" => get_player_life($level)
        ];

        # Push character to characters array
        array_push($characters, $character);

        # Return new character data
        return $characters;
    }

    # Get player's total life
    function get_player_life($level, $player_stats=NULL)
    {
        # Base life
        $life = 50;

        # TODO: Get all other additional life boost
        if (!empty($player_stats))
            ;

        # Level boost
        $life = $life + $level*10;

        # Return player's life
        return $life;
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
                    "blocking" => (int)$row["blocking"],
                    ##########################################
                    "life" => get_player_life((int)$row["level"])
                ];

                # Push character to characters array
                array_push($characters, $character);
            }
        }

        # Return array of JSON characters
        return $characters;
    }
?>