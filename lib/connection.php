<?php

try {
    $dbh = new PDO('mysql:host=localhost;dbname=github_api', "root","");
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

//echo "\n".$mysqli->host_info . "\n";