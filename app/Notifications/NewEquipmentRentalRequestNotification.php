<?php

namespace App\Notifications;

use App\Models\EquipmentRentalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEquipmentRentalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $rentalRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EquipmentRentalRequest $rentalRequest)
    {
        $this->rentalRequest = $rentalRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('prestataire.equipment-rental-requests.show', $this->rentalRequest);

        return (new MailMessage)
                    ->subject('Nouvelle demande de location pour votre matériel')
                    ->line('Vous avez reçu une nouvelle demande de location pour l\'un de vos équipements.')
                    ->line('Équipement : ' . $this->rentalRequest->equipment->name)
                    ->line('Client : ' . $this->rentalRequest->client->user->name)
                    ->action('Voir la demande', $url)
                    ->line('Veuillez répondre à cette demande dans les plus brefs délais.');
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
            'rental_request_id' => $this->rentalRequest->id,
            'equipment_name' => $this->rentalRequest->equipment->name,
            'client_name' => $this->rentalRequest->client->user->name,
            'message' => 'Vous avez une nouvelle demande de location pour ' . $this->rentalRequest->equipment->name,
            'url' => route('prestataire.equipment-rental-requests.show', $this->rentalRequest->id),
        ];
    }
}