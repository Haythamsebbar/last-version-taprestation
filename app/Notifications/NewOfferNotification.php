<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Offer;
use App\Models\ClientRequest;

class NewOfferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $offer;
    protected $clientRequest;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Offer  $offer
     * @param  \App\Models\ClientRequest  $clientRequest
     * @return void
     */
    public function __construct(Offer $offer, ClientRequest $clientRequest)
    {
        $this->offer = $offer;
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
        $url = route('client.requests.show', $this->clientRequest->id);
        
        return (new MailMessage)
            ->subject('Nouvelle offre reçue pour votre demande')
            ->greeting('Bonjour ' . $notifiable->name . '!')
            ->line('Vous avez reçu une nouvelle offre pour votre demande "' . $this->clientRequest->title . '".')
            ->line('Prestataire: ' . $this->offer->prestataire->user->name)
            ->line('Prix proposé: ' . number_format($this->offer->price, 2) . ' €')
            ->line('Message: ' . substr($this->offer->message, 0, 100) . (strlen($this->offer->message) > 100 ? '...' : ''))
            ->action('Voir l\'offre', $url)
            ->line('Consultez toutes les offres et choisissez celle qui vous convient le mieux!');
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
            'type' => 'new_offer',
            'offer_id' => $this->offer->id,
            'client_request_id' => $this->clientRequest->id,
            'prestataire_name' => $this->offer->prestataire->user->name,
            'price' => $this->offer->price,
            'title' => 'Nouvelle offre reçue',
            'message' => 'Vous avez reçu une nouvelle offre de ' . $this->offer->prestataire->user->name . ' pour "' . $this->clientRequest->title . '"',
            'url' => route('client.requests.show', $this->clientRequest->id)
        ];
    }
}