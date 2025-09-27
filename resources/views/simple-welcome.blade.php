<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sky Arena - Ranking</title>
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
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 10px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        .ranking-section {
            margin-top: 30px;
        }
        .ranking-title {
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
        .login-link {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .login-link:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 Sky Arena - Sistema de Ranking</h1>
            <p>Sistema de ranking para esportes de areia</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ $systemStats['total_players'] }}</div>
                <div class="stat-label">Jogadores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $systemStats['total_seasons'] }}</div>
                <div class="stat-label">Temporadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $systemStats['total_tournaments'] }}</div>
                <div class="stat-label">Torneios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $systemStats['total_matches'] }}</div>
                <div class="stat-label">Partidas</div>
            </div>
        </div>

        <div class="ranking-section">
            <h2 class="ranking-title">🏅 Ranking Geral</h2>
            
            <!-- Estatísticas Rápidas do Top 3 -->
            @if($playerRanking->count() >= 3)
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 15px 0; text-align: center;">🏆 Top 3 Jogadores</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        @foreach($playerRanking->take(3) as $index => $player)
                            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5em; font-weight: bold; margin-bottom: 5px;">
                                    {{ $index + 1 }}º {{ $player->name }}
                                </div>
                                <div style="font-size: 0.9em; opacity: 0.9;">
                                    {{ $player->total_points }} pontos • {{ $player->total_wins }} vitórias
                                </div>
                                <div style="margin-top: 8px;">
                                    <a href="{{ route('public.player.stats', $player->id) }}" 
                                       style="background: rgba(255,255,255,0.2); color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.8em;">
                                        Ver Estatísticas
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Filtros de Ranking -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #374151;">Tipo de Ranking:</label>
                        <select name="type" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            <option value="general" {{ $rankingType === 'general' ? 'selected' : '' }}>Geral</option>
                            <option value="category" {{ $rankingType === 'category' ? 'selected' : '' }}>Por Categoria</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #374151;">Categoria:</label>
                        <select name="category" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="D" {{ $category === 'D' ? 'selected' : '' }}>D - Iniciante</option>
                            <option value="C" {{ $category === 'C' ? 'selected' : '' }}>C - Intermediário</option>
                            <option value="B" {{ $category === 'B' ? 'selected' : '' }}>B - Avançado</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #374151;">Gênero:</label>
                        <select name="gender" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            <option value="all" {{ $gender === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Masculino</option>
                            <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Feminino</option>
                        </select>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">🔍 Filtrar</button>
                        <a href="/" style="background: #6b7280; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">🔄 Limpar</a>
                    </div>
                </form>
            </div>
            
            @if($playerRanking->count() > 0)
                <div class="ranking-table">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Posição</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0;">Jogador</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Categoria</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Gênero</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Pontos</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Vitórias</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Derrotas</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;">Torneios</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($playerRanking as $index => $player)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px; font-weight: bold;">{{ $index + 1 }}º</td>
                                    <td style="padding: 12px;">
                                        <a href="{{ route('public.player.stats', $player->id) }}" 
                                           style="color: #2563eb; text-decoration: none; font-weight: 500;">
                                            {{ $player->name }}
                                        </a>
                                        <br>
                                        <small style="color: #6b7280;">Clique para ver estatísticas</small>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        @php
                                            $categoryName = match($player->category) {
                                                'D' => 'Iniciante',
                                                'C' => 'Intermediário',
                                                'B' => 'Avançado',
                                                default => 'Não definido'
                                            };
                                            $categoryColor = match($player->category) {
                                                'D' => '#10b981',
                                                'C' => '#f59e0b', 
                                                'B' => '#ef4444',
                                                default => '#6b7280'
                                            };
                                        @endphp
                                        <span style="background: {{ $categoryColor }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">
                                            {{ $player->category }} - {{ $categoryName }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        @if($player->gender === 'male')
                                            <span style="color: #2563eb;">👨 Masculino</span>
                                        @elseif($player->gender === 'female')
                                            <span style="color: #ec4899;">👩 Feminino</span>
                                        @else
                                            <span style="color: #6b7280;">❓ Não definido</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; text-align: center; font-weight: bold; color: #2563eb;">{{ $player->total_points }}</td>
                                    <td style="padding: 12px; text-align: center; color: #059669;">{{ $player->total_wins }}</td>
                                    <td style="padding: 12px; text-align: center; color: #dc2626;">{{ $player->total_losses }}</td>
                                    <td style="padding: 12px; text-align: center;">
                                        {{ $player->tournaments_played }}
                                        <br>
                                        <a href="{{ route('public.player.stats', $player->id) }}" 
                                           style="background: #2563eb; color: white; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 0.8em; display: inline-block; margin-top: 5px;">
                                            📊 Ver Stats
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h3>📊 Sistema Vazio</h3>
                    <p>Não há jogadores cadastrados ainda.</p>
                    <p>Faça login como administrador para começar a usar o sistema.</p>
                    <a href="/login" class="login-link">🔐 Fazer Login</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>


