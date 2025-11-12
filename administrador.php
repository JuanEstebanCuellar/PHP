<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador - Supermercado JJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('imagenes/LogAdministrador.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(6px);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 50px;
            text-shadow: 0 0 15px rgba(0,0,0,0.6);
        }

        .contenedor-botones {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
            justify-items: center;
        }

        .boton-imagen {
            width: 220px;
            height: 140px;
            background-size: cover;
            background-position: center;
            border-radius: 20px;
            position: relative;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            overflow: hidden;
        }

        .boton-imagen span {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            background: rgba(0,0,0,0.4);
            padding: 6px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .boton-imagen:hover {
            transform: scale(1.07);
            box-shadow: 0 6px 16px rgba(255,255,255,0.4);
        }

        .volver {
            display: inline-block;
            margin-top: 40px;
            padding: 10px 25px;
            color: white;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            text-decoration: none;
            transition: 0.3s;
        }

        .volver:hover {
            background: rgba(255,255,255,0.4);
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="content">
        <h1>Panel del Administrador</h1>

        <div class="contenedor-botones">
            <a href="clientes.php" class="boton-imagen" style="background-image: url('Botones/Clientes.png');">
            </a>
            <a href="cajeros.php" class="boton-imagen" style="background-image: url('Botones/Cajeros.png');">
            </a>
            <a href="productos.php" class="boton-imagen" style="background-image: url('Botones/Productos.png');">
            </a>
            <a href="proveedores.php" class="boton-imagen" style="background-image: url('imagenes/proveedores.png');">
            </a>
            <a href="categorias.php" class="boton-imagen" style="background-image: url('imagenes/categorias.png');">
            </a>
            <a href="historial_facturas.php" class="boton-imagen" style="background-image: url('imagenes/facturas.png');">
            </a>
        </div>

        <a href="index.php" class="volver">⬅️ Volver al Inicio</a>
    </div>
</body>
</html>
