<?php
require_once __DIR__ . '/../config.php';

class Ingredient {
    private ?int $id_ingredient;
    private ?int $id_recette;
    private ?string $nom;
    private ?string $quantite;
    private ?string $unite;
    private ?string $categorie;
    private ?float $calories;
    private ?float $proteines;
    private ?float $glucides;
    private ?float $lipides;
    private ?float $fibres;
    private ?float $sucre;
    private ?float $sel;

    public function __construct(
        ?int $id_ingredient = null,
        ?int $id_recette = null,
        ?string $nom = null,
        ?string $quantite = null,
        ?string $unite = null,
        ?string $categorie = null,
        ?float $calories = null,
        ?float $proteines = null,
        ?float $glucides = null,
        ?float $lipides = null,
        ?float $fibres = null,
        ?float $sucre = null,
        ?float $sel = null
    ) {
        $this->id_ingredient = $id_ingredient;
        $this->id_recette = $id_recette;
        $this->nom = $nom;
        $this->quantite = $quantite;
        $this->unite = $unite;
        $this->categorie = $categorie;
        $this->calories = $calories;
        $this->proteines = $proteines;
        $this->glucides = $glucides;
        $this->lipides = $lipides;
        $this->fibres = $fibres;
        $this->sucre = $sucre;
        $this->sel = $sel;
    }

