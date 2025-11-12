<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Insertar producto nuevo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_pro'])) {
    $nombre = $_POST['nom_pro'];
    $cantidad = $_POST['cant_pro'];
    $valor = $_POST['val_pro'];
    $fecha = $_POST['fec_ven_pro'];
    $categoria = $_POST['cod_cat'];

    // Obtener último código
    $resultado = $conexion->query("SELECT cod_pro FROM productos ORDER BY cod_pro DESC LIMIT 1");
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimoCodigo = intval(substr($fila['cod_pro'], 1)) + 1;
        $nuevoCodigo = 'P' . str_pad($ultimoCodigo, 4, '0', STR_PAD_LEFT);
    } else {
        $nuevoCodigo = 'P0001';
    }

    $insertar = $conexion->prepare("INSERT INTO productos (cod_pro, nom_pro, cant_pro, val_pro, fec_ven_pro, cod_cat) VALUES (?, ?, ?, ?, ?, ?)");
    $insertar->bind_param("ssddss", $nuevoCodigo, $nombre, $cantidad, $valor, $fecha, $categoria);
    if ($insertar->execute()) {
        echo "<script>alert('Producto agregado correctamente con código $nuevoCodigo'); window.location.href='productos.php';</script>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }
    $insertar->close();
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM productos WHERE cod_pro = '$id'");
    echo "<script>alert('Producto eliminado correctamente'); window.location.href='productos.php';</script>";
}

// Obtener lista de productos
$resultado = $conexion->query("SELECT * FROM productos ORDER BY cod_pro ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { margin-top: 40px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table th { background: #0d6efd; color: white; }
        .btn { border-radius: 8px; }
        #buscar { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h1 class="text-center mb-4">Gestión de Productos</h1>

        <h4>Agregar Nuevo Producto</h4>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_pro" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad:</label>
                <input type="number" name="cant_pro" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Valor:</label>
                <input type="number" name="val_pro" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha Vencimiento:</label>
                <input type="date" name="fec_ven_pro" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Categoría:</label>
                <select name="cod_cat" class="form-select" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php
                    $categorias = $conexion->query("SELECT cod_cat, nom_cat FROM categorias ORDER BY nom_cat ASC");
                    while ($cat = $categorias->fetch_assoc()) {
                        echo "<option value='{$cat['cod_cat']}'>{$cat['nom_cat']} ({$cat['cod_cat']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Guardar Producto</button>
            </div>
        </form>

        <h4>Lista de Productos</h4>
        <input type="text" id="buscar" placeholder="🔍 Buscar producto por nombre..." onkeyup="filtrarTabla()">

        <table class="table table-striped table-hover text-center" id="tablaProductos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Valor</th>
                    <th>Fecha Vencimiento</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila['cod_pro'] ?></td>
                    <td><?= $fila['nom_pro'] ?></td>
                    <td><?= $fila['cant_pro'] ?></td>
                    <td>$<?= number_format($fila['val_pro'], 0, ',', '.') ?></td>
                    <td><?= $fila['fec_ven_pro'] ?></td>
                    <td><?= $fila['cod_cat'] ?></td>
                    <td>
                        <a href="editar_productos.php?id=<?= $fila['cod_pro'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['cod_pro'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <a href="Administrador.php" class="btn btn-secondary">⬅️ Volver al Panel</a>
        </div>
    </div>
</div>

<script>
function filtrarTabla() {
    let input = document.getElementById("buscar");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("tablaProductos");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[1];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
</body>
</html>

<?php $conexion->close(); ?>
