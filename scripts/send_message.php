<?php
include("includes.php");
include("session.php");

if (!isset($_SESSION['welcomeCode'])) {
    header("../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'])) {
    $auteur_id = $_SESSION['id'];
    $contenu = trim($_POST['contenu']);
    if ($contenu === '') {
        exit('Message vide');
    }

    // Récupérer le destinataire selon la relation
    if ($_SESSION['role'] === 'parrain') {
        $stmt = $connection->prepare("SELECT filleul_id AS destinataire_id FROM relations WHERE parrain_id = ?");
    } else {
        $stmt = $connection->prepare("SELECT parrain_id AS destinataire_id FROM relations WHERE filleul_id = ?");
    }
    $stmt->execute([$auteur_id]);
    $relation = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$relation) {
        exit('Pas de relation');
    }

    $destinataire_id = $relation['destinataire_id'];

    $insert = $connection->prepare("INSERT INTO messages (auteur_id, destinataire_id, contenu, date) VALUES (?, ?, ?, NOW())");
    $insert->execute([$auteur_id, $destinataire_id, $contenu]);

    echo 'ok';
    exit();
} 

