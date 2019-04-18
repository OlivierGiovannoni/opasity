<?php

function grantAccess($userId, $reviewId, $query)
{
    $columns = "Autorisation,Revue_id,User_id,DateAcces";
    $sqlAccess = "SELECT $columns FROM webcommercial_permissions_revue WHERE User_id='$userId' AND Revue_id='$reviewId';";
    $nbAccess = numberSQL($sqlAccess, $GLOBALS['connection']);

    if ($nbAccess === 0) {

        $today = date("Y-m-d");
        $rowsNames = "User_id,Revue_id,DateAcces,Autorisation";
        $rowsValues = "'$userId','$reviewId','$today',1";
        $sqlGrant = "INSERT INTO webcommercial_permissions_revue ($rowsNames) VALUES ($rowsValues);";
    } else
        $sqlGrant = "UPDATE webcommercial_permissions_revue SET Autorisation=1 WHERE User_id='$userId' AND Revue_id='$reviewId';";
    querySQL($sqlGrant, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
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
        grantAccess($userId, $reviewId, $query);
    else
        header("Location: index.php");
    $connection->close();
}

?>
