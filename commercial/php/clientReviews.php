<?php

function clientReviews($clientId, $published)
{
    $columns = "Revue_id,Gerant_id";
    $sqlReviews = "SELECT $columns FROM webcommercial_client_revue WHERE Client_id='$clientId';";
    $rowsReviews = querySQL($sqlReviews, $GLOBALS['connection']);

    foreach ($rowsReviews as $rowReview) {

        $reviewId = $rowReview['id'];

        $columns = "Nom,Paru,Annee,DateCreation";
        if ($published == 0)
            $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE id='$reviewId' AND Paru='0' ORDER BY DateCreation DESC;";
        else
            $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE id='$reviewId' ORDER BY DateCreation DESC;";
        $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);

        $reviewName = $rowReview['Nom'];
        $published = ($rowReview['Paru'] == 1 ? "Oui" : "Non");
        $reviewYear = $rowReview['Annee'];
        $createdAtYMD = $rowReview['DateCreation'];
        $createdAt = date("d/m/Y", strtotime($createdAtYMD));
        $reviewTitle = $reviewName . " " . $reviewYear;

        $cells = array($reviewTitle, $published, $createdAt);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;

    }
}

require_once "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$clientId = filter_input(INPUT_GET, "clientId");
$pub = filter_input(INPUT_GET, "pub");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $clientName = getClientName($clientId);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Revues incluant $clientName", $style);
        echo $style;

        echo "<h2>Revues incluant $clientName</h2>";
        echo "<table>";

        $published = ($pub == 1 ? 0 : 1);
        $href = "clientReviews.php?clientId=" . $clientId . "&pub=" . $published;
        $text = ($pub == 1 ? "Afficher revues non-parues" : "Afficher toutes les revues");
        $link = generateLink($href, $text, "_self");
        echo $link;

        $cells = array("Nom","Parue","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        clientReviews($clientId, $pub);

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
}


?>
