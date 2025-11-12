<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Supermercado JJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('Imagenes/LogoSuperJJA.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Capa difuminada encima de la imagen */
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
            margin-bottom: 40px;
            text-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .btn-custom {
            width: 220px;
            height: 70px;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 15px;
            transition: all 0.3s;
        }

        .btn-admin {
            background-color: #0d6efd;
            color: white;
        }

        .btn-admin:hover {
            background-color: #084298;
            transform: scale(1.05);
        }

        .btn-fact {
            background-color: #198754;
            color: white;
        }

        .btn-fact:hover {
            background-color: #146c43;
            transform: scale(1.05);
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 100px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="content">
        <h1>🛒 Bienvenido a Supermercado JJA</h1>
        <div class="btn-container">
            <a href="administrador.php" class="btn btn-custom btn-admin">Administrador</a>
            <a href="facturacion.php" class="btn btn-custom btn-fact">Facturación</a>
        </div>
    </div>
</body>
</html>
