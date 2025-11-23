<?php

// Test crear liga
$loginUrl = 'http://localhost:8000/api/auth/login';
$leagueUrl = 'http://localhost:8000/api/leagues';

// 1. Login para obtener token
$loginData = [
    'email' => 'admin@flowfast.com',
    'password' => 'password123'
];

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

echo "Login Response (HTTP $loginHttpCode): $loginResponse\n\n";

if ($loginHttpCode == 200) {
    $loginData = json_decode($loginResponse, true);
    $token = $loginData['data']['token'];
    
    // 2. Crear liga
    $leagueData = [
        'name' => 'Liga de Básquet Test PHP',
        'sport_id' => 2,
        'description' => 'Liga creada desde PHP para test',
        'registration_fee' => 400.50,
        'match_fee_per_team' => 80.00
    ];
    
    $ch = curl_init($leagueUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($leagueData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $leagueResponse = curl_exec($ch);
    $leagueHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "League Creation Response (HTTP $leagueHttpCode): $leagueResponse\n";
    
    if ($leagueHttpCode == 201) {
        echo "\n✅ Liga creada exitosamente!\n";
    } else {
        echo "\n❌ Error al crear liga\n";
    }
} else {
    echo "Error en login\n";
}