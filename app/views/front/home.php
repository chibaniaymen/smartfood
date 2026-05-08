<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Bienvenue sur SmartFood</h1>
    <p>Front office MVC prêt à l'emploi avec connexion, déconnexion et consultation des produits.</p>
    <?php if (isLoggedIn()): ?>
        <div class="sf-alert success">Vous êtes connecté en tant que <?php echo htmlspecialchars($_SESSION['user']['name']); ?>.</div>
    <?php else: ?>
        <p><a href="index.php?action=front_login" class="sf-btn">Connexion Front Office</a></p>
    <?php endif; ?>
</div>
<div class="sf-card">
    <h2>Liste des produits</h2>
    <table>
        <thead>
            <tr><th>Produit</th><th>Restaurant</th><th>Catégorie</th><th>Prix</th></tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['restaurant_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['display_category']); ?></td>
                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
