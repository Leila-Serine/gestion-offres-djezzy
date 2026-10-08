<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommandeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $titre,
        private string $message,
        private int $idCommande
    ) {
    }

    /**
     * Pour le prototype, la notification est
     * enregistrée dans la base de données.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Données enregistrées dans la table notifications.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titre' => $this->titre,
            'message' => $this->message,
            'id_commande' => $this->idCommande,
        ];
    }
}