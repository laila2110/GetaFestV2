<?php
require_once 'funciones.php';

$usuario = trim($_POST['usuario'] ?? '');
$errores = [];
$datos = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errores[] = 'Acceso no permitido: la petición debe enviarse por POST.';
} elseif (!usuarioValido($usuario)) {
    $errores[] = 'Debes iniciar sesión para registrar una reserva.';
    $usuario = '';
} else {
    $nombre      = limpiar($_POST['nombre'] ?? '');
    $email       = limpiar($_POST['email'] ?? '');
    $edad        = filter_var($_POST['edad'] ?? '', FILTER_VALIDATE_INT);
    $pase        = $_POST['pase'] ?? '';
    $diasPost    = is_array($_POST['dias'] ?? null) ? $_POST['dias'] : [];
    $pago        = $_POST['pago'] ?? '';
    $comentarios = limpiar($_POST['comentarios'] ?? '');

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido.';
    }
    if ($edad === false) {
        $errores[] = 'La edad no es válida.';
    } elseif ($edad < 18) {
        // Validación: edad mayor o igual a 18
        $errores[] = 'Solo se admiten asistentes de 18 años o más.';
    }
    if (!array_key_exists($pase, PASES)) {
        $errores[] = 'El tipo de pase no es válido.';
    }
    if (!in_array($pago, PAGOS, true)) {
        $errores[] = 'El método de pago no es válido.';
    }

    // Días marcados: solo los permitidos, en el orden del festival
    $dias = array_values(array_filter(
        array_keys(DIAS),
        fn($d) => in_array($d, $diasPost, true)
    ));
    if (count($dias) === 0) {
        $errores[] = 'Debes marcar al menos un día de asistencia.';
    }

    // Fotografía
    $extension = '';
    $foto = $_FILES['foto'] ?? null;
    if ($foto === null || $foto['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Debes subir una fotografía válida.';
    } else {
        $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $permitidas, true) || getimagesize($foto['tmp_name']) === false) {
            $errores[] = 'La fotografía debe ser una imagen (jpg, png, gif o webp).';
        }
    }

    if (count($errores) === 0) {
        $carpeta = __DIR__ . '/uploads';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }
        $nombreFoto = uniqid('user_') . '.' . $extension;
        $rutaRelativa = 'uploads/' . $nombreFoto;

        if (!move_uploaded_file($foto['tmp_name'], $carpeta . '/' . $nombreFoto)) {
            $errores[] = 'No se pudo guardar la fotografía en el servidor.';
        } else {
            $total = PASES[$pase];
            foreach ($dias as $d) {
                $total += DIAS[$d];
            }

            $linea = implode('|', [
                date('Y-m-d H:i:s'),
                $nombre,
                $email,
                $edad,
                $pase,
                implode(', ', $dias),
                $pago,
                $total . '€',
                $rutaRelativa,
                $comentarios,
            ]) . PHP_EOL;
            file_put_contents(ARCHIVO_RESERVAS, $linea, FILE_APPEND | LOCK_EX);

            $datos = ['nombre' => $nombre, 'pase' => $pase, 'dias' => implode(', ', $dias), 'total' => $total];
        }
    }
}

echo inicioPagina('Reserva - DevMusic Fest');
?>
<body>
<div class="contenedor">
    <?= cabecera($usuario) ?>

    <main class="tarjeta centro">
        <?php if (count($errores) > 0): ?>
            <h2>No se pudo registrar la reserva</h2>
            <?php foreach ($errores as $error): ?>
                <div class="alerta"><?= e($error) ?></div>
            <?php endforeach; ?>
            <?php if ($usuario !== ''): ?>
                <?= botonPost('index.php', $usuario, '← Volver al formulario', [], 'btn btn-borde') ?>
            <?php else: ?>
                <a class="btn btn-borde" href="login.php">Iniciar Sesión</a>
            <?php endif; ?>
        <?php else: ?>
            <h2>¡Reserva registrada!</h2>
            <div class="alerta alerta-ok">
                <?= e($datos['nombre']) ?> · <?= e($datos['pase']) ?> · <?= e($datos['dias']) ?>
                · Total: <?= $datos['total'] ?>€
            </div>
            <?= botonPost('index.php', $usuario, '+ Nueva Reserva') ?>
            <?= botonPost('ver_reservas.php', $usuario, 'Ver Reservas', [], 'btn btn-borde') ?>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
