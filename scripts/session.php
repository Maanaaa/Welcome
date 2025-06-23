<?php
// Durée de vie du cookie : 30 jours
$cookieLifetime = 30 * 24 * 60 * 60;

session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
