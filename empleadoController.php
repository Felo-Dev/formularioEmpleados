<?php
require_once 'Empleado.php';
require_once 'database.php';
require_once 'RolesModel.php';

$empleado = new Empleado($db);
$rolesModel = new RolesModel($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear']) || isset($_POST['editar'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $sexo = trim($_POST['sexo']);
        $area = trim($_POST['area']);
        $roles = $_POST['roles'];
        $descripcion = trim($_POST['descripcion']);
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;

        if (empty($nombre) || empty($email) || empty($sexo) || empty($area) || empty($descripcion)) {
            $error = "Todos los campos son obligatorios."+ $roles;
        } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El correo electrónico no es válido.";
        }
        elseif (empty($roles) || !is_array($roles)) {
            $error = "Debe seleccionar al menos un rol.";
        }
        elseif (!preg_match('/^[0-9]+$/', $area)) {
                $error = "El área debe ser un número.";
        }

        if (isset($error)) {
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }

        if (isset($_POST['crear'])) {
            $empleado->crearEmpleado($nombre, $email, $sexo, $area, $descripcion);
            $empleado->asignarRolesEmpleado($db->lastInsertId(), $roles);
            echo json_encode(['success' => true, 'message' => 'Empleado creado']);
            exit;
        } elseif (isset($_POST['editar'])) {
            $empleado->actualizarEmpleado($id, $nombre, $email, $sexo, $area, $roles, $descripcion);
            $empleado->asignarRolesEmpleado($id, $roles);
            echo json_encode(['success' => true, 'message' => 'Empleado actualizado']);
            exit;
        }
    } elseif (isset($_POST['eliminar'])) {
        $id = trim($_POST['id']);
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de empleado no proporcionado.']);
            exit;
        }
        $resultado = $empleado->eliminarEmpleado($id);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Empleado eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el empleado']);
        }
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['listar']) && $_GET['listar'] == 'true') {
        $empleados = $empleado->listarEmpleados();

        echo json_encode($empleados);
        exit;
    } elseif (isset($_GET['id'])) {
        $id = trim($_GET['id']);
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de empleado no proporcionado.']);
            exit;
        }
        $empleadoData = $empleado->obtenerEmpleadoPorId($id);

        echo json_encode($empleadoData);
        exit;
    }
}
?>
