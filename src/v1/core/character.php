<?php

    /*
        creeds_api - character.py
        Returns all information about character(s) matching filters

        Contribute on https://github.com/CreedsGame/creeds_api
    */

    header("Content-Type:application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods", "POST, GET, PUT, DELETE");
    require "../config.php";
    require "../game.php";
    require "../misc.php";

    # Create connection
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    # Check connection
    if (!$conn)
        response(500, "Unable to connect to the database", NULL);

    # Charset to handle unicode
    $conn->set_charset('utf8mb4');
    mysqli_set_charset($conn, 'utf8mb4');

    # Get current HTTP method
    $method = $_SERVER['REQUEST_METHOD'];

    # Create a new random character (HTTP PUT) or return characters matching criteria (HTTP POST)
    if ($method == "PUT") {
        # Get incoming data
        parse_str(file_get_contents("php://input"), $put_vars);

        # Check for empty character's name
        if (empty($put_vars['name']))
            response(400, "Unspecified character's name", NULL);
        
        # Get received character's name
        $character_name = $put_vars['name'];

        # Check for empty password
        if (empty($put_vars['password']))
            response(400, "Unspecified password", NULL);
        
        # Get received password
        $password = $put_vars['password'];

        # Validate character's name
        if (!validate_character_name($character_name))
            response(400, "Invalid character's name", NULL);

        # Validate password
        if (!validate_password($password))
            response(400, "Invalid password", NULL);

        # Check if character's name already exists
        if (!check_character_name($conn, $character_name))
            response(400, "Character's name already exists", NULL);

        # Create and return a new character
        response(200, "ok", create_character($conn, $character_name, $password));
        
    } elseif ($method == "GET") {
        # Get page
        $page = 0;
        if (!empty($_GET['page']))
            $page = (int)$_GET['page'];

        # Character name direct search
        if (!empty($_GET['name'])) {
            # Character name
            $character_name = strtoupper(build_str(clean_str($conn, $_GET['name'])));

            # Prepare query
            $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$character_name."";
        } else {
            # Filter by character's level
            if (!empty($_GET['level'])) {
                # Level
                $char_level = clean_str($conn, $_GET['level']);

                # Prepare query
                $sql_query = "SELECT * FROM characters WHERE level = '".$char_level."' ORDER BY creation DESC LIMIT 10 OFFSET ".($page*10);
            } else {
                # Prepare query
                $sql_query = "SELECT * FROM characters ORDER BY creation DESC LIMIT 10 OFFSET ".($page*10);
            }
        }
        # Return characters matching query
        response(200, "ok", get_characters($sql_query, $conn));
    } else {
        # Return error
        response(501, "Not implemented", NULL);
    }
?>