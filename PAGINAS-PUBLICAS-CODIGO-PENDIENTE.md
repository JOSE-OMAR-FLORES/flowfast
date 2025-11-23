# 🌐 PÁGINAS PÚBLICAS - Código Restante para Implementar

## Componentes PHP

### 1. LeagueFixtures.php
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Fixture;

class LeagueFixtures extends Component
{
    public $slug;
    public $league;
    public $activeSeason;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport'])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons()->where('status', 'active')->latest()->first();
    }

    public function render()
    {
        $fixtures = collect();
        
        if ($this->activeSeason) {
            $fixtures = Fixture::where('season_id', $this->activeSeason->id)
                ->with(['homeTeam', 'awayTeam', 'venue'])
                ->orderBy('match_date', 'desc')
                ->orderBy('match_time', 'desc')
                ->get()
                ->groupBy(function($fixture) {
                    return $fixture->match_date->format('Y-m-d');
                });
        }

        return view('livewire.public.league-fixtures', [
            'fixtures' => $fixtures
        ])->layout('layouts.public', ['title' => 'Calendario - ' . $this->league->name]);
    }
}
```

### 2. LeagueStandings.php
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Standing;

class LeagueStandings extends Component
{
    public $slug;
    public $league;
    public $activeSeason;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport'])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons()->where('status', 'active')->latest()->first();
    }

    public function render()
    {
        $standings = collect();
        
        if ($this->activeSeason) {
            $standings = Standing::with(['team.club'])
                ->where('season_id', $this->activeSeason->id)
                ->orderBy('points', 'desc')
                ->orderBy('goal_difference', 'desc')
                ->orderBy('goals_for', 'desc')
                ->get();
        }

        return view('livewire.public.league-standings', [
            'standings' => $standings
        ])->layout('layouts.public', ['title' => 'Posiciones - ' . $this->league->name]);
    }
}
```

### 3. LeagueTeams.php
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\League;
use App\Models\Team;

class LeagueTeams extends Component
{
    public $slug;
    public $league;
    public $activeSeason;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->league = League::where('slug', $slug)
            ->where('is_public', true)
            ->with(['sport'])
            ->firstOrFail();

        $this->activeSeason = $this->league->seasons()->where('status', 'active')->latest()->first();
    }

    public function render()
    {
        $teams = collect();
        
        if ($this->activeSeason) {
            $teams = Team::whereHas('seasons', function($query) {
                $query->where('season_id', $this->activeSeason->id);
            })->with(['club'])->get();
        }

        return view('livewire.public.league-teams', [
            'teams' => $teams
        ])->layout('layouts.public', ['title' => 'Equipos - ' . $this->league->name]);
    }
}
```

---

## Rutas (web.php)

```php
// Páginas Públicas (sin autenticación)
Route::get('/', \App\Livewire\Public\Home::class)->name('home');
Route::get('/leagues', \App\Livewire\Public\Leagues::class)->name('public.leagues');

// League Pages
Route::prefix('league/{slug}')->group(function () {
    Route::get('/', \App\Livewire\Public\LeagueHome::class)->name('public.league');
    Route::get('/fixtures', \App\Livewire\Public\LeagueFixtures::class)->name('public.league.fixtures');
    Route::get('/standings', \App\Livewire\Public\LeagueStandings::class)->name('public.league.standings');
    Route::get('/teams', \App\Livewire\Public\LeagueTeams::class)->name('public.league.teams');
});
```

---

## Nota

Por límite de tokens, las vistas Blade están muy extensas. 

**Siguiente acción**: Crear las vistas blade minimalistas funcionales para:
- league-fixtures.blade.php
- league-standings.blade.php  
- league-teams.blade.php
- Agregar las rutas a web.php

**¿Deseas que continúe creando las vistas restantes?**
