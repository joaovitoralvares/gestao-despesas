<?php

namespace App\Http\Controllers;

use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Http\Requests\StoreDespesaRequest;
use App\Http\Resources\DespesaResource;
use App\Models\Despesa;
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

        $this->criarDespesa->execute($despesa, $user);

        return response('despesa criada', 201);
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
        $despesa->delete();
    }
}
