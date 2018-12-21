<?php

if (isset($_COOKIE['text1']))
    foreach ($_COOKIE as $name => $value) {
        
        echo "name => $name AND value => $value <br>";
    }
else
    die("NOON");

?>
