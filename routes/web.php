<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\AdminDashboard;
use App\Livewire\Coach\Dashboard as CoachDashboard;
use App\Livewire\Leagues\Index as LeaguesIndex;
use App\Livewire\Leagues\Create as LeaguesCreate;
use App\Livewire\Leagues\Edit as LeaguesEdit;
use App\Livewire\Seasons\Index as SeasonsIndex;
use App\Livewire\Seasons\Create as SeasonsCreate;
use App\Livewire\Seasons\Edit as SeasonsEdit;
use App\Livewire\Teams\Index as TeamsIndex;
use App\Livewire\Teams\Create as TeamsCreate;
use App\Livewire\Teams\Edit as TeamsEdit;
use App\Livewire\Fixtures\Index as FixturesIndex;
use App\Livewire\Fixtures\Generate as FixturesGenerate;
use App\Livewire\Standings\Index as StandingsIndex;
use App\Livewire\Financial\Dashboard as FinancialDashboard;
use App\Livewire\Public\Home as PublicHome;
use App\Livewire\Public\Leagues as PublicLeagues;
use App\Livewire\Public\LeagueHome;
use App\Livewire\Public\LeagueFixtures;
use App\Livewire\Public\LeagueStandings;
use App\Livewire\Public\LeagueTeams;
use App\Livewire\Invitations\Index as InvitationsIndex;
use App\Livewire\Invitations\Create as InvitationsCreate;
use App\Livewire\Invitations\Accept as InvitationsAccept;

// Public Routes (No authentication required)
Route::get('/', PublicHome::class)->name('public.home');
Route::get('/', PublicHome::class)->name('home'); // Alias
Route::get('/leagues', PublicLeagues::class)->name('public.leagues');
Route::get('/league/{slug}', LeagueHome::class)->name('public.league');
Route::get('/league/{slug}/fixtures', LeagueFixtures::class)->name('public.league.fixtures');
Route::get('/league/{slug}/standings', LeagueStandings::class)->name('public.league.standings');
Route::get('/league/{slug}/teams', LeagueTeams::class)->name('public.league.teams');

// Invitations - Public acceptance route
Route::get('/invite/{token}', InvitationsAccept::class)->name('invite.accept');

