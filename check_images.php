<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Equipment;

$equipment = Equipment::select('id', 'name', 'photos', 'main_photo')
    ->whereNotNull('photos')
    ->orWhereNotNull('main_photo')
    ->take(5)
    ->get();

foreach ($equipment as $item) {
    echo "ID: {$item->id}\n";
    echo "Name: {$item->name}\n";
    echo "Main Photo: {$item->main_photo}\n";
    echo "Photos: " . json_encode($item->photos) . "\n";
    echo "---\n";
}