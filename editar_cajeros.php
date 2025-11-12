<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si llega el ID
if (!isset($_GET['id'])) {
    die("Error: No se especificó el cajero a editar.");
}

$id = $_GET['id'];

// Obtener datos del cajero seleccionado
$resultado = $conexion->query("SELECT * FROM cajeros WHERE ide_caj = '$id'");
if ($resultado->num_rows == 0) {
    die("Error: Cajero no encontrado.");
}
$cajero = $resultado->fetch_assoc();

// Si se envía el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nom_caj'];
    $telefono = $_POST['tel_caj'];
    $direccion = $_POST['dir_caj'];

    $actualizar = $conexion->prepare("UPDATE cajeros SET nom_caj=?, tel_caj=?, dir_caj=? WHERE ide_caj=?");
    $actualizar->bind_param("ssss", $nombre, $telefono, $direccion, $id);

    if ($actualizar->execute()) {
        echo "<script>alert('Cajero actualizado correctamente'); window.location.href='cajeros.php';</script>";
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
    <title>Editar Cajero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { margin-top: 60px; max-width: 600px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn { border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">✏️ Editar Cajero</h2>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">ID Cajero:</label>
                <input type="text" class="form-control" value="<?= $cajero['ide_caj'] ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_caj" class="form-control" value="<?= $cajero['nom_caj'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_caj" class="form-control" value="<?= $cajero['tel_caj'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección:</label>
                <input type="text" name="dir_caj" class="form-control" value="<?= $cajero['dir_caj'] ?>">
            </div>

            <div class="d-flex justify-content-between">
                <a href="cajeros.php" class="btn btn-secondary">⬅️ Volver</a>
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php $conexion->close(); ?>
