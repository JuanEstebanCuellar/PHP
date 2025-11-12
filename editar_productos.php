<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que venga el ID
if (!isset($_GET['id'])) {
    die("Error: No se especificó el producto a editar.");
}

$id = $_GET['id'];

// Obtener datos del producto
$resultado = $conexion->query("SELECT * FROM productos WHERE cod_pro = '$id'");
if ($resultado->num_rows === 0) {
    die("Error: Producto no encontrado.");
}
$producto = $resultado->fetch_assoc();

// Actualizar producto al enviar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nom_pro'];
    $cantidad = $_POST['cant_pro'];
    $valor = $_POST['val_pro'];
    $fecha = $_POST['fec_ven_pro'];
    $categoria = $_POST['cod_cat'];

    $actualizar = $conexion->prepare("UPDATE productos SET nom_pro=?, cant_pro=?, val_pro=?, fec_ven_pro=?, cod_cat=? WHERE cod_pro=?");
    $actualizar->bind_param("sddsss", $nombre, $cantidad, $valor, $fecha, $categoria, $id);

    if ($actualizar->execute()) {
        echo "<script>alert('Producto actualizado correctamente'); window.location.href='productos.php';</script>";
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
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 60px; max-width: 700px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">✏️ Editar Producto</h2>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Código:</label>
                <input type="text" name="cod_pro" class="form-control" value="<?= $producto['cod_pro'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_pro" class="form-control" value="<?= $producto['nom_pro'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad:</label>
                <input type="number" name="cant_pro" class="form-control" value="<?= $producto['cant_pro'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor:</label>
                <input type="number" name="val_pro" class="form-control" value="<?= $producto['val_pro'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha Vencimiento:</label>
                <input type="date" name="fec_ven_pro" class="form-control" value="<?= $producto['fec_ven_pro'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoría:</label>
                <select name="cod_cat" class="form-select" required>
                    <?php
                    $categorias = $conexion->query("SELECT cod_cat, nom_cat FROM categorias ORDER BY nom_cat ASC");
                    while ($cat = $categorias->fetch_assoc()) {
                        $selected = ($cat['cod_cat'] == $producto['cod_cat']) ? 'selected' : '';
                        echo "<option value='{$cat['cod_cat']}' $selected>{$cat['nom_cat']} ({$cat['cod_cat']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                <a href="productos.php" class="btn btn-secondary">⬅️ Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<?php $conexion->close(); ?>
