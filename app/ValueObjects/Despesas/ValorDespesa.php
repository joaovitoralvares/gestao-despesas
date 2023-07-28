<?php

namespace App\ValueObjects\Despesas;

use InvalidArgumentException;

class ValorDespesa
{
    /**
     * @param valor Valor da despesa em reais
     */
    public function __construct(private float $valor)
    {
        if ($this->valor < 0) {
            throw new InvalidArgumentException('valor da despesa nao pode ser negativo');
        }
        
        $this->valor = $valor;
    }

    public function emReais(): float
    {
        return $this->valor;
    }
}
