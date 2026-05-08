<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1>Gestion des utilisateurs</h1>
    <p><a class="sf-btn" href="index.php?action=admin_user_form">Ajouter un utilisateur</a></p>
    <table>
        <thead>
            <tr><th>ID</th><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['email']); ?></td>
                    <td><?php echo htmlspecialchars($item['role']); ?></td>
                    <td>
                        <a href="index.php?action=admin_user_form&id=<?php echo $item['id']; ?>">Modifier</a>
                        |
                        <a href="index.php?action=admin_user_delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
