<?php

function deleteUser($userId)
{
    $sqlUser = "DELETE FROM webcontrat_utilisateurs WHERE id='$userId';";
    querySQL($sqlUser, $GLOBALS['connectionW'], false);
    header("Location: userList.php");
}

require_once "helper.php";

session_start();

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$userId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && isAdmin())
        deleteUser($userId);
    else
        header("Location: index.php");

    $connectionW->close();
}

?>
