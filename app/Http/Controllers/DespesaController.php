<?php

namespace App\Http\Controllers;

use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Http\Requests\StoreDespesaRequest;
use DateTimeImmutable;

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
}
