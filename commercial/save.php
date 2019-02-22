<?php

$ids = "94,972,113,7";

$arrids = explode(",", $ids);

foreach ($arrids as $arrid)
    echo "hey: $arrid \n";

$newids = $ids . "," . 91;

echo $newids;

?>
