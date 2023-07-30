<?php

namespace Tests\Feature;

use App\Models\Despesa;
use App\Models\User;
use App\Notifications\Despesas\DespesaCriada;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DespesasTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    public function test_usuario_autenticado_pode_criar_despesa()
    {
        Notification::fake();
        $user = User::factory()->create();

        $despesa = [
            'descricao' => 'Gastos com Uber',
            'data' => '2023-04-25',
            'valor' => 200.58
        ];

        $response = $this->actingAs($user)->post('/api/despesas', $despesa);

        $response->assertStatus(201);
        $this->assertDatabaseCount('despesas', 1);
        $this->assertDatabaseHas('despesas', $despesa);
        $this->assertTrue($user->despesas->contains(Despesa::first()));
        Notification::assertSentTo($user, DespesaCriada::class);
    }

    public function test_usuario_autenticado_pode_listar_despesas()
    {
        $user = User::factory()->create();
        Despesa::factory()->count(5)->for($user)->create();
        Despesa::factory()->count(10)->for(User::factory())->create();

        $response = $this->actingAs($user)->getJson('/api/despesas');

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['meta', 'links'])
                ->has('data', 5, fn (AssertableJson $jsonDespesas) =>
                    $jsonDespesas->whereType('id', 'string')
                        ->whereType('descricao', 'string')
                        ->whereType('valor', ['integer', 'double'])
                        ->whereType('data', 'string')
                )
        );
    }
}
