<?php

function findReviews($reviewName, $published)
{
    $columns = "id,Nom,Annee,DateCreation,Paru";
    if ($published == 0)
        $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%' AND Paru='0' ORDER BY DateCreation DESC;";
    else
        $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%' ORDER BY DateCreation DESC;";
    $rowsReview = querySQL($sqlReview, $GLOBALS['connectionR']);

    foreach ($rowsReview as $rowReview) {

        $reviewId = $rowReview['id'];
        $reviewName = $rowReview['Nom'] . " " . $rowReview['Annee'];
        $published = ($rowReview['Paru'] == 1 ? "Oui" : "Non");
        $created = date("d/m/Y", strtotime($rowReview['DateCreation']));

        $reviewLink = generateLink("userReviewsAdd.php?reviewId=" . $reviewId, $reviewName);

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
$pub = filter_input(INPUT_GET, "pub");

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
            $adminLink = generateLink("../admin.html", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Revues trouvées:</h1>";
        echo "<table>";

        $published = ($pub == 1 ? 0 : 1);
        $href = "reviewSearch.php?reviewName=" . $reviewName . "&pub=" . $published;
        $text = ($pub == 1 ? "Afficher revues non-parues" : "Afficher toutes les revues");
        $link = generateLink($href, $text, "_self");
        echo $link;

        $cells = array("Revue","Parue","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);

        findReviews($reviewName, $pub);

        echo "</table><br><br><br>";
        echo "</html>";

    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
}

?>
