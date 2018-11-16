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
            $curr = array('Name' => $currReviewName, 'Id' => $currReviewId, 'Year' => $currReviewYear);

            $reviewForm = "<form target=\"_blank\" action=\"reviewOrders.php\" method=\"post\">";
            $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
            $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $curr['Id'] . "\">";
            $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $curr['Name'] . ' ' . $curr['Year'] . "\">";
            $closeForm = "</form>";

            $newDate = date("d/m/Y", strtotime($rowReview['DateCreation']));
            echo "<tr><td>" . $reviewForm . $darkBool . $getPaidOrders . $reviewHidden . $reviewInput . $closeForm . "</td>";
            if ($rowReview['Paru'] == 1)
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

    if ($darkBool == "true") {
        $style = str_replace("searchLight.css", "searchDark.css", $style);
        $style = str_replace("homeLight.css", "homeDark.css", $style);
    }

    $style = str_replace("{type}", "revue", $style);
    $style = str_replace("{query}", $reviewName, $style);

    echo $style;
    echo "<i><h1>Revues trouvées:</h1></i>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Revue</th>";
    echo "<th>Paru</th>";
    echo "<th>Date création</th>";
    echo "</tr>";

    if (mysqli_set_charset($connection, "utf8") === TRUE)
        findReview();
    else
        die("MySQL SET CHARSET error: ". $connection->error);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
