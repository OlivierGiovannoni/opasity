<?php

function importClient($userId, $clientId, $reviewId)
{
    $createdAt = date("Y-m-d");

    $rowNames = "Client_id,User_id,DateAcces,Autorisation";
    $rowValues = "'$clientId','$userId','$createdAt',1";
    $sqlPerm = "INSERT INTO webcommercial_permissions_client ($rowNames) VALUES ($rowValues);";
    querySQL($sqlPerm, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.

    $rowNames = "Client_id,Revue_id";
    $rowValues = "'$clientId','$reviewId'";
    $sqlReview = "INSERT INTO webcommercial_client_revue ($rowNames) VALUES ($rowValues);";
    querySQL($sqlReview, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.

    header("Location: reviewClients.php?reviewId=" . $reviewId);    
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$reviewId = $_SESSION['reviewId'];
$clientId = filter_input(INPUT_GET, "clientId");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $userId = getUserId($_SESSION['author']);

        importClient($userId, $clientId, $reviewId);
    } else
        header("Location: index.php");
    $connection->close();
}

?>
