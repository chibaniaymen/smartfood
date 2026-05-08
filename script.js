/* ================================================
   SMARTFOOD MARKETPLACE - script.js
   ================================================ */

// ---- Panier (Cart State) ----
var cart = [];

// ---- DOM Ready ----
document.addEventListener('DOMContentLoaded', function () {
  initYear();
  initCategories();
  initSearch();
  initSort();
  initCartToggle();
  initScrollHeader();
  initRestaurantSubmission();
  initProductBuilder();
  initAdminManagement();
  initOrderPlacement();
  initQrScanner();
  animateOnScroll();
});

// ------------------------------------------------
// Année dans le footer
// ------------------------------------------------
function initYear() {
  var el = document.getElementById('year');
  if (el) el.textContent = new Date().getFullYear();
}

// ------------------------------------------------
// Header effect au scroll
// ------------------------------------------------
function initScrollHeader() {
  var header = document.querySelector('.sf-header');
  if (!header) return;
  window.addEventListener('scroll', function () {
    if (window.scrollY > 60) {
      header.style.background = 'rgba(13, 35, 24, 0.99)';
    } else {
      header.style.background = 'rgba(26, 60, 46, 0.97)';
    }
  });
}

// ------------------------------------------------
// Filtre par catégorie
// ------------------------------------------------
function initCategories() {
  var catGrid = document.querySelector('.sf-cat-grid');
  if (!catGrid) return;

  catGrid.addEventListener('click', function (event) {
    var card = event.target.closest('.sf-cat-card');
    if (!card) return;

    var allCards = catGrid.querySelectorAll('.sf-cat-card');
    allCards.forEach(function (c) { c.classList.remove('active'); });
    card.classList.add('active');

    var filter = card.getAttribute('data-filter');
    filterProducts(filter);
  });

  var allCard = catGrid.querySelector('.sf-cat-card[data-filter="all"]');
  if (allCard) allCard.classList.add('active');
}

function filterProducts(category) {
  var items = document.querySelectorAll('.sf-product-item');
  items.forEach(function (item) {
    if (category === 'all' || item.getAttribute('data-category') === category) {
      item.classList.remove('hidden');
    } else {
      item.classList.add('hidden');
    }
  });
}

function setActiveCategory(filter) {
  var catGrid = document.querySelector('.sf-cat-grid');
  if (!catGrid) return;
  var allCards = catGrid.querySelectorAll('.sf-cat-card');
  allCards.forEach(function (c) { c.classList.remove('active'); });
  var target = catGrid.querySelector('.sf-cat-card[data-filter="' + filter + '"]');
  if (target) {
    target.classList.add('active');
  }
}

// ------------------------------------------------
// Recherche en temps réel
// ------------------------------------------------
function initSearch() {
  var input = document.getElementById('searchInput');
  if (!input) return;
  input.addEventListener('input', function () {
    var query = input.value.toLowerCase().trim();
    var items = document.querySelectorAll('.sf-product-item');
    items.forEach(function (item) {
      var name = (item.getAttribute('data-name') || '').toLowerCase();
      var cat  = (item.getAttribute('data-category') || '').toLowerCase();
      if (name.indexOf(query) !== -1 || cat.indexOf(query) !== -1) {
        item.classList.remove('hidden');
      } else {
        item.classList.add('hidden');
      }
    });
  });
}

// ------------------------------------------------
// Tri des produits
// ------------------------------------------------
function initSort() {
  var select = document.getElementById('sortSelect');
  if (!select) return;
  select.addEventListener('change', function () {
    sortProducts(select.value);
  });
}

function sortProducts(mode) {
  var grid  = document.getElementById('productsGrid');
  if (!grid) return;
  var items = Array.prototype.slice.call(grid.querySelectorAll('.sf-product-item'));

  items.sort(function (a, b) {
    var priceA = parseFloat(a.getAttribute('data-price')) || 0;
    var priceB = parseFloat(b.getAttribute('data-price')) || 0;
    var nameA  = (a.getAttribute('data-name') || '').toLowerCase();
    var nameB  = (b.getAttribute('data-name') || '').toLowerCase();

    if (mode === 'price-asc')  return priceA - priceB;
    if (mode === 'price-desc') return priceB - priceA;
    if (mode === 'name')       return nameA.localeCompare(nameB);
    return 0;
  });

  items.forEach(function (item) {
    grid.appendChild(item);
  });
}

// ------------------------------------------------
// Panier
// ------------------------------------------------
function initCartToggle() {
  var btn = document.getElementById('cartToggle');
  if (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      toggleCart();
    });
  }
}

