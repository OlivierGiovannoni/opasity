<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewName = filter_input(INPUT_POST, "reviewName"); // NOM REVUE ex: Ann Mines
$getPaid = filter_input(INPUT_POST, "paidBool");

$reviewName = testInput($reviewName);

function credsArr($credsStr)
{
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

$credsFile = "../credentials.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB

function findReview()
{
    $reviewName = $GLOBALS['reviewName'];
    $sqlReview = "SELECT id,Nom,Annee,DateCreation,Paru FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%' ORDER BY DateCreation DESC;";
    if ($resultReview = $GLOBALS['connection']->query($sqlReview)) {

        while ($rowReview = mysqli_fetch_array($resultReview)) {

            $currReviewName = $rowReview['Nom'];
            $currReviewId = $rowReview['id'];
            $currReviewYear = $rowReview['Annee'];
            $published = $rowReview['Paru'];
            $curr = array('Name' => $currReviewName, 'Id' => $currReviewId, 'Year' => $currReviewYear);

            $reviewForm = "<form target=\"_blank\" action=\"searchReviewOrders.php\" method=\"post\">";
            $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
            $pubHidden = "<input type=\"hidden\" name=\"published\" value=\"" . $published . "\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $curr['Id'] . "\">";
            $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $curr['Name'] . ' ' . $curr['Year'] . "\">";
            $closeForm = "</form>";

            echo "<tr><td>" . $reviewForm . $getPaidOrders . $pubHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";

            $newDate = date("d/m/Y", strtotime($rowReview['DateCreation']));
            if ($published == 1)
                echo "<td id=\"isPaid\">Oui</td>";
            else
                echo "<td id=\"isNotPaid\">Non</td>";
            echo "<td>" . $newDate . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlReview ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die("Connection error. Code: ". mysqli_connect_errno() ." Reason: " . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    $style = str_replace("{type}", "revue", $style);
    $style = str_replace("{query}", $reviewName, $style);

    echo $style;
    echo "<h1>Revues trouvées:</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Revue</th>";
    echo "<th>Paru</th>";
    echo "<th>Date création</th>";
    echo "</tr>";

    $charset = mysqli_set_charset($connection, "utf8");

    if ($charset === FALSE)
        die("MySQL SET CHARSET error: ". $connection->error);

    findReview();

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
