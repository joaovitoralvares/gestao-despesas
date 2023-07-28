<?php

namespace App\DataTransferObjects\Despesas;

use DateTimeImmutable;

class CriarDespesaDTO
{
    public function __construct(
        public readonly string $descricao,
        public readonly float $valor,
        public readonly DateTimeImmutable $data,
    ) {}
}
