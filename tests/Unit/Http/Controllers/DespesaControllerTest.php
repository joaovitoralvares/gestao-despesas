<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Http\Controllers\DespesaController;
use App\Http\Requests\StoreDespesaRequest;
use App\Models\Despesa;
use App\Models\User;
use Mockery;

class DespesaControllerTest extends TestCase
{
    public function test_metodo_store_executa_action_criar_despesa()
    {
        $dados = [
            'descricao' => 'Gastos com Uber',
            'data' => '2023-04-25',
            'valor' => 200.58
        ];
        $despesa = Despesa::factory()->make();
        $user = User::factory()->make();
        
        $request = $this->partialMock(StoreDespesaRequest::class, fn ($mock) =>
            $mock->shouldReceive('user')->andReturn($user)
        );
        $request->initialize($dados);

        $this->mock(CriarDespesa::class, function ($mock) use ($despesa) {
            $mock
                ->shouldReceive('execute')
                ->with(Mockery::type(CriarDespesaDTO::class), Mockery::type(User::class))
                ->once()
                ->andReturn($despesa);
        });

        $controller = app()->make(DespesaController::class);

        $controller->store($request);
    }
}
