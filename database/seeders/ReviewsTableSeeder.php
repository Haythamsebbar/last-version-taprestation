<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Prestataire;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Génère des URLs de photos aléatoires pour les avis
     *
     * @param int $count Nombre de photos à générer
     * @return array
     */
    private function generateRandomPhotoUrls($count = 0)
    {
        if ($count <= 0) {
            return null;
        }
        
        $photoUrls = [];
        
        // Assurez-vous que le répertoire existe
        Storage::disk('public')->makeDirectory('reviews/photos', 0755, true, true);
        
        for ($i = 1; $i <= $count; $i++) {
            $photoName = 'review_photo_' . uniqid() . '.jpg';
            $path = 'reviews/photos/' . $photoName;
            $photoUrls[] = $path;
            
            // Créer une image de placeholder pour simuler une photo
            // Dans un environnement de production, vous auriez de vraies images
            $placeholderImage = $this->generatePlaceholderImage(400, 300, 'Review Photo ' . $i);
            Storage::disk('public')->put($path, $placeholderImage);
        }
        
        return $photoUrls;
    }
    
    /**
     * Génère une image placeholder simple
     *
     * @param int $width Largeur de l'image
     * @param int $height Hauteur de l'image
     * @param string $text Texte à afficher sur l'image
     * @return string Contenu de l'image
     */
    private function generatePlaceholderImage($width = 400, $height = 300, $text = 'Placeholder')
    {
        // Créer une image
        $image = imagecreatetruecolor($width, $height);
        
        // Définir les couleurs
        $bgColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Remplir l'arrière-plan
        imagefill($image, 0, 0, $bgColor);
        
        // Ajouter du texte
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        
        imagestring($image, $fontSize, $x, $y, $text, $textColor);
        
        // Capturer l'image dans un buffer
        ob_start();
        imagejpeg($image, null, 90);
        $imageData = ob_get_clean();
        
        // Libérer la mémoire
        imagedestroy($image);
        
        return $imageData;
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer les clients
        $clients = User::where('role', 'client')->get();
        
        // Récupérer les prestataires
        $prestataires = Prestataire::all();
        
        // Récupérer les administrateurs pour la modération
        $admins = User::where('role', 'administrateur')->get();
        $admin = $admins->first();
        
        if ($clients->isNotEmpty() && $prestataires->isNotEmpty()) {
            // Avis du premier client sur le premier prestataire
            if ($clients->isNotEmpty() && $prestataires->isNotEmpty()) {
                $client1 = $clients->first();
                $prestataire1 = $prestataires->first();
                
                Review::create([
                    'client_id' => $client1->id,
                    'prestataire_id' => $prestataire1->id,
                    'rating' => 5,
                    'punctuality_rating' => 5,
                    'quality_rating' => 5,
                    'value_rating' => 4,
                    'communication_rating' => 5,
                    'comment' => 'Excellent travail ! Le site web est exactement ce que je voulais. Communication claire et professionnelle tout au long du projet. Je recommande vivement ce prestataire.',
                    'photos' => $this->generateRandomPhotoUrls(3), // 3 photos pour cet avis
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(20),
                ]);
            }
            
            // Avis du deuxième client sur le premier prestataire
            if ($clients->count() > 1 && $prestataires->isNotEmpty()) {
                $client2 = $clients->get(1);
                $prestataire1 = $prestataires->first();
                
                Review::create([
                    'client_id' => $client2->id,
                    'prestataire_id' => $prestataire1->id,
                    'rating' => 4,
                    'punctuality_rating' => 3, // Léger retard
                    'quality_rating' => 5,
                    'value_rating' => 4,
                    'communication_rating' => 4,
                    'comment' => 'Très bon travail dans l\'ensemble. Le projet a été livré avec un léger retard, mais la qualité était au rendez-vous. Bonne communication et réactivité.',
                    'photos' => $this->generateRandomPhotoUrls(1), // 1 photo pour cet avis
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(15),
                ]);
            }
            
            // Avis du premier client sur le deuxième prestataire
            if ($clients->isNotEmpty() && $prestataires->count() > 1) {
                $client1 = $clients->first();
                $prestataire2 = $prestataires->get(1);
                
                Review::create([
                    'client_id' => $client1->id,
                    'prestataire_id' => $prestataire2->id,
                    'rating' => 3,
                    'punctuality_rating' => 3,
                    'quality_rating' => 3,
                    'value_rating' => 3,
                    'communication_rating' => 2, // Communication difficile
                    'comment' => 'Prestation correcte mais plusieurs révisions ont été nécessaires. La communication était parfois difficile. Le résultat final est satisfaisant.',
                    'photos' => null, // Pas de photos
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(10),
                ]);
            }
            
            // Avis du deuxième client sur le deuxième prestataire
            if ($clients->count() > 1 && $prestataires->count() > 1) {
                $client2 = $clients->get(1);
                $prestataire2 = $prestataires->get(1);
                
                Review::create([
                    'client_id' => $client2->id,
                    'prestataire_id' => $prestataire2->id,
                    'rating' => 5,
                    'punctuality_rating' => 5, // Livré avant la date
                    'quality_rating' => 5,
                    'value_rating' => 5,
                    'communication_rating' => 5,
                    'comment' => 'Travail impeccable et livré avant la date prévue ! Communication excellente et grande réactivité. Je n\'hésiterai pas à faire appel à ce prestataire pour mes futurs projets.',
                    'photos' => $this->generateRandomPhotoUrls(2), // 2 photos
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(5),
                ]);
            }
            
            // Avis du premier client sur le troisième prestataire
            if ($clients->isNotEmpty() && $prestataires->count() > 2) {
                $client1 = $clients->first();
                $prestataire3 = $prestataires->get(2);
                
                Review::create([
                    'client_id' => $client1->id,
                    'prestataire_id' => $prestataire3->id,
                    'rating' => 2,
                    'punctuality_rating' => 1, // Délais non respectés
                    'quality_rating' => 2,
                    'value_rating' => 2,
                    'communication_rating' => 1, // Communication difficile
                    'comment' => 'Déçu par la prestation. Délais non respectés et communication difficile. La qualité du travail n\'était pas à la hauteur de mes attentes.',
                    'photos' => null, // Pas de photos
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(30),
                ]);
            }
            
            // Avis non modéré
            if ($clients->count() > 1 && $prestataires->count() > 2) {
                $client2 = $clients->get(1);
                $prestataire3 = $prestataires->get(2);
                
                Review::create([
                    'client_id' => $client2->id,
                    'prestataire_id' => $prestataire3->id,
                    'rating' => 1,
                    'punctuality_rating' => 1,
                    'quality_rating' => 1,
                    'value_rating' => 1,
                    'communication_rating' => 1,
                    'comment' => 'Très mauvaise expérience. Le prestataire n\'a pas respecté le cahier des charges et a ignoré mes demandes de modification. Je déconseille fortement.',
                    'photos' => null, 
                    'status' => 'pending', 
                    'moderated_by' => null, 
                    'created_at' => Carbon::now()->subDays(2),
                ]);
            }
            
            // Avis pour le nouveau prestataire (prestataire4)
            if ($clients->isNotEmpty() && $prestataires->count() > 3) {
                $prestataire4 = $prestataires->get(3);
                
                // Avis du premier client
                if ($clients->count() >= 1) {
                    $client1 = $clients->first();
                    
                    Review::create([
                        'client_id' => $client1->id,
                        'prestataire_id' => $prestataire4->id,
                        'rating' => 5,
                        'punctuality_rating' => 5,
                        'quality_rating' => 5,
                        'value_rating' => 5,
                        'communication_rating' => 5,
                        'comment' => 'Prestataire exceptionnel ! Travail de très haute qualité, livré dans les délais. Communication parfaite et grande expertise technique. Je recommande vivement.',
                        'photos' => $this->generateRandomPhotoUrls(4), // 4 photos
                        'status' => 'approved',
                        'moderated_by' => $admin ? $admin->id : null,
                        'created_at' => Carbon::now()->subDays(12),
                    ]);
                }
                
                // Avis du deuxième client
                if ($clients->count() >= 2) {
                    $client2 = $clients->get(1);
                    
                    Review::create([
                        'client_id' => $client2->id,
                        'prestataire_id' => $prestataire4->id,
                        'rating' => 4,
                        'punctuality_rating' => 4,
                        'quality_rating' => 4,
                        'value_rating' => 5,
                        'communication_rating' => 4,
                        'comment' => 'Très bon prestataire, professionnel et réactif. Le projet a été réalisé conformément à mes attentes. Quelques petits ajustements ont été nécessaires mais dans l\'ensemble c\'était une excellente collaboration.',
                        'photos' => $this->generateRandomPhotoUrls(2), // 2 photos
                        'status' => 'approved',
                        'moderated_by' => $admins->count() > 1 ? $admins->get(1)->id : ($admin ? $admin->id : null),
                        'created_at' => Carbon::now()->subDays(8),
                    ]);
                }
                
                // Avis du troisième client
                if ($clients->count() >= 3) {
                    $client3 = $clients->get(2);
                    
                    Review::create([
                        'client_id' => $client3->id,
                        'prestataire_id' => $prestataire4->id,
                        'rating' => 5,
                        'punctuality_rating' => 5,
                        'quality_rating' => 5,
                        'value_rating' => 5,
                        'communication_rating' => 5,
                        'comment' => 'Collaboration parfaite ! Le prestataire a parfaitement compris mes besoins et a livré un travail impeccable. Je n\'hésiterai pas à faire appel à lui pour mes futurs projets.',
                        'photos' => null, // Pas de photos
                        'status' => 'approved',
                        'moderated_by' => $admin ? $admin->id : null,
                        'created_at' => Carbon::now()->subDays(5),
                    ]);
                }
            }
            
            // Avis du troisième client sur les autres prestataires
            if ($clients->count() >= 3 && $prestataires->count() >= 2) {
                $client3 = $clients->get(2);
                
                // Avis sur le premier prestataire
                $prestataire1 = $prestataires->first();
                
                Review::create([
                    'client_id' => $client3->id,
                    'prestataire_id' => $prestataire1->id,
                    'rating' => 4,
                    'punctuality_rating' => 4,
                    'quality_rating' => 4,
                    'value_rating' => 4,
                    'communication_rating' => 5, // Communication fluide
                    'comment' => 'Bonne prestation dans l\'ensemble. Le prestataire est compétent et a bien répondu à mes attentes. La communication était fluide et le projet a été livré dans les délais.',
                    'photos' => $this->generateRandomPhotoUrls(1), // 1 photo
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(18),
                ]);
                
                // Avis sur le deuxième prestataire
                if ($prestataires->count() >= 2) {
                    $prestataire2 = $prestataires->get(1);
                    
                    Review::create([
                        'client_id' => $client3->id,
                        'prestataire_id' => $prestataire2->id,
                        'rating' => 3,
                        'punctuality_rating' => 2, // Retard
                        'quality_rating' => 3,
                        'value_rating' => 3,
                        'communication_rating' => 2, // Communication à améliorer
                        'comment' => 'Prestation moyenne. Le travail a été fait correctement mais avec du retard. La communication aurait pu être meilleure. Des améliorations sont possibles.',
                        'photos' => null, // Pas de photos
                        'status' => 'pending', // En attente de modération
                        'moderated_by' => null, // Avis non modéré
                        'created_at' => Carbon::now()->subDays(1),
                    ]);
                }
            }
            
            // Ajout d'avis supplémentaires avec photos pour tester le filtre "Avec photos"
            if ($clients->count() >= 3 && $prestataires->count() >= 3) {
                $client3 = $clients->get(2);
                $prestataire3 = $prestataires->get(2);
                
                // Avis avec photos et note moyenne
                Review::create([
                    'client_id' => $client3->id,
                    'prestataire_id' => $prestataire3->id,
                    'rating' => 3,
                    'punctuality_rating' => 3,
                    'quality_rating' => 3,
                    'value_rating' => 4,
                    'communication_rating' => 3,
                    'comment' => 'Prestation correcte. Le résultat correspond à peu près à ce que j\'attendais. Quelques points auraient pu être améliorés mais dans l\'ensemble c\'est satisfaisant.',
                    'photos' => $this->generateRandomPhotoUrls(2), // 2 photos
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(7),
                ]);
            }
            
            // Ajout d'avis supplémentaires pour tester le filtre "Certificats de satisfaction" (notes >= 4)
            if ($clients->count() >= 2 && $prestataires->count() >= 3) {
                $client2 = $clients->get(1);
                $prestataire3 = $prestataires->get(2);
                
                // Avis avec note élevée sans photos
                Review::create([
                    'client_id' => $client2->id,
                    'prestataire_id' => $prestataire3->id,
                    'rating' => 4,
                    'punctuality_rating' => 4,
                    'quality_rating' => 5,
                    'value_rating' => 4,
                    'communication_rating' => 4,
                    'comment' => 'Très bonne prestation. Le prestataire a été à l\'écoute et a su s\'adapter à mes besoins. Je suis satisfait du résultat final.',
                    'photos' => null, // Pas de photos
                    'status' => 'approved',
                    'moderated_by' => $admin ? $admin->id : null,
                    'created_at' => Carbon::now()->subDays(3),
                ]);
            }
        }
    }
}