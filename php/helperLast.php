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

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

function getAllOrders()
{
    $sqlOrders = "SELECT Commande_courte FROM webcontrat_commentaire;";
    if ($resultOrders = $GLOBALS['connectionW']->query($sqlOrders)) {

        $orders = array();
        while ($rowOrders = mysqli_fetch_array($resultOrders)) {

            $check = in_array($rowOrders['Commande_courte'], $orders);
            if ($check === FALSE)
                array_push($orders, $rowOrders['Commande_courte']);
            else
                continue ;
        }
        return ($orders);
    } else {
        echo "Query error: ". $sqlOrders ." // ". $GLOBALS['connectionW']->error;
    }    
}

function changeLast()
{
    $orders = getAllOrders();
    //foreach yo
    $sqlHelper = "select order by desc";
    if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

        // UPDATE output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
    }
}

mysqli_set_charset($connectionW, "utf8");
changeLast();
unlink(__FILE__);

?>
