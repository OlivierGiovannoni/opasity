<?php

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

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']);

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

function getAllOrders()
{
    $sqlOrders = "SELECT Commande FROM webcontrat_commentaire;";
    if ($resultOrders = $GLOBALS['connectionW']->query($sqlOrders)) {

        $orders = array();
        while ($rowOrders = mysqli_fetch_array($resultOrders)) {

            $check = in_array($rowOrders['Commande'], $orders);
            if ($check === FALSE)
                array_push($orders, $rowOrders['Commande']);
            else
                continue ;
        }
        return ($orders);
    } else {
        echo "Query error: ". $sqlOrders ." // ". $GLOBALS['connectionW']->error;
    }
    return (NULL);
}

function updater($val, $id)
{
    $sqlUpdate = "UPDATE webcontrat_commentaire SET Reglement='$val' WHERE Commande='$id';";
    if ($resultUpdate = $GLOBALS['connectionW']->query($sqlUpdate)) {

        //UPDATE ain't need no fetcha boi bb
    } else {
        echo "Query error: ". $sqlUpdate ." // ". $GLOBALS['connectionW']->error;
    }

}

function changePaid()
{
    $orders = getAllOrders();
    foreach ($orders as $order) {

        $sqlHelper = "SELECT Reglement FROM webcontrat_contrat WHERE Commande='$order';";
        if ($resultHelper = $GLOBALS['connectionR']->query($sqlHelper)) {

            while ($rowHelper = mysqli_fetch_array($resultHelper)) {

                if ($rowHelper['Reglement'] == "R")
                    updater($rowHelper['Reglement'], $order);
            }
        } else {
            echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
        }
    }
}

mysqli_set_charset($connectionW, "utf8");
changePaid();
//unlink(__FILE__);
$connectionR->close();
$connectionW->close();

?>
