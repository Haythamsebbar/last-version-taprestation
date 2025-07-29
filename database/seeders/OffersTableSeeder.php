<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;

class OffersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer les prestataires
        $prestataires = User::where('role', 'prestataire')->get();
        
        // Créer des offres d'exemple sans référence aux demandes clients
        
        if ($prestataires->isNotEmpty()) {
            // Créer quelques offres d'exemple
            foreach ($prestataires as $index => $prestataire) {
                Offer::create([
                     'prestataire_id' => $prestataire->id,
                     'message' => 'Je peux réaliser ce projet dans les délais impartis avec une qualité professionnelle. J\'ai plusieurs années d\'expérience dans ce domaine et je serais ravi de travailler avec vous.',
                     'price' => 500 + ($index * 100),
                     'status' => 'pending',
                     'created_at' => Carbon::now()->subDays(rand(1, 3)),
                 ]);
                
                if ($index < 2) {
                    Offer::create([
                         'prestataire_id' => $prestataire->id,
                         'message' => 'Bonjour, je suis très intéressé par votre projet. Je propose une solution complète et personnalisée qui répondra parfaitement à vos attentes.',
                         'price' => 750 + ($index * 150),
                         'status' => 'accepted',
                         'created_at' => Carbon::now()->subDays(rand(5, 10)),
                     ]);
                }
            }
        }
    }
}