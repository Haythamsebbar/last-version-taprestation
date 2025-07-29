<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\EquipmentRentalRequest;
use App\Models\UrgentSaleContact;
use App\Models\Client;
use App\Models\Service;
use App\Models\Equipment;
use App\Models\UrgentSale;
use App\Models\Prestataire;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création de données de test pour le dashboard unifié...');
        
        // Récupérer les clients existants
        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            $this->command->warn('Aucun client trouvé. Veuillez d\'abord exécuter UsersTableSeeder.');
            return;
        }
        
        $client = $clients->first();
        
        // Créer des réservations récentes (services - bleu)
        $this->createRecentBookings($client);
        
        // Créer des demandes de location d'équipement récentes (vert)
        $this->createRecentEquipmentRentalRequests($client);
        
        // Créer des contacts de ventes urgentes récents (rouge)
        $this->createRecentUrgentSaleContacts($client);
        
        $this->command->info('Données de test créées avec succès!');
    }
    
    /**
     * Créer des réservations récentes
     */
    private function createRecentBookings(Client $client)
    {
        $services = Service::where('status', 'active')->take(5)->get();
        
        if ($services->isEmpty()) {
            $this->command->warn('Aucun service actif trouvé.');
            return;
        }
        
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        
        foreach ($services as $index => $service) {
            $startDate = Carbon::now()->subDays(rand(1, 15))->addHours(rand(8, 17));
            $endDate = $startDate->copy()->addHours(rand(1, 4));
            
            Booking::create([
                'client_id' => $client->id,
                'prestataire_id' => $service->prestataire_id,
                'service_id' => $service->id,
                'start_datetime' => $startDate,
                'end_datetime' => $endDate,
                'status' => $statuses[array_rand($statuses)],
                'total_price' => $service->price,
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(0, 5))
            ]);
        }
        
        $this->command->info('Réservations de test créées.');
    }
    
    /**
     * Créer des demandes de location d'équipement récentes
     */
    private function createRecentEquipmentRentalRequests(Client $client)
    {
        $equipment = Equipment::where('is_available', true)->take(4)->get();
        
        if ($equipment->isEmpty()) {
            $this->command->warn('Aucun équipement disponible trouvé.');
            return;
        }
        
        $statuses = ['pending', 'accepted', 'rejected', 'confirmed'];
        
        foreach ($equipment as $index => $item) {
            $startDate = Carbon::now()->addDays(rand(1, 30));
            $endDate = $startDate->copy()->addDays(rand(1, 7));
            $durationDays = $startDate->diffInDays($endDate) + 1;
            
            EquipmentRentalRequest::create([
                'client_id' => $client->id,
                'equipment_id' => $item->id,
                'prestataire_id' => $item->prestataire_id,
                'request_number' => 'DMD-' . strtoupper(uniqid()),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $durationDays,
                'unit_price' => $item->daily_rate ?? 50.00,
                'total_amount' => ($item->daily_rate ?? 50.00) * $durationDays,
                'security_deposit' => $item->security_deposit ?? 200.00,
                'final_amount' => (($item->daily_rate ?? 50.00) * $durationDays) + ($item->delivery_fee ?? 0),
                'status' => $statuses[array_rand($statuses)],
                'delivery_required' => rand(0, 1),
                'delivery_address' => 'Adresse de livraison test ' . ($index + 1),
                'pickup_address' => $item->address ?? 'Adresse de récupération test',
                'created_at' => Carbon::now()->subDays(rand(1, 12)),
                'updated_at' => Carbon::now()->subDays(rand(0, 6))
            ]);
        }
        
        $this->command->info('Demandes de location d\'équipement de test créées.');
    }
    
    /**
     * Créer des contacts de ventes urgentes récents
     */
    private function createRecentUrgentSaleContacts(Client $client)
    {
        $urgentSales = UrgentSale::where('status', 'active')->take(3)->get();
        
        if ($urgentSales->isEmpty()) {
            $this->command->warn('Aucune vente urgente active trouvée.');
            return;
        }
        
        $statuses = ['pending', 'responded', 'closed'];
        
        foreach ($urgentSales as $index => $sale) {
            UrgentSaleContact::create([
                'urgent_sale_id' => $sale->id,
                'user_id' => $client->user_id,
                'phone' => $client->phone ?? '0612345678',
                'email' => $client->user->email,
                'message' => 'Je suis intéressé(e) par votre vente urgente: ' . $sale->title . '. Pouvez-vous me donner plus d\'informations ?',
                'status' => $statuses[array_rand($statuses)],
                'created_at' => Carbon::now()->subDays(rand(1, 8)),
                'updated_at' => Carbon::now()->subDays(rand(0, 4))
            ]);
        }
        
        $this->command->info('Contacts de ventes urgentes de test créés.');
    }
}