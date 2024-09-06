    <?php
    class Empleado {
        private $conexion;

        public function __construct($db) {
            $this->conexion = $db;
        }

        // Crear empleado
        public function crearEmpleado($nombre, $email, $sexo, $area, $descripcion) {
            $sql = "INSERT INTO empleados (nombre, email, sexo, area_id, descripcion) VALUES (:nombre, :email, :sexo, :area_id, :descripcion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sexo', $sexo);
            $stmt->bindParam(':area_id', $area);
            $stmt->bindParam(':descripcion', $descripcion);
            return $stmt->execute();
        }

        // Listar todos los empleados
        public function listarEmpleados() {
            $sql = "SELECT E.nombre,E.email,A.nombre as area,E.boletin,E.descripcion,".
            "CASE
                WHEN E.sexo = 'M' THEN 'Masculino'
                WHEN E.sexo = 'F' THEN 'Femenino'
            END AS sexo ".
            "FROM  empleados AS E  INNER JOIN areas AS A ON A.id = E.area_id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        }

        // Obtener empleado por ID
        public function obtenerEmpleadoPorId($id) {
                $sql = "SELECT E.id, E.nombre, E.email, A.id as area, E.sexo, E.boletin, E.descripcion,R.id as roles FROM empleados as E INNER JOIN empleado_rol as Er ON Er.empleado_id = E.id INNER JOIN rol as R ON R.id = Er.rol_id INNER JOIN areas as A ON A.id = E.area_id WHERE E.id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Actualizar empleado
        public function actualizarEmpleado($id, $nombre, $email, $sexo, $area, $roles, $descripcion) {
            $sql = "UPDATE empleados SET nombre = :nombre, email = :email, sexo = :sexo, area_id = :area_id, descripcion = :descripcion WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sexo', $sexo);
            $stmt->bindParam(':area_id', $area);
            $stmt->bindParam(':descripcion', $descripcion);
            return $stmt->execute();
        }

        // Eliminar empleado
        public function eliminarEmpleado($id) {
            $sql = "DELETE FROM empleado_rol WHERE empleado_id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        
            $sql = "DELETE FROM empleados WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }

        // Asignar roles a un empleado
        public function asignarRolesEmpleado($empleadoId, $roles) {
            // Elimina roles previos
            $sql = "DELETE FROM empleado_rol WHERE empleado_id = :empleadoId";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':empleadoId', $empleadoId);
            $stmt->execute();
        
            // Asignar los nuevos roles
            foreach ($roles as $rolId) {
                $sql = "INSERT INTO empleado_rol (empleado_id, rol_id) VALUES (:empleadoId, :rolId)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':empleadoId', $empleadoId);
                $stmt->bindParam(':rolId', $rolId);
                $stmt->execute();
            }
        }

        // Listar áreas registradas
        public function listarAreas() {
            $sql = "SELECT nombre, id FROM areas";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $options = '<option value="">Elegir un área</option>';
            foreach ($areas as $area) {
                $options .= '<option value="' . $area['id'] . '">' . $area['nombre'] . '</option>';
            }
            return $options;
        }
    }
    ?>
