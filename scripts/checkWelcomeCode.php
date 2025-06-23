<?php
include("../scripts/includes.php");

$code = $_POST["welcomeCode"] ?? '';
// Requête de vérification du code
$requete = 'SELECT id, role, prenom, nom FROM utilisateurs WHERE code = :welcomeCode';
$resultats = $connection->prepare($requete);
$resultats->bindParam(':welcomeCode', $code, PDO::PARAM_STR);
$resultats->execute();

$utilisateur = $resultats->fetch(PDO::FETCH_ASSOC);
$resultats->closeCursor();

if ($utilisateur) {
    // Création du cookie
    // Durée du cookie : 30 jours (en s)
    $cookieLifetime = 30 * 24 * 60 * 60;

    session_set_cookie_params([
        'lifetime' => $cookieLifetime,
        'path' => '/',
        'secure' => true,   
        'httponly' => true, 
        'samesite' => 'Lax' 
    ]);

    session_start();

    $_SESSION['welcomeCode'] = $code; // Save le welcomeCode en session
    $_SESSION['role'] = $utilisateur['role'];
    $_SESSION['id'] = $utilisateur['id'];

    // Form caché
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Redirection...</title>
    </head>
    <body>
        <form action="../chat" method="post" id="hiddenForm">
            <input type="hidden" name="code" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>" />
        </form>
        <!---- Soumettre le form avec le champ caché sans bouton --->
        <script>
          document.getElementById('hiddenForm').submit();
        </script>
    </body>
    </html>
    <?php
    exit;
} else {
    echo "Code utilisateur invalide.";
}