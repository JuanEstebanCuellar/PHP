<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el último código de cliente
$sql = "SELECT ide_cli FROM clientes ORDER BY ide_cli DESC LIMIT 1";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $ultimo_codigo = $fila['ide_cli'];
    $numero = intval(substr($ultimo_codigo, 2)) + 1;
    $nuevo_codigo = 'CL' . str_pad($numero, 4, '0', STR_PAD_LEFT);
} else {
    $nuevo_codigo = 'CL0001';
}

// Capturar datos del formulario
$nombre = $_POST['nom_cli'] ?? '';
$direccion = $_POST['dir_cli'] ?? '';
$telefono = $_POST['tel_cli'] ?? '';

// Insertar nuevo cliente
if (!empty($nombre) && !empty($telefono)) {
    $insertar = $conexion->prepare("INSERT INTO clientes (ide_cli, nom_cli, dir_cli, tel_cli) VALUES (?, ?, ?, ?)");
    $insertar->bind_param("ssss", $nuevo_codigo, $nombre, $direccion, $telefono);
    if ($insertar->execute()) {
        echo "<script>alert('Cliente agregado correctamente con código $nuevo_codigo'); window.location.href='clientes.php';</script>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }
    $insertar->close();
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM clientes WHERE ide_cli = '$id'");
    echo "<script>alert('Cliente eliminado correctamente'); window.location.href='clientes.php';</script>";
}

// Obtener lista de clientes
$resultado = $conexion->query("SELECT * FROM clientes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; }
        .container { margin-top: 40px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn { border-radius: 8px; }
        table th { background: #007bff; color: white; }
        #buscar { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h1 class="text-center mb-4">Gestor de Clientes</h1>

        <h4>Agregar Nuevo Cliente</h4>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nom_cli" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Dirección:</label>
                <input type="text" name="dir_cli" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="tel_cli" class="form-control" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Guardar</button>
            </div>
        </form>

        <h4>Lista de Clientes</h4>
        <input type="text" id="buscar" placeholder="🔍 Buscar cliente por nombre..." onkeyup="filtrarTabla()">

        <table class="table table-striped table-hover text-center" id="tablaClientes">
            <thead>
                <tr>
                    <th>ID Cliente</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila['ide_cli'] ?></td>
                    <td><?= $fila['nom_cli'] ?></td>
                    <td><?= $fila['dir_cli'] ?></td>
                    <td><?= $fila['tel_cli'] ?></td>
                    <td>
                        <a href="editar_clientes.php?id=<?= $fila['ide_cli'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="?eliminar=<?= $fila['ide_cli'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrarTabla() {
    let input = document.getElementById("buscar");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("tablaClientes");
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
