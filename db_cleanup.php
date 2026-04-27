<?php
$dsn = 'mysql:host=127.0.0.1;dbname=feane_blog;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supprimer d'abord les commentaires car ils sont probablement liés par une clé étrangère
    $pdo->exec("DELETE FROM commentaires");
    echo "Commentaires supprimés.\n";

    // Supprimer tous les articles
    $pdo->exec("DELETE FROM articles");
    echo "Articles supprimés.\n";

    // Réinitialiser les auto-incréments pour repartir de 1
    $pdo->exec("ALTER TABLE articles AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE commentaires AUTO_INCREMENT = 1");
    echo "Auto-incréments réinitialisés.\n";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
