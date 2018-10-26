<?php

$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$paidStr = filter_input(INPUT_POST, "hiddenPaid");
$comment = filter_input(INPUT_POST, "comment");
$darkBool = filter_input(INPUT_POST, "darkBool");
$noComments = ($comment == "Nouveau commentaire" ? TRUE : FALSE);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function generateInput($htmlFileName)
{
    $htmlFileData = file_get_contents($htmlFileName);
    echo $htmlFileData;
}

function listComments()
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $sqlComment = "SELECT Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Payee FROM webcontrat_commentaire WHERE Commande_courte='$orderIdShort' ORDER BY Commentaire_id DESC;";
    if ($result = $GLOBALS['connection']->query($sqlComment)) {

        while ($rowComment = mysqli_fetch_array($result)) {

            echo "<tr><td>" . $rowComment['Commentaire'] . "</td>";
            echo "<td>" . $rowComment['Auteur'] . "</td>";
            echo "<td>" . $rowComment['Date'] . "</td>";
            $mailHref = "<a id=\"tableSub\" href=\"mailto:" . $rowComment['AdresseMail'] . "\">" . $rowComment['AdresseMail'] . "</a>";
            echo "<td>" . $mailHref . "</td>";
            echo "<td>" . $rowComment['NumTelephone'] . "</td>";
            echo "<td>" . $rowComment['Prochaine_relance'] . "</td>";
            echo "<td>" . $rowComment['Payee'] . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("allComments.html");

if ($darkBool == "true")
    $style = str_replace("commentLight.css", "commentDark.css", $style);

$style = str_replace("{order}", $orderIdShort, $style);

echo $style;
echo "<i><h1>Commentaires de " . $orderIdShort . "</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Commentaire</th>";
echo "<th>Auteur</th>";
echo "<th>Date commentaire</th>";
echo "<th>E-mail</th>";
echo "<th>Téléphone</th>";
echo "<th>Prochaine relance</th>";
echo "<th>Payé</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    listComments();
    if ($paidStr == "")
        generateInput("addComment.html");
}

echo "</table>";
echo "</html>";

?>
