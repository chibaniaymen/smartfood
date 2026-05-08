<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1><?php echo $user ? 'Modifier' : 'Créer'; ?> un utilisateur</h1>
    <?php if (!empty($error)): ?>
        <div class="sf-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?action=admin_user_form<?php echo $user ? '&id=' . $user['id'] : ''; ?>">
        <label for="name">Nom</label>
        <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

        <label for="password">Mot de passe <?php echo $user ? '(laisser vide pour conserver)' : ''; ?></label>
        <input id="password" name="password" type="password" <?php echo $user ? '' : 'required'; ?>>

        <label for="role">Rôle</label>
        <select id="role" name="role">
            <option value="user" <?php echo (isset($user['role']) && $user['role'] === 'user') ? 'selected' : ''; ?>>Utilisateur</option>
            <option value="admin" <?php echo (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
        </select>

        <button type="submit" class="sf-btn"><?php echo $user ? 'Mettre à jour' : 'Ajouter'; ?></button>
    </form>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
