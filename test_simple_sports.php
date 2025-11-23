<?php

// Test simple para ver qué devuelve la API de deportes
$sportsUrl = 'http://localhost:8000/api/sports';

$ch = curl_init($sportsUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$sportsResponse = curl_exec($ch);
$sportsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $sportsHttpCode\n";
echo "Response: $sportsResponse\n";

if ($sportsHttpCode == 200) {
    $decoded = json_decode($sportsResponse, true);
    echo "\nDecoded structure:\n";
    print_r(array_keys($decoded));
}