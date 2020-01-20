<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TransactionState;

class TransactionsStatusCreated extends Notification
{
    use Queueable;

    protected $transactionState;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TransactionState $transactionState)
    {
        $this->transactionState=$transactionState;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hola!')
                    ->subject('Cambio de estado de transaccion.')
                    ->line("Hay un cambio de estado en la transaccion con referencia (". $this->transactionState->transaction->reference .").")
                    ->action('Ver detalle de la orden', route("orders.show", ['order' => $this->transactionState->transaction->order->id]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message_left' => "Estado de transaccion.",
            'message_right' => $this->transactionState->transaction->reference,
            'icon' => 'exchange-alt',
            'url' => route("orders.show", ['order' => $this->transactionState->transaction->order->id]),
        ];
    }
}
