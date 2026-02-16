<?php

echo "<h2>Reparador de Enlace de Almacenamiento (Storage)</h2>";

$publicStorage = __DIR__ . '/storage';
$targetStorage = __DIR__ . '/../storage/app/public';

echo "Ruta del enlace: " . $publicStorage . "<br>";
echo "Ruta del destino real: " . $targetStorage . "<br><br>";

// 1. Limpiar si existe algo que no sea un enlace correcto
if (file_exists($publicStorage)) {
    if (is_link($publicStorage)) {
        echo "Eliminando enlace simbólico antiguo...<br>";
        unlink($publicStorage);
    } else {
        echo "ATENCIÓN: Se encontró una CARPETA real en lugar de un enlace. Eliminando carpeta para corregir...<br>";
        // Función rápida para borrar carpeta no vacía
        function rmDirRecursive($dir)
        {
            foreach (scandir($dir) as $file) {
                if ('.' === $file || '..' === $file)
                    continue;
                if (is_dir("$dir/$file"))
                    rmDirRecursive("$dir/$file");
                else
                    unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        rmDirRecursive($publicStorage);
    }
}

// 2. Intentar crear el enlace simbólico
if (symlink($targetStorage, $publicStorage)) {
    echo "<b>¡ÉXITO!</b> El enlace simbólico se ha creado correctamente.<br>";
} else {
    echo "<b>ERROR:</b> No se pudo crear el enlace simbólico. Revisa los permisos de la carpeta 'public'.<br>";
}

echo "<br><hr>";
echo "Estado final:<br>";
echo "- ¿Existe public/storage?: " . (file_exists($publicStorage) ? "SÍ" : "NO") . "<br>";
echo "- ¿Es un enlace real?: " . (is_link($publicStorage) ? "SÍ" : "NO") . "<br>";
if (is_link($publicStorage)) {
    echo "- Apunta a: " . readlink($publicStorage) . "<br>";
}
