<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RankingBalanceService;
use App\Models\Season;
use App\Models\Tournament;

class ApplyRankingBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranking:balance 
                            {--season= : ID da temporada específica}
                            {--tournament= : ID do torneio específico}
                            {--all : Aplicar a todas as temporadas}
                            {--dry-run : Simular sem aplicar mudanças}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aplica o sistema de balanceamento de ranking para motivar jogadores com menos pontos';

    private RankingBalanceService $balanceService;

    public function __construct(RankingBalanceService $balanceService)
    {
        parent::__construct();
        $this->balanceService = $balanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Iniciando aplicação do sistema de balanceamento de ranking...');

        if ($this->option('dry-run')) {
            $this->warn('🔍 Modo de simulação ativado - nenhuma mudança será aplicada');
        }

        if ($this->option('all')) {
            $this->applyToAllSeasons();
        } elseif ($this->option('season')) {
            $this->applyToSeason($this->option('season'));
        } elseif ($this->option('tournament')) {
            $this->applyToTournament($this->option('tournament'));
        } else {
            $this->error('❌ Especifique uma opção: --season=ID, --tournament=ID ou --all');
            return 1;
        }

        $this->info('✅ Balanceamento aplicado com sucesso!');
        return 0;
    }

    private function applyToAllSeasons()
    {
        $seasons = Season::all();
        $this->info("📊 Aplicando balanceamento a {$seasons->count()} temporadas...");

        $progressBar = $this->output->createProgressBar($seasons->count());
        $progressBar->start();

        foreach ($seasons as $season) {
            $this->applyToSeason($season->id, false);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function applyToSeason($seasonId, $showProgress = true)
    {
        $season = Season::find($seasonId);
        if (!$season) {
            $this->error("❌ Temporada com ID {$seasonId} não encontrada");
            return;
        }

        if ($showProgress) {
            $this->info("📈 Aplicando balanceamento à temporada: {$season->name}");
        }

        // Verificar estatísticas antes do balanceamento
        $statsBefore = $this->getSeasonStats($seasonId);
        
        if ($showProgress) {
            $this->displaySeasonStats($statsBefore, 'Antes do balanceamento');
        }

        if (!$this->option('dry-run')) {
            $this->balanceService->applyBalanceToSeason($seasonId);
        }

        // Verificar estatísticas depois do balanceamento
        $statsAfter = $this->getSeasonStats($seasonId);
        
        if ($showProgress) {
            $this->displaySeasonStats($statsAfter, 'Depois do balanceamento');
            $this->displayBalanceImpact($statsBefore, $statsAfter);
        }
    }

    private function applyToTournament($tournamentId)
    {
        $tournament = Tournament::find($tournamentId);
        if (!$tournament) {
            $this->error("❌ Torneio com ID {$tournamentId} não encontrado");
            return;
        }

        $this->info("🏆 Aplicando balanceamento ao torneio: {$tournament->name}");

        if (!$this->option('dry-run')) {
            $this->balanceService->applyBalanceToTournament($tournamentId);
        }

        $this->info("✅ Balanceamento aplicado ao torneio {$tournament->name}");
    }

    private function getSeasonStats($seasonId)
    {
        $ranking = $this->balanceService->calculateSeasonRanking($seasonId);
        
        return [
            'total_players' => $ranking->count(),
            'total_points' => $ranking->sum('total_points'),
            'avg_points' => $ranking->avg('total_points'),
            'top_player' => $ranking->first(),
            'bottom_player' => $ranking->last()
        ];
    }

    private function displaySeasonStats($stats, $title)
    {
        $this->info("📊 {$title}:");
        $this->line("   👥 Total de jogadores: {$stats['total_players']}");
        $this->line("   🎯 Total de pontos: {$stats['total_points']}");
        $this->line("   📈 Média de pontos: " . round($stats['avg_points'], 1));
        
        if ($stats['top_player']) {
            $this->line("   🏆 Líder: {$stats['top_player']['player_name']} ({$stats['top_player']['total_points']} pts)");
        }
        
        if ($stats['bottom_player']) {
            $this->line("   📉 Último: {$stats['bottom_player']['player_name']} ({$stats['bottom_player']['total_points']} pts)");
        }
    }

    private function displayBalanceImpact($statsBefore, $statsAfter)
    {
        $pointsDiff = $statsAfter['total_points'] - $statsBefore['total_points'];
        $avgDiff = $statsAfter['avg_points'] - $statsBefore['avg_points'];
        
        $this->newLine();
        $this->info("📈 Impacto do balanceamento:");
        $this->line("   🎯 Diferença total de pontos: " . ($pointsDiff > 0 ? '+' : '') . $pointsDiff);
        $this->line("   📊 Diferença média por jogador: " . ($avgDiff > 0 ? '+' : '') . round($avgDiff, 1));
        
        if ($pointsDiff > 0) {
            $this->warn("   ⚠️  O balanceamento aumentou o total de pontos");
        } elseif ($pointsDiff < 0) {
            $this->info("   ✅ O balanceamento reduziu o total de pontos");
        } else {
            $this->info("   ➡️  O balanceamento manteve o total de pontos");
        }
    }
}