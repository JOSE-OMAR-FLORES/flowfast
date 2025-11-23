<?php

// Test del algoritmo Round Robin
$loginUrl = 'http://localhost:8000/api/auth/login';
$baseUrl = 'http://localhost:8000/api';

echo "=== TEST ROUND ROBIN ALGORITHM ===\n\n";

// 1. Login
$loginData = ['email' => 'admin@flowfast.com', 'password' => 'password123'];

$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($loginHttpCode !== 200) {
    die("❌ Error en login: $loginResponse\n");
}

$loginData = json_decode($loginResponse, true);
$token = $loginData['data']['token'];
echo "✅ Login exitoso\n\n";

// 2. Crear temporada de prueba
echo "--- Creando temporada de prueba ---\n";
$seasonData = [
    'name' => 'Temporada Round Robin Test',
    'league_id' => 2, // Asumiendo que existe la liga con ID 2
    'format' => 'league',
    'round_robin_type' => 'single',
    'start_date' => '2025-12-01', // Fecha futura
    'end_date' => '2026-06-30',
    'game_days' => ['saturday', 'sunday'],
    'daily_matches' => 3,
    'match_times' => ['10:00', '14:00', '18:00']
];

$ch = curl_init("$baseUrl/seasons");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($seasonData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$seasonResponse = curl_exec($ch);
$seasonHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($seasonHttpCode !== 201) {
    echo "❌ Error al crear temporada: $seasonResponse\n";
    exit;
}

$seasonData = json_decode($seasonResponse, true);
$seasonId = $seasonData['data']['id'];
echo "✅ Temporada creada: ID $seasonId\n\n";

// 3. Crear equipos de prueba
echo "--- Creando equipos de prueba ---\n";
$teams = [
    'Real Madrid',
    'FC Barcelona', 
    'Atletico Madrid',
    'Valencia CF',
    'Sevilla FC',
    'Athletic Bilbao'
];

$teamIds = [];
foreach ($teams as $teamName) {
    $teamData = [
        'name' => $teamName,
        'season_id' => $seasonId
    ];
    
    $ch = curl_init("$baseUrl/teams");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($teamData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $teamResponse = curl_exec($ch);
    $teamHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($teamHttpCode === 201) {
        $teamData = json_decode($teamResponse, true);
        $teamIds[] = $teamData['data']['id'];
        echo "✅ Equipo creado: $teamName\n";
    } else {
        echo "❌ Error creando $teamName: $teamResponse\n";
    }
}

echo "\n--- Generando Preview del Fixture ---\n";

// 4. Preview del fixture
$ch = curl_init("$baseUrl/seasons/$seasonId/fixture/preview");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$previewResponse = curl_exec($ch);
$previewHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $previewHttpCode: ";
if ($previewHttpCode === 200) {
    $preview = json_decode($previewResponse, true);
    $fixture = $preview['data']['fixture_preview'];
    
    echo "✅ Preview generado exitosamente\n";
    echo "  - Total de rondas: {$fixture['total_rounds']}\n";
    echo "  - Total de partidos: {$fixture['total_matches']}\n";
    echo "  - Tiene descanso: " . ($fixture['has_bye'] ? 'Sí' : 'No') . "\n\n";
    
    // Mostrar algunas rondas
    echo "Primeras 3 rondas:\n";
    for ($i = 0; $i < min(3, count($fixture['rounds'])); $i++) {
        $round = $fixture['rounds'][$i];
        echo "  Ronda {$round['round_number']} ({$round['matches_count']} partidos):\n";
        foreach ($round['matches'] as $match) {
            echo "    - {$match['home_team_name']} vs {$match['away_team_name']}\n";
        }
        echo "\n";
    }
    
    // 5. Generar fixture en BD
    echo "--- Generando Fixture en Base de Datos ---\n";
    
    $ch = curl_init("$baseUrl/seasons/$seasonId/fixture/generate");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $generateResponse = curl_exec($ch);
    $generateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $generateHttpCode: ";
    if ($generateHttpCode === 200) {
        echo "✅ Fixture generado y guardado en BD\n";
        
        $generated = json_decode($generateResponse, true);
        $summary = $generated['data']['fixture_summary'];
        echo "  - Rondas creadas: {$summary['total_rounds']}\n";
        echo "  - Partidos creados: {$summary['total_matches']}\n";
    } else {
        echo "❌ Error al generar fixture: $generateResponse\n";
    }
    
} else {
    echo "❌ Error en preview: $previewResponse\n";
}

echo "\n=== FIN DEL TEST ===\n";