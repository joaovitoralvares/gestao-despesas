<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDespesaRequest;
use App\Models\Despesa;

class DespesaController extends Controller
{
    public function store(StoreDespesaRequest $request)
    {
        $user = $request->user();
        $despesa = new Despesa();
        $despesa->descricao = $request->descricao;
        $despesa->data = $request->data;
        $despesa->valor = $request->valor;

        return $user->despesas()->save($despesa);
    }
}
