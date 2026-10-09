<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientValidationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private int $idClient,
        private string $titre,
        private string $message
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titre' => $this->titre,
            'message' => $this->message,
            'id_client_validation' => $this->idClient,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
