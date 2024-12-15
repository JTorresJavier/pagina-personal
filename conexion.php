<?php
$host = 'localhost';
$user = 'root'; // Usuario predeterminado de XAMPP
$password = ''; // Contraseña predeterminada (vacía)
$dbname = 'mi_base_de_datos';

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa";
?>
