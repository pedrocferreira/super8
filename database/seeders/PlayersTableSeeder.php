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
            ['name' => 'João Silva', 'email' => 'joao.silva@example.com', 'phone' => '11999991111', 'gender' => 'male'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '11999992222', 'gender' => 'female'],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@example.com', 'phone' => '11999993333', 'gender' => 'male'],
            ['name' => 'Ana Costa', 'email' => 'ana.costa@example.com', 'phone' => '11999994444', 'gender' => 'female'],
            ['name' => 'Lucas Souza', 'email' => 'lucas.souza@example.com', 'phone' => '11999995555', 'gender' => 'male'],
            ['name' => 'Julia Lima', 'email' => 'julia.lima@example.com', 'phone' => '11999996666', 'gender' => 'female'],
            ['name' => 'Rafael Santos', 'email' => 'rafael.santos@example.com', 'phone' => '11999997777', 'gender' => 'male'],
            ['name' => 'Carla Ferreira', 'email' => 'carla.ferreira@example.com', 'phone' => '11999998888', 'gender' => 'female'],
        ];

        // Lista de jogadores para o Super 12 (12 jogadores adicionais)
        $super12Players = [
            ['name' => 'Marcos Pereira', 'email' => 'marcos.pereira@example.com', 'phone' => '11999990001', 'gender' => 'male'],
            ['name' => 'Patricia Alves', 'email' => 'patricia.alves@example.com', 'phone' => '11999990002', 'gender' => 'female'],
            ['name' => 'Fernando Costa', 'email' => 'fernando.costa@example.com', 'phone' => '11999990003', 'gender' => 'male'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz.santos@example.com', 'phone' => '11999990004', 'gender' => 'female'],
            ['name' => 'Ricardo Lima', 'email' => 'ricardo.lima@example.com', 'phone' => '11999990005', 'gender' => 'male'],
            ['name' => 'Camila Oliveira', 'email' => 'camila.oliveira@example.com', 'phone' => '11999990006', 'gender' => 'female'],
            ['name' => 'Bruno Silva', 'email' => 'bruno.silva@example.com', 'phone' => '11999990007', 'gender' => 'male'],
            ['name' => 'Amanda Costa', 'email' => 'amanda.costa@example.com', 'phone' => '11999990008', 'gender' => 'female'],
            ['name' => 'Thiago Santos', 'email' => 'thiago.santos@example.com', 'phone' => '11999990009', 'gender' => 'male'],
            ['name' => 'Laura Ferreira', 'email' => 'laura.ferreira@example.com', 'phone' => '11999990010', 'gender' => 'female'],
            ['name' => 'Gabriel Souza', 'email' => 'gabriel.souza@example.com', 'phone' => '11999990011', 'gender' => 'male'],
            ['name' => 'Isabella Lima', 'email' => 'isabella.lima@example.com', 'phone' => '11999990012', 'gender' => 'female'],
        ];

        // Jogadores extras para substituições/reservas (10 jogadores)
        $reservePlayers = [
            ['name' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@example.com', 'phone' => '11999995555', 'gender' => 'male'],
            ['name' => 'Marcos Lima', 'email' => 'marcos.lima@example.com', 'phone' => '11999996666', 'gender' => 'male'],
            ['name' => 'Patricia Alves', 'email' => 'patricia.alves2@example.com', 'phone' => '11999990002', 'gender' => 'female'],
            ['name' => 'Fernando Costa', 'email' => 'fernando.costa2@example.com', 'phone' => '11999990003', 'gender' => 'male'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz.santos2@example.com', 'phone' => '11999990004', 'gender' => 'female'],
            ['name' => 'Ricardo Lima', 'email' => 'ricardo.lima2@example.com', 'phone' => '11999990005', 'gender' => 'male'],
            ['name' => 'Camila Oliveira', 'email' => 'camila.oliveira2@example.com', 'phone' => '11999990006', 'gender' => 'female'],
            ['name' => 'Bruno Silva', 'email' => 'bruno.silva2@example.com', 'phone' => '11999990007', 'gender' => 'male'],
            ['name' => 'Amanda Costa', 'email' => 'amanda.costa2@example.com', 'phone' => '11999990008', 'gender' => 'female'],
            ['name' => 'Thiago Santos', 'email' => 'thiago.santos2@example.com', 'phone' => '11999990009', 'gender' => 'male'],
            ['name' => 'Laura Ferreira', 'email' => 'laura.ferreira2@example.com', 'phone' => '11999990010', 'gender' => 'female'],
            ['name' => 'Gabriel Souza', 'email' => 'gabriel.souza2@example.com', 'phone' => '11999990011', 'gender' => 'male'],
            ['name' => 'Isabella Lima', 'email' => 'isabella.lima2@example.com', 'phone' => '11999990012', 'gender' => 'female'],
        ];

        // Combina todas as listas e cria os jogadores
        $allPlayers = array_merge($super8Players, $super12Players, $reservePlayers);

        // Cria jogadores adicionais usando Faker com gêneros consistentes
        for ($i = 0; $i < 20; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $allPlayers[] = [
                'name' => $gender === 'male' ? $faker->firstNameMale . ' ' . $faker->lastName : $faker->firstNameFemale . ' ' . $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->numerify('119#######'),
                'gender' => $gender
            ];
        }

        // Cria ou atualiza os jogadores
        foreach ($allPlayers as $player) {
            Player::updateOrCreate(
                ['email' => $player['email']],
                [
                    'name' => $player['name'],
                    'phone' => $player['phone'],
                    'gender' => $player['gender'] ?? null,
                    'category' => $faker->randomElement(['D', 'C', 'B']) // Categoria aleatória
                ]
            );
        }

        // Passo final: garantir que nenhum jogador permaneça sem gênero ou categoria
        $missingData = Player::whereNull('gender')->orWhereNull('category')->get();
        foreach ($missingData as $pl) {
            if (!$pl->gender) {
                $pl->gender = $faker->randomElement(['male', 'female']);
            }
            if (!$pl->category) {
                $pl->category = $faker->randomElement(['D', 'C', 'B']);
            }
            $pl->save();
        }
    }
}
