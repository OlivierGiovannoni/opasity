<?php

function userAccess($query, $userId)
{
    $columns = "id,username";
    $sqlUsers = "SELECT $columns FROM webcontrat_utilisateurs WHERE username LIKE '%$query%';";
    $rowsUsers = querySQL($sqlUsers, $GLOBALS['connection']);

    foreach ($rowsUsers as $rowUser) {

        $accessId = $rowUser['id'];
        $username = $rowUser['username'];

        $columns = "User_id,Acces_id";
        $sqlAccess = "SELECT $columns FROM webcommercial_multiacces WHERE User_id='$userId' AND Acces_id='$accessId';";
        $nbAccess = numberSQL($sqlAccess, $GLOBALS['connection']);

        if ($nbAccess === 0) {

            $hasAccess = "Non";
            $accessHref = "userMaskAdd.php?";
            $accessHref = $accessHref . "userId=" . $userId . "&accessId=" . $accessId . "&query=" . $query;
            $accessDesc = "Donner l'accès";
            $accessImage = generateImage("../png/add.png", $accessDesc, 24, 24);
        } else {

            $hasAccess = "Oui";
            $accessHref = "userMaskDel.php?";
            $accessHref = $accessHref . "userId=" . $userId . "&accessId=" . $accessId . "&query=" . $query;
            $accessDesc = "Enlever l'accès";
            $accessImage = generateImage("../png/uncheck.png", $accessDesc, 24, 24);
        }
        $accessLink = generateLink($accessHref, $accessImage, "_self");

        $cells = array($username, $hasAccess, $accessLink);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$query = filter_input(INPUT_GET, "query");
$userId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/userMaskAdd.html");
        $style = str_replace("{userId}", $userId, $style);
        $style = str_replace("{query}", $query, $style);
        echo $style;

        echo "<table>";

        $cells = array("Utilisateur","A accès","Modifier accès");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        if (isset($query))
            userAccess($query, $userId);
    } else
        header("Location: index.php");

    $connection->close();
}

?>