function toggleCart() {
  var sidebar = document.getElementById('cartSidebar');
  var overlay = document.getElementById('cartOverlay');
  if (!sidebar) return;
  sidebar.classList.toggle('open');
  overlay.classList.toggle('active');
  document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

function addToCart(name, price) {
  var existing = null;
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].name === name) { existing = cart[i]; break; }
  }

  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ name: name, price: price, qty: 1 });
  }

  renderCart();
  updateCartCount();
  showToast(name + ' ajouté au panier !');
}

function removeFromCart(name) {
  cart = cart.filter(function (item) { return item.name !== name; });
  renderCart();
  updateCartCount();
}

function changeQty(name, delta) {
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].name === name) {
      cart[i].qty += delta;
      if (cart[i].qty <= 0) {
        cart.splice(i, 1);
      }
      break;
    }
  }
  renderCart();
  updateCartCount();
}

function renderCart() {
  var container = document.getElementById('cartItems');
  var footer    = document.getElementById('cartFooter');
  var totalEl   = document.getElementById('cartTotal');
  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML =
      '<div class="sf-cart-empty"><span>🛒</span><p>Votre panier est vide</p></div>';
    if (footer) footer.style.display = 'none';
    return;
  }

  var html  = '';
  var total = 0;

  cart.forEach(function (item) {
    var lineTotal = item.price * item.qty;
    total += lineTotal;
    html +=
      '<div class="sf-cart-item">' +
        '<span class="sf-cart-item-name">' + item.name + '</span>' +
        '<div class="sf-cart-item-qty">' +
          '<button class="sf-qty-btn" onclick="changeQty(\'' + item.name + '\', -1)">−</button>' +
          '<span>' + item.qty + '</span>' +
          '<button class="sf-qty-btn" onclick="changeQty(\'' + item.name + '\', 1)">+</button>' +
        '</div>' +
        '<span class="sf-cart-item-price">' + lineTotal.toFixed(2) + ' DT</span>' +
        '<button class="sf-cart-item-remove" onclick="removeFromCart(\'' + item.name + '\')">' +
          '<i class="fa fa-times"></i>' +
        '</button>' +
      '</div>';
  });

  container.innerHTML = html;

  if (footer) footer.style.display = 'block';
  if (totalEl) totalEl.textContent = total.toFixed(2) + ' DT';
}

function updateCartCount() {
  var countEl = document.getElementById('cartCount');
  if (!countEl) return;
  var total = 0;
  cart.forEach(function (item) { total += item.qty; });
  countEl.textContent = total;
  countEl.style.display = total > 0 ? 'flex' : 'none';
}

// ------------------------------------------------
// Toast notification
// ------------------------------------------------
function showToast(message) {
  var toast  = document.getElementById('toast');
  var msgEl  = document.getElementById('toastMsg');
  if (!toast) return;
  if (msgEl) msgEl.textContent = message;
  toast.classList.add('show');
  setTimeout(function () {
    toast.classList.remove('show');
  }, 2800);
}

// ------------------------------------------------
// QR Scanner
// ------------------------------------------------
var qrScanner = null;

function initQrScanner() {
  var openBtn = document.getElementById('openQrScanner');
  var closeBtn = document.getElementById('closeQrScanner');
  var modal = document.getElementById('qrModal');
  if (!openBtn || !modal) return;

  openBtn.addEventListener('click', function () {
    openQrModal();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      closeQrModal();
    });
  }

  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      closeQrModal();
    }
  });
}

function openQrModal() {
  var modal = document.getElementById('qrModal');
  var status = document.getElementById('qrStatus');
  if (!modal || !status) return;
  status.textContent = 'Placez le QR code dans le cadre.';
  modal.classList.add('open');
  modal.setAttribute('aria-hidden', 'false');

  if (!qrScanner && window.Html5Qrcode) {
    qrScanner = new Html5Qrcode('qrReader');
  }

  if (qrScanner) {
    Html5Qrcode.getCameras().then(function (devices) {
      var cameraId = devices && devices.length ? devices[0].id : null;
      if (!cameraId) {
        status.textContent = 'Camera non disponible.';
        return;
      }
      qrScanner.start(
        cameraId,
        { fps: 10, qrbox: { width: 240, height: 240 } },
        function (decodedText) {
          handleQrResult(decodedText);
        },
        function () {}
      ).catch(function () {
        status.textContent = 'Impossible de demarrer la camera.';
      });
    }).catch(function () {
      status.textContent = 'Acces camera refuse.';
    });
  }
}

function closeQrModal() {
  var modal = document.getElementById('qrModal');
  if (!modal) return;
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden', 'true');

  if (qrScanner) {
    qrScanner.stop().then(function () {
      qrScanner.clear();
    }).catch(function () {});
  }
}

