<?php
require 'db.php';

class Category {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllCategories() {
        try {
            $stmt = $this->db->query("SELECT ID_categoria AS id, nombre AS name FROM categorias");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las categorías: " . $e->getMessage());
        }
    }
}
?>