    public function save(): bool {
        $pdo = config::getConnexion();
        try {
            $desc = $pdo->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
            $fields = array_column($desc, 'Field');
            $pk = in_array('id_ingredient', $fields) ? 'id_ingredient' : (in_array('id_ingre', $fields) ? 'id_ingre' : $fields[0]);
            $fk = in_array('id_recette', $fields) ? 'id_recette' : (in_array('id_rec', $fields) ? 'id_rec' : 'id_recette');

            $nutrients = ['calories','proteines','glucides','lipides','fibres','sucre','sel'];

            if ($this->id_ingredient === null) {
                $cols = [$fk, 'nom'];
                $placeholders = [':fk', ':nom'];
                $params = ['fk' => $this->id_recette, 'nom' => $this->nom];

                if (in_array('quantite', $fields)) { $cols[] = 'quantite'; $placeholders[] = ':quantite'; $params['quantite'] = $this->quantite ?? null; }
                if (in_array('unite', $fields)) { $cols[] = 'unite'; $placeholders[] = ':unite'; $params['unite'] = $this->unite ?? null; }

                foreach ($nutrients as $n) {
                    if (in_array($n, $fields)) { $cols[] = $n; $placeholders[] = ':' . $n; $params[$n] = $this->{$n} ?? 0; }
                }

                if (in_array('categorie', $fields)) { $cols[] = 'categorie'; $placeholders[] = ':categorie'; $params['categorie'] = $this->categorie ?? null; }

                $sql = "INSERT INTO ingredient (" . implode(', ', array_map(function($c){ return "`$c`"; }, $cols)) . ") VALUES (" . implode(', ', $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $this->id_ingredient = (int)$pdo->lastInsertId();
            } else {
                $sets = ['`nom` = :nom'];
                $params = ['nom' => $this->nom, 'id' => $this->id_ingredient];
                if (in_array('quantite', $fields)) { $sets[] = '`quantite` = :quantite'; $params['quantite'] = $this->quantite ?? null; }
                if (in_array('unite', $fields)) { $sets[] = '`unite` = :unite'; $params['unite'] = $this->unite ?? null; }

                foreach ($nutrients as $n) {
                    if (in_array($n, $fields)) { $sets[] = "`$n` = :$n"; $params[$n] = $this->{$n} ?? 0; }
                }

                if (in_array('categorie', $fields)) { $sets[] = '`categorie` = :categorie'; $params['categorie'] = $this->categorie ?? null; }
                if (in_array($fk, $fields)) { $sets[] = "`$fk` = :fkval"; $params['fkval'] = $this->id_recette; }

                $sql = "UPDATE ingredient SET " . implode(', ', $sets) . " WHERE `$pk` = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            return true;
        } catch (PDOException $e) {
            error_log('Ingredient save error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(): bool {
        if ($this->id_ingredient !== null) {
            try {
                $pdo = config::getConnexion();
                $desc = $pdo->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
                $fields = array_column($desc, 'Field');
                $pk = in_array('id_ingredient', $fields) ? 'id_ingredient' : (in_array('id_ingre', $fields) ? 'id_ingre' : $fields[0]);
                $stmt = $pdo->prepare("DELETE FROM ingredient WHERE `$pk` = :id");
                $stmt->execute(['id' => $this->id_ingredient]);
                return true;
            } catch (PDOException $e) {
                error_log('Ingredient delete error: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function findById(int $id): ?Ingredient {
        $pdo = config::getConnexion();
        $desc = $pdo->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
        $fields = array_column($desc, 'Field');
        $pk = in_array('id_ingredient', $fields) ? 'id_ingredient' : (in_array('id_ingre', $fields) ? 'id_ingre' : $fields[0]);
        $stmt = $pdo->prepare("SELECT * FROM ingredient WHERE `$pk` = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? self::fromArray($data) : null;
    }

    public static function findByRecetteId(int $id_recette): array {
        $pdo = config::getConnexion();
        $desc = $pdo->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
        $fields = array_column($desc, 'Field');
        $fk = in_array('id_recette', $fields) ? 'id_recette' : (in_array('id_rec', $fields) ? 'id_rec' : 'id_recette');
        $pk = in_array('id_ingredient', $fields) ? 'id_ingredient' : (in_array('id_ingre', $fields) ? 'id_ingre' : $fields[0]);
        $stmt = $pdo->prepare("SELECT * FROM ingredient WHERE `$fk` = ? ORDER BY `$pk` ASC");
        $stmt->execute([$id_recette]);
        $rows = $stmt->fetchAll();
        $list = [];
        foreach ($rows as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }

    private static function fromArray(array $data): Ingredient {
        $id = $data['id_ingredient'] ?? $data['id_ingre'] ?? null;
        $rec = $data['id_recette'] ?? $data['id_rec'] ?? null;
        $nom = $data['nom'] ?? null;
        $quant = $data['quantite'] ?? $data['unite'] ?? null;

        $ing = new Ingredient(
            $id !== null ? (int)$id : null,
            $rec !== null ? (int)$rec : null,
            $nom,
            $quant
        );

        $ing->setUnite($data['unite'] ?? null);
        $ing->setCategorie($data['categorie'] ?? null);
        $ing->setCalories(isset($data['calories']) ? (float)$data['calories'] : null);
        $ing->setProteines(isset($data['proteines']) ? (float)$data['proteines'] : null);
        $ing->setGlucides(isset($data['glucides']) ? (float)$data['glucides'] : null);
        $ing->setLipides(isset($data['lipides']) ? (float)$data['lipides'] : null);
        $ing->setFibres(isset($data['fibres']) ? (float)$data['fibres'] : null);
        $ing->setSucre(isset($data['sucre']) ? (float)$data['sucre'] : null);
        $ing->setSel(isset($data['sel']) ? (float)$data['sel'] : null);

        return $ing;
    }

    // Getters
    public function getIdIngredient(): ?int { return $this->id_ingredient; }
    public function getIdRecette(): ?int { return $this->id_recette; }
    public function getNom(): ?string { return $this->nom; }
    public function getQuantite(): ?string { return $this->quantite; }
    public function getUnite(): ?string { return $this->unite; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function getCalories(): ?float { return $this->calories; }
    public function getProteines(): ?float { return $this->proteines; }
    public function getGlucides(): ?float { return $this->glucides; }
    public function getLipides(): ?float { return $this->lipides; }
    public function getFibres(): ?float { return $this->fibres; }
    public function getSucre(): ?float { return $this->sucre; }
    public function getSel(): ?float { return $this->sel; }

    // Setters
    public function setIdIngredient(?int $v) { $this->id_ingredient = $v; }
    public function setIdRecette(?int $v) { $this->id_recette = $v; }
    public function setNom(?string $v) { $this->nom = $v; }
    public function setQuantite(?string $v) { $this->quantite = $v; }
    public function setUnite(?string $v) { $this->unite = $v; }
    public function setCategorie(?string $v) { $this->categorie = $v; }
    public function setCalories(?float $v) { $this->calories = $v; }
    public function setProteines(?float $v) { $this->proteines = $v; }
    public function setGlucides(?float $v) { $this->glucides = $v; }
    public function setLipides(?float $v) { $this->lipides = $v; }
    public function setFibres(?float $v) { $this->fibres = $v; }
    public function setSucre(?float $v) { $this->sucre = $v; }
    public function setSel(?float $v) { $this->sel = $v; }

    public static function findAll(): array {
        $pdo = config::getConnexion();
        $desc = $pdo->query("DESCRIBE ingredient")->fetchAll(PDO::FETCH_ASSOC);
        $fields = array_column($desc, 'Field');
        $pk = in_array('id_ingredient', $fields) ? 'id_ingredient' : (in_array('id_ingre', $fields) ? 'id_ingre' : $fields[0]);
        $stmt = $pdo->prepare("SELECT * FROM ingredient ORDER BY `$pk` ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $list = [];
        foreach ($rows as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }
}
