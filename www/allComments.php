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

function generateInput($htmlFileName, $orderId, $orderIdShort)
{
    $htmlFileData = file_get_contents($htmlFileName);
    $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
    $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
    echo $htmlFileData;
}

function getContactName($orderId)
{
    $sqlContactId = "SELECT Client_id FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultContactId = $GLOBALS['connection']->query($sqlContactId)) {

        $rowContactId = mysqli_fetch_array($resultContactId);
        $contactId = $rowContactId['Client_id'];
        $sqlContactName = "SELECT NomContact1 FROM webcontrat_client WHERE id='$contactId';";
        if ($resultContactName = $GLOBALS['connection']->query($sqlContactName)) {

            $rowContactName = mysqli_fetch_array($resultContactName);
            return ($rowContactName['NomContact1']);
        } else {
            echo "Query error: ". $sqlContactName ." // ". $GLOBALS['connection']->error;
        }
    } else {
        echo "Query error: ". $sqlContactId ." // ". $GLOBALS['connection']->error;
    }
}


function listComments()
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $sqlComment = "SELECT Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Payee FROM webcontrat_commentaire WHERE Commande_courte='$orderIdShort' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        while ($rowComment = mysqli_fetch_array($resultComment)) {

            $contactName = getContactName($GLOBALS['orderId']);
            echo "<tr><td>" . $rowComment['Commentaire'] . "</td>";
            echo "<td>" . $rowComment['Auteur'] . "</td>";
            echo "<td>" . $rowComment['Date'] . "</td>";
            echo "<td>" . $contactName . "</td>";
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

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("allComments.html");

    if ($darkBool == "true")
        $style = str_replace("commentLight.css", "commentDark.css", $style);

    $style = str_replace("{order}", $orderIdShort, $style);

    echo $style;
    echo "<i><h1>Fiche: " . $orderIdShort . "</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Commentaire</th>";
    echo "<th>Auteur</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>E-mail</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Prochaine relance</th>";
    echo "<th>Payé</th>";
    echo "</tr>";

    listComments();
    if ($paidStr == "")
        generateInput("addComment.html", $orderId, $orderIdShort);

    echo "</table>";
    echo "</html>";
}

?>
