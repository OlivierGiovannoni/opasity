<?php

function findReviews($reviewName)
{
    $columns = "id,Nom,Annee,DateCreation,Paru";
    $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%' ORDER BY DateCreation DESC;";
    $rowsReview = querySQL($sqlReview, $GLOBALS['connectionR']);

    foreach ($rowsReview as $rowReview) {

        $reviewId = $rowReview['id'];
        $reviewName = $rowReview['Nom'] . " " . $rowReview['Annee'];
        $published = ($rowReview['Paru'] == 1 ? "Oui" : "Non");
        $created = date("d/m/Y", strtotime($rowReview['DateCreation']));

        $reviewLink = generateLink("searchReviewOrders.php?id=" . $reviewId, $reviewName);

        $cells = array($reviewLink, $published, $created);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$reviewName = filter_input(INPUT_GET, "reviewName"); // NOM REVUE ex: Ann Mines
$reviewName = sanitizeInput($reviewName);

if (mysqli_connect_error()) {
    die("Connection error. Code: ". mysqli_connect_errno() ." Reason: " . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "revue", $style);
        $style = str_replace("{query}", $reviewName, $style);

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Revues trouvées:</h1>";
        echo "<table>";

        $cells = array("Revue","Parue","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);

        findReviews($reviewName);

        echo "</table><br><br><br>";
        echo "</html>";

    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
}

?>
