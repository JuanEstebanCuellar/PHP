<?php
$conexion = new mysqli("localhost", "root", "", "supermercadojja", 3309);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Crear nueva factura si se envía el formulario
if (isset($_POST['crear_factura'])) {
    $nombre_cliente = trim($_POST['nom_cli']);
    $cajero = $_POST['ide_caj'];
    $fecha = date('Y-m-d');

    // Buscar el ID del cliente según el nombre
    $consulta_cliente = $conexion->prepare("SELECT ide_cli FROM clientes WHERE nom_cli = ?");
    $consulta_cliente->bind_param("s", $nombre_cliente);
    $consulta_cliente->execute();
    $resultado = $consulta_cliente->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $cliente = $fila['ide_cli'];

        // Crear la factura
        $conexion->query("INSERT INTO facturas (val_tot_fac, fec_fac, ide_cli, ide_caj)
                          VALUES (0, '$fecha', '$cliente', '$cajero')");
        $nro_fac = $conexion->insert_id;

        header("Location: facturacion.php?nro_fac=$nro_fac");
        exit;
    } else {
        echo "<script>alert('⚠️ El cliente no existe. Verifique el nombre.'); window.history.back();</script>";
        exit;
    }
}

// Agregar producto al detalle de factura
if (isset($_POST['agregar_detalle'])) {
    $nro_fac = $_POST['nro_fac'];
    $producto = $_POST['cod_pro'];
    $cantidad = $_POST['cant_pro'];

    // Obtener precio unitario del producto
    $consulta = $conexion->query("SELECT val_pro FROM productos WHERE cod_pro='$producto'");
    $fila = $consulta->fetch_assoc();
    $valor = $fila['val_pro'];

    $conexion->query("INSERT INTO detalle_facturas (cod_pro, nro_fac, val_uni_pro, cant_pro, val_total_pro)
                      VALUES ('$producto', '$nro_fac', '$valor', '$cantidad', '$valor' * '$cantidad')");

    echo "<script>alert('✅ Producto agregado correctamente'); window.location='facturacion.php?nro_fac=$nro_fac';</script>";
}

// Obtener listas desplegables
$cajeros = $conexion->query("SELECT ide_caj, nom_caj FROM cajeros");
$productos = $conexion->query("SELECT cod_pro, nom_pro, val_pro FROM productos");

// Mostrar detalles si hay factura activa
$nro_fac = $_GET['nro_fac'] ?? null;
$detalles = null;
if ($nro_fac) {
    $detalles = $conexion->query("SELECT d.*, p.nom_pro FROM detalle_facturas d 
                                  JOIN productos p ON d.cod_pro = p.cod_pro 
                                  WHERE nro_fac = '$nro_fac'");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación - Supermercado JJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e3f2fd);
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h1 class="text-center mb-4">🧾 Sistema de Facturación</h1>

        <!-- Crear factura -->
        <?php if (!$nro_fac) { ?>
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente:</label>
                    <input type="text" name="nom_cli" class="form-control" placeholder="Escriba el nombre del cliente" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cajero:</label>
                    <select name="ide_caj" class="form-select" required>
                        <option value="">Seleccione un cajero</option>
                        <?php while ($cj = $cajeros->fetch_assoc()) { ?>
                            <option value="<?= $cj['ide_caj'] ?>"><?= $cj['nom_caj'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 text-center">
                    <button type="submit" name="crear_factura" class="btn btn-primary mt-3">🧾 Crear Factura</button>
                </div>
            </form>
        <?php } else { ?>

        <!-- Agregar detalle -->
        <form method="POST" class="row g-3 mb-4">
            <input type="hidden" name="nro_fac" value="<?= $nro_fac ?>">
            <div class="col-md-6">
                <label class="form-label">Producto:</label>
                <select name="cod_pro" class="form-select" required>
                    <option value="">Seleccione un producto</option>
                    <?php while ($p = $productos->fetch_assoc()) { ?>
                        <option value="<?= $p['cod_pro'] ?>"><?= $p['nom_pro'] ?> - $<?= number_format($p['val_pro'],0,',','.') ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cantidad:</label>
                <input type="number" name="cant_pro" class="form-control" min="1" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="agregar_detalle" class="btn btn-success w-100">➕ Agregar</button>
            </div>
        </form>

        <!-- Detalle de factura -->
        <h4>🧩 Detalle de la Factura N° <?= $nro_fac ?></h4>
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Producto</th>
                    <th>Valor Unitario</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $total = 0;
            while ($d = $detalles->fetch_assoc()) { 
                $total += $d['val_total_pro']; ?>
                <tr>
                    <td><?= $d['nom_pro'] ?></td>
                    <td>$<?= number_format($d['val_uni_pro'], 0, ',', '.') ?></td>
                    <td><?= $d['cant_pro'] ?></td>
                    <td>$<?= number_format($d['val_total_pro'], 0, ',', '.') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <h3 class="text-end text-success fw-bold">Total: $<?= number_format($total, 0, ',', '.') ?></h3>

        <div class="text-center mt-3">
            <a href="facturacion.php" class="btn btn-secondary">🧾 Nueva Factura</a>
            <a href="Administrador.php" class="btn btn-outline-dark">⬅️ Volver al Panel</a>
        </div>

        <?php } ?>
    </div>
</div>
</body>
</html>
