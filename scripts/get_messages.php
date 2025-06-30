<?php
include("includes.php");
include("session.php");

if (!isset($_SESSION['welcomeCode'])) {
    header("../index.php");
    exit('Non autorisé');
}

$utilisateur_id = $_SESSION['id'];
$role_utilisateur = $_SESSION['role'];

// Trouver l'autre personne (filleul / parrain)
if ($role_utilisateur === 'parrain') {
    $stmt = $connection->prepare("SELECT filleul_id AS autre_id FROM relations WHERE parrain_id = ?");
} else {
    $stmt = $connection->prepare("SELECT parrain_id AS autre_id FROM relations WHERE filleul_id = ?");
}
$stmt->execute([$utilisateur_id]);
$autre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$autre) {
    header("../index.php");
    exit('Relation non trouvée');
}

$autre_id = $autre['autre_id'];

// Récupérer les messages entre les deux
$stmt = $connection->prepare("
    SELECT m.*, u.role AS emetteur_role, m.auteur_id AS emetteur_id
    FROM messages m
    JOIN utilisateurs u ON m.auteur_id = u.id
    WHERE (m.auteur_id = :utilisateur_id AND m.destinataire_id = :autre_id)
       OR (m.auteur_id = :autre_id AND m.destinataire_id = :utilisateur_id)
    ORDER BY m.date ASC
");


$stmt->execute([
    'utilisateur_id' => $utilisateur_id,
    'autre_id' => $autre_id
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
