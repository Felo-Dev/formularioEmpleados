<?php
class RolesModel {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Obtener roles
    public function getRoles() {
        $sql = "SELECT * FROM rol";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
