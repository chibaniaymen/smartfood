<?php
/**
 * Vue BackOffice — Jointure commentaires × articles (Workshop Jointure)
 * Affiche tous les commentaires avec le titre de l'article associé (INNER JOIN)
 */
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Article.php';
require_once __DIR__ . '/../../../Model/Commentaire.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$articleModel          = new Article($pdo);
$commentaireModel      = new Commentaire($pdo);
$articleController     = new ArticleController($articleModel);
$commentaireController = new CommentaireController($commentaireModel);

// Récupérer tous les articles pour le filtre
$articles = $articleController->getAll();

// Traitement filtre POST
$list      = null;
$idArticle = null;
$modeAll   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_all'])) {
        // Jointure globale — tous les commentaires
        $modeAll = true;
        $list    = $commentaireController->getAllWithArticle();
    } elseif (isset($_POST['search']) && isset($_POST['article'])) {
        // Jointure filtrée par article
        $idArticle = (int) $_POST['article'];
        if (isValidId($idArticle)) {
            $list = $commentaireController->getCommentairesByArticleJoin($idArticle);
        }
    }
}

$pageTitle  = 'Jointure — Commentaires par article';
$activeMenu = 'jointure';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
  :root { --green:#2D6A4F; --orange:#E76F51; }

  /* CARD formulaire */
  .join-card {
    background: white; border-radius: 12px; padding: 28px 32px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 28px;
  }
  .join-card h2 {
    font-size: 1.1rem; font-weight: 700; color: #1a1a2e;
    margin-bottom: 18px; padding-bottom: 12px;
    border-bottom: 2px solid #e9ecef;
    display: flex; align-items: center; gap: 10px;
  }
  .form-row  { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
  .form-grp  { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px; }
  .form-grp label { font-size: .8rem; font-weight: 600; color: #495057; }
  .form-select-sf {
    padding: 9px 12px; border: 1.5px solid #dee2e6; border-radius: 8px;
    font-size: .88rem; background: #f8f9fa; width: 100%; cursor: pointer;
  }
  .form-select-sf:focus { outline: none; border-color: var(--green); }
  .btn-sf {
    padding: 10px 22px; border: none; border-radius: 8px; font-size: .88rem;
    font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    transition: opacity .2s;
  }
  .btn-sf:hover { opacity: .85; }
  .btn-sf-green { background: var(--green); color: white; }
  .btn-sf-dark  { background: #1a1a2e; color: white; }
  .btn-sep      { font-size: .78rem; color: #adb5bd; padding: 10px 4px; }

  /* SQL badge */
  .sql-box {
    background: #1a1a2e; border-radius: 8px; padding: 14px 18px;
    font-family: 'Courier New', monospace; font-size: .78rem;
    color: #95D5B2; line-height: 1.7; margin-top: 18px; overflow-x: auto;
  }
  .sql-kw  { color: #52B788; font-weight: 700; }
  .sql-tbl { color: #E76F51; }
  .sql-col { color: #ffd166; }
  .sql-val { color: #a8dadc; }

  /* Résultats */
  .results-header-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
  }
  .results-title-sf { font-size: 1rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 8px; }
  .badge-count { background: var(--green); color: white; padding: 3px 12px; border-radius: 50px; font-size: .75rem; font-weight: 700; }

  /* Table */
  .join-table { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.06); overflow: hidden; }
  .join-table table { width: 100%; margin: 0; font-size: .86rem; border-collapse: collapse; }
  .join-table thead { background: #f8f9fa; }
  .join-table th { padding: 13px 18px; font-weight: 600; color: #495057; text-align: left; border-bottom: 1px solid #e9ecef; }
  .join-table td { padding: 13px 18px; vertical-align: middle; border-bottom: 1px solid #f1f3f5; }
  .join-table tbody tr:last-child td { border-bottom: none; }
  .join-table tbody tr:hover { background: #f8fffe; }

  .avatar-sm {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, #52B788, #2D6A4F);
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: .7rem; text-transform: uppercase;
  }
  .author-cell { display: flex; align-items: center; gap: 8px; }
  .article-badge {
    background: #e8f5e9; color: var(--green); padding: 3px 10px;
    border-radius: 50px; font-size: .75rem; font-weight: 600;
    display: inline-block; max-width: 200px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .text-muted-sf { color: #adb5bd; font-size: .8rem; }
  .empty-row td { text-align: center; padding: 40px; color: #adb5bd; }
</style>

<!-- En-tête de page -->
<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-code-branch" style="color:var(--sf-green);margin-right:8px;"></i>Workshop — Jointure SQL</h1>
    <div class="breadcrumb-sf">Admin → <a href="#">Jointure</a></div>
  </div>
  <span style="background:#e8f5e9;color:#2D6A4F;padding:6px 16px;border-radius:50px;font-size:.8rem;font-weight:600;">
    INNER JOIN commentaires × articles
  </span>
</div>

<!-- ── FORMULAIRE DE RECHERCHE ──────────────────────────────────── -->
<div class="join-card">
  <h2>
    <i class="fas fa-search" style="color:var(--sf-green);"></i>
    Recherche de commentaires par article
  </h2>

  <form method="POST" action="">
    <div class="form-row">
      <div class="form-grp">
        <label for="article">Sélectionnez un article :</label>
        <select name="article" id="article" class="form-select-sf">
          <?php foreach ($articles as $art): ?>
            <option value="<?php echo $art['id']; ?>"
              <?php echo ($idArticle == $art['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars(mb_substr($art['title'], 0, 65)); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" name="search" value="1" class="btn-sf btn-sf-green">
        <i class="fas fa-search"></i> Rechercher
      </button>

      <span class="btn-sep">ou</span>

      <button type="submit" name="search_all" value="1" class="btn-sf btn-sf-dark">
        <i class="fas fa-list"></i> Tous les commentaires
      </button>
    </div>
  </form>

  <!-- Requête SQL affichée dynamiquement -->
  <?php if ($list !== null): ?>
  <div class="sql-box">
    <span class="sql-kw">SELECT</span>
    <span class="sql-col">c.id, c.author, c.content, c.created_at,
            a.id AS article_id, a.title AS article_title</span><br>
    <span class="sql-kw">FROM</span>
    <span class="sql-tbl">commentaires</span> c<br>
    <span class="sql-kw">INNER JOIN</span>
    <span class="sql-tbl">articles</span> a
    <span class="sql-kw">ON</span>
    <span class="sql-col">c.article_id = a.id</span>
    <?php if (!$modeAll): ?>
    <br><span class="sql-kw">WHERE</span>
    <span class="sql-col">a.id</span> = <span class="sql-val"><?php echo (int)$idArticle; ?></span>
    <?php endif; ?>
    <br><span class="sql-kw">ORDER BY</span>
    <span class="sql-col">c.created_at</span> <span class="sql-kw">DESC</span>;
  </div>
  <?php endif; ?>
</div>

<!-- ── RÉSULTATS ──────────────────────────────────────────────────── -->
<?php if ($list !== null): ?>
<div class="results-header-bar">
  <div class="results-title-sf">
    <i class="fas fa-comments" style="color:var(--sf-green);"></i>
    Commentaires correspondants
    <?php if (!$modeAll && $idArticle): ?>
      — article #<?php echo $idArticle; ?>
    <?php else: ?>
      — tous les articles
    <?php endif; ?>
  </div>
  <div style="display:flex;align-items:center;gap:12px;">
    <span class="badge-count"><?php echo count($list); ?> résultat(s)</span>
    <?php 
    $exportUrl = 'export.php';
    if (!$modeAll && $idArticle) {
        $exportUrl .= '?article=' . $idArticle;
    }
    ?>
    <a href="<?php echo $exportUrl; ?>" class="btn-sf btn-sf-green" style="padding:6px 16px;text-decoration:none;font-size:.8rem;">
      <i class="fas fa-file-csv"></i> Exporter en CSV
    </a>
  </div>
</div>

<div class="join-table">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Auteur</th>
        <th>Article (jointure)</th>
        <th>Commentaire</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($list)): ?>
        <tr class="empty-row">
          <td colspan="5">😶 Aucun commentaire pour cet article.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($list as $row): ?>
        <tr>
          <td class="text-muted-sf">#<?php echo $row['commentaire_id']; ?></td>
          <td>
            <div class="author-cell">
              <div class="avatar-sm">
                <?php echo mb_strtoupper(mb_substr($row['author'], 0, 2)); ?>
              </div>
              <span style="font-weight:600;"><?php echo htmlspecialchars($row['author']); ?></span>
            </div>
          </td>
          <td>
            <span class="article-badge" title="<?php echo htmlspecialchars($row['article_title']); ?>">
              <?php echo htmlspecialchars(mb_substr($row['article_title'], 0, 35)); ?>
            </span>
          </td>
          <td style="max-width:280px;color:#495057;">
            <?php echo htmlspecialchars(mb_substr($row['commentaire_content'], 0, 80)); ?>…
          </td>
          <td class="text-muted-sf" style="white-space:nowrap;">
            <?php echo date('d/m/Y H:i', strtotime($row['commentaire_date'])); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
