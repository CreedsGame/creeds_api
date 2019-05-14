<?php

    /*
        creeds_api - login.py
        Log in a character and return it's information
        
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

    # Only HTTP GET supported (for now)
    if ($method == "GET")
    {
        # Check for fighter's name
        if (empty($_GET['name']))
            response(400, "Unspecified fighter", NULL);

        # Check for password
        if (empty($_GET['password']))
            response(400, "Unspecified password", NULL);

        # Get password
        $password = $_GET['password'];

        # Validate password
        if (!validate_password($password))
            response(400, "Invalid password", NULL);

        # Character name
        $character_name = strtoupper(build_str(clean_str($conn, $_GET['name'])));

        # Character password
        $character_password = build_str(string_to_hash($_GET['password']));

        # Prepare query
        $sql_query = "SELECT * FROM characters WHERE upper(name) = ".$character_name." AND password = ".$character_password."";

        # Character's matching (should be one or none)
        $characters_matching = get_characters($sql_query, $conn);

        # Check if there were any results
        if (!$characters_matching)
            response(400, "Invalid login", NULL);

        # Return OK
        response(200, "ok", $characters_matching[0]);
    }
    else
    {
        # Return error
        response(501, "Not implemented", NULL);
    }
?>