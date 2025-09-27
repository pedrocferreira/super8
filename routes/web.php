<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\TournamentRegistrationController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\PublicRankingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeasonController;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas de Torneios
    Route::resource('tournaments', TournamentController::class);

    // Rotas de Temporadas
    Route::resource('seasons', SeasonController::class)->except(['show']);
    
    // Adiciona o middleware log.errors apenas nesta rota
    Route::post('/tournaments/{tournament}/generate-matches', [TournamentController::class, 'generateMatches'])
        ->name('tournaments.generate-matches');
    Route::post('/tournaments/{tournament}/select-players', [TournamentController::class, 'selectPlayers'])
        ->name('tournaments.select-players');
    Route::post('/tournaments/{tournament}/select-pairs', [TournamentController::class, 'selectPairs'])
        ->name('tournaments.select-pairs');

    // Rotas de Jogadores
    Route::resource('players', PlayerController::class);

    // Rotas de Partidas
    Route::resource('matches', MatchController::class);

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dentro do grupo de rotas autenticadas
    Route::patch('matches/{match}/update-score', [MatchController::class, 'updateScore'])
        ->name('matches.update-score');

    // Dentro do grupo de rotas autenticadas
    Route::get('/tournaments/{tournament}/select-pairs', [TournamentController::class, 'selectPairsForm'])
        ->name('tournaments.select-pairs-form');
    Route::post('/tournaments/{tournament}/select-pairs', [TournamentController::class, 'storePairs'])
        ->name('tournaments.store-pairs');

    // Adicione esta nova rota
    Route::get('/tournaments/{tournament}/pairs', [TournamentController::class, 'showPairs'])
        ->name('tournaments.show-pairs');

    // Dentro do grupo middleware auth
    Route::post('/tournaments/{tournament}/generate-random-pairs', [TournamentController::class, 'generateRandomPairs'])
        ->name('tournaments.generate-random-pairs');
});

// Rotas públicas para estatísticas dos jogadores
Route::get('/player-stats', function() {
    return view('player-stats.search');
})->name('player-stats.search');

Route::get('/player-stats/show', function(Request $request) {
    $player = Player::where('email', $request->email)->first();

    if (!$player) {
        return redirect()->route('player-stats.search')
            ->with('error', 'Jogador não encontrado.');
    }

    return view('player-stats.show', compact('player'));
})->name('player-stats.show');

Route::put('/tournaments/matches/{matchId}/score', [TournamentController::class, 'updateScore'])
    ->name('tournaments.matches.update-score');

// Rotas públicas para registro em torneios
Route::get('/tournament-register/{code}', [TournamentRegistrationController::class, 'showRegistration'])
    ->name('tournament.register');
Route::post('/tournament-register/{code}', [TournamentRegistrationController::class, 'register'])
    ->name('tournament.register.submit');

// Rotas do sistema de ranking e balanceamento (requer login)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/rankings/season/{seasonId}', [RankingController::class, 'season'])
        ->name('rankings.season');
    Route::get('/rankings/player/{playerId}/season/{seasonId}/balance', [RankingController::class, 'playerBalance'])
        ->name('rankings.player.balance');
    Route::post('/rankings/season/{seasonId}/apply-balance', [RankingController::class, 'applyBalance'])
        ->name('rankings.apply.balance');
    Route::get('/rankings/statistics', [RankingController::class, 'statistics'])
        ->name('rankings.statistics');
});

// Rotas públicas do sistema de ranking (não requer login)
Route::get('/public/rankings', [PublicRankingController::class, 'index'])
    ->name('public.rankings.index');
Route::get('/public/rankings/season/{seasonId}', [PublicRankingController::class, 'season'])
    ->name('public.rankings.season');
Route::get('/public/rankings/player/{playerId}/season/{seasonId}/balance', [PublicRankingController::class, 'playerBalance'])
    ->name('public.rankings.player.balance');
Route::get('/public/rankings/statistics', [PublicRankingController::class, 'statistics'])
    ->name('public.rankings.statistics');
Route::get('/public/player/{playerId}/stats', [PublicRankingController::class, 'playerStats'])
    ->name('public.player.stats');

require __DIR__.'/auth.php';

