<?php

namespace Tests\Unit\Notifications\Despesas;

use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use App\Models\User;
use App\Notifications\Despesas\DespesaCriada;
use DateTimeImmutable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DespesaCriadaTest extends TestCase
{
    public function test_conteudo_da_notificacao_via_email_esta_correto()
    {
        $user = User::factory()->make();
        $despesa = new CriarDespesaDTO('Gastos com uber', 200.35, new DateTimeImmutable('2023-07-27'));
        
        $notificacaoEmail = (new DespesaCriada($despesa))->toMail($user);
        
        $this->assertSame('despesa cadastrada', $notificacaoEmail->subject);
        $this->assertSame('Olá '. $user->name . ', segue abaixo os dados da despesa:', $notificacaoEmail->greeting);
        $this->assertSame('Saudações', $notificacaoEmail->salutation);
        $this->assertContains('Descrição: Gastos com uber', $notificacaoEmail->introLines);
        $this->assertContains('Valor: R$200.35', $notificacaoEmail->introLines);
        $this->assertContains('Data: 27/07/2023', $notificacaoEmail->introLines);
    }

    public function test_notificacao_enviada_para_fila()
    {
        Queue::fake();
        
        $user = User::factory()->make();
        $despesa = new CriarDespesaDTO('Gastos com uber', 200.35, new DateTimeImmutable('2023-07-27'));
        $notificacao = new DespesaCriada($despesa);

        $user->notify($notificacao);

        Queue::assertPushed(SendQueuedNotifications::class, 1);
        Queue::assertPushed(SendQueuedNotifications::class, function (SendQueuedNotifications $job) {
            return $job->notification::class == DespesaCriada::class;
        });
    }
}
