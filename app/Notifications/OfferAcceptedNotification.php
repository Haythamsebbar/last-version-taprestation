<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Offer;
use App\Models\ClientRequest;
use App\Models\Booking;

class OfferAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $offer;
    protected $clientRequest;
    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Offer  $offer
     * @param  \App\Models\ClientRequest  $clientRequest
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function __construct(Offer $offer, ClientRequest $clientRequest, Booking $booking)
    {
        $this->offer = $offer;
        $this->clientRequest = $clientRequest;
        $this->booking = $booking;
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
            ->subject('Votre offre a été acceptée')
            ->greeting('Bonjour ' . $notifiable->name . '!')
            ->line('Bonne nouvelle! Votre offre pour la demande "' . $this->clientRequest->title . '" a été acceptée par le client.')
            ->line('Prix accepté: ' . number_format($this->offer->price, 2) . ' €')
            ->line('Une réservation a été créée automatiquement.')
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
            'title' => 'Offre acceptée',
            'message' => 'Votre offre pour la demande "' . $this->clientRequest->title . '" a été acceptée par le client.',
            'offer_id' => $this->offer->id,
            'client_request_id' => $this->clientRequest->id,
            'booking_id' => $this->booking->id,
            'type' => 'offer_accepted',
            'url' => route('prestataire.responses.show', $this->offer->id)
        ];
    }
}