function handleQrResult(decodedText) {
  var status = document.getElementById('qrStatus');
  var slug = extractRestaurantSlug(decodedText);
  if (!slug) {
    if (status) status.textContent = 'Lien QR invalide.';
    return;
  }

  setActiveCategory(slug);
  filterProducts(slug);
  closeQrModal();

  var productsSection = document.getElementById('products');
  if (productsSection) {
    productsSection.scrollIntoView({ behavior: 'smooth' });
  }
}

function extractRestaurantSlug(urlText) {
  try {
    var url = new URL(urlText);
    var slug = url.searchParams.get('restaurant') || url.searchParams.get('slug');
    if (slug) return slug;

    var parts = url.pathname.split('/').filter(Boolean);
    if (parts.length) {
      var last = parts[parts.length - 1];
      if (last) return last;
    }
  } catch (e) {
    return null;
  }
  return null;
}

function initOrderPlacement() {
  var btn = document.getElementById('placeOrderBtn');
  if (!btn) return;
  btn.addEventListener('click', function () {
    if (!cart.length) {
      showToast('Votre panier est vide.');
      return;
    }

    var nameInput = document.getElementById('orderCustomerName');
    var phoneInput = document.getElementById('orderCustomerPhone');
    var customerName = nameInput ? nameInput.value.trim() : '';
    var customerPhone = phoneInput ? phoneInput.value.trim() : '';

    if (!customerName) {
      showToast('Veuillez saisir votre nom.');
      return;
    }

    var payload = {
      customer: {
        name: customerName,
        phone: customerPhone
      },
      items: cart.map(function (item) {
        return {
          name: item.name,
          price: item.price,
          qty: item.qty
        };
      })
    };

    fetch('place_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) {
            throw new Error(data.error || 'Erreur lors de la commande.');
          }
          return data;
        });
      })
      .then(function (data) {
        if (!data.success || !data.order_id) {
          throw new Error('Commande non enregistree.');
        }

        if (nameInput) nameInput.value = '';
        if (phoneInput) phoneInput.value = '';
        cart = [];
        renderCart();
        updateCartCount();
        showToast('Commande enregistree. Facture en cours...');

        window.open('invoice.php?id=' + encodeURIComponent(data.order_id), '_blank');

        loadAdminManagementTables(true);
      })
      .catch(function (error) {
        showToast(error.message || 'Impossible de passer la commande.');
      });
  });
}

function addRestaurantCategoryCard(restaurant) {
  var catGrid = document.querySelector('.sf-cat-grid');
  if (!catGrid) return;

  var existingCard = document.querySelector('.sf-cat-card[data-filter="' + escapeHtml(restaurant.slug) + '"]');
  if (existingCard) {
    existingCard.innerHTML =
      '<div class="sf-cat-icon">' + escapeHtml(restaurant.emoji || '🍽️') + '</div>' +
      '<span>' + escapeHtml(restaurant.name) + '</span>' +
      '<small class="sf-cat-sub">' + escapeHtml(restaurant.cuisine || '') + '</small>';
    return;
  }

  var wrapper = document.createElement('div');
  wrapper.className = 'col-6 col-md-3 col-lg-2';

  var card = document.createElement('div');
  card.className = 'sf-cat-card';
  card.setAttribute('data-filter', restaurant.slug);
  card.innerHTML =
    '<div class="sf-cat-icon">' + escapeHtml(restaurant.emoji || '🍽️') + '</div>' +
    '<span>' + escapeHtml(restaurant.name) + '</span>' +
    '<small class="sf-cat-sub">' + escapeHtml(restaurant.cuisine || '') + '</small>';

  wrapper.appendChild(card);
  catGrid.appendChild(wrapper);
}

function buildStarRating(rating) {
  var fullStars = Math.floor(rating);
  var halfStar = rating % 1 >= 0.5 ? 1 : 0;
  var emptyStars = 5 - fullStars - halfStar;
  var html = '';

  for (var i = 0; i < fullStars; i++) {
    html += '<i class="fa fa-star"></i>';
  }
  if (halfStar) {
    html += '<i class="fa fa-star-half-o"></i>';
  }
  for (var j = 0; j < emptyStars; j++) {
    html += '<i class="fa fa-star-o"></i>';
  }
  return html;
}

