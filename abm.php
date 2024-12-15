<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Redirige si no hay sesión activa
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username_db = "root";  // Usuario de la base de datos
$password_db = "";  // Contraseña de la base de datos
$dbname = "mi_base_de_datos";  // Nombre de la base de datos

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para los datos del formulario
$nombre = $apellido = $dni = $edad = $nivel_estudios = "";

// Acción de buscar
$search_dni = ""; // Para almacenar el DNI de búsqueda
$search_nivel_estudios = ""; // Para almacenar el filtro por nivel de estudios

if (isset($_GET['search_dni'])) {
    $search_dni = $_GET['search_dni'];
}

if (isset($_GET['search_nivel_estudios'])) {
    $search_nivel_estudios = $_GET['search_nivel_estudios'];
}

// Parámetro de ordenación
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Orden por defecto: ascendente

// Acciones de eliminar, agregar y editar
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'eliminar' && isset($_GET['id'])) {
        // Eliminar alumno
        $id = $_GET['id'];
        $sql_delete = "DELETE FROM alumnos WHERE id = $id";
        if ($conn->query($sql_delete) === TRUE) {
            echo "<div class='alert alert-success'>Alumno eliminado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar el alumno.</div>";
        }
    } elseif ($_GET['action'] == 'agregar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        // Agregar alumno
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $dni = $_POST['dni'];
        $edad = $_POST['edad'];
        $nivel_estudios = $_POST['nivel_estudios'];
        $sql_insert = "INSERT INTO alumnos (nombre, apellido, dni, edad, nivel_estudios) 
                       VALUES ('$nombre', '$apellido', '$dni', $edad, '$nivel_estudios')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "<div class='alert alert-success'>Alumno agregado correctamente.</div>";
            // Redirigir para limpiar los campos del formulario
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error al agregar el alumno.</div>";
        }
    } elseif ($_GET['action'] == 'editar' && isset($_GET['id'])) {
        // Editar alumno
        $id = $_GET['id'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $dni = $_POST['dni'];
            $edad = $_POST['edad'];
            $nivel_estudios = $_POST['nivel_estudios'];
            $sql_update = "UPDATE alumnos SET nombre = '$nombre', apellido = '$apellido', dni = '$dni', 
                          edad = $edad, nivel_estudios = '$nivel_estudios' WHERE id = $id";
            if ($conn->query($sql_update) === TRUE) {
                echo "<div class='alert alert-success'>Alumno actualizado correctamente.</div>";
                // Redirigir para limpiar los campos del formulario
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error al actualizar el alumno.</div>";
            }
        }
        // Obtener los datos del alumno a editar
        $sql_edit = "SELECT * FROM alumnos WHERE id = $id";
        $result_edit = $conn->query($sql_edit);
        if ($result_edit->num_rows > 0) {
            $row = $result_edit->fetch_assoc();
            $nombre = $row['nombre'];
            $apellido = $row['apellido'];
            $dni = $row['dni'];
            $edad = $row['edad'];
            $nivel_estudios = $row['nivel_estudios'];
        }
    }
}

// Modificar la consulta SQL para la búsqueda por DNI y ordenación
$sql = "SELECT * FROM alumnos WHERE 1";
if (!empty($search_dni)) {
    $sql .= " AND dni LIKE '%$search_dni%'";
}
if (!empty($search_nivel_estudios)) {
    $sql .= " AND nivel_estudios = '$search_nivel_estudios'";
}
$sql .= " ORDER BY edad " . ($order === 'desc' ? 'DESC' : 'ASC');

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM de Alumnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="abm.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Gestión de Alumnos</h2>
        <a href="logout.php" class="btn btn-danger mb-3">Cerrar Sesión</a>

        <!-- Formulario de búsqueda por DNI y filtro por nivel de estudio -->
