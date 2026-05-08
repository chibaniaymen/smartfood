<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Dashboard Back Office</h1>
    <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>. Gérez les utilisateurs et les produits de SmartFood.</p>
    <p>
        <a class="sf-btn" href="index.php?action=admin_users">Utilisateurs</a>
        <a class="sf-btn sf-btn-alt" href="index.php?action=admin_products">Produits</a>
    </p>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