// Authentication Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (solo admin y league_manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    });
    
    // Referee Routes - Separate area for referees
    Route::middleware(['role:referee'])->prefix('referee')->name('referee.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Referee\Dashboard::class)->name('dashboard');
        Route::get('/my-payments', \App\Livewire\Referee\MyPayments::class)->name('my-payments');
        Route::get('/matches', FixturesIndex::class)->name('matches.index');
        Route::get('/matches/{matchId}/live', \App\Livewire\Matches\Live::class)->name('matches.live');
    });
    
    // Coach Routes - Separate area for coaches
    Route::middleware(['role:coach'])->prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', CoachDashboard::class)->name('dashboard');
        Route::get('/teams', TeamsIndex::class)->name('teams.index');
        Route::get('/teams/{team}', TeamsIndex::class)->name('teams.show');
        Route::get('/teams/{team}/edit', TeamsEdit::class)->name('teams.edit');
        Route::get('/players', \App\Livewire\Players\Index::class)->name('players.index');
        Route::get('/fixtures', FixturesIndex::class)->name('fixtures');
        Route::get('/standings', StandingsIndex::class)->name('standings');
        Route::get('/payments', \App\Livewire\Payments\TeamPayments::class)->name('payments.index');
        Route::get('/appeals', \App\Livewire\Coach\Appeals::class)->name('appeals');
    });
    
    // Player Routes - Separate area for players  
    Route::middleware(['role:player'])->prefix('player')->name('player.')->group(function () {
        Route::get('/team', TeamsIndex::class)->name('team.index');
        Route::get('/fixtures', FixturesIndex::class)->name('fixtures.index');
        Route::get('/standings', StandingsIndex::class)->name('standings.index');
    });
    
    // Leagues Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/leagues', LeaguesIndex::class)->name('leagues.index');
        Route::get('/admin/leagues/create', LeaguesCreate::class)->name('leagues.create')->middleware('role:admin');
        Route::get('/admin/leagues/{league}/edit', LeaguesEdit::class)->name('leagues.edit');
    }); 
    
    // Seasons Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/seasons', SeasonsIndex::class)->name('seasons.index');
        Route::get('/admin/seasons/create', SeasonsCreate::class)->name('seasons.create');
        Route::get('/admin/seasons/{season}/edit', SeasonsEdit::class)->name('seasons.edit');
    });
    
    // Teams Routes (Admin, League Manager & Coach)
    Route::middleware(['role:admin,league_manager,coach'])->group(function () {
        Route::get('/admin/teams', TeamsIndex::class)->name('teams.index');
        Route::get('/admin/teams/create', TeamsCreate::class)->name('teams.create');
        Route::get('/admin/teams/{team}/edit', TeamsEdit::class)->name('teams.edit');
    });
    
    // Fixtures Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/fixtures', FixturesIndex::class)->name('fixtures.index');
        Route::get('/admin/fixtures/generate', FixturesGenerate::class)->name('fixtures.generate');
        Route::get('/admin/fixtures/{fixtureId}/manage', \App\Livewire\Fixtures\Manage::class)->name('fixtures.manage');
    });

    // Matches Routes (Admin, League Manager & Referee)
    Route::middleware(['role:admin,league_manager,referee'])->group(function () {
        Route::get('/admin/matches/{matchId}/live', \App\Livewire\Matches\Live::class)->name('matches.live');
    });

    // Friendly Matches Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/friendly-matches', \App\Livewire\FriendlyMatches\Index::class)->name('friendly-matches.index');
        Route::get('/admin/friendly-matches/create', \App\Livewire\FriendlyMatches\Create::class)->name('friendly-matches.create');
        Route::get('/admin/friendly-matches/{id}', \App\Livewire\FriendlyMatches\Show::class)->name('friendly-matches.show');
    });
    
    // Invitations Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/invitations', InvitationsIndex::class)->name('invitations.index');
        Route::get('/admin/invitations/create', InvitationsCreate::class)->name('invitations.create');
    });

    // Appeals Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/appeals', \App\Livewire\Admin\Appeals::class)->name('appeals.index');
    });
    
    // Referees Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->prefix('admin')->group(function () {
        Route::get('/referees', \App\Livewire\Referees\Index::class)->name('referees.index');
        Route::get('/referees/{referee}/edit', \App\Livewire\Referees\Edit::class)->name('referees.edit');
    });
    
    // Coaches Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->prefix('admin')->group(function () {
        Route::get('/coaches', \App\Livewire\Coaches\Index::class)->name('coaches.index');
        Route::get('/coaches/{coach}/edit', \App\Livewire\Coaches\Edit::class)->name('coaches.edit');
    });
    
    // Players Routes (Admin, League Manager & Coach)
    Route::middleware(['role:admin,league_manager,coach'])->group(function () {
        Route::get('/admin/players', \App\Livewire\Players\Index::class)->name('players.index');
        Route::get('/admin/players/create', \App\Livewire\Players\Create::class)->name('players.create');
        Route::get('/admin/players/import', \App\Livewire\Players\Import::class)->name('players.import');
        Route::get('/admin/players/download-template', [App\Http\Controllers\PlayerTemplateController::class, 'downloadTemplate'])->name('players.download-template');
        Route::get('/admin/players/{player}/edit', \App\Livewire\Players\Edit::class)->name('players.edit');
    });
    
    // Standings Routes (All authenticated users can view)
    Route::get('/admin/standings', StandingsIndex::class)->name('standings.index');
    
    // Financial Routes (Admin & League Manager)
    Route::middleware(['role:admin,league_manager'])->prefix('admin/financial')->name('financial.')->group(function () {
        Route::get('/dashboard/{leagueId}', FinancialDashboard::class)->name('dashboard');
        
        // Income Routes - con filtro opcional por liga
        Route::get('/income/{leagueId?}', \App\Livewire\Financial\Income\Index::class)->name('income.index');
        Route::get('/income/create/{leagueId?}', \App\Livewire\Financial\Income\Create::class)->name('income.create');
        
        // Expense Routes - con filtro opcional por liga
        Route::get('/expense/{leagueId?}', \App\Livewire\Financial\Expense\Index::class)->name('expense.index');
        Route::get('/expense/create/{leagueId?}', \App\Livewire\Financial\Expense\Create::class)->name('expense.create');
    });
    
    // Payment Routes (All authenticated users can view their team payments)
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/team/{teamId?}', \App\Livewire\Payments\TeamPayments::class)->name('team');
        
        // Referee Payments (Admin & League Manager only)
        Route::middleware(['role:admin,league_manager'])->group(function () {
            Route::get('/referees', \App\Livewire\Payments\RefereePayments::class)->name('referees');
        });
    });
    
    // Default redirect based on user role
    Route::get('/dashboard', function () {
        return redirect('/admin');
    })->name('dashboard.redirect');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
 