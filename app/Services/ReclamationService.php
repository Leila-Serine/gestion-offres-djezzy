<?php

namespace App\Services;

use App\Models\Reclamation;
use App\Models\ReclamationMessage;
use App\Models\User;
use App\Notifications\ReclamationNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReclamationService
{
    /**
     * Réclamations appartenant au client.
     */
    public function getByClient(
        User $client
    ): Collection {
        return Reclamation::query()
            ->where(
                'id_client',
                $client->id_user
            )
            ->with([
                'messages.auteur',
            ])
            ->latest('id_reclamation')
            ->get();
    }

    /**
     * Création d'une nouvelle réclamation.
     */
    public function createForClient(
        User $client,
        array $data
    ): Reclamation {
        $reclamation = DB::transaction(
            function () use (
                $client,
                $data
            ) {
                return Reclamation::create([
                    'id_client' =>
                        $client->id_user,

                    'objet' =>
                        $data['objet'],

                    'message' =>
                        $data['message'],

                    'statut' =>
                        'ouverte',
                ]);
            }
        );

        /*
         * 🔔 Tous les commerciaux reçoivent
         * la nouvelle réclamation.
         */
        $commercials = User::query()
            ->where(
                'role',
                'commercial'
            )
            ->get();

        foreach (
            $commercials
            as $commercial
        ) {
            $commercial->notify(
                new ReclamationNotification(
                    'Nouvelle réclamation',
                    'Le client '
                    . $client->name
                    . ' a envoyé la réclamation #'
                    . $reclamation->id_reclamation
                    . '.',
                    $reclamation->id_reclamation
                )
            );
        }

        return $reclamation;
    }

    /**
     * Vérifie qu'une réclamation appartient
     * bien au client connecté.
     */
    public function belongsToClient(
        Reclamation $reclamation,
        User $client
    ): bool {
        return $reclamation->id_client
            === $client->id_user;
    }

    /**
     * Ajoute un message dans la conversation.
     */
    public function addMessage(
        Reclamation $reclamation,
        User $auteur,
        string $contenu
    ): ReclamationMessage {
        if (
            $reclamation->statut
            === 'resolue'
        ) {
            throw new RuntimeException(
                'Cette réclamation est déjà résolue.'
            );
        }

        $message = DB::transaction(
            function () use (
                $reclamation,
                $auteur,
                $contenu
            ) {
                $message =
                    ReclamationMessage::create([
                        'id_reclamation' =>
                            $reclamation
                                ->id_reclamation,

                        'id_auteur' =>
                            $auteur->id_user,

                        'contenu' =>
                            $contenu,
                    ]);

                /*
                 * Une conversation active passe
                 * la réclamation en cours.
                 */
                if (
                    $reclamation->statut
                    === 'ouverte'
                ) {
                    $reclamation->update([
                        'statut' => 'en_cours',
                    ]);
                }

                return $message;
            }
        );

        /*
         * Si le commercial répond,
         * le client reçoit la notification.
         */
        if (
            $auteur->role
            === 'commercial'
        ) {
            $reclamation->load('client');

            $reclamation->client->notify(
                new ReclamationNotification(
                    'Nouvelle réponse',
                    'Un commercial a répondu à votre réclamation #'
                    . $reclamation->id_reclamation
                    . '.',
                    $reclamation->id_reclamation
                )
            );

            return $message;
        }

        /*
         * Si le client ajoute un message,
         * les commerciaux sont notifiés.
         */
        $commercials = User::query()
            ->where(
                'role',
                'commercial'
            )
            ->get();

        foreach (
            $commercials
            as $commercial
        ) {
            $commercial->notify(
                new ReclamationNotification(
                    'Nouveau message client',
                    'Le client '
                    . $auteur->name
                    . ' a ajouté un message à la réclamation #'
                    . $reclamation->id_reclamation
                    . '.',
                    $reclamation->id_reclamation
                )
            );
        }

        return $message;
    }

    /**
     * Le commercial marque une
     * réclamation comme résolue.
     */
    public function resolve(
        Reclamation $reclamation
    ): Reclamation {
        if (
            $reclamation->statut
            === 'resolue'
        ) {
            throw new RuntimeException(
                'Cette réclamation est déjà résolue.'
            );
        }

        $reclamation->update([
            'statut' => 'resolue',
        ]);

        $reclamation->load('client');

        /*
         * 🔔 Notification au client.
         */
        $reclamation->client->notify(
            new ReclamationNotification(
                'Réclamation résolue',
                'Votre réclamation #'
                . $reclamation->id_reclamation
                . ' a été marquée comme résolue.',
                $reclamation->id_reclamation
            )
        );

        return $reclamation->refresh();
    }

    /**
     * Réclamations visibles dans
     * l'espace commercial.
     */
    public function getAllForCommercial(): Collection
    {
        return Reclamation::query()
            ->with([
                'client',
                'messages.auteur',
            ])
            ->latest('id_reclamation')
            ->get();
    }
}