<?php

function revokeAccess($userId, $reviewId, $query)
{
    $sqlRevoke = "UPDATE webcommercial_permissions_revue SET Autorisation=0 WHERE User_id='$userId' AND Revue_id='$reviewId';";
    querySQL($sqlRevoke, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
    $location = "userReviewsAdd.php?reviewId=" . $reviewId . "&username=" . $query;
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
$reviewId = filter_input(INPUT_GET, "reviewId");
$query = filter_input(INPUT_GET, "query");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged())
        revokeAccess($userId, $reviewId, $query);
    else
        header("Location: index.php");
    $connection->close();
}


?>
