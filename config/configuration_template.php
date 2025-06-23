<?php
$serverAdress = $_SERVER['SERVER_ADDR'];

// Connexion à la db Docker
if ($serverAdress != "IP serveur 46....") {
    $host = 'db';
    $dbname = "";
    $user = "";
    $pass = "";
}
// Connexion à Hostinger
else {
    $host = '127.0.0.1';
    $dbname = "";
    $user = "";
    $pass = "";
}

