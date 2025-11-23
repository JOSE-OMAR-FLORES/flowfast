<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PlayerTemplateController extends Controller
{
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="plantilla-jugadores.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'first_name',
                'last_name',
                'email',
                'phone',
                'birth_date',
                'jersey_number',
                'position',
                'status'
            ]);
            
            // Ejemplos
            fputcsv($file, [
                'Juan',
                'Pérez',
                'juan.perez@example.com',
                '555-1234',
                '1995-05-15',
                '10',
                'midfielder',
                'active'
            ]);
            
            fputcsv($file, [
                'Carlos',
                'González',
                'carlos.gonzalez@example.com',
                '555-5678',
                '1998-08-22',
                '1',
                'goalkeeper',
                'active'
            ]);
            
            fputcsv($file, [
                'Luis',
                'Martínez',
                'luis.martinez@example.com',
                '555-9012',
                '1997-03-10',
                '5',
                'defender',
                'active'
            ]);
            
            fputcsv($file, [
                'Pedro',
                'Rodríguez',
                '',
                '',
                '1996-11-30',
                '9',
                'forward',
                'active'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
