<?php
require_once 'funciones.php';

$error = '';
$loginOk = false;
$usuarioOk = '';

if (!file_exists(ARCHIVO_USUARIOS)) {
    $error = 'Error: No existe el archivo usuario.txt';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '') {
        foreach (file(ARCHIVO_USUARIOS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $partes = explode(':', trim($linea), 2);
            if (count($partes) === 2 && $partes[0] === $usuario && $partes[1] === $password) {
                $loginOk = true;
                $usuarioOk = $usuario;
                break;
            }
        }
    }
    if (!$loginOk) {
        $error = 'Usuario o contraseña incorrectos.';
    }
}

echo inicioPagina('Iniciar sesión - DevMusic Fest');
?>
<body<?= $loginOk ? ' onload="document.getElementById(\'paso\').submit()"' : '' ?>>
<div class="contenedor">
    <?= cabecera() ?>

    <main class="tarjeta">
        <h2>Iniciar Sesión</h2>

        <?php if ($loginOk): ?>
            <form id="paso" method="post" action="index.php" class="centro">
                <input type="hidden" name="usuario" value="<?= e($usuarioOk) ?>">
                <div class="alerta alerta-ok">Acceso correcto. Entrando...</div>
                <noscript><button type="submit" class="btn">Continuar</button></noscript>
            </form>
        <?php else: ?>
            <?php if ($error !== ''): ?>
                <div class="alerta"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <button type="submit" class="btn btn-full">Entrar al Sistema</button>
            </form>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
