<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Insertar nueva categoría
if (!empty($_POST['nom_cat'])) {
    $nombre = $_POST['nom_cat'];

    // Obtener último código de categoría
    $sql = "SELECT cod_cat FROM categorias ORDER BY cod_cat DESC LIMIT 1";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimo_codigo = $fila['cod_cat'];
        $numero = intval(substr($ultimo_codigo, 3)) + 1;
        $nuevo_codigo = 'cat' . str_pad($numero, 2, '0', STR_PAD_LEFT);
    } else {
        $nuevo_codigo = 'cat01';
    }

    $insertar = $conexion->prepare("INSERT INTO categorias (cod_cat, nom_cat) VALUES (?, ?)");
    $insertar->bind_param("ss", $nuevo_codigo, $nombre);
    if ($insertar->execute()) {
        echo "<script>alert('Categoría agregada correctamente con código $nuevo_codigo'); window.location.href='categorias.php';</script>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }
    $insertar->close();
}

// Eliminar categoría
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM categorias WHERE cod_cat = '$id'");
    echo "<script>alert('Categoría eliminada correctamente'); window.location.href='categorias.php';</script>";
}

// Obtener categorías
$resultado = $conexion->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías</title>
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
        <h1 class="text-center mb-4">Categorías</h1>

        <h4>Agregar Nueva Categoría</h4>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label">Nombre de la categoría:</label>
                <input type="text" name="nom_cat" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Guardar</button>
            </div>
        </form>

        <h4>Lista de Categorías</h4>
        <input type="text" id="buscar" placeholder="🔍 Buscar categoría..." onkeyup="filtrarTabla()">

        <table class="table table-striped table-hover text-center" id="tablaCategorias">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila['cod_cat'] ?></td>
                    <td><?= $fila['nom_cat'] ?></td>
                    <td>
                        <a href="?eliminar=<?= $fila['cod_cat'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">🗑️ Eliminar</a>
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
    let table = document.getElementById("tablaCategorias");
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
