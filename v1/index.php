<?php
    require "config.php";
    $html = "<!DOCTYPE html>
    <head>
        <meta charset=\"utf-8\">
        <title>Endpoints — ".$API_NAME."</title>
    </head>
    <body>
        <h2>Endpoints</h2>
        <hr>
        <ul>
            <li>
                <a href=\"battle/\">/battle</a> — Start a 1v1 battle and return its result
            </li>
            <li>
                <a href=\"character/\">/character</a> — Return the character(s) information matching the criteria
            </li>
            <li>
                <a href=\"login/\">/login</a> — Log in character
            </li>
        </ul>
        <hr>
        <span style=\"font-size: smaller\">
            <i>
                ".$API_NAME."/".$API_VERSION."
            </i>
        </span>
    </body>
</html>";
    die($html);
?>