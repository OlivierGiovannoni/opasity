<?php

function createClient($clientName, $address1, $address2, $zip, $city, $country, $siretCode, $apeCode, $author)
{
    $createdAt = date("Y-m-d");
    $rowNames = "NomSociete,Addr1,Addr2,CP,Ville,Pays,SIRET,CodeAPE,DateCreation,Createur";
    $rowValues = "'$clientName','$address1','$address2','$zip','$city','$country','$siretCode','$apeCode','$createdAt','$author'";
    $sqlReview = "INSERT INTO webcommercial_client ($rowNames) VALUES ($rowValues);";
    querySQL($sqlReview, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    $sqlLast = "SELECT LAST_INSERT_ID();";
    $rowLast = querySQL($sqlLast, $GLOBALS['connection'], true, true);
    $lastId = $rowLast['LAST_INSERT_ID()'];
    $userId = getUserId($author);
    $rowNames = "Client_id,User_id,DateAcces,Autorisation";
    $rowValues = "'$lastId','$userId','$createdAt',1";
    $sqlPerm = "INSERT INTO webcommercial_permissions_client ($rowNames) VALUES ($rowValues);";
    querySQL($sqlPerm, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    header("Location: clientList.php");
}

require "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$clientName = filter_input(INPUT_POST, "clientName");
$address1 = filter_input(INPUT_POST, "address1");
$address2 = filter_input(INPUT_POST, "address2");
$zipCode = filter_input(INPUT_POST, "zipCode");
$city = filter_input(INPUT_POST, "city");
$country = filter_input(INPUT_POST, "country");
$phone = filter_input(INPUT_POST, "phone");
$siretCode = filter_input(INPUT_POST, "siretCode");
$apeCode = filter_input(INPUT_POST, "apeCode");

$clientName = sanitizeInput($clientName);
$address1 = sanitizeInput($address1);
$address2 = sanitizeInput($address2);
$zipCode = sanitizeInput($zipCode);
$city = sanitizeInput($city);
$country = sanitizeInput($country);
$phone = sanitizeInput($phone);
$siretCode = sanitizeInput($siretCode);
$apeCode = sanitizeInput($apeCode);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $author = $_COOKIE['author'];
        createClient($clientName, $address1, $address2, $zipCode, $city, $country, $siretCode, $apeCode, $author);
    } else
        header("Location: index.php");

    $connection->close();
}

?>
