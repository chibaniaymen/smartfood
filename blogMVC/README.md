# 📚 blogMVC - Architecture MVC du Blog SmartFood

## 🎯 Structure du Projet

Cette application utilise l'architecture **MVC** (Model-View-Controller) pour une séparation claire des responsabilités.

```
blogMVC/
├── Model/
│   └── Blog.php              # Classe modèle Blog (manipulation des données)
├── View/
│   ├── FrontOffice/          # Interface utilisateur publique
│   │   ├── index.php         # Liste des articles
│   │   └── article.php       # Détails d'un article + commentaires
│   └── BackOffice/           # Interface d'administration
│       └── admin.php         # Gestion articles et commentaires
├── Controller/
│   └── BlogController.php    # Contrôleur principal (logique métier)
├── config.php                # Configuration BD et fonctions utiles
└── index.php                 # Point d'entrée FrontOffice
```

---

## 📖 Explication de chaque couche

### 1️⃣ **Model** (`Model/Blog.php`)

La couche modèle gère **toutes les données** et l'interaction avec la base de données.

**Responsabilités** :
- Connexion PDO à la base de données
- Récupération des articles
- Ajout/modification/suppression d'articles
- Gestion des commentaires
- Statistiques (nombre d'articles, commentaires)

**Exemple de méthode** :
```php
public function addArticle(string $title, string $content): bool
{
    $stmt = $this->pdo->prepare('INSERT INTO articles (title, content) VALUES (?, ?)');
    return $stmt->execute([$title, $content]);
}
```

**Avantages** :
- La logique de base de données est centralisée
- Facile à modifier ou tester
- Réutilisable par plusieurs contrôleurs

---

### 2️⃣ **Controller** (`Controller/BlogController.php`)

La couche contrôleur gère **la logique métier** et communique entre le modèle et la vue.

**Responsabilités** :
- Validation des données
- Appel des méthodes du modèle
- Gestion des erreurs
- Passage des données aux vues

**Exemple de méthode** :
```php
public function createArticle(array $data): array
{
    $title = trim($data['title'] ?? '');
    $content = trim($data['content'] ?? '');

    if (empty($title)) {
        return ['success' => false, 'message' => 'Le titre est obligatoire.'];
    }

    $this->blog->addArticle($title, $content);
    return ['success' => true, 'message' => 'Article créé avec succès.'];
}
```

**Avantages** :
- Les données sont validées avant utilisation
- Les erreurs sont gérées proprement
- Format de retour uniforme

---

### 3️⃣ **View** (`View/FrontOffice/` et `View/BackOffice/`)

La couche vue gère **l'affichage et l'interaction avec l'utilisateur**.

#### **FrontOffice**
- `index.php` : Liste les articles publishés
- `article.php` : Affiche un article complet avec les commentaires

#### **BackOffice**
- `admin.php` : Tableau de bord d'administration avec gestion complète

**Responsabilités** :
- Affichage des données
- Réception des formulaires
- Appel du contrôleur
- Redirection vers d'autres vues

**Avantages** :
- Code HTML séparé du code métier
- Facile à modifier l'interface sans toucher aux données
- Réutilisabilité des templates

---

## 🔄 Flux de Données

### **Exemple : Ajouter un article**

```
1. Utilisateur remplit le formulaire (BackOffice)
        ↓
2. Vue (admin.php) envoie POST à BlogController
        ↓
3. Controller valide les données
        ↓
4. Controller appelle Model->addArticle()
        ↓
5. Model exécute la requête PDO
        ↓
6. Données insérées en BD
        ↓
7. Controller retourne un résultat
        ↓
8. Vue affiche le message de succès/erreur
```

---

## 🏗️ Diagramme d'interaction UML

```
┌─────────────────┐
│   FrontOffice   │
│  (index.php)    │
│ (article.php)   │
└────────┬────────┘
         │ appelle
         ▼
┌─────────────────────────────┐
│   Controller                │
│  BlogController             │
│  - createArticle()          │
│  - getArticleById()         │
│  - addComment()             │
│  - etc...                   │
└────────┬────────────────────┘
         │ utilise
         ▼
┌─────────────────┐
│   BackOffice    │
│  (admin.php)    │
│                 │
└────────┬────────┘
         │ appelle
         ▼
┌─────────────────────────────┐
│   Model                     │
│  Blog.php                   │
│  - getAllArticles()         │
│  - addArticle()             │
│  - getAllComments()         │
│  - etc...                   │
└────────┬────────────────────┘
         │ utilise PDO
         ▼
┌─────────────────┐
│   Database      │
│  (MySQL)        │
│  - articles     │
│  - commentaires │
└─────────────────┘
```

---

## 🔐 Sécurité

### **PDO Prepared Statements** (Prévention SQL Injection)
```php
// ✅ BON - Utilise les paramètres liés
$stmt = $pdo->prepare('INSERT INTO articles (title, content) VALUES (?, ?)');
$stmt->execute([$title, $content]);

// ❌ MAUVAIS - Concaténation directe (injection SQL)
$sql = "INSERT INTO articles VALUES ('" . $title . "')";
```

### **Validation Serveur-Side** (Pas HTML5 uniquement)
```php
if (empty($title)) {
    return ['success' => false, 'message' => 'Le titre est obligatoire.'];
}
```

### **Échappement HTML** (Prévention XSS)
```php
<?php echo h($article['title']); ?>
// La fonction h() échappe les caractères HTML
```

---

## 📋 Classes et Méthodes

### **Classe Blog (Model)**

| Méthode | Paramètres | Retour | Description |
|---------|-----------|--------|-------------|
| `getAllArticles()` | - | array | Récupère tous les articles |
| `getArticleById()` | int $id | array\|null | Récupère un article |
| `addArticle()` | string $title, string $content | bool | Ajoute un article |
| `updateArticle()` | int $id, string $title, string $content | bool | Modifie un article |
| `deleteArticle()` | int $id | bool | Supprime un article |
| `getAllComments()` | - | array | Récupère tous les commentaires |
| `addComment()` | int $articleId, string $author, string $content | bool | Ajoute un commentaire |
| `deleteComment()` | int $id | bool | Supprime un commentaire |
| `getArticleCount()` | - | int | Compte les articles |
| `getCommentCount()` | - | int | Compte les commentaires |

### **Classe BlogController**

Chaque méthode du modèle a un équivalent dans le contrôleur qui :
- Valide les données
- Gère les erreurs
- Retourne `['success' => bool, 'message' => string]`

---

## 🚀 Points d'Entrée

### **FrontOffice**
```
http://localhost/smartfood/blogMVC/View/FrontOffice/index.php
```

### **BackOffice**
```
http://localhost/smartfood/blogMVC/View/BackOffice/admin.php
```

---

## 🎓 Concepts OOP Utilisés

✅ **Encapsulation** : Les propriétés sont privées, données via getters/setters  
✅ **Héritage** : (Peut être étendu)  
✅ **Polymorphisme** : Les méthodes ont des signatures cohérentes  
✅ **Exception Handling** : Try-catch pour les erreurs PDO  
✅ **Interfaces claires** : Les méthodes ont des noms explicites  

---

## 📝 Exemple d'Utilisation

```php
<?php
// Initialiser le modèle et contrôleur
require 'config.php';
require 'Model/Blog.php';
require 'Controller/BlogController.php';

$blog = new Blog($pdo);
$controller = new BlogController($blog);

// Récupérer tous les articles
$articles = $controller->getAllArticles();

// Ajouter un article
$result = $controller->createArticle([
    'title' => 'Mon article',
    'content' => 'Contenu de l\'article'
]);

if ($result['success']) {
    echo 'Article créé!';
} else {
    echo 'Erreur: ' . $result['message'];
}
```

---

**Version** : 1.0  
**Date** : 15 avril 2026  
**Framework** : Vanilla PHP + Bootstrap + PDO
