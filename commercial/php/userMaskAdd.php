<?php

function grantAccess($userId, $accessId, $query)
{
    $today = date("Y-m-d");
    $rowsNames = "User_id,Acces_id,DateAcces";
    $rowsValues = "'$userId','$accessId','$today'";
    $sqlGrant = "INSERT INTO webcommercial_multiacces ($rowsNames) VALUES ($rowsValues);";
    querySQL($sqlGrant, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    $location = "userMaskList.php?id=" . $userId . "&query=" . $query;
    header("Location: $location");
}

require_once "helper.php";

session_start();

$credentialsW = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$userId = filter_input(INPUT_GET, "userId");
$accessId = filter_input(INPUT_GET, "accessId");
$query = filter_input(INPUT_GET, "query");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged())
        grantAccess($userId, $accessId, $query);
    else
        header("Location: index.php");
    $connection->close();
}

?>
