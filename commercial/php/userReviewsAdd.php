<?php

function userAccess($query, $reviewId)
{
    $columns = "id,username";
    $sqlUsers = "SELECT $columns FROM webcontrat_utilisateurs WHERE username LIKE '%$query%';";
    $rowsUsers = querySQL($sqlUsers, $GLOBALS['connection']);

    foreach ($rowsUsers as $rowUser) {

        $userId = $rowUser['id'];
        $username = $rowUser['username'];

        $columns = "Autorisation,Revue_id,User_id,DateAcces";
        $sqlAccess = "SELECT $columns FROM webcommercial_permissions_revue WHERE User_id='$userId' AND Revue_id='$reviewId';";
        $nbAccess = numberSQL($sqlAccess, $GLOBALS['connection']);
        $rowAccess = querySQL($sqlAccess, $GLOBALS['connection'], true, true);
        $access = $rowAccess['Autorisation'];

        if ($nbAccess === 0 || $access === "0") {

            $hasAccess = "Non";
            $accessHref = "accessGrant.php?";
            $accessHref = $accessHref . "userId=" . $userId . "&reviewId=" . $reviewId . "&query=" . $query;
            $accessDesc = "Donner l'accès";
            $accessImage = generateImage("../png/add.png", $accessDesc, 24, 24);
        } else {

            $hasAccess = "Oui";
            $accessHref = "accessRevoke.php?";
            $accessHref = $accessHref . "userId=" . $userId . "&reviewId=" . $reviewId . "&query=" . $query;
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

require "helper.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$credentialsW = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$reviewId = filter_input(INPUT_GET, "reviewId");
$username = filter_input(INPUT_GET, "username");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $reviewName = getReviewName($reviewId);

        $style = file_get_contents("../html/userReviewsAdd.html");
        $style = str_replace("{reviewName}", $reviewName, $style);
        $style = str_replace("{reviewId}", $reviewId, $style);
        echo $style;

        echo "<table>";

        $cells = array("Utilisateur","A accès","Modifier accès");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        if (isset($username))
            userAccess($username, $reviewId);

    } else
        header("Location: index.php");

    $connection->close();
    $connectionR->close();
}

?>
