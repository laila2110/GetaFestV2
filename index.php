<?php
require_once 'funciones.php';

$usuario = trim($_POST['usuario'] ?? '');
$logueado = usuarioValido($usuario);
if (!$logueado) {
    $usuario = '';
}

echo inicioPagina('Reservas - DevMusic Fest');
?>
<body>
<div class="contenedor">
    <?= cabecera($usuario) ?>

    <?php if (!$logueado): ?>
        <main class="tarjeta centro">
            <h2>Acceso restringido</h2>
            <p>Inicia sesión para gestionar las reservas del festival.</p>
            <a class="btn" href="login.php">Iniciar Sesión</a>
        </main>
    <?php else: ?>
        <main class="tarjeta">
            <div class="titulo-fila">
                <h2>Nueva Reserva</h2>
                <?= botonPost('ver_reservas.php', $usuario, 'Ver Reservas') ?>
            </div>

            <form method="post" action="procesar.php" enctype="multipart/form-data">
                <input type="hidden" name="usuario" value="<?= e($usuario) ?>">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del asistente" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.es" required>

                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" min="1" max="120" required>

                <label for="pase">Tipo de pase:</label>
                <select id="pase" name="pase" required>
                    <?php foreach (PASES as $nombre => $precio): ?>
                        <option value="<?= e($nombre) ?>"><?= e($nombre) ?> (<?= $precio ?>€)</option>
                    <?php endforeach; ?>
                </select>

                <label>Días de asistencia (+<?= max(DIAS) ?>€ por día):</label>
                <div class="dias">
                    <?php foreach (DIAS as $dia => $precio): ?>
                        <label><input type="checkbox" name="dias[]" value="<?= e($dia) ?>"> <?= e($dia) ?></label>
                    <?php endforeach; ?>
                </div>

                <label for="pago">Método de pago:</label>
                <select id="pago" name="pago" required>
                    <?php foreach (PAGOS as $pago): ?>
                        <option value="<?= e($pago) ?>"><?= e($pago) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="foto">Fotografía:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>

                <label for="comentarios">Comentarios:</label>
                <textarea id="comentarios" name="comentarios" placeholder="Opcional"></textarea>

                <button type="submit" class="btn btn-full">Reservar entrada</button>
            </form>
        </main>
    <?php endif; ?>
</div>
</body>
</html>
