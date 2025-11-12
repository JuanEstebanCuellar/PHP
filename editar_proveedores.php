<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener NIT del proveedor a editar
if (!isset($_GET['id'])) {
    die("Error: No se especificó el proveedor a editar.");
}
$nit = $_GET['id'];

// Consultar proveedor
$resultado = $conexion->query("SELECT * FROM proveedores WHERE nit_prov = '$nit'");
if ($resultado->num_rows == 0) {
    die("Error: No se encontró el proveedor.");
}
$proveedor = $resultado->fetch_assoc();

// Actualizar proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nom_prov'];
    $telefono = $_POST['tel_prov'];
    $email = $_POST['email_prov'];

    $actualizar = $conexion->prepare("UPDATE proveedores SET nom_prov=?, tel_prov=?, email_prov=? WHERE nit_prov=?");
    $actualizar->bind_param("ssss", $nombre, $telefono, $email, $nit);

    if ($actualizar->execute()) {
        echo "<script>alert('Proveedor actualizado correctamente'); window.location.href='proveedores.php';</script>";
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
    $actualizar->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { margin-top: 60px; max-width: 600px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 30px; }
        .btn { border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">✏️ Editar Proveedor</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">NIT (No editable):</label>
                <input type="text" class="form-control" value="<?= $proveedor['nit_prov'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_prov" class="form-control" value="<?= $proveedor['nom_prov'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_prov" class="form-control" value="<?= $proveedor['tel_prov'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email_prov" class="form-control" value="<?= $proveedor['email_prov'] ?>" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="proveedores.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php $conexion->close(); ?>
