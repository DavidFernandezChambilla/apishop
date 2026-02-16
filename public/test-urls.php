<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h2>Explorador de URLs de Imágenes</h2>";
echo "APP_URL en .env: " . env('APP_URL') . "<br>";
echo "Asset URL (test): " . asset('storage/test.jpg') . "<br>";

$publicStorage = __DIR__ . '/storage';
echo "Check de Enlace Simbólico:<br>";
echo "- ¿Existe public/storage?: " . (file_exists($publicStorage) ? "SÍ" : "NO") . "<br>";
echo "- ¿Es un enlace simbólico?: " . (is_link($publicStorage) ? "SÍ" : "NO") . "<br>";
if (is_link($publicStorage)) {
    echo "- Destino del enlace: " . readlink($publicStorage) . "<br>";
}
echo "<hr>";

$images = \App\Models\ProductImage::latest()->limit(5)->get();

foreach ($images as $img) {
    $relativePath = $img->url;
    $fullPath = storage_path('app/public/' . $relativePath);
    $exists = file_exists($fullPath) ? "SÍ" : "NO";
    $realPath = realpath($fullPath) ?: 'No encontrado';

    echo "URL en DB (cruda): " . $img->url . "<br>";
    echo "¿Existe en disco?: " . $exists . "<br>";
    echo "Ruta absoluta: " . $realPath . "<br>";
    echo "Image URL (accessor): <a href='" . $img->image_url . "' target='_blank'>" . $img->image_url . "</a><br>";
    echo "<img src='" . $img->image_url . "' style='width:100px; border:1px solid #ccc;' alt='Preview'><br><hr>";
}
