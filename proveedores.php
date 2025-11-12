<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Insertar nuevo proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_prov'])) {
    $nit = $_POST['nit_prov'];
    $nombre = $_POST['nom_prov'];
    $telefono = $_POST['tel_prov'];
    $email = $_POST['email_prov'];

    $insertar = $conexion->prepare("INSERT INTO proveedores (nit_prov, nom_prov, tel_prov, email_prov) VALUES (?, ?, ?, ?)");
    $insertar->bind_param("ssss", $nit, $nombre, $telefono, $email);

    if ($insertar->execute()) {
        echo "<script>alert('Proveedor agregado correctamente'); window.location.href='proveedores.php';</script>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }

    $insertar->close();
}

// Eliminar proveedor
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM proveedores WHERE nit_prov = '$id'");
    echo "<script>alert('Proveedor eliminado correctamente'); window.location.href='proveedores.php';</script>";
}

// Obtener lista de proveedores
$resultado = $conexion->query("SELECT * FROM proveedores ORDER BY nit_prov ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
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
        <h1 class="text-center mb-4">Gestión de Proveedores</h1>

        <h4>Agregar Nuevo Proveedor</h4>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">NIT:</label>
                <input type="text" name="nit_prov" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_prov" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_prov" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email_prov" class="form-control" required>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Guardar Proveedor</button>
            </div>
        </form>

        <h4>Lista de Proveedores</h4>
        <input type="text" id="buscar" placeholder="🔍 Buscar proveedor por nombre..." onkeyup="filtrarTabla()">

        <table class="table table-striped table-hover text-center" id="tablaProveedores">
            <thead>
                <tr>
                    <th>NIT</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila['nit_prov'] ?></td>
                    <td><?= $fila['nom_prov'] ?></td>
                    <td><?= $fila['tel_prov'] ?></td>
                    <td><?= $fila['email_prov'] ?></td>
                    <td>
                        <a href="editar_proveedores.php?id=<?= $fila['nit_prov'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['nit_prov'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este proveedor?')">🗑️ Eliminar</a>
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
    let table = document.getElementById("tablaProveedores");
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
