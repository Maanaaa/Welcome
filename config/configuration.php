<?php
require_once __DIR__ . '../includes/load_env.php';
loadEnv(__DIR__ . '../.env');

$serverAdress = $_SERVER['SERVER_ADDR'];

// Connexion à la db Docker
if ($serverAdress != "46.202.172.172") {
    $host = 'db';
    $dbname = getenv('DEV_DB_NAME');
    $user = getenv('DEV_DB_USER');
    $pass = getenv('DEV_DB_PASSWORD');
}
// Connexion à Hostinger
else {
    $host = '127.0.0.1';
    $dbname = getenv('HOST_DB_NAME');
    $user = getenv('HOST_DB_USER');
    $pass = getenv('HOST_DB_PASSWORD');
}

// Cnnexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Bien connecté
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
