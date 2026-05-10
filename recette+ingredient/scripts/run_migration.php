<?php
require_once __DIR__ . '/../config.php';

$sqlFile = __DIR__ . '/../sql/create_recette_ingredient.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
$db = config::getConnexion();

try {
    // Split statements conservatively by semicolon followed by newline or end
    $parts = preg_split('/;\s*\r?\n/', $sql);
    if (!$parts || count($parts) === 0) {
        $parts = array_filter(array_map('trim', explode(';', $sql)));
    }

    $executed = 0;
    foreach ($parts as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try {
            $db->exec($stmt);
            $executed++;
            echo "Executed: " . (strlen($stmt) > 120 ? substr($stmt,0,120).'...' : $stmt) . "\n";
        } catch (PDOException $e) {
            // continue on error but report
            echo "Statement error: " . $e->getMessage() . "\n";
        }
    }

    // Verify tables
    $recetteExists = (bool)$db->query("SHOW TABLES LIKE 'recette'")->fetch();
    $ingredientExists = (bool)$db->query("SHOW TABLES LIKE 'ingredient'")->fetch();

    echo "Summary: executed $executed statements\n";
    echo "recette table exists: " . ($recetteExists ? 'yes' : 'no') . "\n";
    echo "ingredient table exists: " . ($ingredientExists ? 'yes' : 'no') . "\n";

    exit(0);
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
