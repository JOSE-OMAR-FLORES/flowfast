<?php

// Test API de deportes
$loginUrl = 'http://localhost:8000/api/auth/login';
$sportsUrl = 'http://localhost:8000/api/sports';

// 1. Login para obtener token
$loginData = [
    'email' => 'admin@flowfast.com',
    'password' => 'password123'
];

echo "=== TEST API DE DEPORTES ===\n\n";

$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($loginHttpCode == 200) {
    $loginData = json_decode($loginResponse, true);
    $token = $loginData['data']['token'];
    
    echo "✅ Login exitoso\n\n";
    
    // 2. Obtener lista de deportes (público)
    echo "--- 1. Obtener lista de deportes ---\n";
    $ch = curl_init($sportsUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $sportsResponse = curl_exec($ch);
    $sportsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $sportsHttpCode: ";
    if ($sportsHttpCode == 200) {
        $sports = json_decode($sportsResponse, true);
        echo "✅ " . count($sports['data']) . " deportes obtenidos\n";
        foreach($sports['data'] as $sport) {
            echo "  - {$sport['name']} ({$sport['players_per_team']} jugadores)\n";
        }
    } else {
        echo "❌ Error al obtener deportes\n";
    }
    
    echo "\n--- 2. Crear nuevo deporte ---\n";
    // 3. Crear nuevo deporte (requiere admin)
    $newSportData = [
        'name' => 'Rugby',
        'players_per_team' => 15,
        'match_duration' => 80,
        'scoring_system' => [
            'try' => 5,
            'conversion' => 2,
            'penalty' => 3,
            'drop_goal' => 3
        ]
    ];
    
    $ch = curl_init($sportsUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newSportData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $createResponse = curl_exec($ch);
    $createHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $createHttpCode: ";
    if ($createHttpCode == 201) {
        $newSport = json_decode($createResponse, true);
        echo "✅ Deporte creado: {$newSport['data']['name']} (ID: {$newSport['data']['id']})\n";
        $sportId = $newSport['data']['id'];
        
        // 4. Obtener deporte específico
        echo "\n--- 3. Obtener deporte específico ---\n";
        $ch = curl_init("$sportsUrl/$sportId");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $sportResponse = curl_exec($ch);
        $sportHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP $sportHttpCode: ";
        if ($sportHttpCode == 200) {
            $sport = json_decode($sportResponse, true);
            echo "✅ Deporte obtenido: {$sport['data']['name']}\n";
            echo "  - Slug: {$sport['data']['slug']}\n";
            echo "  - Jugadores por equipo: {$sport['data']['players_per_team']}\n";
            echo "  - Duración partido: {$sport['data']['match_duration']} min\n";
        }
        
    } else {
        echo "❌ Error al crear deporte: $createResponse\n";
    }
    
} else {
    echo "❌ Error en login: $loginResponse\n";
}

echo "\n=== FIN DEL TEST ===\n";