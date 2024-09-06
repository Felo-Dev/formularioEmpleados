<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=empleados', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    die();
}
?>
