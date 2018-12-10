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

function sqlQuery($query)
{
    $sqlPaid = "SELECT Reglement FROM $table WHERE Commande LIKE '$orderId';";
    if ($resultPaid = $GLOBALS[$connection]->query($sqlPaid)) {

        $rowPaid = mysqli_fetch_array($resultPaid);
        return ($rowPaid['Reglement']);
    } else {
        echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionR']->error;
    }

}

function generateForm()
{

}

function generateTable()
{

}

function getData($table, $columns, $connection, $whereColumn, $whereOperator, $whereValue)
{
    $sqlPaid = "SELECT $columns FROM $table WHERE Commande LIKE '$orderId';";
    if ($resultData = $GLOBALS[$connection]->query($sqlPaid)) {

        $rowPaid = mysqli_fetch_all($resultPaid);
        return ($rowPaid['Reglement']);
    } else {
        echo "MySQL query error.<br>Query: ". $sqlData . "<br>Error: ". $connection->error . "<br>";
        return (NULL);
    }
}