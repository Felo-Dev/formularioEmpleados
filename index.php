<?php
require 'Empleado.php';
require 'database.php'; // Archivo para la conexión a la base de datos
require 'RolesModel.php';

$empleado = new Empleado($db);
$rolesModel = new RolesModel($db);
$empleados = $empleado->listarEmpleados();
$empleadoEditar = null;

if (isset($_GET['editar'])) {
    $empleadoEditar = $empleado->obtenerEmpleadoPorId($_GET['editar']);
}

// Obtener roles para la vista
$roles = $rolesModel->getRoles();

if ($roles === false) {
    die("Error al obtener roles.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>CRUD Empleados</title>
</head>

<body>
    <div class="container mt-5">
        <h1><?= $empleadoEditar ? 'Editar Empleado' : 'Crear Empleado' ?></h1>
        <!-- Formulario para crear o editar un empleado -->
        <form action="empleadoController.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($empleadoEditar['id'] ?? ''); ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo:</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?= htmlspecialchars($empleadoEditar['nombre'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?= htmlspecialchars($empleadoEditar['email'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="sexo" class="form-label">Sexo:</label>
                <select class="form-select" id="sexo" name="sexo" required>
                    <option value="" <?= !isset($empleadoEditar['sexo']) ? 'selected' : ''; ?>>Elegir sexo</option>
                    <option value="M"
                        <?= (isset($empleadoEditar['sexo']) && $empleadoEditar['sexo'] == 'M') ? 'selected' : ''; ?>>
                        Masculino</option>
                    <option value="F"
                        <?= (isset($empleadoEditar['sexo']) && $empleadoEditar['sexo'] == 'F') ? 'selected' : ''; ?>>
                        Femenino</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="area" class="form-label">Área:</label>
                <select class="form-select" id="area" name="area" required>
                    <?= $empleado->listarAreas(); ?>
                </select>
            </div>


            <div class="mb-3">
                <label for="boletin" class="form-label">Deseo recibir boletín informativo</label>
                <div class="form-check">
                    <?php
                    $boletin = isset($empleadoEditar['boletin']) ? htmlspecialchars($empleadoEditar['boletin']) : '';
                    $checked = isset($empleadoEditar['boletin']) && in_array($boletin, explode(',', $empleadoEditar['boletin'])) ? 'checked' : '';
                    ?>
                    <input class="form-check-input" type="checkbox" id="rol<?= $boletin; ?>" name="boletin[]"
                        value="<?= $boletin; ?>" <?= $checked; ?>>
                    <label class="form-check-label" for="rol<?= $boletin; ?>">
                        <?= $boletin; ?>
                    </label>
                </div>






                <div class="mb-3">
                    <label class="form-label">Roles:</label>
                    <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $row): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rol<?= htmlspecialchars($row['id']); ?>"
                            name="roles[]" value="<?= htmlspecialchars($row['id']); ?>"
                            <?= isset($empleadoEditar['roles']) && in_array($row['id'], explode(',', $empleadoEditar['roles'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="rol<?= htmlspecialchars($row['id']); ?>">
                            <?= htmlspecialchars($row['nombre']); ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p>No hay roles disponibles.</p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"
                        rows="3"><?= htmlspecialchars($empleadoEditar['descripcion'] ?? ''); ?></textarea>
                </div>



                <?php if ($empleadoEditar): ?>
                <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
                <?php else: ?>
                <button type="submit" name="crear" class="btn btn-primary">Crear</button>
                <?php endif; ?>

        </form>

        <h2 class="mt-5">Lista de Empleados</h2>
        <table class="table" id="tabla-empleados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Sexo</th>
                    <th>Área</th>
                    <th>Descripción</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?= htmlspecialchars($empleado['id']); ?></td>
                    <td><?= htmlspecialchars($empleado['nombre']); ?></td>
                    <td><?= htmlspecialchars($empleado['email']); ?></td>
                    <td><?= htmlspecialchars($empleado['sexo']); ?></td>
                    <td><?= htmlspecialchars($empleado['area_id']); ?></td>
                    <td><?= htmlspecialchars($empleado['descripcion']); ?></td>
                    <td>
                        <a href="index.php?editar=<?= htmlspecialchars($empleado['id']); ?>"
                            class="btn btn-warning btn-sm">Editar</a>
                        <form action="empleadoController.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($empleado['id']); ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Está seguro de que desea eliminar este empleado?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="./empleadoScript.js"></script>
</body>

</html>