<?php
require_once __DIR__ . '/../../Controller/RecetteController.php';
require_once __DIR__ . '/../../config.php';

$rc = new RecetteController();
$recettes = $rc->listRecettes();

$debug = [];
if (empty($recettes)) {
    try {
        $db = config::getConnexion();
        $cntRow = $db->query("SELECT COUNT(*) AS cnt FROM recette")->fetch();
        $debug['count'] = $cntRow ? (int)$cntRow['cnt'] : 0;
        $desc = $db->query("DESCRIBE recette")->fetchAll();
        $cols = [];
        foreach ($desc as $c) { $cols[] = $c['Field']; }
        $debug['columns'] = $cols;
    } catch (Exception $e) {
        $debug['error'] = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recettes - Table</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/window_controls.php'; ?>
    <div class="container">
        <h1>Recettes</h1>
        <?php if (!empty($recettes)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Temps (prépa / cuisson)</th>
                        <th>Portions</th>
                        <th>Difficulté</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recettes as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['id_recette'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['nom'] ?? $r['titre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(mb_strimwidth($r['description'] ?? '', 0, 120, '...')); ?></td>
                        <td><?php echo htmlspecialchars(($r['temp_preparation'] ?? '') . ' / ' . ($r['temp_cuisson'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($r['nombre_portion'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['difficulte'] ?? ($r['difficulté'] ?? '')); ?></td>
                        <td>
                            <?php if (!empty($r['image'])): ?>
                                <img src="<?php echo htmlspecialchars($r['image']); ?>" alt="" style="max-width:120px; max-height:80px;" />
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune recette trouvée.</p>
        <?php endif; ?>
    </div>
</body>
</html>
