<?php require APP_ROOT . '/app/views/shared/header.php'; ?>
<div class="sf-card">
    <h1><?php echo $product ? 'Modifier' : 'Ajouter'; ?> un produit</h1>
    <form method="post" action="index.php?action=admin_product_form<?php echo $product ? '&id=' . $product['id'] : ''; ?>">
        <label for="restaurant_id">ID Restaurant</label>
        <input id="restaurant_id" name="restaurant_id" type="number" value="<?php echo htmlspecialchars($product['restaurant_id'] ?? ''); ?>" required>

        <label for="name">Nom du produit</label>
        <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>

        <label for="category">Catégorie</label>
        <input id="category" name="category" type="text" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>" required>

        <label for="display_category">Affichage catégorie</label>
        <input id="display_category" name="display_category" type="text" value="<?php echo htmlspecialchars($product['display_category'] ?? ''); ?>" required>

        <label for="price">Prix</label>
        <input id="price" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>

        <label for="unit">Unité</label>
        <input id="unit" name="unit" type="text" value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>

        <label for="badge">Badge</label>
        <input id="badge" name="badge" type="text" value="<?php echo htmlspecialchars($product['badge'] ?? ''); ?>">

        <label for="emoji">Emoji</label>
        <input id="emoji" name="emoji" type="text" value="<?php echo htmlspecialchars($product['emoji'] ?? ''); ?>">

        <button type="submit" class="sf-btn"><?php echo $product ? 'Mettre à jour' : 'Créer'; ?></button>
    </form>
</div>
<?php require APP_ROOT . '/app/views/shared/footer.php'; ?>
