<?php
class Database
{
    public static function connect()
    {
        if (!function_exists('mysql_connect')) {
            die('Legacy MySQL extension is not available. Enable mysql or use PHP 5.x.');
        }

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        if (!$conn) {
            die('Erreur de connexion MySQL : ' . mysql_error());
        }
        if (!mysql_select_db(DB_NAME, $conn)) {
            die('Erreur de sélection de la base : ' . mysql_error());
        }
        mysql_set_charset('utf8mb4', $conn);
        return $conn;
    }
}
