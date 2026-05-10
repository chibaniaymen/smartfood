<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Ingredient.php';

class IngredientController {

    // Detect actual ingredient table schema and return useful column names
    private function detectSchema(PDO $db): array {
        $desc = $db->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
        $fields = array_column($desc, 'Field');
        $schema = [];
        // primary key
        if (in_array('id_ingredient', $fields)) $schema['pk'] = 'id_ingredient';
        elseif (in_array('id_ingre', $fields)) $schema['pk'] = 'id_ingre';
        else $schema['pk'] = $fields[0] ?? 'id_ingredient';

        // foreign key to recette
        if (in_array('id_recette', $fields)) $schema['fk'] = 'id_recette';
        elseif (in_array('id_rec', $fields)) $schema['fk'] = 'id_rec';
        else $schema['fk'] = 'id_recette';

        // optional columns
        $schema['has_quantite'] = in_array('quantite', $fields);
        $schema['quantite_col'] = $schema['has_quantite'] ? 'quantite' : (in_array('unite', $fields) ? 'unite' : null);

        // nutritional columns
        $schema['nutrients'] = array_filter(['calories','proteines','glucides','lipides','fibres','sucre','sel'], function($c) use ($fields){ return in_array($c,$fields); });
        $schema['has_categorie'] = in_array('categorie', $fields);

        // name column (preferred)
        if (in_array('nom', $fields)) $schema['name_col'] = 'nom';
        elseif (in_array('name', $fields)) $schema['name_col'] = 'name';
        else $schema['name_col'] = $fields[0] ?? null;

        return $schema;
    }

    public function listByRecette($id_recette, ?string $search = null, string $sort = 'name_asc') {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $fk = $schema['fk'];
            $pk = $schema['pk'];
            $nameCol = $schema['name_col'] ?? 'nom';

            $sql = "SELECT * FROM ingredient WHERE `$fk` = :val";
            $params = ['val' => (int)$id_recette];
            if ($search !== null && $search !== '') {
                $sql .= " AND (`$nameCol` LIKE :q OR categorie LIKE :q)";
                $params['q'] = '%' . $search . '%';
            }

            switch ($sort) {
                case 'oldest':
                    $sql .= " ORDER BY `$pk` ASC";
                    break;
                case 'name_desc':
                    $sql .= " ORDER BY `$nameCol` DESC";
                    break;
                case 'name_asc':
                default:
                    $sql .= " ORDER BY `$nameCol` ASC";
                    break;
            }

            $query = $db->prepare($sql);
            $query->execute($params);
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);

