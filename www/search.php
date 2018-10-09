<?php

$namesArr = array();
$valuesArr = array();

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

foreach ($_POST as $name => $value) {

    $value = testInput($value);
    array_push($namesArr, $name);
    if (!empty($value)) {
        array_push($valuesArr, $value);
    } else {
        array_push($valuesArr, "NULL");
    }
}

$allNames = implode(", ", $namesArr);
$allValues = implode("','", $valuesArr);

$support = $valuesArr[0];
$numParution = $valuesArr[1];
$numContrat = $valuesArr[2];

echo $allNames . "<br>";
echo $allValues . "<br>";
echo "<br>";

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error ('. mysqli_connect_errno() .') ' . mysqli_connect_error());
} else {
    //$sql = "INSERT INTO users ($allNames) VALUES ('$allValues')";
    $sql = "SELECT Commande FROM webcontrat_contrat;";
    //echo $sql . "<br>";
    if ($result = $conn->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $retSociete = substr_compare($row['Commande'], $codeSociete, 0, 2, TRUE);
            $retSupport = substr_compare($row['Commande'], $support, 2, 2, TRUE);
            $retParution = substr_compare($row['Commande'], $numParution, 4, 6, TRUE);
            $retContrat = substr_compare($row['Commande'], $numContrat, 10, 4, TRUE);

            if (!$retSociete || !$retSupport || !$retParution || !$retContrat)
                echo "matching: <a href=\"#\">" . $row['Commande'] . "</a><br>";

            //echo $codeSociete . $support . $numParution . $numContrat . "<br>";
            //echo "raw: " . $row['Commande'] . "<br>";
        }
        //$cmdArr = array();
        /* if ($codeSociete != "NULL") { */


        /* } */
        /* else if ($support != "NULL") */
        /*     ; */
        /* else if ($numParution != "NULL") */
        /*     ; */
        /* else if ($numContrat != "NULL") */
        /*     ; */
        /* echo "New record is inserted sucessfully" . "<br>"; */
    } else {
        echo "Error: ". $sql ." // ". $conn->error;
    }
    $conn->close();
}

?>
