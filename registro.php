<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mi_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Variables para el mensaje de error y éxito
$error_message = '';
$success_message = '';

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = md5($_POST['password']); // Encriptar contraseña (MD5 como ejemplo)

    // Verificar si el usuario o email ya existen
    $sql_check = "SELECT * FROM usuarios WHERE username = '$user' OR email = '$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        $error_message = "El usuario o correo ya están registrados.";
    } else {
        // Insertar nuevo usuario
        $sql_insert = "INSERT INTO usuarios (username, email, password) VALUES ('$user', '$email', '$pass')";
        if ($conn->query($sql_insert) === TRUE) {
            $success_message = "¡Registro exitoso!";
        } else {
            $error_message = "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="text-center">Crear Cuenta</h2>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                    <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de registro -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
                <div class="d-grid">
                    <a href="login.php" class="btn btn-secondary">Volver al Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
