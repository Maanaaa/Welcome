<?php
include("../scripts/includes.php");
include("../scripts/session.php");

$code = $_POST["welcomeCode"] ?? '';

$requete = 'SELECT id, role, prenom, nom FROM utilisateurs WHERE code = :welcomeCode';
$resultats = $connection->prepare($requete);
$resultats->bindParam(':welcomeCode', $code, PDO::PARAM_STR);
$resultats->execute();

$utilisateur = $resultats->fetch(PDO::FETCH_ASSOC);
$resultats->closeCursor();

if ($utilisateur) {
    $cookieLifetime = 30 * 24 * 60 * 60;

    session_set_cookie_params([
        'lifetime' => $cookieLifetime,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // session_start() est déjà appelé dans session.php

    $_SESSION['welcomeCode'] = $code;
    $_SESSION['role'] = $utilisateur['role'];
    $_SESSION['id'] = $utilisateur['id'];

    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Redirection...</title>
    </head>
    <body>
        <form action="../chat/index.php" method="post" id="hiddenForm">
            <input type="hidden" name="code" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>" />
        </form>
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
