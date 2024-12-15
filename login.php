<?php
// Conexión a la base de datos
$servername = "localhost";
$username_db = "root";  // Usuario de la base de datos
$password_db = "";  // Contraseña de la base de datos
$dbname = "mi_base_de_datos";  // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // Asegurarse de encriptar la contraseña como en el registro

    // Consultar la base de datos para verificar las credenciales
    $sql = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    // Si se encuentra un usuario con esas credenciales
    if ($result->num_rows > 0) {
        // Iniciar sesión
        session_start();
        $_SESSION['username'] = $username;  // Guardar el nombre de usuario en la sesión

        // Redirigir al ABM de alumnos
        header("Location: abm.php");
        exit();
    } else {
        $error_message = "Usuario o contraseña incorrectos";
    }

}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Moderno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="text-center">Iniciar Sesión</h2>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
            </form>
            <div class="d-grid">
                <!-- Botón Registrarse redirige a registro.php -->
                <a href="registro.php" class="btn btn-secondary">Registrarse</a>
            </div>
        </div>
    </div>
</body>
</html>
