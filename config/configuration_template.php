<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/load_env.php');
loadEnv($_SERVER['DOCUMENT_ROOT'] . '/.env');

$serverAdress = $_SERVER['SERVER_ADDR'];

// Connexion à la db Docker
if ($serverAdress != "46.202.172.172") {
    $host = 'db';
    $dbname = getenv('DEV_DB_NAME');
    $user = getenv('DEV_DB_USER');
    $pass = getenv('DEV_DB_PASSWORD');
    echo $host;
    echo $dbname;
    echo $user;
    echo $pass;
}
// Connexion à Hostinger
else {
    $host = '127.0.0.1';
    $dbname = getenv('HOST_DB_NAME');
    $user = getenv('HOST_DB_USER');
    $pass = getenv('HOST_DB_PASSWORD');
}

