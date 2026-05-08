<?php

/**
 * Configuration globale et fonctions utiles
 * 
 * @package blogMVC
 */

// Configuration PDO
$dsn = 'mysql:host=127.0.0.1;dbname=feane_events;charset=utf8mb4';
$dbUser = 'root';
$dbPassword = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('<h1>Erreur de connexion à la base de données</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}

/**
 * Échappe une chaîne pour éviter les failles XSS
 * 
 * @param mixed $value Valeur à échapper
 * @return string Chaîne échappée
 */
function h($value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 * 
 * @param string $url URL de redirection
 * @return void
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Vérifie si une chaîne est un nombre entier positif
 * 
 * @param mixed $value Valeur à vérifier
 * @return bool True si c'est un entier positif
 */
function isValidId($value): bool
{
    return ctype_digit((string)$value) && (int)$value > 0;
}

/**
 * Valide une adresse email
 * 
 * @param string $email Email à valider
 * @return bool True si l'email est valide
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
