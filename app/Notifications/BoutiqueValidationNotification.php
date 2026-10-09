<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BoutiqueValidationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private int $idBoutique,
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
            'id_boutique_validation' => $this->idBoutique,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