function createProductCard(product, restaurant) {
  var col = document.createElement('div');
  col.className = 'col-md-6 col-lg-4 sf-product-item';
  col.setAttribute('data-category', restaurant.slug);
  col.setAttribute('data-product-id', product.id || '');
  col.setAttribute('data-restaurant-id', restaurant.id || '');
  col.setAttribute('data-price', product.price || 0);
  col.setAttribute('data-name', product.name || '');

  var badgeHtml = product.badge ? '<span class="sf-product-badge">' + escapeHtml(product.badge) + '</span>' : '';
  var coverColor = restaurant.cover_color || '#52b788';
  var ratingHtml = buildStarRating(product.rating || 0);
  var safeProductName = escapeJsString(product.name || '');
  var safeRestaurantName = escapeJsString(restaurant.name || '');

  col.innerHTML =
    '<div class="sf-product-card">' +
      '<div class="sf-product-img" style="background:linear-gradient(135deg,' + escapeHtml(coverColor) + ',#ffffff);">' +
        '<div class="sf-product-emoji">' + escapeHtml(product.emoji || '🍽️') + '</div>' +
        badgeHtml +
        '<div class="sf-restaurant-tag">' + escapeHtml(product.emoji || '') + ' ' + escapeHtml(restaurant.name) + '</div>' +
        '<div class="sf-product-actions">' +
          '<button class="sf-action-btn"><i class="fa fa-heart-o"></i></button>' +
          '<button class="sf-action-btn"><i class="fa fa-eye"></i></button>' +
        '</div>' +
      '</div>' +
      '<div class="sf-product-info">' +
        '<span class="sf-product-cat">' + escapeHtml(product.category || '') + ' · ' + escapeHtml(restaurant.name) + '</span>' +
        '<h3>' + escapeHtml(product.name || '') + '</h3>' +
        '<p>' + escapeHtml(product.description || '') + '</p>' +
        '<div class="sf-product-footer">' +
          '<div class="sf-product-price"><span class="price-current">' + Number(product.price || 0).toFixed(2) + ' DT</span><span class="price-unit">/ ' + escapeHtml(product.unit || '') + '</span></div>' +
          '<div class="sf-product-rating">' + ratingHtml + '<span>(' + (product.rating ? Number(product.rating).toFixed(1) : '0') + ')</span></div>' +
        '</div>' +
        '<button class="sf-add-cart" onclick="addToCart(' + safeProductName + ', ' + Number(product.price || 0) + ', ' + safeRestaurantName + ')"><i class="fa fa-shopping-basket"></i> Ajouter au panier</button>' +
      '</div>' +
    '</div>';

  return col;
}

function getActiveCategoryFilter() {
  var activeCard = document.querySelector('.sf-cat-card.active');
  return activeCard ? activeCard.getAttribute('data-filter') : 'all';
}

function addProductsToGrid(products, restaurant) {
  var grid = document.getElementById('productsGrid');
  if (!grid) return;
  var activeFilter = getActiveCategoryFilter();
  products.forEach(function (product) {
    var card = createProductCard(product, restaurant);
    if (activeFilter !== 'all' && activeFilter !== restaurant.slug) {
      card.classList.add('hidden');
    }
    grid.appendChild(card);
  });
}

// ------------------------------------------------
// Restaurant form submission
// ------------------------------------------------
function initRestaurantSubmission() {
  var form = document.getElementById('restaurantForm');
  if (!form) return;
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    submitRestaurantForm(form);
  });
}

function initProductBuilder() {
  var addButton = document.getElementById('addProductRowBtn');
  if (!addButton) return;
  addButton.addEventListener('click', function () {
    addProductRow();
  });
  addProductRow();
}

function addProductRow(data) {
  var productRows = document.getElementById('productRows');
  if (!productRows) return;

  var index = productRows.children.length + 1;
  var row = document.createElement('div');
  row.className = 'product-row border rounded p-3 mb-3';
  row.setAttribute('data-product-id', data && data.id ? data.id : '');
  row.innerHTML =
    '<div class="d-flex justify-content-between align-items-center mb-3">' +
      '<h5 class="mb-0">Produit <span class="product-index">' + index + '</span></h5>' +
      '<button type="button" class="btn btn-sm btn-danger removeProductRowBtn">Supprimer</button>' +
    '</div>' +
    '<div class="form-row">' +
      '<div class="form-group col-md-6">' +
        '<label>Nom</label>' +
        '<input type="text" class="form-control product-name" value="' + (data && data.name ? escapeHtml(data.name) : '') + '" required />' +
      '</div>' +
      '<div class="form-group col-md-3">' +
        '<label>Emoji</label>' +
        '<input type="text" class="form-control product-emoji" value="' + (data && data.emoji ? escapeHtml(data.emoji) : '') + '" maxlength="4" required />' +
      '</div>' +
      '<div class="form-group col-md-3">' +
        '<label>Catégorie</label>' +
        '<input type="text" class="form-control product-category" value="' + (data && data.category ? escapeHtml(data.category) : '') + '" required />' +
      '</div>' +
    '</div>' +
    '<div class="form-row">' +
      '<div class="form-group col-md-3">' +
        '<label>Prix</label>' +
        '<input type="number" step="0.01" class="form-control product-price" value="' + (data && data.price ? escapeHtml(data.price) : '') + '" required />' +
      '</div>' +
      '<div class="form-group col-md-3">' +
        '<label>Unité</label>' +
        '<input type="text" class="form-control product-unit" value="' + (data && data.unit ? escapeHtml(data.unit) : '') + '" required />' +
      '</div>' +
      '<div class="form-group col-md-3">' +
        '<label>Badge</label>' +
        '<input type="text" class="form-control product-badge" value="' + (data && data.badge ? escapeHtml(data.badge) : '') + '" />' +
      '</div>' +
      '<div class="form-group col-md-3">' +
        '<label>Note</label>' +
        '<input type="number" step="0.1" min="0" max="5" class="form-control product-rating" value="' + (data && data.rating ? escapeHtml(data.rating) : '') + '" required />' +
      '</div>' +
    '</div>' +
    '<div class="form-group">' +
      '<label>Description</label>' +
      '<textarea class="form-control product-description" rows="2" required>' + (data && data.description ? escapeHtml(data.description) : '') + '</textarea>' +
    '</div>';

  productRows.appendChild(row);

  var removeBtn = row.querySelector('.removeProductRowBtn');
  if (removeBtn) {
    removeBtn.addEventListener('click', function () {
      row.parentElement.removeChild(row);
      updateProductIndexes();
    });
  }
}

