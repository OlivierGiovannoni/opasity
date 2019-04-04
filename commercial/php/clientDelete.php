<?php

function deleteClient($clientId, $username)
{
    if (isAdmin())
        $sqlDelete = "DELETE FROM webcommercial_client WHERE id='$clientId';";
    else {

        $userId = getUserId($username);
        $sqlDelete = "UPDATE webcommercial_permissions_client SET Autorisation=0 WHERE Client_id='$clientId' AND User_id='$userId';";
    }
    querySQL($sqlDelete, $GLOBALS['connection'], false); // DELETE/UPDATE output doesn't need to be fetched.
    header("Location: clientList.php");
}

require "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$clientId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged())
        deleteClient($clientId, $_SESSION['author']);
    else
        header("Location: index.php");

    $connection->close();
}

?>
