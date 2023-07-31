<?php

namespace App\Models;

use App\ValueObjects\Despesas\ValorDespesa;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    use HasFactory, HasUuids;

    public const DEFAULT_DATE_FORMAT = 'd/m/Y';

    protected $casts = [
        'data' => 'date:' . self::DEFAULT_DATE_FORMAT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function valor(): Attribute
    {
        return Attribute::make(
            get: fn (float $valor) => new ValorDespesa($valor),
            set: fn (ValorDespesa $valor) => $valor->emReais(),
        );
    }
}
