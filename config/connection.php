<?php
// Cnnexion PDO
try {
    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Bien connecté
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>