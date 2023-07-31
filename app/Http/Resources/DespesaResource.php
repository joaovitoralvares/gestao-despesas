<?php

namespace App\Http\Resources;

use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DespesaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'descricao' => $this->descricao,
            'valor' => $this->valor->emReais(),
            'data' => gettype($this->data) === 'string'
                ? $this->data
                : $this->data->format(Despesa::DEFAULT_DATE_FORMAT)
        ];
    }
}