function updateProductIndexes() {
  var rows = document.querySelectorAll('#productRows .product-row');
  rows.forEach(function (row, index) {
    var indexEl = row.querySelector('.product-index');
    if (indexEl) {
      indexEl.textContent = index + 1;
    }
  });
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function escapeJsString(value) {
  return "'" + String(value)
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/\n/g, '\\n')
    .replace(/\r/g, '\\r') + "'";
}

function collectProductsFromRows() {
  var rows = document.querySelectorAll('#productRows .product-row');
  var products = [];
  rows.forEach(function (row) {
    var name = (row.querySelector('.product-name') || {}).value || '';
    var emoji = (row.querySelector('.product-emoji') || {}).value || '';
    var category = (row.querySelector('.product-category') || {}).value || '';
    var price = parseFloat((row.querySelector('.product-price') || {}).value || 0);
    var unit = (row.querySelector('.product-unit') || {}).value || '';
    var badge = (row.querySelector('.product-badge') || {}).value || '';
    var rating = parseFloat((row.querySelector('.product-rating') || {}).value || 0);
    var description = (row.querySelector('.product-description') || {}).value || '';
    var productId = (row.getAttribute('data-product-id') || '').trim();

    if (!name || !category || !unit || !description) {
      return;
    }

    var productObject = {
      name: name.trim(),
      emoji: emoji.trim(),
      category: category.trim(),
      price: isNaN(price) ? 0 : price,
      unit: unit.trim(),
      badge: badge.trim(),
      rating: isNaN(rating) ? 0 : rating,
      description: description.trim()
    };

    if (productId) {
      productObject.id = productId;
    }

    products.push(productObject);
  });
  return products;
}

function submitRestaurantForm(form) {
  var messageEl = document.getElementById('restaurantFormMessage');
  if (messageEl) {
    messageEl.textContent = '';
    messageEl.className = 'alert d-none';
  }

  var restaurantId = document.getElementById('restaurantId').value.trim();
  var restaurant = {
    id: restaurantId || null,
    name: document.getElementById('restaurantName').value.trim(),
    slug: document.getElementById('restaurantSlug').value.trim(),
    cuisine: document.getElementById('restaurantCuisine').value.trim(),
    emoji: document.getElementById('restaurantEmoji').value.trim(),
    description: document.getElementById('restaurantDescription').value.trim(),
    address: document.getElementById('restaurantAddress').value.trim(),
    delivery_time: document.getElementById('restaurantDeliveryTime').value.trim(),
    min_order: document.getElementById('restaurantMinOrder').value.trim(),
    badge: document.getElementById('restaurantBadge').value.trim(),
    cover_color: document.getElementById('restaurantCoverColor').value.trim()
  };

  var products = collectProductsFromRows();
  if (!Array.isArray(products) || products.length === 0) {
    return showRestaurantMessage(false, 'Ajoutez au moins un produit valide.');
  }

  var action = restaurant.id ? 'update_restaurant' : 'create_restaurant';
  fetch('save_restaurant.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: action, restaurant: restaurant, products: products })
  })
    .then(function (response) {
      return response.json().then(function (data) {
        if (!response.ok) {
          throw new Error(data.error || 'Erreur lors de la requête.');
        }
        return data;
      });
    })
    .then(function (data) {
      if (data.success) {
        var savedAction = action;
        showRestaurantMessage(true, 'Restaurant enregistré avec succès. ID: ' + data.restaurant_id + '.');
        if (savedAction === 'create_restaurant') {
          addRestaurantCategoryCard(restaurant);
          addProductsToGrid(products, restaurant);
          showRestaurantMessage(true, 'Restaurant créé avec succès. ID: ' + data.restaurant_id + '.');
        } else {
          restaurant.id = data.restaurant_id;
          updateRestaurantManagementRow(restaurant);
          updateRestaurantCards(restaurant, products);
          showRestaurantMessage(true, 'Restaurant mis à jour. ID: ' + data.restaurant_id + '.');
        }
        form.reset();
        document.getElementById('productRows').innerHTML = '';
        addProductRow();
      } else {
        throw new Error(data.error || 'Échec de l’enregistrement.');
      }
    })
    .catch(function (error) {
      showRestaurantMessage(false, error.message || 'Impossible d’enregistrer le restaurant.');
    });
}

