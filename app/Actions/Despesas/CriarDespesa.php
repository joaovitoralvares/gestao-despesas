<?php

namespace App\Actions\Despesas;

use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Models\Despesa;
use App\Models\User;
use App\ValueObjects\Despesas\ValorDespesa;
use DomainException;
use Illuminate\Support\Carbon;

class CriarDespesa
{
    public function execute(CriarDespesaDTO $dados, User $user): Despesa
    {
        if (Carbon::createFromImmutable($dados->data)->gt(now())) {
            throw new DomainException('a data da despesa nao pode ser uma data futura');
        }

        $despesa = new Despesa();

        $despesa->descricao = $dados->descricao;
        $despesa->valor = new ValorDespesa($dados->valor);
        $despesa->data = $dados->data;

        $user->despesas()->save($despesa);

        return $despesa;
    }
}
