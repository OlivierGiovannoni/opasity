<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return ($data);
}

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");

?>