function showRestaurantMessage(success, text) {
  var messageEl = document.getElementById('restaurantFormMessage');
  if (!messageEl) {
    alert(text);
    return;
  }
  messageEl.textContent = text;
  messageEl.className = success ? 'alert alert-success' : 'alert alert-danger';
}

function initAdminManagement() {
  var restaurantTable = document.getElementById('restaurantManagementTable');
  var productTable = document.getElementById('productManagementTable');
  var cancelBtn = document.getElementById('cancelRestaurantEditBtn');

  loadAdminManagementTables();

  if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
      resetRestaurantForm();
    });
  }

  if (restaurantTable) {
    restaurantTable.addEventListener('click', function (event) {
      var button = event.target.closest('button');
      if (!button) return;
      var row = button.closest('tr');
      if (!row) return;
      var restaurantId = row.getAttribute('data-restaurant-id');
      if (button.classList.contains('edit-restaurant')) {
        editRestaurantFromRow(row);
      } else if (button.classList.contains('delete-restaurant')) {
        deleteRestaurant(restaurantId, row);
      }
    });
  }

  if (productTable) {
    productTable.addEventListener('click', function (event) {
      var button = event.target.closest('button');
      if (!button) return;
      var row = button.closest('tr');
      if (!row) return;
      var productId = row.getAttribute('data-product-id');
      if (button.classList.contains('edit-product')) {
        editProductFromRow(row);
      } else if (button.classList.contains('delete-product')) {
        deleteProduct(productId, row);
      }
    });
  }
}

function renderRestaurantManagementRow(restaurant) {
  var tbody = document.querySelector('#restaurantManagementTable tbody');
  if (!tbody) return;

  var restaurantUrl = buildRestaurantUrl(restaurant.slug || '');
  var qrUrl = buildQrUrl(restaurantUrl);

  var row = document.createElement('tr');
  row.setAttribute('data-restaurant-id', restaurant.id || '');
  row.setAttribute('data-restaurant-slug', restaurant.slug || '');
  row.innerHTML =
    '<td>' + escapeHtml(restaurant.id || '') + '</td>' +
    '<td>' + escapeHtml(restaurant.name || '') + '</td>' +
    '<td>' + escapeHtml(restaurant.cuisine || '') + '</td>' +
    '<td>' +
      '<a href="' + escapeHtml(qrUrl) + '" target="_blank" rel="noopener">' +
        '<img src="' + escapeHtml(qrUrl) + '" alt="QR" width="56" height="56" />' +
      '</a>' +
    '</td>' +
    '<td>' +
      '<button type="button" class="btn btn-sm btn-outline-primary edit-restaurant">Modifier</button> ' +
      '<button type="button" class="btn btn-sm btn-outline-danger delete-restaurant">Supprimer</button>' +
    '</td>';
  tbody.appendChild(row);
}

function buildRestaurantUrl(slug) {
  if (!slug) return '';
  return window.location.origin + window.location.pathname + '?restaurant=' + encodeURIComponent(slug);
}

function buildQrUrl(targetUrl) {
  if (!targetUrl) return '';
  return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(targetUrl);
}

function renderProductManagementRow(product, restaurantName) {
  var tbody = document.querySelector('#productManagementTable tbody');
  if (!tbody) return;

  var row = document.createElement('tr');
  row.setAttribute('data-product-id', product.id || '');
  row.setAttribute('data-restaurant-id', product.restaurant_id || '');
  row.innerHTML =
    '<td>' + escapeHtml(product.id || '') + '</td>' +
    '<td>' + escapeHtml(product.name || '') + '</td>' +
    '<td>' + escapeHtml(restaurantName || '') + '</td>' +
    '<td>' + escapeHtml(Number(product.price || 0).toFixed(2)) + ' DT</td>' +
    '<td>' +
      '<button type="button" class="btn btn-sm btn-outline-primary edit-product">Modifier</button> ' +
      '<button type="button" class="btn btn-sm btn-outline-danger delete-product">Supprimer</button>' +
    '</td>';
  tbody.appendChild(row);
}

