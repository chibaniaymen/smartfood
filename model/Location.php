<?php
class Location {
    private ?int $id = null;
    private ?string $name = null;
    private ?string $address = null;
    private ?string $city = null;
    private ?string $country = null;
    private ?int $capacity = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->city = $data['city'] ?? null;
            $this->country = $data['country'] ?? null;
            $this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
        }
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(string $city): void { $this->city = $city; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(string $country): void { $this->country = $country; }
    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }
}
?>
