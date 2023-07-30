<?php

namespace Database\Factories;

use App\ValueObjects\Despesas\ValorDespesa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Despesa>
 */
class DespesaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('pt_BR');

        return [
            'descricao' => $faker->text(191),
            'valor' => new ValorDespesa($faker->randomFloat(nbMaxDecimals:2, max:1000000)),
            'data' => $faker->date(),
        ];
    }
}