function renderOrderManagementRow(order) {
  var tbody = document.querySelector('#orderManagementTable tbody');
  if (!tbody) return;

  var row = document.createElement('tr');
  row.innerHTML =
    '<td>' + escapeHtml(order.id || '') + '</td>' +
    '<td>' + escapeHtml(order.customer_name || '') + '</td>' +
    '<td>' + escapeHtml(order.customer_phone || '') + '</td>' +
    '<td>' + escapeHtml(Number(order.total || 0).toFixed(2)) + ' DT</td>' +
    '<td>' + escapeHtml(order.status || '') + '</td>' +
    '<td>' + escapeHtml(order.created_at || '') + '</td>';
  tbody.appendChild(row);
}

function updateRestaurantManagementRow(restaurant) {
  var row = document.querySelector('#restaurantManagementTable tr[data-restaurant-id="' + escapeHtml(restaurant.id) + '"]');
  if (!row) return;
  row.setAttribute('data-restaurant-slug', restaurant.slug || '');
  var cells = row.querySelectorAll('td');
  if (cells.length >= 4) {
    cells[1].textContent = restaurant.name || '';
    cells[2].textContent = restaurant.cuisine || '';
    var restaurantUrl = buildRestaurantUrl(restaurant.slug || '');
    var qrUrl = buildQrUrl(restaurantUrl);
    cells[3].innerHTML = '<a href="' + escapeHtml(qrUrl) + '" target="_blank" rel="noopener">' +
      '<img src="' + escapeHtml(qrUrl) + '" alt="QR" width="56" height="56" />' +
    '</a>';
  }
}

function updateRestaurantCards(restaurant, products) {
  removeRestaurantCategoryCard(restaurant.id);
  addRestaurantCategoryCard(restaurant);
  removeProductsByRestaurant(restaurant.id);
  if (Array.isArray(products) && products.length > 0) {
    products.forEach(function (product) {
      addProductsToGrid([product], restaurant);
    });
  }
}

function resetRestaurantForm() {
  var form = document.getElementById('restaurantForm');
  if (!form) return;
  form.reset();
  document.getElementById('restaurantId').value = '';
  document.getElementById('restaurantFormSubmitBtn').textContent = 'Enregistrer le restaurant';
  document.getElementById('cancelRestaurantEditBtn').classList.add('d-none');
  document.getElementById('productRows').innerHTML = '';
  addProductRow();
}

function editRestaurantFromRow(row) {
  var restaurantId = row.getAttribute('data-restaurant-id');
  if (!restaurantId) return;
  fetch('marketplace_data.php?restaurant_id=' + encodeURIComponent(restaurantId))
    .then(function (response) { return response.json(); })
    .then(function (data) {
      if (!data.success) {
        throw new Error(data.error || 'Impossible de charger le restaurant.');
      }
      populateRestaurantForm(data.restaurant, data.products || []);
    })
    .catch(function (error) {
      showRestaurantMessage(false, error.message);
    });
}

function editProductFromRow(row) {
  var productId = row.getAttribute('data-product-id');
  if (!productId) return;
  fetch('marketplace_data.php?product_id=' + encodeURIComponent(productId))
    .then(function (response) { return response.json(); })
    .then(function (data) {
      if (!data.success) {
        throw new Error(data.error || 'Impossible de charger le produit.');
      }
      populateRestaurantForm(data.restaurant, data.products || []);
    })
    .catch(function (error) {
      showRestaurantMessage(false, error.message);
    });
}

function populateRestaurantForm(restaurant, products) {
  document.getElementById('restaurantId').value = restaurant.id || '';
  document.getElementById('restaurantName').value = restaurant.name || '';
  document.getElementById('restaurantSlug').value = restaurant.slug || '';
  document.getElementById('restaurantCuisine').value = restaurant.cuisine || '';
  document.getElementById('restaurantEmoji').value = restaurant.emoji || '';
  document.getElementById('restaurantDescription').value = restaurant.description || '';
  document.getElementById('restaurantAddress').value = restaurant.address || '';
  document.getElementById('restaurantDeliveryTime').value = restaurant.delivery_time || '';
  document.getElementById('restaurantMinOrder').value = restaurant.min_order || '';
  document.getElementById('restaurantBadge').value = restaurant.badge || '';
  document.getElementById('restaurantCoverColor').value = restaurant.cover_color || '#52b788';

  var productRows = document.getElementById('productRows');
  productRows.innerHTML = '';
  if (Array.isArray(products) && products.length > 0) {
    products.forEach(function (product) {
      addProductRow(product);
    });
  } else {
    addProductRow();
  }

  document.getElementById('restaurantFormSubmitBtn').textContent = 'Mettre à jour le restaurant';
  document.getElementById('cancelRestaurantEditBtn').classList.remove('d-none');
}

