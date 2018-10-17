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
    $sql = "SELECT id,Nom FROM webcontrat_revue WHERE Nom LIKE '%$reviewName%';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $currReviewName = $row['Nom'];
            $currReviewId = $row['id'];
            $curr = array('Name' => $currReviewName, 'Id' => $currReviewId);
            if (strpos($currReviewName, $GLOBALS['reviewName']) !== FALSE) {
                $reviewForm = "<form action=\"reviewOrders.php\" method=\"post\">";
                $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
                $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $curr['Id'] . "\">";
                $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $curr['Name'] . "\">";
                $closeForm = "</form>";
                echo "<tr><td>" . $reviewForm . $darkBool . $reviewHidden . $reviewInput . $closeForm . "</td></tr>";
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("search.html");

if ($darkBool)
    $style = str_replace("search.css", "dark.css", $style);

echo $style;
echo "<i><h1>Revues trouvées:</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Revue</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findReview();
}

echo "</table>";
echo "</html>";

?>
