# 🚀 GUIDE DE DÉMARRAGE RAPIDE - blogMVC

## ✅ Structure créée

```
blogMVC/
├── Model/
│   └── Blog.php              ← Classe Blog (gestion données)
├── View/
│   ├── FrontOffice/
│   │   ├── index.php         ← Affichage articles
│   │   └── article.php       ← Détail + commentaires
│   └── BackOffice/
│       └── admin.php         ← Administration
├── Controller/
│   └── BlogController.php    ← Logique métier
├── config.php                ← Config BD + fonctions utiles
├── index.php                 ← Point d'entrée
└── README.md                 ← Documentation complète
```

---

## 🎯 Accéder à l'application

### **FrontOffice** (Utilisateurs - Lecture)
```
http://localhost/smartfood/blogMVC/
http://localhost/smartfood/blogMVC/View/FrontOffice/index.php
```
- Voir tous les articles
- Consulter un article
- Ajouter des commentaires

### **BackOffice** (Admin - Gestion complète)
```
http://localhost/smartfood/blogMVC/View/BackOffice/admin.php
```
- Tableau de bord avec statistiques
- Ajouter/modifier/supprimer articles
- Gérer les commentaires

---

## 🏗️ Architecture MVC expliquée

### **1. Model (Blog.php)**
Représente **les données** et l'interaction avec la BD.

```php
$blog = new Blog($pdo);
$articles = $blog->getAllArticles();
$blog->addArticle($title, $content);
```

### **2. Controller (BlogController.php)**
Représente **la logique métier** et la validation.

```php
$controller = new BlogController($blog);
$result = $controller->createArticle(['title' => '...', 'content' => '...']);
// Retour: ['success' => true/false, 'message' => '...']
```

### **3. View (FrontOffice & BackOffice)**
Représente **l'affichage et l'interface**.

```php
- index.php : Liste articles
- article.php : Détail + commentaires
- admin.php : Gestion complète
```

---

## 🔄 Flux d'une action

**Exemple : Ajouter un article**

```
1. Admin accède à : /blogMVC/View/BackOffice/admin.php?action=add
   ↓
2. Il remplit le formulaire (titre, contenu)
   ↓
3. Il clique "Créer l'article" (POST)
   ↓
4. Vue (admin.php) appelle le Controller :
   $result = $controller->createArticle($_POST);
   ↓
5. Controller valide :
   - Titre non vide? ✓
   - Contenu non vide? ✓
   ↓
6. Si OK, Controller appelle le Model :
   $this->blog->addArticle($title, $content);
   ↓
7. Model exécute la requête PDO :
   INSERT INTO articles (title, content) VALUES (?, ?)
   ↓
8. Données sauvegardées en BD ✓
   ↓
9. Controller retourne: ['success' => true, 'message' => 'Article créé...']
   ↓
10. Vue affiche le message de succès
```

---

## 🔒 Sécurité intégrée

✅ **PDO Prepared Statements** - Prévention injection SQL  
✅ **Validation Serveur-Side** - Données vérifiées avant insertion  
✅ **Fonction h()** - Échappe les caractères HTML (prévention XSS)  
✅ **Exception Handling** - Gestion des erreurs propre  

---

## 📚 Méthodes principales

### **Blog Model**
```php
$blog->getAllArticles()           // Tous les articles
$blog->getArticleById($id)        // 1 article par ID
$blog->addArticle($title, $content)
$blog->updateArticle($id, $title, $content)
$blog->deleteArticle($id)

$blog->getAllComments()           // Tous les commentaires
$blog->getCommentsByArticle($id)  // Commentaires d'1 article
$blog->addComment($articleId, $author, $content)
$blog->deleteComment($id)

$blog->getArticleCount()          // Nombre d'articles
$blog->getCommentCount()          // Nombre de commentaires
```

### **BlogController**
```php
$controller->getAllArticles()
$controller->createArticle(array $data)      // Retour: ['success' => ..., 'message' => ...]
$controller->updateArticle(int $id, array $data)
$controller->deleteArticle(int $id)

$controller->addComment(int $articleId, array $data)
$controller->deleteComment(int $id)

$controller->getStats()           // ['articles_count' => ..., 'comments_count' => ...]
```

---

## 🧪 Tester l'application

### **Test 1 : Voir les articles**
1. Aller sur `http://localhost/smartfood/blogMVC/`
2. Voir la liste des articles du blog

### **Test 2 : Ajouter un article (Admin)**
1. Aller sur `http://localhost/smartfood/blogMVC/View/BackOffice/admin.php`
2. Cliquer "Nouvel article"
3. Remplir titre et contenu
4. Cliquer "Créer l'article"

### **Test 3 : Voir un article + commentaires**
1. Sur FrontOffice, cliquer sur "Lire la suite"
2. Voir l'article complet
3. Ajouter un commentaire

---

## 🎓 Concepts OOP appliqués

- ✅ **Encapsulation** : Propriétés privées, méthodes publiques
- ✅ **Héritage** : Classes peuvent être étendues
- ✅ **Polymorphisme** : Interfaces cohérentes
- ✅ **Abstraction** : Modèle cache la complexité BD
- ✅ **Exception Handling** : Gestion d'erreurs robuste
- ✅ **Type Hinting** : Déclaration des types de paramètres/retour

---

## 📦 Dépendances

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5 (CDN)
- Font Awesome 6 (CDN)

---

## 🐛 Troubleshooting

**Q: Erreur "Call to undefined function h()"**
- Vérifier que `config.php` est bien inclus
- `require_once __DIR__ . '/../../config.php';`

**Q: Erreur "Table doesn't exist"**
- Vérifier que les tables `articles` et `commentaires` existent
- Vérifier le nom de la base de données dans `config.php`

**Q: Page blanche**
- Vérifier les logs Apache : `C:\xampp\apache\logs\error.log`
- Lancer : `php -l fichier.php` pour valider la syntaxe

---

## 📝 Notes

- Les articles et commentaires existants (si présents) s'affichent automatiquement
- Aucune donnée de test n'est créée automatiquement
- Le design est responsive (mobile-friendly)
- Tous les formulaires ont validation serveur-side

---

**✅ Application prête à l'emploi!** 🎉

Pour plus de détails, voir `README.md`.