function deleteRestaurant(restaurantId, row) {
  if (!restaurantId || !confirm('Supprimer ce restaurant et tous ses produits ?')) {
    return;
  }
  fetch('save_restaurant.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete_restaurant', restaurant_id: restaurantId })
  })
    .then(function (response) { return response.json(); })
    .then(function (data) {
      if (!data.success) {
        throw new Error(data.error || 'Impossible de supprimer le restaurant.');
      }
      if (row && row.parentElement) {
        row.parentElement.removeChild(row);
      }
      removeRestaurantCategoryCard(restaurantId);
      removeProductsByRestaurant(restaurantId);
      var currentId = document.getElementById('restaurantId').value.trim();
      if (currentId === String(restaurantId)) {
        resetRestaurantForm();
      }
      showRestaurantMessage(true, 'Restaurant supprimé.');
    })
    .catch(function (error) {
      showRestaurantMessage(false, error.message);
    });
}

function deleteProduct(productId, row) {
  if (!productId || !confirm('Supprimer ce produit ?')) {
    return;
  }
  fetch('save_restaurant.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete_product', product_id: productId })
  })
    .then(function (response) { return response.json(); })
    .then(function (data) {
      if (!data.success) {
        throw new Error(data.error || 'Impossible de supprimer le produit.');
      }
      row.parentElement.removeChild(row);
      removeProductCard(productId);
      showRestaurantMessage(true, 'Produit supprimé.');
    })
    .catch(function (error) {
      showRestaurantMessage(false, error.message);
    });
}

function loadAdminManagementTables(forceClear) {
  fetch('marketplace_data.php')
    .then(function (response) { return response.json(); })
    .then(function (data) {
      if (!data.restaurants || !data.products) {
        return;
      }

      if (forceClear) {
        var restBody = document.querySelector('#restaurantManagementTable tbody');
        var prodBody = document.querySelector('#productManagementTable tbody');
        var orderBody = document.querySelector('#orderManagementTable tbody');
        if (restBody) restBody.innerHTML = '';
        if (prodBody) prodBody.innerHTML = '';
        if (orderBody) orderBody.innerHTML = '';
      }

      var restaurantMap = {};
      data.restaurants.forEach(function (restaurant) {
        renderRestaurantManagementRow(restaurant);
        restaurantMap[restaurant.id] = restaurant.name;
      });

      data.products.forEach(function (product) {
        renderProductManagementRow(product, restaurantMap[product.restaurant_id] || product.restaurant_name || '');
      });

      if (Array.isArray(data.orders)) {
        data.orders.forEach(function (order) {
          renderOrderManagementRow(order);
        });
      }
    });
}

function removeRestaurantCategoryCard(restaurantId) {
  var row = document.querySelector('#restaurantManagementTable tr[data-restaurant-id="' + escapeHtml(restaurantId) + '"]');
  var slug = row ? row.getAttribute('data-restaurant-slug') : null;
  if (slug) {
    var card = document.querySelector('.sf-cat-card[data-filter="' + escapeHtml(slug) + '"]');
    if (card && card.parentElement) {
      card.parentElement.removeChild(card);
    }
  }
}

function removeProductsByRestaurant(restaurantId) {
  document.querySelectorAll('.sf-product-item[data-restaurant-id="' + escapeHtml(restaurantId) + '"]').forEach(function (item) {
    if (item.parentElement) {
      item.parentElement.removeChild(item);
    }
  });
}

function removeProductCard(productId) {
  document.querySelectorAll('.sf-product-item').forEach(function (item) {
    if (item.getAttribute('data-product-id') === String(productId)) {
      item.parentElement.removeChild(item);
    }
  });
}

// ------------------------------------------------
// Animation à l'apparition (Intersection Observer)
// ------------------------------------------------
function animateOnScroll() {
  if (!window.IntersectionObserver) return;

  var items = document.querySelectorAll('.sf-product-card, .sf-why-card, .sf-cat-card');
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.style.opacity    = '1';
        entry.target.style.transform  = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  items.forEach(function (item) {
    item.style.opacity   = '0';
    item.style.transform = 'translateY(20px)';
    item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(item);
  });
}

// ------------------------------------------------
// Bouton "Charger plus" (simulé)
// ------------------------------------------------
var loadMoreBtn = document.getElementById('loadMoreBtn');
if (loadMoreBtn) {
  loadMoreBtn.addEventListener('click', function () {
    loadMoreBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Chargement...';
    loadMoreBtn.disabled = true;
    setTimeout(function () {
      loadMoreBtn.innerHTML = '✓ Tous les produits sont affichés';
      loadMoreBtn.style.opacity = '0.5';
      loadMoreBtn.style.cursor  = 'default';
    }, 1500);
  });
}