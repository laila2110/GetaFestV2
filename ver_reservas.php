<?php
require_once 'funciones.php';

$usuario = trim($_POST['usuario'] ?? '');
$logueado = usuarioValido($usuario);
if (!$logueado) {
    $usuario = '';
}

$reservas = $logueado ? leerReservas() : [];

echo inicioPagina('Listado de reservas - DevMusic Fest');
?>
<body>
<div class="contenedor">
    <?= cabecera($usuario) ?>

    <?php if (!$logueado): ?>
        <main class="tarjeta centro">
            <h2>Acceso restringido</h2>
            <p>Inicia sesión para consultar las reservas.</p>
            <a class="btn" href="login.php">Iniciar Sesión</a>
        </main>
    <?php else: ?>
        <main class="tarjeta">
            <div class="titulo-fila">
                <h2>Listado de Reservas</h2>
                <?= botonPost('index.php', $usuario, '+ Nueva Reserva') ?>
            </div>

            <?php if (count($reservas) === 0): ?>
                <p class="nota">Todavía no hay reservas registradas.</p>
            <?php else: ?>
                <div class="tabla-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Edad</th>
                                <th>Pase</th>
                                <th>Pago</th>
                                <th>Total</th>
                                <th>Acción Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $id => $r): ?>
                                <tr>
                                    <td><?= $id ?></td>
                                    <td><?= e($r[0]) ?></td>
                                    <td><?= e($r[1]) ?></td>
                                    <td><?= e($r[2]) ?></td>
                                    <td><?= e($r[3]) ?></td>
                                    <td><?= e($r[4]) ?></td>
                                    <td><?= e($r[6]) ?></td>
                                    <td><?= e($r[7]) ?></td>
                                    <td><?= botonPost('ver_pase.php', $usuario, 'Ver Pase', ['id' => $id], 'btn btn-cian') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    <?php endif; ?>
</div>
</body>
</html>
