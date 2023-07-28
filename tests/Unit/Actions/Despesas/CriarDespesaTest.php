<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Actions\Despesas\CriarDespesa;
use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Models\Despesa;
use App\Models\User;
use Carbon\Carbon;
use DateTimeImmutable;
use DomainException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;
use Mockery;

class CriarDespesaTest extends TestCase
{
    /**
     * @dataProvider criar_despesa_provider
     */
    public function test_criar_despesa($descricao, $valor, $dataDespesa)
    {
        Carbon::setTestNow('2023-07-27');

        $dados = new CriarDespesaDTO(
            $descricao,
            $valor,
            $dataDespesa,
        );

        $userMock = Mockery::mock(User::class);
        $hasManyDespesasMock = Mockery::mock(HasMany::class);

        $userMock->shouldReceive('despesas')->andReturn($hasManyDespesasMock);
        $hasManyDespesasMock->shouldReceive('save')->once();

        $criarDespesa = new CriarDespesa();

        $despesa = $criarDespesa->execute($dados, $userMock);

        $this->assertSame($descricao, $despesa->descricao);
        $this->assertSame($valor, $despesa->valor->emReais());
        $this->assertSame($dataDespesa, $despesa->data);
    }

    public static function criar_despesa_provider()
    {
        $descricaoCom191Caracteres = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla bibendum odio nec ipsum consequat, nec malesuada purus cursus. Vestibulum auctor quam nec lectus malesuada commodo. Quisque susc';

        return [
            [$descricaoCom191Caracteres, 0.1, new DateTimeImmutable('2023-07-27')],
            ['seguro viagem', 100.0, new DateTimeImmutable('2022-12-01')],
            ['uber', 102500.59, new DateTimeImmutable('2005-01-02')]
        ];
    }

    /**
     * @dataProvider despesa_invalida_provider
     */
    public function test_despesa_invalida_gera_exception($valor, $dataDespesa, $exception, $erro)
    {
        Carbon::setTestNow('2023-07-27');

        $dados = new CriarDespesaDTO(
            'descricao',
            $valor,
            $dataDespesa,
        );

        $userMock = Mockery::mock(User::class);

        $criarDespesa = new CriarDespesa();

        $this->expectException($exception);
        $this->expectExceptionMessage($erro);
        
        $criarDespesa->execute($dados, $userMock);
    }

    public static function despesa_invalida_provider()
    {
        $mensagemErroValorDespesa = 'valor da despesa nao pode ser negativo';
        $mensagemErroDataDespesa = 'a data da despesa nao pode ser uma data futura';

        return [
            [-0.1, new DateTimeImmutable('2023-07-01'), InvalidArgumentException::class, $mensagemErroValorDespesa],
            [-500.49, new DateTimeImmutable('2023-07-01'), InvalidArgumentException::class, $mensagemErroValorDespesa],
            [10.55, new DateTimeImmutable('2023-08-01'), DomainException::class, $mensagemErroDataDespesa],
            [10.55, new DateTimeImmutable('5001-12-28'), DomainException::class, $mensagemErroDataDespesa]
        ];
    }
}