<form action="" method="GET" class="mb-3 d-flex">
    <div class="input-group me-3">
        <input type="text" name="search_dni" class="form-control" placeholder="Buscar por DNI" value="<?php echo $search_dni; ?>">
    </div>
    
    <div class="input-group me-3">
        <select name="search_nivel_estudios" class="form-select" aria-label="Filtrar por Nivel de Estudios">
            <option value="">Seleccionar nivel</option>
            <option value="Primaria" <?php echo ($search_nivel_estudios == 'Primaria') ? 'selected' : ''; ?>>Primaria</option>
            <option value="Secundaria" <?php echo ($search_nivel_estudios == 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
            <option value="Universidad" <?php echo ($search_nivel_estudios == 'Universidad') ? 'selected' : ''; ?>>Universidad</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>

        <!-- Formulario para agregar o editar alumno -->
        <h3><?php echo (isset($_GET['action']) && $_GET['action'] == 'editar') ? 'Editar Alumno' : 'Agregar Alumno'; ?></h3>
        <form action="?action=<?php echo (isset($_GET['action']) && $_GET['action'] == 'editar') ? 'editar&id='.$_GET['id'] : 'agregar'; ?>" method="POST">

        <div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control" 
           value="<?php echo isset($nombre) ? $nombre : ''; ?>" required 
           minlength="3" maxlength="50" pattern="[A-Za-záéíóúÁÉÍÓÚ]+" 
           title="El nombre solo puede contener letras">
</div>

<div class="mb-3">
    <label for="apellido" class="form-label">Apellido</label>
    <input type="text" name="apellido" id="apellido" class="form-control" 
           value="<?php echo isset($apellido) ? $apellido : ''; ?>" required 
           minlength="3" maxlength="50" pattern="[A-Za-záéíóúÁÉÍÓÚ]+" 
           title="El apellido solo puede contener letras">
</div>

<div class="mb-3">
    <label for="dni" class="form-label">DNI</label>
    <input type="text" name="dni" id="dni" class="form-control" 
           value="<?php echo isset($dni) ? $dni : ''; ?>" required 
           pattern="^[0-9]{8}$" title="El DNI debe ser un número de exactamente 8 dígitos" 
           maxlength="8">
</div>

<div class="mb-3">
    <label for="edad" class="form-label">Edad</label>
    <input type="number" name="edad" id="edad" class="form-control" 
           value="<?php echo isset($edad) ? $edad : ''; ?>" required 
           min="6" max="100" title="La edad debe ser un número entre 18 y 100">
</div>

<div class="mb-3">
    <label for="nivel_estudios" class="form-label">Nivel de Estudios</label>
    <select name="nivel_estudios" id="nivel_estudios" class="form-control" required>
        <option value="Primaria" <?php echo (isset($nivel_estudios) && $nivel_estudios == 'Primaria') ? 'selected' : ''; ?>>Primaria</option>
        <option value="Secundaria" <?php echo (isset($nivel_estudios) && $nivel_estudios == 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
        <option value="Universidad" <?php echo (isset($nivel_estudios) && $nivel_estudios == 'Universidad') ? 'selected' : ''; ?>>Universidad</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">
    <?php echo (isset($_GET['action']) && $_GET['action'] == 'editar') ? 'Actualizar' : 'Agregar'; ?>
</button>



        </form>

        <hr>

        <!-- Mostrar la tabla de alumnos -->
        <h3>Lista de Alumnos</h3>
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Edad
                    <a href="?order=asc" class="arrow-link">↑</a>
                    <a href="?order=desc" class="arrow-link">↓</a>
                    </th>
                    <th>Nivel de Estudios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['nombre'] . "</td>";
                        echo "<td>" . $row['apellido'] . "</td>";
                        echo "<td>" . $row['dni'] . "</td>";
                        echo "<td>" . $row['edad'] . "</td>";
                        echo "<td>" . $row['nivel_estudios'] . "</td>";
                        echo "<td>
                                <a href='?action=editar&id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                <a href='?action=eliminar&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de eliminar este alumno?\");'>Eliminar</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No se encontraron resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
