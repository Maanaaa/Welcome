<?php
// Connexion BDD
$host = 'db';
$dbname = 'welcome';
$user = 'welcomeuser';
$pass = 'M@ny@Th30430@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// Récupération du code
$code = $_GET['code'] ?? $_COOKIE['chat_code'] ?? null;
if (!$code) {
    die("Merci de fournir un code utilisateur dans l'URL, ex: ?code=CODEPARRAIN123");
}

// Stockage du code en cookie pour les prochaines fois
setcookie("chat_code", $code, time() + (86400 * 30), "/");

// Récupération utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE code = ?");
$stmt->execute([$code]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("Code utilisateur inconnu");
}

$user_id = $user['id'];
$user_role = $user['role'];

// Récupération de l'autre utilisateur
if ($user_role === 'parrain') {
    $stmt = $pdo->prepare("SELECT filleul_id AS autre_id FROM relations WHERE parrain_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT parrain_id AS autre_id FROM relations WHERE filleul_id = ?");
}
$stmt->execute([$user_id]);
$autre = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$autre) {
    die("Aucun lien trouvé entre ce parrain/filleul.");
}
$autre_id = $autre['autre_id'];

// Envoi de message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Requête invalide']);
        exit;
    }

    $message = trim($_POST['message'] ?? '');
    $fichierChemin = null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/medias/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $tmpName = $_FILES['file']['tmp_name'];
        $originalName = basename($_FILES['file']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $newName = uniqid('file_', true) . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'upload']);
            exit;
        }

        $fichierChemin = 'medias/' . $newName;
    }

    if ($message === '' && $fichierChemin === null) {
        echo json_encode(['success' => false, 'error' => 'Message et fichier vides']);
        exit;
    }

    $now = new DateTime();
    $stmt = $pdo->prepare("INSERT INTO messages (id_auteur, contenu, date_envoi, heure_envoi, fichier) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $message, $now->format('Y-m-d'), $now->format('H:i:s'), $fichierChemin]);

    echo json_encode(['success' => true]);
    exit;
}

// Récupération messages
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $stmt = $pdo->prepare("
        SELECT m.*, u.prenom, u.nom FROM messages m
        JOIN utilisateurs u ON m.id_auteur = u.id
        WHERE m.id_auteur IN (?, ?)
        ORDER BY date_envoi ASC, heure_envoi ASC
    ");
    $stmt->execute([$user_id, $autre_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Chat Parrain / Filleul</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 2em auto; background: #f9f9f9; }
        #chatbox { border: 1px solid #ccc; padding: 10px; height: 400px; overflow-y: scroll; background: white; }
        .msg { margin-bottom: 10px; padding: 5px 10px; border-radius: 10px; max-width: 70%; }
        .msg.me { background: #d2f8d2; margin-left: auto; }
        .msg.them { background: #eee; margin-right: auto; }
        .date { font-size: 0.75em; color: #666; text-align: center; margin: 10px 0; }
        img.attachment { max-width: 150px; max-height: 150px; margin-top: 5px; border-radius: 5px; }
    </style>
</head>
<body>

<h2>Chat entre <?=htmlspecialchars($user['prenom'])?> (<?=htmlspecialchars($user_role)?>) et son <?= $user_role==='parrain' ? 'filleul' : 'parrain' ?></h2>

<div id="chatbox"></div>

<form id="chatForm" enctype="multipart/form-data">
    <input type="text" id="messageInput" name="message" placeholder="Écrire un message..." autocomplete="off" style="width:70%;" />
    <input type="file" id="fileInput" name="file" style="width:25%;" />
    <button type="submit">Envoyer</button>
</form>

<script>
const chatbox = document.getElementById('chatbox');
const form = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const fileInput = document.getElementById('fileInput');
const userId = <?=json_encode($user_id)?>;
let lastMessageIds = new Set();

Notification.requestPermission().then(permission => {
    console.log("Notification permission:", permission);
});

function notifyUser(msg) {
    if (document.hidden && Notification.permission === "granted") {
        const title = "Nouveau message de " + msg.prenom;
        const body = msg.contenu ? msg.contenu.slice(0, 100) : "Fichier joint";
        new Notification(title, {
            body,
            icon: "/favicon.ico"
        });
    }
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function fetchMessages() {
    fetch('?code=<?=urlencode($code)?>&action=fetch')
        .then(res => res.json())
        .then(data => {
            const scrollBottomBefore = chatbox.scrollTop + chatbox.clientHeight >= chatbox.scrollHeight - 20;
            const currentMsg = messageInput.value;
            const currentFile = fileInput.value;

            chatbox.innerHTML = '';
            let currentDate = null;
            const newIds = new Set();

            data.forEach(msg => {
                newIds.add(msg.id);

                if (!lastMessageIds.has(msg.id) && msg.id_auteur != userId) {
                    notifyUser(msg);
                }

                if (msg.date_envoi !== currentDate) {
                    const dateDiv = document.createElement('div');
                    dateDiv.className = 'date';
                    dateDiv.textContent = formatDate(msg.date_envoi);
                    chatbox.appendChild(dateDiv);
                    currentDate = msg.date_envoi;
                }

                const div = document.createElement('div');
                div.className = 'msg ' + (msg.id_auteur == userId ? 'me' : 'them');

                let contenuHtml = `<strong>${escapeHtml(msg.prenom)} :</strong> ${escapeHtml(msg.contenu).replace(/\n/g, '<br>')}`;

                if (msg.fichier) {
                    const ext = msg.fichier.split('.').pop().toLowerCase();
                    const imgExt = ['jpg','jpeg','png','gif'];
                    if (imgExt.includes(ext)) {
                        contenuHtml += `<br><img class="attachment" src="${escapeHtml(msg.fichier)}" alt="Image jointe" />`;
                    } else {
                        contenuHtml += `<br><a href="${escapeHtml(msg.fichier)}" target="_blank">Fichier joint</a>`;
                    }
                }

                div.innerHTML = contenuHtml;
                chatbox.appendChild(div);
            });

            lastMessageIds = newIds;

            if (scrollBottomBefore) {
                chatbox.scrollTop = chatbox.scrollHeight;
            }

            messageInput.value = currentMsg;
            fileInput.value = currentFile;
        });
}

setInterval(fetchMessages, 2000);
fetchMessages();

form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch('?code=<?=urlencode($code)?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            messageInput.value = '';
            fileInput.value = '';
            fetchMessages();
        } else {
            alert('Erreur : ' + resp.error);
        }
    });
});
</script>

</body>
</html>
