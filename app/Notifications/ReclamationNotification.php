<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReclamationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $titre,
        private string $message,
        private int $idReclamation
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titre' => $this->titre,
            'message' => $this->message,
            'id_reclamation' => $this->idReclamation,
        ];
    }
}