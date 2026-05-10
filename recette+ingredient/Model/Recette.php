<?php
require_once __DIR__ . '/../config.php';

class Recette {
    private ?int $id_recette;
    private ?string $nom;
    private ?string $description;
    private ?string $instruction;
    private ?int $temp_preparation;
    private ?int $temp_cuisson;
    private ?int $nombre_portion;
    private ?string $difficulte;
    private ?string $image;

    public function __construct(?int $id_recette = null, ?string $nom = null, ?string $description = null, ?string $instruction = null, ?int $temp_preparation = null, ?int $temp_cuisson = null, ?int $nombre_portion = null, ?string $difficulte = null, ?string $image = null) {
        $this->id_recette = $id_recette;
        $this->nom = $nom;
        $this->description = $description;
        $this->instruction = $instruction;
        $this->temp_preparation = $temp_preparation;
        $this->temp_cuisson = $temp_cuisson;
        $this->nombre_portion = $nombre_portion;
        $this->difficulte = $difficulte;
        $this->image = $image;
    }

    public function save(): bool {
        $pdo = config::getConnexion();
        try {
            $cols = array_column($pdo->query("DESCRIBE recette")->fetchAll(), 'Field');
            if (in_array('nom', $cols)) {
                $dcol = null;
                if (in_array('difficulté', $cols)) { $dcol = '`difficulté`'; }
                elseif (in_array('difficulte', $cols)) { $dcol = '`difficulte`'; }

                if ($this->id_recette === null) {
                    $columns = ['`nom`', '`description`', '`instruction`', '`temp_preparation`', '`temp_cuisson`', '`nombre_portion`'];
                    $placeholders = [':nom', ':description', ':instruction', ':temp_preparation', ':temp_cuisson', ':nombre_portion'];
                    if ($dcol) { $columns[] = $dcol; $placeholders[] = ':difficulte'; }
                    $columns[] = '`image`'; $placeholders[] = ':image';

                    $sql = "INSERT INTO recette (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $stmt = $pdo->prepare($sql);
                    $params = [
                        'nom' => $this->nom,
                        'description' => $this->description,
                        'instruction' => $this->instruction,
                        'temp_preparation' => $this->temp_preparation,
                        'temp_cuisson' => $this->temp_cuisson,
                        'nombre_portion' => $this->nombre_portion,
                        'image' => $this->image ?? ''
                    ];
                    if ($dcol) { $params['difficulte'] = $this->difficulte; }
                    $stmt->execute($params);
                    $this->id_recette = (int)$pdo->lastInsertId();
                } else {
                    $sets = ['`nom` = :nom', '`description` = :description', '`instruction` = :instruction', '`temp_preparation` = :temp_preparation', '`temp_cuisson` = :temp_cuisson', '`nombre_portion` = :nombre_portion'];
                    if ($dcol) { $sets[] = $dcol . ' = :difficulte'; }
                    $sets[] = '`image` = :image';

                    $sql = "UPDATE recette SET " . implode(', ', $sets) . " WHERE id_recette=:id";
                    $stmt = $pdo->prepare($sql);
                    $params = [
                        'nom' => $this->nom,
                        'description' => $this->description,
                        'instruction' => $this->instruction,
                        'temp_preparation' => $this->temp_preparation,
                        'temp_cuisson' => $this->temp_cuisson,
                        'nombre_portion' => $this->nombre_portion,
                        'image' => $this->image ?? '',
                        'id' => $this->id_recette
                    ];
                    if ($dcol) { $params['difficulte'] = $this->difficulte; }
                    $stmt->execute($params);
                }
            } else {
                if ($this->id_recette === null) {
                    $stmt = $pdo->prepare("INSERT INTO recette (titre, description, instructions, image) VALUES (:titre, :description, :instructions, :image)");
                    $stmt->execute([
                        'titre' => $this->nom,
                        'description' => $this->description,
                        'instructions' => $this->instruction,
                        'image' => $this->image
                    ]);
                    $this->id_recette = (int)$pdo->lastInsertId();
                } else {
                    $stmt = $pdo->prepare("UPDATE recette SET titre=:titre, description=:description, instructions=:instructions, image=:image WHERE id_recette=:id");
                    $stmt->execute([
                        'titre' => $this->nom,
                        'description' => $this->description,
                        'instructions' => $this->instruction,
                        'image' => $this->image,
                        'id' => $this->id_recette
                    ]);
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log('Recette save error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(): bool {
        if ($this->id_recette !== null) {
            try {
                $pdo = config::getConnexion();
                $stmt = $pdo->prepare("DELETE FROM recette WHERE id_recette = :id");
                $stmt->execute(['id' => $this->id_recette]);
                return true;
            } catch (PDOException $e) {
                error_log('Recette delete error: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function findById(int $id): ?Recette {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM recette WHERE id_recette = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? self::fromArray($data) : null;
    }

    public static function findAll(): array {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM recette ORDER BY id_recette DESC");
        $rows = $stmt->fetchAll();
        $list = [];
        foreach ($rows as $row) {
            $list[] = self::fromArray($row);
        }
        return $list;
    }

    private static function fromArray(array $data): Recette {
        $nom = $data['nom'] ?? $data['titre'] ?? null;
        $instruction = $data['instruction'] ?? $data['instructions'] ?? null;
        return new Recette(
            isset($data['id_recette']) ? (int)$data['id_recette'] : null,
            $nom,
            $data['description'] ?? null,
            $instruction,
            isset($data['temp_preparation']) ? (int)$data['temp_preparation'] : null,
            isset($data['temp_cuisson']) ? (int)$data['temp_cuisson'] : null,
            isset($data['nombre_portion']) ? (int)$data['nombre_portion'] : null,
            $data['difficulte'] ?? ($data['difficulté'] ?? null),
            $data['image'] ?? null
        );
    }

    public function getIdRecette(): ?int { return $this->id_recette; }
    public function getNom(): ?string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getInstruction(): ?string { return $this->instruction; }
    public function getTempPreparation(): ?int { return $this->temp_preparation; }
    public function getTempCuisson(): ?int { return $this->temp_cuisson; }
    public function getNombrePortion(): ?int { return $this->nombre_portion; }
    public function getDifficulte(): ?string { return $this->difficulte; }
    public function getImage(): ?string { return $this->image; }

    public function setIdRecette(?int $v) { $this->id_recette = $v; }
    public function setNom(?string $v) { $this->nom = $v; }
    public function setDescription(?string $v) { $this->description = $v; }
    public function setInstruction(?string $v) { $this->instruction = $v; }
    public function setTempPreparation(?int $v) { $this->temp_preparation = $v; }
    public function setTempCuisson(?int $v) { $this->temp_cuisson = $v; }
    public function setNombrePortion(?int $v) { $this->nombre_portion = $v; }
    public function setDifficulte(?string $v) { $this->difficulte = $v; }
    public function setImage(?string $v) { $this->image = $v; }
}
