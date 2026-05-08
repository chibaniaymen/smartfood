<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Gestion des produits</h1>
    <p><a class="sf-btn" href="index.php?action=admin_product_form">Ajouter un produit</a></p>
    <table>
        <thead>
            <tr><th>ID</th><th>Produit</th><th>Restaurant</th><th>Prix</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($products as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                    <td><?php echo number_format($item['price'], 2, ',', ' '); ?> €</td>
                    <td>
                        <a href="index.php?action=admin_product_form&id=<?php echo $item['id']; ?>">Modifier</a>
                        |
                        <a href="index.php?action=admin_product_delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
