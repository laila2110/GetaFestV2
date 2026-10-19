<?php
const PASES = ['General' => 40, 'VIP' => 80, 'Super VIP' => 160];
const DIAS  = ['Viernes' => 30, 'Sábado' => 30, 'Domingo' => 30];
const PAGOS = ['Bizum', 'PayPal', 'Tarjeta'];

const ARCHIVO_USUARIOS = __DIR__ . '/usuario.txt';
const ARCHIVO_RESERVAS = __DIR__ . '/reservas.txt';

/** Escapa texto para imprimirlo en HTML. */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/** Limpia un campo de formulario para que no rompa el formato de reservas.txt. */
function limpiar(string $texto): string
{
    return trim(str_replace(['|', "\r", "\n"], ' ', $texto));
}

/** Comprueba que el usuario recibido por POST existe en usuario.txt. */
function usuarioValido(string $usuario): bool
{
    if ($usuario === '' || !file_exists(ARCHIVO_USUARIOS)) {
        return false;
    }
    foreach (file(ARCHIVO_USUARIOS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $partes = explode(':', trim($linea), 2);
        if (count($partes) === 2 && $partes[0] === $usuario) {
            return true;
        }
    }
    return false;
}

/** Devuelve la cabecera (header.html) con el bloque de acceso según haya usuario o no. */
function cabecera(string $usuario = ''): string
{
    $html = file_get_contents(__DIR__ . '/header.html');
    if ($usuario !== '') {
        $acceso = 'Usuario activo: <strong>' . e($usuario) . '</strong> '
                . '<a href="login.php">Cerrar Sesión / Salir</a>';
    } else {
        $acceso = 'Acceso al portal: 🔑 <a href="login.php">Iniciar Sesión</a>';
    }
    return str_replace('{{ACCESO}}', $acceso, $html);
}

/**
 * Botón que navega a otra página por POST propagando el usuario en un campo oculto
 * (sustituye a $_SESSION). $extra permite añadir más campos ocultos (por ejemplo, el id).
 */
function botonPost(string $destino, string $usuario, string $texto, array $extra = [], string $clase = 'btn'): string
{
    $h  = '<form method="post" action="' . e($destino) . '" class="form-inline">';
    $h .= '<input type="hidden" name="usuario" value="' . e($usuario) . '">';
    foreach ($extra as $nombre => $valor) {
        $h .= '<input type="hidden" name="' . e((string) $nombre) . '" value="' . e((string) $valor) . '">';
    }
    $h .= '<button type="submit" class="' . e($clase) . '">' . $texto . '</button></form>';
    return $h;
}

/**
 * Lee reservas.txt y devuelve [id => campos]. El id es el número de línea del fichero (empieza en 1).
 * Campos: 0 fecha | 1 nombre | 2 email | 3 edad | 4 pase | 5 días | 6 pago | 7 total | 8 foto | 9 comentarios
 */
function leerReservas(): array
{
    if (!file_exists(ARCHIVO_RESERVAS)) {
        return [];
    }
    $reservas = [];
    foreach (file(ARCHIVO_RESERVAS, FILE_IGNORE_NEW_LINES) as $i => $linea) {
        if (trim($linea) === '') {
            continue;
        }
        $campos = explode('|', $linea);
        if (count($campos) < 9) {
            continue;
        }
        $reservas[$i + 1] = array_pad($campos, 10, '');
    }
    return $reservas;
}

/** Cabecera HTML común de cada página. */
function inicioPagina(string $titulo): string
{
    return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
         . '<meta name="viewport" content="width=device-width, initial-scale=1">'
         . '<title>' . e($titulo) . '</title>'
         . '<link rel="stylesheet" href="style.css"></head>';
}
