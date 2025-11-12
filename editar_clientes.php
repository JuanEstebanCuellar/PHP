<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se recibe el ID del cliente por GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $resultado = $conexion->query("SELECT * FROM clientes WHERE ide_cli = '$id'");

    if ($resultado->num_rows == 1) {
        $cliente = $resultado->fetch_assoc();
    } else {
        echo "<script>alert('Cliente no encontrado'); window.location.href='clientes.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID no especificado'); window.location.href='clientes.php';</script>";
    exit;
}

// Si se envía el formulario (por POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nom_cli'];
    $direccion = $_POST['dir_cli'];
    $telefono = $_POST['tel_cli'];

    $actualizar = $conexion->prepare("UPDATE clientes SET nom_cli=?, dir_cli=?, tel_cli=? WHERE ide_cli=?");
    $actualizar->bind_param("ssss", $nombre, $direccion, $telefono, $id);

    if ($actualizar->execute()) {
        echo "<script>alert('Cliente actualizado correctamente'); window.location.href='clientes.php';</script>";
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
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; }
        .container { margin-top: 50px; max-width: 600px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn { border-radius: 8px; }
        h2 { color: #007bff; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">✏️ Editar Cliente</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Código de Cliente:</label>
                <input type="text" class="form-control" value="<?= $cliente['ide_cli'] ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_cli" class="form-control" value="<?= $cliente['nom_cli'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección:</label>
                <input type="text" name="dir_cli" class="form-control" value="<?= $cliente['dir_cli'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_cli" class="form-control" value="<?= $cliente['tel_cli'] ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="clientes.php" class="btn btn-secondary">⬅️ Volver</a>
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php $conexion->close(); ?>
