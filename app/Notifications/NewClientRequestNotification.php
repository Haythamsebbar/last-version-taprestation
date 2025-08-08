<?php

namespace App\Notifications;

use App\Models\ClientRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClientRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $clientRequest;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\ClientRequest  $clientRequest
     * @return void
     */
    public function __construct(ClientRequest $clientRequest)
    {
        $this->clientRequest = $clientRequest;
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
        $clientName = $this->clientRequest->client->user->name ?? 'Un client';
        $url = route('prestataire.requests.show', $this->clientRequest->id);
        
        return (new MailMessage)
            ->subject('Nouvelle demande client reçue')
            ->greeting('Bonjour ' . $notifiable->name . '!')
            ->line('Vous avez reçu une nouvelle demande de ' . $clientName . '.')
            ->line('Titre: "' . $this->clientRequest->title . '"')
            ->line('Budget: ' . number_format($this->clientRequest->budget, 2) . ' €')
            ->line('Description: "' . \Str::limit($this->clientRequest->description, 100) . '"')
            ->action('Voir la demande', $url)
            ->line('Répondez rapidement pour avoir plus de chances d\'être sélectionné!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'new_client_request',
            'client_request_id' => $this->clientRequest->id,
            'client_id' => $this->clientRequest->client_id,
            'client_name' => $this->clientRequest->client->user->name ?? 'Client',
            'title' => 'Nouvelle demande client',
            'message' => 'Vous avez reçu une nouvelle demande: "' . $this->clientRequest->title . '"',
            'request_title' => $this->clientRequest->title,
            'budget' => $this->clientRequest->budget,
            'description_preview' => \Str::limit($this->clientRequest->description, 50),
            'url' => route('prestataire.requests.show', $this->clientRequest->id)
        ];
    }
}