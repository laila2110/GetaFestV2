<?php
require_once 'funciones.php';

$usuario = trim($_POST['usuario'] ?? '');
$esAdmin = ($usuario === 'admin' && usuarioValido($usuario));
$error = '';
$reserva = null;
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

if (!$esAdmin) {
    http_response_code(403);
    $error = 'Acceso denegado: solo el administrador puede ver los pases.';
    if (!usuarioValido($usuario)) {
        $usuario = '';
    }
} elseif ($id === false) {
    $error = 'El identificador de reserva no es válido.';
} else {
    $reservas = leerReservas();
    if (isset($reservas[$id])) {
        $reserva = $reservas[$id];
    } else {
        $error = 'No existe ninguna reserva con el ID ' . $id . '.';
    }
}

echo inicioPagina('Pase digital - DevMusic Fest');
?>
<body>
<div class="contenedor">
    <?= cabecera($usuario) ?>

    <main class="tarjeta">
        <?php if ($error !== ''): ?>
            <h2 class="centro">Pase no disponible</h2>
            <div class="alerta"><?= e($error) ?></div>
            <div class="centro">
                <?php if ($usuario !== ''): ?>
                    <?= botonPost('ver_reservas.php', $usuario, '← Volver al listado de reservas', [], 'btn btn-borde') ?>
                <?php else: ?>
                    <a class="btn btn-borde" href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php
            $foto = 'uploads/' . basename($reserva[8]);
            ?>
            <h2 class="centro">Acreditación #<?= $id ?> (Vista Administrador)</h2>

            <article class="pase">
                <?php if (is_file(__DIR__ . '/' . $foto)): ?>
                    <img class="foto" src="<?= e($foto) ?>" alt="Foto de <?= e($reserva[1]) ?>">
                <?php else: ?>
                    <div class="foto foto-vacia">👤</div>
                <?php endif; ?>

                <h3><?= e($reserva[1]) ?></h3>
                <p><strong>Email:</strong> <?= e($reserva[2]) ?></p>
                <p><strong>Edad:</strong> <?= e($reserva[3]) ?> años</p>
                <p><strong>Tipo de Entrada:</strong> <?= e($reserva[4]) ?></p>
                <p><strong>Días de asistencia:</strong> <?= e($reserva[5]) ?></p>
                <p><strong>Método de pago:</strong> <?= e($reserva[6]) ?></p>
                <?php if (trim($reserva[9]) !== ''): ?>
                    <p class="nota">"<?= e($reserva[9]) ?>"</p>
                <?php endif; ?>
                <p class="total">Total: <?= e($reserva[7]) ?></p>
            </article>

            <div class="centro">
                <?= botonPost('ver_reservas.php', $usuario, '← Volver al listado de reservas', [], 'btn btn-borde') ?>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
