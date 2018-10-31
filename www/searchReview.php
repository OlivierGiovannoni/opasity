<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewName = filter_input(INPUT_POST, "reviewName"); // NOM REVUE ex: Ann Mines
$getPaid = filter_input(INPUT_POST, "paidBool");
$darkBool = filter_input(INPUT_POST, "darkBool");

$reviewName = testInput($reviewName);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function findReview()
{
    $reviewName = $GLOBALS['reviewName'];
    $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%';";
    if ($resultReview = $GLOBALS['connection']->query($sqlReview)) {

        while ($rowReview = mysqli_fetch_array($resultReview)) {

            $currReviewName = $rowReview['Nom'];
            $currReviewId = $rowReview['id'];
            $currReviewYear = $rowReview['Annee'];
            $curr = array('Name' => $currReviewName, 'Id' => $currReviewId, 'Year' => $currReviewYear);

            $reviewForm = "<form action=\"reviewOrders.php\" method=\"post\">";
            $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
            $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $curr['Id'] . "\">";
            $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $curr['Name'] . ' ' . $curr['Year'] . "\">";
            $closeForm = "</form>";
            echo "<tr><td>" . $reviewForm . $darkBool . $getPaidOrders . $reviewHidden . $reviewInput . $closeForm . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlReview ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die("Connection error. Code: ". mysqli_connect_errno() ." Reason: " . mysqli_connect_error());
} else {
    $style = file_get_contents("search.html");

    if ($darkBool == "true")
        $style = str_replace("searchLight.css", "searchDark.css", $style);

    $style = str_replace("{type}", "revue", $style);
    $style = str_replace("{query}", $reviewName, $style);

    echo $style;
    echo "<i><h1>Revues trouvées:</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Revue</th>";
    echo "</tr>";

    if (mysqli_set_charset($connection, "utf8") === TRUE)
        findReview();
    else
        die("MySQL SET CHARSET error: ". $connection->error);

    echo "</table>";
    echo "</html>";
}

?>
