<?php
class Product
{
    public static function getAll()
    {
        $conn = Database::connect();
        $result = $conn->query('SELECT p.*, r.name AS restaurant_name FROM products p LEFT JOIN restaurants r ON p.restaurant_id = r.id ORDER BY p.name ASC');
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $conn->close();
        return $products;
    }

    public static function getById($id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $product ?: null;
    }

    public static function create($data)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('INSERT INTO products (restaurant_id, name, category, display_category, price, unit, description, badge, emoji) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssdssss', $data['restaurant_id'], $data['name'], $data['category'], $data['display_category'], $data['price'], $data['unit'], $data['description'], $data['badge'], $data['emoji']);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function update($id, $data)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('UPDATE products SET restaurant_id = ?, name = ?, category = ?, display_category = ?, price = ?, unit = ?, description = ?, badge = ?, emoji = ? WHERE id = ?');
        $stmt->bind_param('isssdssssi', $data['restaurant_id'], $data['name'], $data['category'], $data['display_category'], $data['price'], $data['unit'], $data['description'], $data['badge'], $data['emoji'], $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function delete($id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
}
