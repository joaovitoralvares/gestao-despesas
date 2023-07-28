<?php

namespace App\Actions\Despesas;

use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Models\Despesa;
use App\Models\User;

class CriarDespesa
{
    public function execute(CriarDespesaDTO $dados, User $user): Despesa
    {
        $despesa = new Despesa();

        $despesa->descricao = $dados->descricao;
        $despesa->valor = $dados->valor;
        $despesa->data = $dados->data;

        $user->despesas()->save($despesa);

        return $despesa;
    }
}