            // normalize keys for views
            $out = [];
            $nutrients = $schema['nutrients'];
            foreach ($rows as $r) {
                $norm = $r;
                $norm['id_ingredient'] = $r[$pk] ?? ($r['id_ingredient'] ?? null);
                $norm['id_recette'] = $r[$fk] ?? ($r['id_recette'] ?? $id_recette);
                if ($schema['quantite_col']) $norm['quantite'] = $r[$schema['quantite_col']] ?? null;
                else $norm['quantite'] = $r['quantite'] ?? ($r['unite'] ?? null);
                foreach ($nutrients as $n) { $norm[$n] = isset($r[$n]) ? $r[$n] : 0; }
                $norm['unite'] = $r['unite'] ?? ($r[$schema['quantite_col']] ?? null);
                $norm['categorie'] = $r['categorie'] ?? '';
                $out[] = $norm;
            }
            return $out;
        } catch (Exception $e) {
            error_log('listByRecette error: ' . $e->getMessage());
            return [];
        }
    }

    public function addIngredient($i) {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $cols = [];
            $placeholders = [];
            $params = [];

            // foreign key
            $cols[] = $schema['fk']; $placeholders[] = ':fk'; $params['fk'] = $i->getIdRecette();
            // name
            $cols[] = 'nom'; $placeholders[] = ':nom'; $params['nom'] = $i->getNom();

            // quantite/unite
            if ($schema['quantite_col']) {
                $cols[] = $schema['quantite_col']; $placeholders[] = ':quantite';
                $params['quantite'] = $i->getQuantite() ?? '';
            }

            // nutrients: take values from model if provided, otherwise default 0
            foreach ($schema['nutrients'] as $n) {
                $cols[] = $n; $placeholders[] = ':' . $n;
                $getter = 'get' . ucfirst($n);
                $val = method_exists($i, $getter) ? $i->$getter() : null;
                $params[$n] = ($val !== null && $val !== '') ? $val : 0;
            }

            // categorie
            if ($schema['has_categorie']) { $cols[] = 'categorie'; $placeholders[] = ':categorie'; $params['categorie'] = $i->getCategorie() ?? ''; }

            $sql = "INSERT INTO ingredient (" . implode(', ', array_map(function($c){ return "`$c`"; }, $cols)) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $query = $db->prepare($sql);
            return $query->execute($params);
        } catch (Exception $e) {
            error_log('addIngredient error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateIngredient($i, $id) {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $pk = $schema['pk'];
            $sets = ['`nom` = :nom'];
            $params = ['nom' => $i->getNom(), 'id' => $id];
            if ($schema['quantite_col']) { $sets[] = "`{$schema['quantite_col']}` = :quantite"; $params['quantite'] = $i->getQuantite() ?? ''; }
            // nutrients
            foreach ($schema['nutrients'] as $n) {
                $sets[] = "`$n` = :$n";
                $getter = 'get' . ucfirst($n);
                $val = method_exists($i, $getter) ? $i->$getter() : null;
                $params[$n] = ($val !== null && $val !== '') ? $val : 0;
            }
            // categorie
            if ($schema['has_categorie']) { $sets[] = "`categorie` = :categorie"; $params['categorie'] = $i->getCategorie() ?? ''; }
            // foreign key update if needed
            if (in_array($schema['fk'], array_keys($this->detectSchema($db)))) {
                // noop - keep fk as-is unless model provides different id_recette
            }
            $sql = "UPDATE ingredient SET " . implode(', ', $sets) . " WHERE `$pk` = :id";
            $query = $db->prepare($sql);
            return $query->execute($params);
        } catch (Exception $e) {
            error_log('updateIngredient error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteIngredient($id) {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $pk = $schema['pk'];
            $sql = "DELETE FROM ingredient WHERE `$pk` = :id";
            $req = $db->prepare($sql);
            $req->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $req->execute();
            return true;
        } catch (Exception $e) {
            error_log('deleteIngredient error: ' . $e->getMessage());
            return false;
        }
    }

    public function getIngredient($id) {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $pk = $schema['pk'];
            $sql = "SELECT * FROM ingredient WHERE `$pk` = :id";
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $r = $query->fetch(PDO::FETCH_ASSOC);
            if (!$r) return null;
            $r['id_ingredient'] = $r[$pk] ?? ($r['id_ingredient'] ?? null);
            $r['id_recette'] = $r[$schema['fk']] ?? ($r['id_recette'] ?? null);
            if ($schema['quantite_col']) $r['quantite'] = $r[$schema['quantite_col']] ?? null;
            else $r['quantite'] = $r['quantite'] ?? ($r['unite'] ?? null);
            // nutrients
            foreach ($schema['nutrients'] as $n) { $r[$n] = isset($r[$n]) ? $r[$n] : 0; }
            $r['unite'] = $r['unite'] ?? ($r[$schema['quantite_col']] ?? null);
            $r['categorie'] = $r['categorie'] ?? '';
            return $r;
        } catch (Exception $e) {
            error_log('getIngredient error: ' . $e->getMessage());
            return null;
        }
    }

    public function listAll() {
        $db = config::getConnexion();
        try {
            $schema = $this->detectSchema($db);
            $pk = $schema['pk'];
            $fk = $schema['fk'];
            $sql = "SELECT * FROM ingredient ORDER BY `$pk` ASC";
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $out = [];
            $nutrients = $schema['nutrients'];
            foreach ($rows as $r) {
                $norm = $r;
                $norm['id_ingredient'] = $r[$pk] ?? ($r['id_ingredient'] ?? null);
                $norm['id_recette'] = $r[$fk] ?? ($r['id_recette'] ?? null);
                if ($schema['quantite_col']) $norm['quantite'] = $r[$schema['quantite_col']] ?? null;
                else $norm['quantite'] = $r['quantite'] ?? ($r['unite'] ?? null);
                foreach ($nutrients as $n) { $norm[$n] = isset($r[$n]) ? $r[$n] : 0; }
                $norm['unite'] = $r['unite'] ?? ($r[$schema['quantite_col']] ?? null);
                $norm['categorie'] = $r['categorie'] ?? '';
                $out[] = $norm;
            }
            return $out;
        } catch (Exception $e) {
            error_log('listAllIngredients error: ' . $e->getMessage());
            return [];
        }
    }
}
