<?php
/**
 * Panel de control para la generación y visualización de documentación.
 * 
 * Este archivo proporciona una interfaz de usuario para generar y ver la documentación del sistema.
 * Requiere que el usuario haya iniciado sesión.
 * 
 * Autor: franHR
 */

session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "Intento de acceso incorrecto";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Control - Documentación</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f2f5;
        }
        .panel {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-generate {
            background: #1a73e8;
            color: white;
        }
        .btn-view {
            background: #34a853;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background: #e6f4ea;
            color: #34a853;
        }
        .error {
            background: #fce8e6;
            color: #ea4335;
        }
    </style>
</head>
<body>
    <div class="panel">
        <h1>Panel de Control de Documentación</h1>
        <div class="buttons">
            <a href="generar.php" class="btn btn-generate">Generar Documentación</a>
            <a href="index.php" class="btn btn-view">Ver Documentación</a>
        </div>
        <?php
        if (isset($_GET['status'])) {
            $class = ($_GET['status'] === 'success') ? 'success' : 'error';
            echo "<div class='status {$class}'>";
            echo htmlspecialchars($_GET['message'] ?? 'Operación completada');
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
