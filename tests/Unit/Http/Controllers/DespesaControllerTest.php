<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Http\Controllers\DespesaController;
use App\Http\Requests\StoreDespesaRequest;
use App\Models\Despesa;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Mockery;

class DespesaControllerTest extends TestCase
{
    public function test_metodo_store_executa_action_criar_despesa()
    {
        $despesa = [
            'descricao' => 'Gastos com Uber',
            'data' => '2023-04-25',
            'valor' => 200.58
        ];
        
        $user = Mockery::mock(User::class);
        
        $request = $this->mock(StoreDespesaRequest::class, function ($mock) use ($user, $despesa) {
            $mock->shouldReceive('user')->once()->andReturn($user);
            $mock->shouldReceive('all')->andReturn($despesa);
        });

        $this->mock(CriarDespesa::class, function ($mock) {
            $mock
                ->shouldReceive('execute')
                ->with(Mockery::type(CriarDespesaDTO::class), Mockery::type(User::class))
                ->once();
        });

        $controller = app()->make(DespesaController::class);
        $controller->store($request);
    }
}
