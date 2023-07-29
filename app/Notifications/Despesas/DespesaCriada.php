<?php

namespace App\Notifications\Despesas;

use App\DataTransferObjects\Despesas\CriarDespesaDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DespesaCriada extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private CriarDespesaDTO $despesa)
    {
        $this->connection = 'redis';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('despesa cadastrada')
                    ->greeting('Olá '. $notifiable->name . ', segue abaixo os dados da despesa:')
                    ->salutation('Saudações')
                    ->line('Descrição: ' . $this->despesa->descricao)
                    ->line('Valor: R$' . $this->despesa->valor)
                    ->line('Data: ' . $this->despesa->data->format('d/m/Y'))
                    ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
