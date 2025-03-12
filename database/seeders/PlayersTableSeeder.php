<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use Faker\Factory as Faker;

class PlayersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        // Lista de jogadores para o Super 8 (8 jogadores)
        $super8Players = [
            ['name' => 'João Silva', 'email' => 'joao.silva@example.com', 'phone' => '11999991111'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '11999992222'],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@example.com', 'phone' => '11999993333'],
            ['name' => 'Ana Costa', 'email' => 'ana.costa@example.com', 'phone' => '11999994444'],
            ['name' => 'Lucas Souza', 'email' => 'lucas.souza@example.com', 'phone' => '11999995555'],
            ['name' => 'Julia Lima', 'email' => 'julia.lima@example.com', 'phone' => '11999996666'],
            ['name' => 'Rafael Santos', 'email' => 'rafael.santos@example.com', 'phone' => '11999997777'],
            ['name' => 'Carla Ferreira', 'email' => 'carla.ferreira@example.com', 'phone' => '11999998888'],
        ];

        // Lista de jogadores para o Super 12 (12 jogadores adicionais)
        $super12Players = [
            ['name' => 'Marcos Pereira', 'email' => 'marcos.pereira@example.com', 'phone' => '11999990001'],
            ['name' => 'Patricia Alves', 'email' => 'patricia.alves@example.com', 'phone' => '11999990002'],
            ['name' => 'Fernando Costa', 'email' => 'fernando.costa@example.com', 'phone' => '11999990003'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz.santos@example.com', 'phone' => '11999990004'],
            ['name' => 'Ricardo Lima', 'email' => 'ricardo.lima@example.com', 'phone' => '11999990005'],
            ['name' => 'Camila Oliveira', 'email' => 'camila.oliveira@example.com', 'phone' => '11999990006'],
            ['name' => 'Bruno Silva', 'email' => 'bruno.silva@example.com', 'phone' => '11999990007'],
            ['name' => 'Amanda Costa', 'email' => 'amanda.costa@example.com', 'phone' => '11999990008'],
            ['name' => 'Thiago Santos', 'email' => 'thiago.santos@example.com', 'phone' => '11999990009'],
            ['name' => 'Laura Ferreira', 'email' => 'laura.ferreira@example.com', 'phone' => '11999990010'],
            ['name' => 'Gabriel Souza', 'email' => 'gabriel.souza@example.com', 'phone' => '11999990011'],
            ['name' => 'Isabella Lima', 'email' => 'isabella.lima@example.com', 'phone' => '11999990012'],
        ];

        // Jogadores extras para substituições/reservas (10 jogadores)
        $reservePlayers = [
            ['name' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@example.com', 'phone' => '11999995555'],
            ['name' => 'Marcos Lima', 'email' => 'marcos.lima@example.com', 'phone' => '11999996666'],
            ['name' => 'Patricia Alves', 'email' => 'patricia.alves@example.com', 'phone' => '11999990002'],
            ['name' => 'Fernando Costa', 'email' => 'fernando.costa@example.com', 'phone' => '11999990003'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz.santos@example.com', 'phone' => '11999990004'],
            ['name' => 'Ricardo Lima', 'email' => 'ricardo.lima@example.com', 'phone' => '11999990005'],
            ['name' => 'Camila Oliveira', 'email' => 'camila.oliveira@example.com', 'phone' => '11999990006'],
            ['name' => 'Bruno Silva', 'email' => 'bruno.silva@example.com', 'phone' => '11999990007'],
            ['name' => 'Amanda Costa', 'email' => 'amanda.costa@example.com', 'phone' => '11999990008'],
            ['name' => 'Thiago Santos', 'email' => 'thiago.santos@example.com', 'phone' => '11999990009'],
            ['name' => 'Laura Ferreira', 'email' => 'laura.ferreira@example.com', 'phone' => '11999990010'],
            ['name' => 'Gabriel Souza', 'email' => 'gabriel.souza@example.com', 'phone' => '11999990011'],
            ['name' => 'Isabella Lima', 'email' => 'isabella.lima@example.com', 'phone' => '11999990012'],
        ];

        // Combina todas as listas e cria os jogadores
        $allPlayers = array_merge($super8Players, $super12Players, $reservePlayers);

        // Cria jogadores adicionais usando Faker
        for ($i = 0; $i < 20; $i++) {
            $allPlayers[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->numerify('119#######')
            ];
        }

        // Cria ou atualiza os jogadores
        foreach ($allPlayers as $player) {
            Player::updateOrCreate(
                ['email' => $player['email']],
                [
                    'name' => $player['name'],
                    'phone' => $player['phone']
                ]
            );
        }
    }
}
