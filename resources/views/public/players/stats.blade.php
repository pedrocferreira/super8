<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $player->name }} - Estatísticas | Sky Arena</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 10px;
        }
        .player-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .player-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
            font-weight: bold;
        }
        .player-details h2 {
            margin: 0;
            color: #1e293b;
        }
        .player-details p {
            margin: 5px 0;
            color: #64748b;
        }
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
        }
        .category-D { background: #10b981; }
        .category-C { background: #f59e0b; }
        .category-B { background: #ef4444; }
        
        .back-link {
            display: inline-block;
            background: #6b7280;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .back-link:hover {
            background: #4b5563;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #2563eb;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            color: #64748b;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        
        .partnerships-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .partnership-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .partnership-name {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .partnership-stats {
            font-size: 0.9em;
            color: #64748b;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
        
        .filters {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .filter-group select {
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }
        .filter-btn {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Voltar ao Ranking</a>
        
        <div class="header">
            <div class="player-info">
                <div class="player-avatar">
                    {{ strtoupper(substr($player->name, 0, 1)) }}
                </div>
                <div class="player-details">
                    <h2>{{ $player->name }}</h2>
                    <p>
                        <span class="category-badge category-{{ $player->category }}">
                            {{ $player->category }} - {{ $player->getCategoryName() }}
                        </span>
                    </p>
                    <p>
                        @if($player->gender === 'male')
                            👨 Masculino
                        @elseif($player->gender === 'female')
                            👩 Feminino
                        @else
                            ❓ Não definido
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <form method="GET">
                <div class="filter-group">
                    <label>Temporadas:</label>
                    <select name="seasons[]" multiple>
                        <option value="all" {{ in_array('all', $selectedSeasons) ? 'selected' : '' }}>Todas</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ in_array($season->id, $selectedSeasons) ? 'selected' : '' }}>
                                {{ $season->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Tipo de Análise:</label>
                    <select name="analysis_type">
                        <option value="combined" {{ $analysisType === 'combined' ? 'selected' : '' }}>Combinado</option>
                        <option value="comparison" {{ $analysisType === 'comparison' ? 'selected' : '' }}>Comparação</option>
                        <option value="evolution" {{ $analysisType === 'evolution' ? 'selected' : '' }}>Evolução</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">🔍 Filtrar</button>
            </form>
        </div>

        <!-- Estatísticas Gerais -->
        <div class="section">
            <h3>📊 Estatísticas Gerais</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_points'] ?? 0 }}</div>
                    <div class="stat-label">Pontos Totais</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_wins'] ?? 0 }}</div>
                    <div class="stat-label">Vitórias</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_losses'] ?? 0 }}</div>
                    <div class="stat-number">{{ $stats['tournaments_played'] ?? 0 }}</div>
                    <div class="stat-label">Torneios</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['win_rate'] ?? 0 }}%</div>
                    <div class="stat-label">Aproveitamento</div>
                </div>
            </div>
        </div>

        <!-- Parcerias -->
        <div class="section">
            <h3>🤝 Parcerias</h3>
            <div class="partnerships-grid">
                <div class="partnership-card">
                    <div class="partnership-name">Melhor Parceiro</div>
                    @if($partnershipStats['best_partner'])
                        <div class="partnership-stats">
                            <strong>{{ $partnershipStats['best_partner']->name }}</strong><br>
                            {{ $partnershipStats['best_partner']->wins }} vitórias
                        </div>
                    @else
                        <div class="partnership-stats">Nenhum parceiro ainda</div>
                    @endif
                </div>

                <div class="partnership-card">
                    <div class="partnership-name">Parceiro Menos Vitorioso</div>
                    @if($partnershipStats['worst_partner'])
                        <div class="partnership-stats">
                            <strong>{{ $partnershipStats['worst_partner']->name }}</strong><br>
                            {{ $partnershipStats['worst_partner']->wins }} vitórias
                        </div>
                    @else
                        <div class="partnership-stats">Nenhum parceiro ainda</div>
                    @endif
                </div>

                <div class="partnership-card">
                    <div class="partnership-name">Parceiro Mais Frequente</div>
                    @if($partnershipStats['most_frequent_partner'])
                        <div class="partnership-stats">
                            <strong>{{ $partnershipStats['most_frequent_partner']->name }}</strong><br>
                            {{ $partnershipStats['most_frequent_partner']->matches_count }} partidas
                        </div>
                    @else
                        <div class="partnership-stats">Nenhum parceiro ainda</div>
                    @endif
                </div>

                <div class="partnership-card">
                    <div class="partnership-name">Total de Parceiros</div>
                    <div class="partnership-stats">
                        <strong>{{ $partnershipStats['different_partners_count'] ?? 0 }}</strong><br>
                        parceiros diferentes
                    </div>
                </div>
            </div>
        </div>

        <!-- Confrontos Diretos -->
        <div class="section">
            <h3>⚔️ Confrontos Diretos</h3>
            <div class="partnerships-grid">
                <div class="partnership-card">
                    <div class="partnership-name">Vítima Favorita</div>
                    @if($headToHeadStats['favorite_victim'])
                        <div class="partnership-stats">
                            <strong>{{ $headToHeadStats['favorite_victim']->name }}</strong><br>
                            {{ $headToHeadStats['favorite_victim']->wins }} vitórias contra
                        </div>
                    @else
                        <div class="partnership-stats">Nenhum adversário ainda</div>
                    @endif
                </div>

                <div class="partnership-card">
                    <div class="partnership-name">Adversário Mais Difícil</div>
                    @if($headToHeadStats['toughest_opponent'])
                        <div class="partnership-stats">
                            <strong>{{ $headToHeadStats['toughest_opponent']->name }}</strong><br>
                            {{ $headToHeadStats['toughest_opponent']->wins }} vitórias contra você
                        </div>
                    @else
                        <div class="partnership-stats">Nenhum adversário ainda</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performance por Quadra -->
        <div class="section">
            <h3>🏟️ Performance por Quadra</h3>
            <div class="partnerships-grid">
                <div class="partnership-card">
                    <div class="partnership-name">Quadra da Sorte</div>
                    @if($courtStats['best_court'])
                        <div class="partnership-stats">
                            <strong>{{ $courtStats['best_court']->name }}</strong><br>
                            {{ number_format($courtStats['best_court']->win_rate, 1) }}% aproveitamento<br>
                            {{ $courtStats['best_court']->total_matches }} partidas
                        </div>
                    @else
                        <div class="partnership-stats">Sem dados suficientes</div>
                    @endif
                </div>

                <div class="partnership-card">
                    <div class="partnership-name">Quadra do Azar</div>
                    @if($courtStats['worst_court'])
                        <div class="partnership-stats">
                            <strong>{{ $courtStats['worst_court']->name }}</strong><br>
                            {{ number_format($courtStats['worst_court']->win_rate, 1) }}% aproveitamento<br>
                            {{ $courtStats['worst_court']->total_matches }} partidas
                        </div>
                    @else
                        <div class="partnership-stats">Sem dados suficientes</div>
                    @endif
                </div>
            </div>
        </div>

        @if(empty($stats) || ($stats['total_wins'] + $stats['total_losses']) == 0)
            <div class="empty-state">
                <h3>📊 Sem Dados</h3>
                <p>Este jogador ainda não possui estatísticas suficientes.</p>
                <p>Participe de mais torneios para ver suas estatísticas detalhadas!</p>
            </div>
        @endif
    </div>
</body>
</html>
