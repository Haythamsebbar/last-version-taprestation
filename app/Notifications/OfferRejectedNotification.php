<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Offer;
use App\Models\ClientRequest;

class OfferRejectedNotification extends Notification implements ShouldQueue
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
        $url = route('prestataire.responses.show', $this->offer->id);
        
        return (new MailMessage)
            ->subject('Votre offre a été refusée')
            ->greeting('Bonjour ' . $notifiable->name . '!')
            ->line('Nous sommes désolés de vous informer que votre offre pour la demande "' . $this->clientRequest->title . '" a été refusée par le client.')
            ->line('Ne vous découragez pas! Continuez à proposer vos services pour d\'autres demandes.')
            ->action('Voir les détails', $url)
            ->line('Merci d\'utiliser notre plateforme!');
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
            'title' => 'Offre refusée',
            'message' => 'Votre offre pour la demande "' . $this->clientRequest->title . '" a été refusée par le client.',
            'offer_id' => $this->offer->id,
            'client_request_id' => $this->clientRequest->id,
            'type' => 'offer_rejected',
            'url' => route('prestataire.responses.show', $this->offer->id)
        ];
    }
}