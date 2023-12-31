<?php

namespace App\Http\Controllers;

use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Http\Requests\StoreDespesaRequest;
use App\Http\Requests\UpdateDespesaRequest;
use App\Http\Resources\DespesaResource;
use App\Models\Despesa;
use App\ValueObjects\Despesas\ValorDespesa;
use DateTimeImmutable;
use Illuminate\Http\Request;

class DespesaController extends Controller
{
    public function __construct(private CriarDespesa $criarDespesa) {}

    public function store(StoreDespesaRequest $request)
    {
        $user = $request->user();
        $despesa = new CriarDespesaDTO(
            $request->descricao,
            $request->valor,
            new DateTimeImmutable($request->data),
        );

        $despesaCriada = $this->criarDespesa->execute($despesa, $user);

        return response()->json(DespesaResource::make($despesaCriada), 201);
    }

    public function index(Request $request)
    {
        return DespesaResource::collection(
            $request->user()
                ->despesas()
                ->orderBy('data', 'desc')
                ->paginate()
            );
    }

    public function show(Despesa $despesa)
    {
        $this->authorize('view', $despesa);

        return DespesaResource::make($despesa);
    }

    public function destroy(Despesa $despesa)
    {
        $this->authorize('delete', $despesa);

        $despesa->delete();
    }

    public function update(UpdateDespesaRequest $request, Despesa $despesa)
    {      
        $this->authorize('update', $despesa);

        $despesa->descricao = $request->descricao;
        $despesa->valor = new ValorDespesa($request->valor);
        $despesa->data = $request->data;
        $despesa->save();

        return DespesaResource::make($despesa);
    }
}
