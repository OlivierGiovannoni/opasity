<?php

function deleteUser($userId)
{
    $sqlUser = "DELETE FROM webcommercial_utilisateurs WHERE id='$userId';";
    querySQL($sqlUser, $GLOBALS['connection'], false);
    header("Location: userList.php");
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$userId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && isAdmin())
        deleteUser($userId);
    else
        header("Location: index.php");

    $connection->close();
}

?>
