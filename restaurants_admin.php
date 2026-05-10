<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>SmartFood - Gestion des restaurants</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header class="sf-header">
    <div class="container">
      <nav class="navbar navbar-expand-lg sf-navbar">
        <a class="navbar-brand sf-logo" href="marketplace.php">
          <span class="logo-icon">🥦</span> SmartFood
        </a>
        <button class="navbar-toggler sf-toggler" type="button" data-toggle="collapse" data-target="#sfNav">
          <span></span><span></span><span></span>
        </button>
        <div class="collapse navbar-collapse" id="sfNav">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
            <li class="nav-item active"><a class="nav-link" href="restaurants_admin.php">Restaurants</a></li>
          </ul>
          <div class="sf-nav-actions">
            <a href="marketplace.php" class="sf-btn-outline">Retour à la marketplace</a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <section class="sf-hero sf-hero--compact">
    <div class="sf-hero-overlay"></div>
    <div class="container">
      <div class="sf-hero-content">
        <span class="sf-badge">Administration</span>
        <h1>Modifier ou supprimer un restaurant</h1>
        <p>Choisis un restaurant dans la liste, ouvre-le pour le modifier, puis enregistre ou supprime-le.</p>
      </div>
    </div>
  </section>

  <section class="sf-admin-management pt-5 pb-5" id="adminManagement">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h5 mb-3">Restaurants</h3>
              <div class="table-responsive">
                <table class="table table-sm table-striped" id="restaurantManagementTable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nom</th>
                      <th>Cuisine</th>
                      <th>QR</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h3 class="h5 mb-3">Éditer le restaurant</h3>
              <div id="restaurantFormMessage" class="alert d-none" role="alert"></div>
              <form id="restaurantForm" class="sf-restaurant-form">
                <div class="form-group">
                  <label for="restaurantName">Nom</label>
                  <input id="restaurantName" name="name" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantSlug">Slug</label>
                  <input id="restaurantSlug" name="slug" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantCuisine">Cuisine</label>
                  <input id="restaurantCuisine" name="cuisine" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantEmoji">Emoji</label>
                  <input id="restaurantEmoji" name="emoji" type="text" class="form-control" maxlength="4" required />
                </div>
                <div class="form-group">
                  <label for="restaurantDescription">Description</label>
                  <textarea id="restaurantDescription" name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="restaurantAddress">Adresse</label>
                  <input id="restaurantAddress" name="address" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantDeliveryTime">Temps de livraison</label>
                  <input id="restaurantDeliveryTime" name="delivery_time" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantMinOrder">Commande minimum</label>
                  <input id="restaurantMinOrder" name="min_order" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantBadge">Badge</label>
                  <input id="restaurantBadge" name="badge" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="restaurantCoverColor">Couleur de couverture</label>
                  <input id="restaurantCoverColor" name="cover_color" type="text" class="form-control" placeholder="#52b788" required />
                </div>
                <input id="restaurantId" name="restaurant_id" type="hidden" />

                <div class="sf-product-builder mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="mb-0" for="productRows"><strong>Produits</strong></label>
                    <button type="button" id="addProductRowBtn" class="btn btn-sm btn-outline-secondary">Ajouter un produit</button>
                  </div>
                  <div id="productRows"></div>
                  <small class="form-text text-muted">Conservez ou modifiez les produits existants avant d’enregistrer.</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <button type="button" id="cancelRestaurantEditBtn" class="sf-btn-outline d-none">Annuler</button>
                  <button type="submit" id="restaurantFormSubmitBtn" class="sf-btn-primary">Mettre à jour le restaurant</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
