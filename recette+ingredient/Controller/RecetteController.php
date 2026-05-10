<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Recette.php';
require_once __DIR__ . '/../Model/Ingredient.php';

class RecetteController {

    public function listRecettes(?string $search = null, string $sort = 'newest') {
        $db = config::getConnexion();
        try {
            // detect name column
            $cols = array_column($db->query("DESCRIBE recette")->fetchAll(), 'Field');
            $nameCol = in_array('nom', $cols) ? 'nom' : 'titre';

            $sql = "SELECT * FROM recette";
            $params = [];
            if ($search !== null && $search !== '') {
                $sql .= " WHERE (`" . $nameCol . "` LIKE :q OR description LIKE :q)";
                $params['q'] = '%' . $search . '%';
            }

            // whitelist sort options
            switch ($sort) {
                case 'oldest':
                    $sql .= " ORDER BY id_recette ASC";
                    break;
                case 'name_asc':
                    $sql .= " ORDER BY `" . $nameCol . "` ASC";
                    break;
                case 'name_desc':
                    $sql .= " ORDER BY `" . $nameCol . "` DESC";
                    break;
                case 'newest':
                default:
                    $sql .= " ORDER BY id_recette DESC";
                    break;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('listRecettes error: ' . $e->getMessage());
            return [];
        }
    }

    public function ajouterRecette($r) {
        $db = config::getConnexion();
        try {
            // detect schema: prefer new columns if present
            $cols = array_column($db->query("DESCRIBE recette")->fetchAll(), 'Field');
            if (in_array('nom', $cols)) {
                // determine which difficulty column exists (accented or not)
                $dcol = null;
                if (in_array('difficulté', $cols)) {
                    $dcol = '`difficulté`';
                } elseif (in_array('difficulte', $cols)) {
                    $dcol = '`difficulte`';
                }

                $columns = ['`nom`', '`description`', '`instruction`', '`temp_preparation`', '`temp_cuisson`', '`nombre_portion`'];
                $placeholders = [':nom', ':description', ':instruction', ':temp_preparation', ':temp_cuisson', ':nombre_portion'];
                if ($dcol) { $columns[] = $dcol; $placeholders[] = ':difficulte'; }
                $columns[] = '`image`'; $placeholders[] = ':image';

                $sql = "INSERT INTO recette (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
                $query = $db->prepare($sql);
                    $params = [
                        'nom' => $r->getNom(),
                        'description' => $r->getDescription(),
                        'instruction' => $r->getInstruction(),
                        'temp_preparation' => $r->getTempPreparation(),
                        'temp_cuisson' => $r->getTempCuisson(),
                        'nombre_portion' => $r->getNombrePortion(),
                        'image' => $r->getImage() ?? ''
                    ];
                if ($dcol) { $params['difficulte'] = $r->getDifficulte(); }
                return $query->execute($params);
            } else {
                // fallback to legacy schema (titre/instructions/image)
                $sql = "INSERT INTO recette (titre, description, instructions, image) VALUES (:titre, :description, :instructions, :image)";
                $query = $db->prepare($sql);
                    return $query->execute([
                        'titre' => $r->getNom(),
                        'description' => $r->getDescription(),
                        'instructions' => $r->getInstruction(),
                        'image' => $r->getImage() ?? ''
                    ]);
            }
        } catch (Exception $e) {
            error_log('ajouterRecette error: ' . $e->getMessage());
            return false;
        }
    }

    public function modifierRecette($r, $id) {
        $db = config::getConnexion();
        try {
            $cols = array_column($db->query("DESCRIBE recette")->fetchAll(), 'Field');
            if (in_array('nom', $cols)) {
                // detect difficulty column spelling
                $dcol = null;
                if (in_array('difficulté', $cols)) {
                    $dcol = '`difficulté`';
                } elseif (in_array('difficulte', $cols)) {
                    $dcol = '`difficulte`';
                }

                $sets = ['`nom` = :nom', '`description` = :description', '`instruction` = :instruction', '`temp_preparation` = :temp_preparation', '`temp_cuisson` = :temp_cuisson', '`nombre_portion` = :nombre_portion'];
                if ($dcol) { $sets[] = $dcol . ' = :difficulte'; }
                $sets[] = '`image` = :image';

                $sql = "UPDATE recette SET " . implode(', ', $sets) . " WHERE id_recette=:id";
                $query = $db->prepare($sql);
                    $params = [
                        'id' => $id,
                        'nom' => $r->getNom(),
                        'description' => $r->getDescription(),
                        'instruction' => $r->getInstruction(),
                        'temp_preparation' => $r->getTempPreparation(),
                        'temp_cuisson' => $r->getTempCuisson(),
                        'nombre_portion' => $r->getNombrePortion(),
                        'image' => $r->getImage() ?? ''
                    ];
                if ($dcol) { $params['difficulte'] = $r->getDifficulte(); }
                $query->execute($params);
            } else {
                // legacy
                $sql = "UPDATE recette SET titre=:titre, description=:description, instructions=:instructions, image=:image WHERE id_recette=:id";
                $query = $db->prepare($sql);
                    $query->execute([
                        'id' => $id,
                        'titre' => $r->getNom(),
                        'description' => $r->getDescription(),
                        'instructions' => $r->getInstruction(),
                        'image' => $r->getImage() ?? ''
                    ]);
            }
            return true;
        } catch (Exception $e) {
            error_log('modifierRecette error: ' . $e->getMessage());
            return false;
        }
    }

    public function supprimerRecette($id) {
        $sql = "DELETE FROM recette WHERE id_recette = :id";
        $db = config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $req->execute();
            return true;
        } catch (Exception $e) {
            error_log('supprimerRecette error: ' . $e->getMessage());
            return false;
        }
    }

    public function showRecette($id) {
        $sql = "SELECT * FROM recette WHERE id_recette = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log('showRecette error: ' . $e->getMessage());
            return null;
        }
    }

    public function saveUploadedImage($file) {
        if (!isset($file) || $file['error'] !== 0) {
            return null;
        }
        $folder = __DIR__ . "/../View/FrontOffice/images/uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = time() . '_' . uniqid() . '.' . $ext;
        $path = $folder . $name;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return null;
        }
        return "images/uploads/" . $name;
    }
}
