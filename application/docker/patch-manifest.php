<?php
$file = 'vendor/laravel/framework/src/Illuminate/Foundation/PackageManifest.php';

if (!file_exists($file)) {
    echo "PackageManifest.php no encontrado, saltando parche.\n";
    exit(0);
}

$content = file_get_contents($file);

if (strpos($content, "packages'] ??") !== false) {
    echo "Ya compatible con Composer 2.x.\n";
    exit(0);
}

// Laravel 5.8 lee installed.json esperando el formato de Composer 1.x (array plano).
// Composer 2.x usa { "packages": [...] }. Este parche agrega compatibilidad.
$patched = preg_replace(
    '/(\$installed\s*=\s*json_decode\([^;]+;\s*)\n(\s+)(\$packages\s*=\s*\$installed;)/',
    "$1\n$2\$installed = \$installed['packages'] ?? \$installed;\n$2$3",
    $content
);

if ($patched === null || $patched === $content) {
    echo "Patron no encontrado en PackageManifest.php. Puede que ya este corregido o el formato sea diferente.\n";
    exit(0);
}

file_put_contents($file, $patched);
echo "PackageManifest.php parcheado correctamente.\n";
