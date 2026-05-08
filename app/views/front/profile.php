<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Mon profil</h1>
    <?php if (!empty($message)): ?>
        <div class="sf-alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="sf-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?action=front_profile">
        <label for="name">Nom</label>
        <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="password">Nouveau mot de passe <small>(laisser vide pour conserver)</small></label>
        <input id="password" name="password" type="password">

        <button type="submit" class="sf-btn">Mettre à jour</button>
    </form>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
