<?php

namespace Tests\Feature;

use App\Models\Despesa;
use App\Models\User;
use App\Notifications\Despesas\DespesaCriada;
use Carbon\Carbon;
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

    public function test_usuario_autenticado_pode_visualizar_despesa()
    {
       $user = User::factory()->create();
       $despesa = Despesa::factory()->for($user)->create();

       $response = $this->actingAs($user)->getJson("api/despesas/$despesa->id");

       $response->assertOk()
        ->assertExactJson([
            'id' => $despesa->id,
            'descricao' => $despesa->descricao,
            'valor' => $despesa->valor->emReais(),
            'data' => $despesa->data
        ]);
    }

    public function test_api_retorna_not_found_quando_despesa_nao_existe()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('api/despesas/id-inexistente');

        $response->assertNotFound();
    }

    public function test_usuario_nao_pode_visualizar_despesa_de_outro_usuario()
    {
        $user = User::factory()->create();
        $despesa = Despesa::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->getJson("api/despesas/$despesa->id");

        $response->assertNotFound();
    }

    public function test_usuario_autenticado_pode_excluir_despesa()
    {
        $user = User::factory()->create();
        $despesa = Despesa::factory()->count(2)->for($user)->create()->first();

        $response = $this->actingAs($user)->deleteJson("api/despesas/$despesa->id");

        $response->assertOk();
        $this->assertDatabaseMissing('despesas', ['id' => $despesa->id]);
        $this->assertDatabaseCount('despesas', 1);
    }

    public function test_usuario_autenticado_nao_pode_excluir_despesa_de_outro_usuario()
    {
        $user = User::factory()->create();
        $despesa = Despesa::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->deleteJson("api/despesas/$despesa->id");

        $response->assertNotFound();
        $this->assertDatabaseHas('despesas', ['id' => $despesa->id]);
    }

    public function test_usuario_autenticado_pode_atualizar_despesa()
    {
        $user = User::factory()->create();
        $despesa = Despesa::factory()->for($user)->create();

        $despesaAtualizada = [
            'descricao' => 'gastos com uber',
            'valor' => 15.5,
            'data' => '2023-07-30'
        ];

        $response = $this->actingAs($user)->putJson("api/despesas/$despesa->id", $despesaAtualizada);

        $response->assertOk();
        $this->assertDatabaseHas('despesas', ['id' => $despesa->id, ...$despesaAtualizada]);
    }

    public function test_usuario_autenticado_nao_pode_atualizar_despesa_de_outro_usuario()
    {
        $user = User::factory()->create();
        $despesa = Despesa::factory()->for(User::factory())->create();

        $despesaAtualizada = [
            'descricao' => 'gastos com uber',
            'valor' => 15.5,
            'data' => '2023-07-30'
        ];

        $response = $this->actingAs($user)->putJson("api/despesas/$despesa->id", $despesaAtualizada);

        $response->assertNotFound();
        $this->assertDatabaseHas('despesas', [
            'id' => $despesa->id,
            'descricao' => $despesa->descricao,
            'valor' => $despesa->valor->emReais(),
            'data' => $despesa->data,
        ]);
    }
}
