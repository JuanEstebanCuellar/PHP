<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Insertar cajero nuevo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_caj'])) {
    $nombre = $_POST['nom_caj'];
    $telefono = $_POST['tel_caj'];
    $direccion = $_POST['dir_caj'];

    // Obtener último código
    $resultado = $conexion->query("SELECT ide_caj FROM cajeros ORDER BY ide_caj DESC LIMIT 1");
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimoCodigo = intval(substr($fila['ide_caj'], 2)) + 1;
        $nuevoCodigo = 'CJ' . str_pad($ultimoCodigo, 4, '0', STR_PAD_LEFT);
    } else {
        $nuevoCodigo = 'CJ0001';
    }

    // Insertar nuevo registro
    $insertar = $conexion->prepare("INSERT INTO cajeros (ide_caj, nom_caj, tel_caj, dir_caj) VALUES (?, ?, ?, ?)");
    $insertar->bind_param("ssss", $nuevoCodigo, $nombre, $telefono, $direccion);
    if ($insertar->execute()) {
        echo "<script>alert('Cajero agregado correctamente con código $nuevoCodigo'); window.location.href='cajeros.php';</script>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }
    $insertar->close();
}

// Eliminar cajero
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM cajeros WHERE ide_caj = '$id'");
    echo "<script>alert('Cajero eliminado correctamente'); window.location.href='cajeros.php';</script>";
}

// Obtener lista de cajeros
$resultado = $conexion->query("SELECT * FROM cajeros ORDER BY ide_caj ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Cajeros</title>
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
        <h1 class="text-center mb-4">Gestión de Cajeros</h1>

        <h4>Agregar Nuevo Cajero</h4>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_caj" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_caj" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Dirección:</label>
                <input type="text" name="dir_caj" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Guardar</button>
            </div>
        </form>

        <h4>Lista de Cajeros</h4>
        <input type="text" id="buscar" placeholder="🔍 Buscar cajero por nombre..." onkeyup="filtrarTabla()">

        <table class="table table-striped table-hover text-center" id="tablaCajeros">
            <thead>
                <tr>
                    <th>ID Cajero</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila['ide_caj'] ?></td>
                    <td><?= $fila['nom_caj'] ?></td>
                    <td><?= $fila['tel_caj'] ?></td>
                    <td><?= $fila['dir_caj'] ?></td>
                    <td>
                        <a href="editar_cajeros.php?id=<?= $fila['ide_caj'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['ide_caj'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este cajero?')">🗑️ Eliminar</a>
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
    let table = document.getElementById("tablaCajeros");
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
