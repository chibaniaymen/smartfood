<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>SmartFood - Marketplace</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>

<body>

  <!-- ===== HEADER ===== -->
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
            <li class="nav-item"><a class="nav-link" href="index.html">Accueil</a></li>
            <li class="nav-item active"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
            <li class="nav-item"><a class="nav-link" href="restaurants_admin.php">Restaurants</a></li>
            <li class="nav-item"><a class="nav-link" href="about.html">À Propos</a></li>
            <li class="nav-item"><a class="nav-link" href="book.html">Réserver</a></li>
          </ul>
          <div class="sf-nav-actions">
            <a href="#" class="sf-icon-btn"><i class="fa fa-search"></i></a>
            <button type="button" class="sf-icon-btn" id="openQrScanner" aria-label="Scanner QR">
              <i class="fa fa-qrcode"></i>
            </button>
            <a href="#" class="sf-icon-btn sf-cart-btn" id="cartToggle">
              <i class="fa fa-shopping-basket"></i>
              <span class="cart-count" id="cartCount" style="display:none;">0</span>
            </a>
            <a href="#" class="sf-btn-primary">Commander</a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <!-- ===== HERO ===== -->
  <section class="sf-hero">
    <div class="sf-hero-overlay"></div>
    <div class="container">
      <div class="sf-hero-content">
        <span class="sf-badge">🌿 100% Naturel &amp; Bio</span>
        <h1>La Marketplace<br><em>de l'alimentation<br>intelligente</em></h1>
        <p>Découvrez des produits soigneusement sélectionnés pour votre bien-être. Frais, bio, livrés chez vous.</p>
        <div class="sf-hero-btns">
          <a href="#restaurants" class="sf-btn-primary">Nos Restaurants</a>
          <a href="#products" class="sf-btn-outline">Voir les produits</a>
        </div>
        <div class="sf-hero-stats">
          <div class="stat"><strong>6</strong><span>Restaurants</span></div>
          <div class="stat-divider"></div>
          <div class="stat"><strong>30+</strong><span>Produits Frais</span></div>
          <div class="stat-divider"></div>
          <div class="stat"><strong>24h</strong><span>Livraison Rapide</span></div>
        </div>
      </div>
    </div>
    <div class="sf-hero-scroll"><span></span></div>
  </section>

  <!-- ===== AJOUTER UN RESTAURANT ===== -->
  <section class="sf-add-restaurant" id="addRestaurant">
    <div class="container">
      <div class="sf-section-header">
        <span class="sf-eyebrow">Administration</span>
        <h2>Ajouter un restaurant</h2>
      </div>
      <form id="restaurantForm" class="sf-restaurant-form">
        <div id="restaurantFormMessage" class="alert d-none" role="alert"></div>
        <div class="row">
          <div class="col-lg-6">
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
          </div>
          <div class="col-lg-6">
            <div class="sf-product-builder">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="mb-0" for="productRows"><strong>Produits</strong></label>
                <button type="button" id="addProductRowBtn" class="btn btn-sm btn-outline-secondary">Ajouter un produit</button>
              </div>
              <div id="productRows"></div>
              <small class="form-text text-muted">Ajoutez au moins un produit. Le formulaire enverra automatiquement chaque produit au format requis.</small>
            </div>
          </div>
        </div>
        <input id="restaurantId" name="restaurant_id" type="hidden" />
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" id="cancelRestaurantEditBtn" class="sf-btn-outline d-none">Annuler</button>
          <button type="submit" id="restaurantFormSubmitBtn" class="sf-btn-primary">Enregistrer le restaurant</button>
        </div>
      </form>
    </div>
  </section>

  <!-- ===== GESTION DES RESTAURANTS ET PRODUITS ===== -->
  <section class="sf-admin-management" id="adminManagement">
    <div class="container">
      <div class="sf-section-header">
        <span class="sf-eyebrow">Administration</span>
        <h2>Modifier / Supprimer</h2>
      </div>
      <div class="row">
        <div class="col-lg-6 mb-4">
          <div class="card">
            <div class="card-body">
              <h3 class="h5">Restaurants</h3>
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
          <div class="card">
            <div class="card-body">
              <h3 class="h5">Produits</h3>
              <div class="table-responsive">
                <table class="table table-sm table-striped" id="productManagementTable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nom</th>
                      <th>Restaurant</th>
                      <th>Prix</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <h3 class="h5">Commandes</h3>
              <div class="table-responsive">
                <table class="table table-sm table-striped" id="orderManagementTable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Client</th>
                      <th>Téléphone</th>
                      <th>Total</th>
                      <th>Statut</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== RESTAURANTS (remplace catégories) ===== -->
  <section class="sf-categories" id="restaurants">
    <div class="container">
      <div class="sf-section-header">
        <span class="sf-eyebrow">Parcourir par</span>
        <h2>Nos Restaurants</h2>
      </div>
      <div class="row sf-cat-grid">

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card active" data-filter="all">
            <div class="sf-cat-icon">🍽️</div>
            <span>Tout</span>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="green-bowl">
            <div class="sf-cat-icon">🥗</div>
            <span>Green Bowl</span>
            <small class="sf-cat-sub">Salades &amp; Bowls</small>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="sushi-zen">
            <div class="sf-cat-icon">🍣</div>
            <span>Sushi Zen</span>
            <small class="sf-cat-sub">Japonais</small>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="casa-pizza">
            <div class="sf-cat-icon">🍕</div>
            <span>Casa Pizza</span>
            <small class="sf-cat-sub">Italien</small>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="burger-farm">
            <div class="sf-cat-icon">🍔</div>
            <span>Burger Farm</span>
            <small class="sf-cat-sub">Burgers Bio</small>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="detox-lab">
            <div class="sf-cat-icon">🧃</div>
            <span>Détox Lab</span>
            <small class="sf-cat-sub">Jus &amp; Smoothies</small>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <div class="sf-cat-card" data-filter="pasta-fresca">
            <div class="sf-cat-icon">🍝</div>
            <span>Pasta Fresca</span>
            <small class="sf-cat-sub">Pâtes Fraîches</small>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ===== SEARCH BAR ===== -->
  <section class="sf-search-bar">
    <div class="container">
      <div class="sf-search-wrap">
        <div class="sf-search-input-wrap">
          <i class="fa fa-search"></i>
          <input type="text" id="searchInput" placeholder="Rechercher un produit..." />
        </div>
        <div class="sf-filter-wrap">
          <select id="sortSelect">
            <option value="default">Trier par défaut</option>
            <option value="price-asc">Prix croissant</option>
            <option value="price-desc">Prix décroissant</option>
            <option value="name">Nom A-Z</option>
          </select>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== PRODUCTS GRID ===== -->
  <section class="sf-products" id="products">
    <div class="container">
      <div class="sf-section-header">
        <span class="sf-eyebrow">Sélection du moment</span>
        <h2 id="productsTitle">Tous nos Produits</h2>
      </div>

      <div class="row" id="productsGrid">

        <!-- ===== GREEN BOWL ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="green-bowl" data-price="18.50" data-name="Buddha Bowl Quinoa">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#2d6a4f,#52b788);">
              <div class="sf-product-emoji">🥗</div>
              <span class="sf-product-badge">Best-seller</span>
              <div class="sf-restaurant-tag">🥗 Green Bowl</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Bowls · Green Bowl</span>
              <h3>Buddha Bowl Quinoa</h3>
              <p>Quinoa, avocat, pois chiches rôtis, légumes grillés et sauce tahini maison.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">18,50 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(45)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Buddha Bowl Quinoa', 18.50, 'Green Bowl')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="green-bowl" data-price="14.00" data-name="Salade César Bio">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#2d6a4f,#40916c);">
              <div class="sf-product-emoji">🥬</div>
              <div class="sf-restaurant-tag">🥗 Green Bowl</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Salades · Green Bowl</span>
              <h3>Salade César Bio</h3>
              <p>Laitue romaine, parmesan, croûtons maison et sauce César sans anchois.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">14,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(38)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Salade César Bio', 14.00, 'Green Bowl')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="green-bowl" data-price="9.50" data-name="Smoothie Vert">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#1b4332,#52b788);">
              <div class="sf-product-emoji">🥤</div>
              <span class="sf-product-badge sf-badge-green">Detox</span>
              <div class="sf-restaurant-tag">🥗 Green Bowl</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Boissons · Green Bowl</span>
              <h3>Smoothie Vert</h3>
              <p>Épinards, pomme verte, gingembre, citron et eau de coco. 100% naturel.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">9,50 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(29)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Smoothie Vert', 9.50, 'Green Bowl')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="green-bowl" data-price="16.00" data-name="Wrap Avocat Falafel">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#2d6a4f,#74c69d);">
              <div class="sf-product-emoji">🌯</div>
              <div class="sf-restaurant-tag">🥗 Green Bowl</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Wraps · Green Bowl</span>
              <h3>Wrap Avocat Falafel</h3>
              <p>Falafels croustillants, avocat, tomates, concombre et houmous dans un wrap complet.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">16,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(22)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Wrap Avocat Falafel', 16.00, 'Green Bowl')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="green-bowl" data-price="19.00" data-name="Acai Bowl">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#4a1942,#9b5de5);">
              <div class="sf-product-emoji">🫐</div>
              <span class="sf-product-badge sf-badge-new">Nouveau</span>
              <div class="sf-restaurant-tag">🥗 Green Bowl</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Bowls · Green Bowl</span>
              <h3>Acai Bowl</h3>
              <p>Base acai, granola, banane, fraises, miel et noix de coco râpée.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">19,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(18)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Acai Bowl', 19.00, 'Green Bowl')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <!-- ===== SUSHI ZEN ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="sushi-zen" data-price="45.00" data-name="Plateau Sushi Mix">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#1a3c5e,#2d7dd2);">
              <div class="sf-product-emoji">🍣</div>
              <span class="sf-product-badge">Top Vente</span>
              <div class="sf-restaurant-tag">🍣 Sushi Zen</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Plateaux · Sushi Zen</span>
              <h3>Plateau Sushi Mix</h3>
              <p>Assortiment de maki, nigiri et california roll avec poisson ultra-frais.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">45,00 DT</span><span class="price-unit">/ 16 pièces</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(62)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Plateau Sushi Mix', 45.00, 'Sushi Zen')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="sushi-zen" data-price="22.00" data-name="Ramen Tonkotsu">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#2c3e50,#4a6fa5);">
              <div class="sf-product-emoji">🍜</div>
              <div class="sf-restaurant-tag">🍣 Sushi Zen</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Ramen · Sushi Zen</span>
              <h3>Ramen Tonkotsu</h3>
              <p>Bouillon de porc mijoté 12h, nouilles fraîches, œuf mollet, chashu et champignons.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">22,00 DT</span><span class="price-unit">/ bol</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(41)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Ramen Tonkotsu', 22.00, 'Sushi Zen')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="sushi-zen" data-price="18.00" data-name="California Roll">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#1a3c5e,#5390d9);">
              <div class="sf-product-emoji">🌀</div>
              <div class="sf-restaurant-tag">🍣 Sushi Zen</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Makis · Sushi Zen</span>
              <h3>California Roll</h3>
              <p>Crabe, avocat, concombre et tobiko. Un classique revisité avec finesse.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">18,00 DT</span><span class="price-unit">/ 8 pièces</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(35)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('California Roll', 18.00, 'Sushi Zen')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="sushi-zen" data-price="24.00" data-name="Tataki Saumon">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#c85250,#f4a261);">
              <div class="sf-product-emoji">🐟</div>
              <span class="sf-product-badge sf-badge-chef">Chef</span>
              <div class="sf-restaurant-tag">🍣 Sushi Zen</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Entrées · Sushi Zen</span>
              <h3>Tataki Saumon</h3>
              <p>Saumon légèrement saisi, sauce ponzu, sésame et ciboulette japonaise.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">24,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(27)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Tataki Saumon', 24.00, 'Sushi Zen')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="sushi-zen" data-price="12.00" data-name="Mochi Glacé">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#2d6a4f,#b5e48c);">
              <div class="sf-product-emoji">🍡</div>
              <div class="sf-restaurant-tag">🍣 Sushi Zen</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Desserts · Sushi Zen</span>
              <h3>Mochi Glacé</h3>
              <p>Mochis glacés au thé matcha, mangue et fruits rouges. Fondants et délicats.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">12,00 DT</span><span class="price-unit">/ 3 pièces</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(19)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Mochi Glacé', 12.00, 'Sushi Zen')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <!-- ===== CASA PIZZA ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="casa-pizza" data-price="22.00" data-name="Pizza Margherita">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#8b3a3a,#c1666b);">
              <div class="sf-product-emoji">🍕</div>
              <div class="sf-restaurant-tag">🍕 Casa Pizza</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Pizzas · Casa Pizza</span>
              <h3>Pizza Margherita</h3>
              <p>Tomate San Marzano, mozzarella fior di latte, basilic frais. La classique parfaite.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">22,00 DT</span><span class="price-unit">/ pizza</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(88)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Pizza Margherita', 22.00, 'Casa Pizza')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="casa-pizza" data-price="28.00" data-name="Pizza Quattro Stagioni">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#8b3a3a,#e07b3a);">
              <div class="sf-product-emoji">🍕</div>
              <span class="sf-product-badge sf-badge-sig">Signature</span>
              <div class="sf-restaurant-tag">🍕 Casa Pizza</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Pizzas · Casa Pizza</span>
              <h3>Pizza Quattro Stagioni</h3>
              <p>Jambon, champignons, artichaut, olives. Quatre saveurs en une pizza généreuse.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">28,00 DT</span><span class="price-unit">/ pizza</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(56)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Pizza Quattro Stagioni', 28.00, 'Casa Pizza')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="casa-pizza" data-price="16.00" data-name="Burrata Fraîche">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#7a3e00,#e9c46a);">
              <div class="sf-product-emoji">🫙</div>
              <div class="sf-restaurant-tag">🍕 Casa Pizza</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Entrées · Casa Pizza</span>
              <h3>Burrata Fraîche</h3>
              <p>Burrata crémeuse, tomates cerises, huile d'olive extra vierge et pesto de basilic.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">16,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(44)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Burrata Fraîche', 16.00, 'Casa Pizza')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="casa-pizza" data-price="11.00" data-name="Tiramisu Maison">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#5c3d2e,#b08850);">
              <div class="sf-product-emoji">🍰</div>
              <span class="sf-product-badge sf-badge-maison">Maison</span>
              <div class="sf-restaurant-tag">🍕 Casa Pizza</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Desserts · Casa Pizza</span>
              <h3>Tiramisu Maison</h3>
              <p>Tiramisu traditionnel avec mascarpone, café fort et biscuits savoiardi.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">11,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(61)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Tiramisu Maison', 11.00, 'Casa Pizza')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="casa-pizza" data-price="7.00" data-name="Limonade Sicilienne">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#4a7c59,#f4d35e);">
              <div class="sf-product-emoji">🍋</div>
              <div class="sf-restaurant-tag">🍕 Casa Pizza</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Boissons · Casa Pizza</span>
              <h3>Limonade Sicilienne</h3>
              <p>Limonade fraîche aux citrons de Sicile, menthe et sirop d'agave.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">7,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(33)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Limonade Sicilienne', 7.00, 'Casa Pizza')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <!-- ===== BURGER FARM ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="burger-farm" data-price="26.00" data-name="Farm Classic Burger">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#6b4c2a,#d4793a);">
              <div class="sf-product-emoji">🍔</div>
              <span class="sf-product-badge">Best-seller</span>
              <div class="sf-restaurant-tag">🍔 Burger Farm</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Burgers · Burger Farm</span>
              <h3>Farm Classic Burger</h3>
              <p>Bœuf bio 180g, cheddar affiné, laitue, tomate, oignon caramélisé et sauce BBQ maison.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">26,00 DT</span><span class="price-unit">/ burger</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(74)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Farm Classic Burger', 26.00, 'Burger Farm')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="burger-farm" data-price="24.00" data-name="Crispy Chicken Burger">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#6b4c2a,#f2a65a);">
              <div class="sf-product-emoji">🐔</div>
              <div class="sf-restaurant-tag">🍔 Burger Farm</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Burgers · Burger Farm</span>
              <h3>Crispy Chicken Burger</h3>
              <p>Poulet croustillant mariné au babeurre, salade coleslaw et pickles maison.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">24,00 DT</span><span class="price-unit">/ burger</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(58)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Crispy Chicken Burger', 24.00, 'Burger Farm')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="burger-farm" data-price="9.00" data-name="Sweet Potato Fries">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#7a3e00,#e07b3a);">
              <div class="sf-product-emoji">🍟</div>
              <div class="sf-restaurant-tag">🍔 Burger Farm</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Accompagnements · Burger Farm</span>
              <h3>Sweet Potato Fries</h3>
              <p>Frites de patate douce croustillantes servies avec dip au yaourt épicé.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">9,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(49)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Sweet Potato Fries', 9.00, 'Burger Farm')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="burger-farm" data-price="12.00" data-name="Milkshake Chocolat">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#3d1c02,#7b3f00);">
              <div class="sf-product-emoji">🍫</div>
              <div class="sf-restaurant-tag">🍔 Burger Farm</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Boissons · Burger Farm</span>
              <h3>Milkshake Chocolat</h3>
              <p>Milkshake onctueux au chocolat artisanal, crème fouettée et brownie émietté.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">12,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(36)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Milkshake Chocolat', 12.00, 'Burger Farm')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="burger-farm" data-price="22.00" data-name="Veggie Burger">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#3a6b2e,#74c69d);">
              <div class="sf-product-emoji">🥬</div>
              <span class="sf-product-badge sf-badge-green">Végé</span>
              <div class="sf-restaurant-tag">🍔 Burger Farm</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Burgers · Burger Farm</span>
              <h3>Veggie Burger</h3>
              <p>Steak de légumineuses maison, avocat, tomate, roquette et mayo au citron.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">22,00 DT</span><span class="price-unit">/ burger</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(27)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Veggie Burger', 22.00, 'Burger Farm')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <!-- ===== DÉTOX LAB ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="detox-lab" data-price="6.50" data-name="Green Detox Shot">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#1b4332,#52b788);">
              <div class="sf-product-emoji">💚</div>
              <span class="sf-product-badge sf-badge-green">Detox</span>
              <div class="sf-restaurant-tag">🧃 Détox Lab</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Shots · Détox Lab</span>
              <h3>Green Detox Shot</h3>
              <p>Céleri, concombre, gingembre, citron et curcuma. Un coup de boost immédiat.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">6,50 DT</span><span class="price-unit">/ shot</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(52)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Green Detox Shot', 6.50, 'Détox Lab')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="detox-lab" data-price="13.00" data-name="Smoothie Tropical">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#e9b44c,#e07b3a);">
              <div class="sf-product-emoji">🌴</div>
              <span class="sf-product-badge">Top</span>
              <div class="sf-restaurant-tag">🧃 Détox Lab</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Smoothies · Détox Lab</span>
              <h3>Smoothie Tropical</h3>
              <p>Mangue, ananas, noix de coco, banane et eau de coco. L'évasion en verre.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">13,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(44)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Smoothie Tropical', 13.00, 'Détox Lab')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="detox-lab" data-price="11.00" data-name="Jus Cold Press Betterave">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#6d1b7b,#c2185b);">
              <div class="sf-product-emoji">❤️</div>
              <div class="sf-restaurant-tag">🧃 Détox Lab</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Jus Pressés · Détox Lab</span>
              <h3>Jus Cold Press Betterave</h3>
              <p>Betterave, pomme, gingembre et citron. Pressé à froid pour garder tous les nutriments.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">11,00 DT</span><span class="price-unit">/ bouteille</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(31)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Jus Cold Press Betterave', 11.00, 'Détox Lab')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="detox-lab" data-price="15.00" data-name="Protein Shake Vanille">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#3a6b2e,#b5e48c);">
              <div class="sf-product-emoji">💪</div>
              <div class="sf-restaurant-tag">🧃 Détox Lab</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Protéines · Détox Lab</span>
              <h3>Protein Shake Vanille</h3>
              <p>Protéine de whey bio, lait d'amande, vanille de Madagascar et miel de thym.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">15,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(22)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Protein Shake Vanille', 15.00, 'Détox Lab')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="detox-lab" data-price="8.00" data-name="Infusion Froide Hibiscus">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#9b2226,#e63946);">
              <div class="sf-product-emoji">🌺</div>
              <span class="sf-product-badge sf-badge-maison">Maison</span>
              <div class="sf-restaurant-tag">🧃 Détox Lab</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Infusions · Détox Lab</span>
              <h3>Infusion Froide Hibiscus</h3>
              <p>Hibiscus, gingembre, citron et miel. Infusée à froid pendant 12h pour un goût profond.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">8,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(18)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Infusion Froide Hibiscus', 8.00, 'Détox Lab')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <!-- ===== PASTA FRESCA ===== -->
        <div class="col-md-6 col-lg-4 sf-product-item" data-category="pasta-fresca" data-price="26.00" data-name="Carbonara Authentique">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#7a4f1a,#e9c46a);">
              <div class="sf-product-emoji">🍝</div>
              <span class="sf-product-badge sf-badge-sig">Signature</span>
              <div class="sf-restaurant-tag">🍝 Pasta Fresca</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Pâtes · Pasta Fresca</span>
              <h3>Carbonara Authentique</h3>
              <p>Spaghetti frais, guanciale, pecorino romano, jaune d'œuf et poivre noir. Zéro crème.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">26,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><span>(67)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Carbonara Authentique', 26.00, 'Pasta Fresca')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="pasta-fresca" data-price="28.00" data-name="Ravioli Ricotta Épinards">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#7a4f1a,#52b788);">
              <div class="sf-product-emoji">🥟</div>
              <span class="sf-product-badge sf-badge-maison">Maison</span>
              <div class="sf-restaurant-tag">🍝 Pasta Fresca</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Pâtes Farcies · Pasta Fresca</span>
              <h3>Ravioli Ricotta Épinards</h3>
              <p>Ravioli fait main, farci à la ricotta fraîche et épinards, sauce beurre sauge.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">28,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(43)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Ravioli Ricotta Épinards', 28.00, 'Pasta Fresca')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="pasta-fresca" data-price="8.00" data-name="Focaccia Romarin">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#c8963e,#e9c46a);">
              <div class="sf-product-emoji">🫓</div>
              <div class="sf-restaurant-tag">🍝 Pasta Fresca</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Pains · Pasta Fresca</span>
              <h3>Focaccia Romarin</h3>
              <p>Focaccia moelleuse à l'huile d'olive, romarin frais et fleur de sel de Guérande.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">8,00 DT</span><span class="price-unit">/ pièce</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(55)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Focaccia Romarin', 8.00, 'Pasta Fresca')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="pasta-fresca" data-price="10.00" data-name="Panna Cotta Coulis Fruits">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#7a4f1a,#f4a261);">
              <div class="sf-product-emoji">🍮</div>
              <div class="sf-restaurant-tag">🍝 Pasta Fresca</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Desserts · Pasta Fresca</span>
              <h3>Panna Cotta Coulis Fruits</h3>
              <p>Panna cotta à la vanille de Tahiti, coulis de fruits rouges frais du marché.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">10,00 DT</span><span class="price-unit">/ portion</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(38)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Panna Cotta Coulis Fruits', 10.00, 'Pasta Fresca')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4 sf-product-item" data-category="pasta-fresca" data-price="14.00" data-name="Vin Rouge Maison">
          <div class="sf-product-card">
            <div class="sf-product-img" style="background:linear-gradient(135deg,#4a1942,#c2185b);">
              <div class="sf-product-emoji">🍷</div>
              <div class="sf-restaurant-tag">🍝 Pasta Fresca</div>
              <div class="sf-product-actions">
                <button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>
                <button class="sf-action-btn"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="sf-product-info">
              <span class="sf-product-cat">Boissons · Pasta Fresca</span>
              <h3>Vin Rouge Maison</h3>
              <p>Sélection de vins tunisiens de qualité, accordés avec les plats du chef.</p>
              <div class="sf-product-footer">
                <div class="sf-product-price"><span class="price-current">14,00 DT</span><span class="price-unit">/ verre</span></div>
                <div class="sf-product-rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><span>(24)</span></div>
              </div>
              <button class="sf-add-cart" onclick="addToCart('Vin Rouge Maison', 14.00, 'Pasta Fresca')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>
            </div>
          </div>
        </div>

      </div><!-- end row -->

      <div class="sf-load-more">
        <button id="loadMoreBtn" class="sf-btn-outline">
          <i class="fa fa-refresh"></i> Charger plus de produits
        </button>
      </div>
    </div>
  </section>

  <!-- ===== WHY SMARTFOOD ===== -->
  <section class="sf-why">
    <div class="container">
      <div class="sf-section-header">
        <span class="sf-eyebrow">Pourquoi nous choisir</span>
        <h2>L'avantage SmartFood</h2>
      </div>
      <div class="row">
        <div class="col-md-4"><div class="sf-why-card"><div class="sf-why-icon">🌱</div><h4>100% Bio &amp; Naturel</h4><p>Tous nos produits sont certifiés biologiques, cultivés sans pesticides ni OGM.</p></div></div>
        <div class="col-md-4"><div class="sf-why-card"><div class="sf-why-icon">🚚</div><h4>Livraison Express</h4><p>Livraison en moins de 24h directement depuis les producteurs jusqu'à votre porte.</p></div></div>
        <div class="col-md-4"><div class="sf-why-card"><div class="sf-why-icon">💚</div><h4>Impact Positif</h4><p>Chaque achat soutient les agriculteurs locaux et réduit l'empreinte carbone.</p></div></div>
      </div>
    </div>
  </section>

  <!-- ===== CART SIDEBAR ===== -->
  <div class="sf-cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
  <div class="sf-cart-sidebar" id="cartSidebar">
    <div class="sf-cart-header">
      <h3><i class="fa fa-shopping-basket"></i> Mon Panier</h3>
      <button onclick="toggleCart()" class="sf-cart-close">&times;</button>
    </div>
    <div class="sf-cart-items" id="cartItems">
      <div class="sf-cart-empty"><span>🛒</span><p>Votre panier est vide</p></div>
    </div>
    <div class="sf-cart-footer" id="cartFooter" style="display:none;">
      <div class="sf-cart-total">
        <span>Total:</span>
        <strong id="cartTotal">0,00 DT</strong>
      </div>
      <div class="sf-cart-customer">
        <input type="text" id="orderCustomerName" class="form-control" placeholder="Nom complet" />
        <input type="text" id="orderCustomerPhone" class="form-control" placeholder="Telephone" />
      </div>
      <button class="sf-btn-primary w-100" id="placeOrderBtn">Passer la commande</button>
    </div>
  </div>

  <!-- ===== QR SCANNER ===== -->
  <div class="sf-qr-modal" id="qrModal" aria-hidden="true">
    <div class="sf-qr-card">
      <div class="sf-qr-header">
        <h4>Scanner un restaurant</h4>
        <button type="button" class="sf-qr-close" id="closeQrScanner" aria-label="Fermer">&times;</button>
      </div>
      <div id="qrReader" class="sf-qr-reader"></div>
      <div class="sf-qr-status" id="qrStatus">Placez le QR code dans le cadre.</div>
    </div>
  </div>

  <!-- ===== TOAST ===== -->
  <div class="sf-toast" id="toast">
    <i class="fa fa-check-circle"></i>
    <span id="toastMsg">Produit ajouté au panier !</span>
  </div>

  <!-- ===== FOOTER ===== -->
  <footer class="sf-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 sf-footer-col">
          <div class="sf-footer-brand">
            <a href="marketplace.php" class="sf-footer-logo">🥦 SmartFood</a>
            <p>La marketplace de l'alimentation intelligente. Des produits frais, bio et locaux livrés chez vous.</p>
            <div class="sf-footer-social">
              <a href="#"><i class="fa fa-facebook"></i></a>
              <a href="#"><i class="fa fa-instagram"></i></a>
              <a href="#"><i class="fa fa-twitter"></i></a>
              <a href="#"><i class="fa fa-youtube-play"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-4 sf-footer-col">
          <h5>Navigation</h5>
          <ul class="sf-footer-links">
            <li><a href="index.html"><i class="fa fa-chevron-right"></i> Accueil</a></li>
            <li><a href="marketplace.php"><i class="fa fa-chevron-right"></i> Marketplace</a></li>
            <li><a href="about.html"><i class="fa fa-chevron-right"></i> À Propos</a></li>
            <li><a href="book.html"><i class="fa fa-chevron-right"></i> Réserver une table</a></li>
          </ul>
        </div>
        <div class="col-md-4 sf-footer-col">
          <h5>Contact</h5>
          <ul class="sf-footer-contact">
            <li><i class="fa fa-map-marker"></i> 12 Rue de la Santé, Tunis</li>
            <li><i class="fa fa-phone"></i> +216 71 000 000</li>
            <li><i class="fa fa-envelope"></i> contact@smartfood.tn</li>
            <li><i class="fa fa-clock-o"></i> Lun – Sam : 8h00 – 20h00</li>
          </ul>
        </div>
      </div>
      <div class="sf-footer-bottom">
        <p>&copy; <span id="year"></span> SmartFood. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
  <script src="script.js"></script>
</body>
</html>