<?php
// update_assigned_leagues.php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

// ID del usuario que quieres actualizar
$userId = 7; // Cambia por el ID correspondiente

// IDs de ligas que quieres asignar, separados por coma
$leagueIds = [1, 2, 3, 5]; // Cambia por los IDs que necesites

$user = User::find($userId);
if ($user) {
    $user->assigned_leagues = implode(',', $leagueIds);
    $user->save();
    echo "assigned_leagues actualizado correctamente.\n";
} else {
    echo "Usuario no encontrado.\n";
}
