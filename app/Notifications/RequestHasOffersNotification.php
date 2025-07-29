<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ClientRequest;

class RequestHasOffersNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $clientRequest;
    protected $offersCount;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\ClientRequest  $clientRequest
     * @param  int  $offersCount
     * @return void
     */
    public function __construct(ClientRequest $clientRequest, $offersCount)
    {
        $this->clientRequest = $clientRequest;
        $this->offersCount = $offersCount;
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
            ->subject('Votre demande intéresse les prestataires !')
            ->greeting('Bonjour ' . $notifiable->name . '!')
            ->line('Votre demande "' . $this->clientRequest->title . '" a reçu ' . $this->offersCount . ' offre(s) de prestataires qualifiés.')
            ->line('C\'est le moment de consulter les propositions et de choisir le prestataire qui vous convient le mieux.')
            ->action('Voir les offres', $url)
            ->line('Prenez le temps de comparer les offres et n\'hésitez pas à poser des questions aux prestataires.');
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
            'type' => 'request_has_offers',
            'client_request_id' => $this->clientRequest->id,
            'offers_count' => $this->offersCount,
            'title' => 'Nouvelles offres reçues',
            'message' => 'Votre demande "' . $this->clientRequest->title . '" a reçu ' . $this->offersCount . ' offre(s). Consultez-les maintenant !',
            'url' => route('client.requests.show', $this->clientRequest->id)
        ];
    }
}