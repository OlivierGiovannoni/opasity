<?php

function getFiles($directory)
{
    $fullpath = "./files/" . $directory . "/";
    $files = scandir($fullpath);

    foreach ($files as $file) {

        if ($file === "." || $file === "..")
            continue ;
        $file = sanitizeInput($file);
        $file = stripslashes($file);
        $fileLink = generateLink($fullpath . $file, $file);
        echo $fileLink . "<br>";
    }
}

require_once "helper.php";

session_start();

$directory = filter_input(INPUT_GET, "dir");

getFiles($directory);

?>
