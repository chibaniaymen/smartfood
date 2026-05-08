<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Connexion Back Office</h1>
    <p>Accédez au panneau d'administration SmartFood.</p>
    <?php if (!empty($error)): ?>
        <div class="sf-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?action=admin_login">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="sf-btn">Se connecter</button>
    </form>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
