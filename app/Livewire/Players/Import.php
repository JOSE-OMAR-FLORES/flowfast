<?php

namespace App\Livewire\Players;

use App\Models\Player;
use App\Models\Team;
use App\Models\League;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Import extends Component
{
    use WithFileUploads;

    public $file;
    public $league_id;
    public $team_id;
    public $leagues;
    public $teams = [];
    
    // Estados del proceso
    public $step = 1; // 1: Upload, 2: Preview, 3: Result
    public $preview = [];
    public $validRows = [];
    public $invalidRows = [];
    public $imported = 0;
    public $importErrors = 0;

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->user_type === 'admin') {
            $this->leagues = League::orderBy('name')->get();
        } elseif ($user->user_type === 'league_manager') {
            $leagueManager = $user->userable;
            $this->leagues = League::where('id', $leagueManager->league_id)->get();
            $this->league_id = $leagueManager->league_id;
            $this->loadTeams();
        }
    }

    public function updatedLeagueId()
    {
        $this->team_id = '';
        $this->loadTeams();
    }

    public function loadTeams()
    {
        if ($this->league_id) {
            $seasonIds = \App\Models\Season::where('league_id', $this->league_id)->pluck('id');
            $this->teams = Team::whereIn('season_id', $seasonIds)
                ->orderBy('name')
                ->get();
        } else {
            $this->teams = collect();
        }
    }

    protected function rules()
    {
        return [
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240',
            'league_id' => 'required|exists:leagues,id',
            'team_id' => 'required|exists:teams,id',
        ];
    }

    protected function messages()
    {
        return [
            'file.required' => 'Debes seleccionar un archivo.',
            'file.mimes' => 'El archivo debe ser CSV, TXT o Excel (.xlsx, .xls).',
            'file.max' => 'El archivo no puede superar 10MB.',
            'league_id.required' => 'La liga es obligatoria.',
            'team_id.required' => 'El equipo es obligatorio.',
        ];
    }

    public function processFile()
    {
        $this->validate();

        try {
            $extension = $this->file->getClientOriginalExtension();
            
            if (in_array($extension, ['csv', 'txt'])) {
                $data = $this->parseCsv($this->file->getRealPath());
            } else {
                $data = $this->parseExcel($this->file->getRealPath());
            }

            $this->validateData($data);
            $this->step = 2;

        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    protected function parseCsv($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        // Leer encabezados
        $headers = fgetcsv($handle);
        
        // Leer filas
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        fclose($handle);
        return $data;
    }

    protected function parseExcel($filePath)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];
        
        // Leer encabezados
        $headers = [];
        foreach ($worksheet->getRowIterator(1, 1) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            foreach ($cellIterator as $cell) {
                $headers[] = $cell->getValue();
            }
        }
        
        // Leer filas
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            
            if (count($rowData) === count($headers) && !empty(array_filter($rowData))) {
                $data[] = array_combine($headers, $rowData);
            }
        }
        
        return $data;
    }

    protected function validateData($data)
    {
        $this->validRows = [];
        $this->invalidRows = [];

        foreach ($data as $index => $row) {
            $validator = Validator::make($row, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'jersey_number' => 'nullable|integer|min:0|max:999',
                'position' => 'required|in:goalkeeper,defender,midfielder,forward,Portero,Defensa,Mediocampista,Delantero',
                'status' => 'nullable|in:active,injured,suspended,inactive,Activo,Lesionado,Suspendido,Inactivo',
            ]);

            $row['row_number'] = $index + 2; // +2 porque Excel empieza en 1 y la primera es header

            if ($validator->fails()) {
                $row['errors'] = $validator->errors()->all();
                $this->invalidRows[] = $row;
            } else {
                // Normalizar posición y estado
                $row['position'] = $this->normalizePosition($row['position'] ?? '');
                $row['status'] = $this->normalizeStatus($row['status'] ?? 'active');
                
                // Verificar número de camiseta único
                if (!empty($row['jersey_number'])) {
                    $exists = Player::where('team_id', $this->team_id)
                        ->where('jersey_number', $row['jersey_number'])
                        ->exists();
                    
                    if ($exists) {
                        $row['errors'] = ["El número {$row['jersey_number']} ya está en uso."];
                        $this->invalidRows[] = $row;
                        continue;
                    }
                }
                
                $this->validRows[] = $row;
            }
        }

        $this->preview = [
            'total' => count($data),
            'valid' => count($this->validRows),
            'invalid' => count($this->invalidRows),
        ];
    }

    protected function normalizePosition($position)
    {
        $map = [
            'Portero' => 'goalkeeper',
            'Defensa' => 'defender',
            'Mediocampista' => 'midfielder',
            'Delantero' => 'forward',
        ];

        return $map[$position] ?? $position;
    }

    protected function normalizeStatus($status)
    {
        $map = [
            'Activo' => 'active',
            'Lesionado' => 'injured',
            'Suspendido' => 'suspended',
            'Inactivo' => 'inactive',
        ];

        return $map[$status] ?? $status;
    }

    public function import()
    {
        $this->imported = 0;
    $this->importErrors = 0;

        foreach ($this->validRows as $row) {
            try {
                Player::create([
                    'team_id' => $this->team_id,
                    'league_id' => $this->league_id,
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'birth_date' => $row['birth_date'] ?? null,
                    'jersey_number' => $row['jersey_number'] ?? null,
                    'position' => $row['position'],
                    'status' => $row['status'] ?? 'active',
                    'notes' => null,
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                $this->importErrors++;
                $row['errors'] = [$e->getMessage()];
                $this->invalidRows[] = $row;
            }
        }

        $this->step = 3;
    }

    public function resetImport()
    {
        $this->file = null;
        $this->step = 1;
        $this->preview = [];
        $this->validRows = [];
        $this->invalidRows = [];
        $this->imported = 0;
    $this->importErrors = 0;
    }

    public function render()
    {
        return view('livewire.players.import', [
            'positions' => Player::positions(),
            'statuses' => Player::statuses(),
        ])->layout('layouts.app');
    }
}
