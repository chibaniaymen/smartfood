<?php
class User
{
    public static function getByEmail($email)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user ?: null;
    }

    public static function getById($id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user ?: null;
    }

    public static function getAll()
    {
        $conn = Database::connect();
        $result = $conn->query('SELECT * FROM users ORDER BY role DESC, name ASC');
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $conn->close();
        return $users;
    }

    public static function create($data)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param('ssss', $data['name'], $data['email'], $hash, $data['role']);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function update($id, $data)
    {
        $conn = Database::connect();
        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?');
            $stmt->bind_param('ssssi', $data['name'], $data['email'], $hash, $data['role'], $id);
        } else {
            $stmt = $conn->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
            $stmt->bind_param('sssi', $data['name'], $data['email'], $data['role'], $id);
        }
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function delete($id)
    {
        $conn = Database::connect();
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function authenticate($email, $password)
    {
        $user = self::getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    public static function current()
    {
        return $_SESSION['user'] ?? null;
    }
}
