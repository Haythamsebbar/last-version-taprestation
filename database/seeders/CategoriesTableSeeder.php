<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des catégories principales (sans parent)
        $plomberie = Category::firstOrCreate(['name' => 'Plomberie'],[
            'name' => 'Plomberie',
            'description' => 'Services de plomberie et installations sanitaires',
        ]);
        
        // Sous-catégories de Plomberie
        Category::firstOrCreate(['name' => 'Installation sanitaire'],[
            'name' => 'Installation sanitaire',
            'description' => 'Installation de sanitaires et équipements de salle de bain',
            'parent_id' => $plomberie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Dépannage plomberie'],[
            'name' => 'Dépannage plomberie',
            'description' => 'Services de dépannage et réparation en plomberie',
            'parent_id' => $plomberie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Chauffage'],[
            'name' => 'Chauffage',
            'description' => 'Installation et entretien de systèmes de chauffage',
            'parent_id' => $plomberie->id,
        ]);
        
        $electricite = Category::firstOrCreate(['name' => 'Électricité'],[
            'name' => 'Électricité',
            'description' => 'Services d\'installation et réparation électrique',
        ]);
        
        // Sous-catégories d'Électricité
        Category::firstOrCreate(['name' => 'Installation électrique'],[
            'name' => 'Installation électrique',
            'description' => 'Installation et mise aux normes de systèmes électriques',
            'parent_id' => $electricite->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Dépannage électrique'],[
            'name' => 'Dépannage électrique',
            'description' => 'Services de dépannage et réparation électrique',
            'parent_id' => $electricite->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Domotique'],[
            'name' => 'Domotique',
            'description' => 'Installation de systèmes domotiques et maison intelligente',
            'parent_id' => $electricite->id,
        ]);
        
        $informatique = Category::firstOrCreate(['name' => 'Informatique'],[
            'name' => 'Informatique',
            'description' => 'Services informatiques et assistance technique',
        ]);
        
        // Sous-catégories d'Informatique
        Category::firstOrCreate(['name' => 'Dépannage informatique'],[
            'name' => 'Dépannage informatique',
            'description' => 'Dépannage et réparation d\'ordinateurs et périphériques',
            'parent_id' => $informatique->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Développement web'],[
            'name' => 'Développement web',
            'description' => 'Création et maintenance de sites web et applications',
            'parent_id' => $informatique->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Réseaux'],[
            'name' => 'Réseaux',
            'description' => 'Installation et maintenance de réseaux informatiques',
            'parent_id' => $informatique->id,
        ]);
        
        $graphisme = Category::firstOrCreate(['name' => 'Graphisme'],[
            'name' => 'Graphisme',
            'description' => 'Services de conception graphique et design',
        ]);
        
        // Sous-catégories de Graphisme
        Category::firstOrCreate(['name' => 'Identité visuelle'],[
            'name' => 'Identité visuelle',
            'description' => 'Création de logos et identités visuelles',
            'parent_id' => $graphisme->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Illustration'],[
            'name' => 'Illustration',
            'description' => 'Création d\'illustrations et dessins',
            'parent_id' => $graphisme->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Print'],[
            'name' => 'Print',
            'description' => 'Conception de supports imprimés (flyers, affiches, etc.)',
            'parent_id' => $graphisme->id,
        ]);
        
        $marketing = Category::firstOrCreate(['name' => 'Marketing'],[
            'name' => 'Marketing',
            'description' => 'Services de marketing et communication',
        ]);
        
        // Sous-catégories de Marketing
        Category::firstOrCreate(['name' => 'Marketing digital'],[
            'name' => 'Marketing digital',
            'description' => 'Stratégies de marketing en ligne et réseaux sociaux',
            'parent_id' => $marketing->id,
        ]);
        
        Category::firstOrCreate(['name' => 'SEO'],[
            'name' => 'SEO',
            'description' => 'Optimisation pour les moteurs de recherche',
            'parent_id' => $marketing->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Relations publiques'],[
            'name' => 'Relations publiques',
            'description' => 'Services de relations publiques et communication',
            'parent_id' => $marketing->id,
        ]);
        
        $menuiserie = Category::firstOrCreate(['name' => 'Menuiserie'],[
            'name' => 'Menuiserie',
            'description' => 'Services de menuiserie et travaux du bois',
        ]);
        
        // Sous-catégories de Menuiserie
        Category::firstOrCreate(['name' => 'Menuiserie intérieure'],[
            'name' => 'Menuiserie intérieure',
            'description' => 'Fabrication et pose de meubles et aménagements intérieurs',
            'parent_id' => $menuiserie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Menuiserie extérieure'],[
            'name' => 'Menuiserie extérieure',
            'description' => 'Fabrication et pose de structures extérieures en bois',
            'parent_id' => $menuiserie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Ébénisterie'],[
            'name' => 'Ébénisterie',
            'description' => 'Création de meubles et objets en bois de qualité',
            'parent_id' => $menuiserie->id,
        ]);
        
        $peinture = Category::firstOrCreate(['name' => 'Peinture'],[
            'name' => 'Peinture',
            'description' => 'Services de peinture intérieure et extérieure',
        ]);
        
        // Sous-catégories de Peinture
        Category::firstOrCreate(['name' => 'Peinture intérieure'],[
            'name' => 'Peinture intérieure',
            'description' => 'Travaux de peinture pour l\'intérieur des bâtiments',
            'parent_id' => $peinture->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Peinture extérieure'],[
            'name' => 'Peinture extérieure',
            'description' => 'Travaux de peinture pour l\'extérieur des bâtiments',
            'parent_id' => $peinture->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Peinture décorative'],[
            'name' => 'Peinture décorative',
            'description' => 'Techniques de peinture décorative et artistique',
            'parent_id' => $peinture->id,
        ]);
        
        $maconnerie = Category::firstOrCreate(['name' => 'Maçonnerie'],[
            'name' => 'Maçonnerie',
            'description' => 'Services de maçonnerie et travaux de construction',
        ]);
        
        // Sous-catégories de Maçonnerie
        Category::firstOrCreate(['name' => 'Construction'],[
            'name' => 'Construction',
            'description' => 'Construction de murs, fondations et structures',
            'parent_id' => $maconnerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Rénovation maçonnerie'],[
            'name' => 'Rénovation maçonnerie',
            'description' => 'Rénovation et restauration de maçonnerie existante',
            'parent_id' => $maconnerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Pavage'],[
            'name' => 'Pavage',
            'description' => 'Installation de pavés et dallages',
            'parent_id' => $maconnerie->id,
        ]);
        
        $jardinage = Category::firstOrCreate(['name' => 'Jardinage'],[
            'name' => 'Jardinage',
            'description' => 'Services d\'entretien de jardins et espaces verts',
        ]);
        
        // Sous-catégories de Jardinage
        Category::firstOrCreate(['name' => 'Entretien régulier'],[
            'name' => 'Entretien régulier',
            'description' => 'Tonte de pelouse, taille de haies et entretien courant',
            'parent_id' => $jardinage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Aménagement paysager'],[
            'name' => 'Aménagement paysager',
            'description' => 'Création et aménagement de jardins et espaces verts',
            'parent_id' => $jardinage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Élagage et abattage'],[
            'name' => 'Élagage et abattage',
            'description' => 'Services d\'élagage, abattage et soins aux arbres',
            'parent_id' => $jardinage->id,
        ]);
        
        $nettoyage = Category::firstOrCreate(['name' => 'Nettoyage'],[
            'name' => 'Nettoyage',
            'description' => 'Services de nettoyage et entretien',
        ]);
        
        // Sous-catégories de Nettoyage
        Category::firstOrCreate(['name' => 'Nettoyage résidentiel'],[
            'name' => 'Nettoyage résidentiel',
            'description' => 'Services de nettoyage pour particuliers et résidences',
            'parent_id' => $nettoyage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Nettoyage commercial'],[
            'name' => 'Nettoyage commercial',
            'description' => 'Services de nettoyage pour entreprises et commerces',
            'parent_id' => $nettoyage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Nettoyage spécialisé'],[
            'name' => 'Nettoyage spécialisé',
            'description' => 'Services de nettoyage spécifiques (après sinistre, fin de chantier, etc.)',
            'parent_id' => $nettoyage->id,
        ]);
        
        $serrurerie = Category::firstOrCreate(['name' => 'Serrurerie'],[
            'name' => 'Serrurerie',
            'description' => 'Services de serrurerie et sécurité',
        ]);
        
        // Sous-catégories de Serrurerie
        Category::firstOrCreate(['name' => 'Dépannage serrurerie'],[
            'name' => 'Dépannage serrurerie',
            'description' => 'Services d\'urgence et dépannage en serrurerie',
            'parent_id' => $serrurerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Installation de serrures'],[
            'name' => 'Installation de serrures',
            'description' => 'Installation et remplacement de serrures et systèmes de sécurité',
            'parent_id' => $serrurerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Coffres-forts'],[
            'name' => 'Coffres-forts',
            'description' => 'Installation et ouverture de coffres-forts',
            'parent_id' => $serrurerie->id,
        ]);
        
        $carrelage = Category::firstOrCreate(['name' => 'Carrelage'],[
            'name' => 'Carrelage',
            'description' => 'Services de pose et réparation de carrelage',
        ]);
        
        // Sous-catégories de Carrelage
        Category::firstOrCreate(['name' => 'Pose de carrelage'],[
            'name' => 'Pose de carrelage',
            'description' => 'Installation de carrelage mural et au sol',
            'parent_id' => $carrelage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Réparation de carrelage'],[
            'name' => 'Réparation de carrelage',
            'description' => 'Réparation et remplacement de carreaux endommagés',
            'parent_id' => $carrelage->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Mosaïque'],[
            'name' => 'Mosaïque',
            'description' => 'Création et pose de mosaïques décoratives',
            'parent_id' => $carrelage->id,
        ]);
        
        $vitrerie = Category::firstOrCreate(['name' => 'Vitrerie'],[
            'name' => 'Vitrerie',
            'description' => 'Services de vitrerie et miroiterie',
        ]);
        
        // Sous-catégories de Vitrerie
        Category::firstOrCreate(['name' => 'Remplacement de vitres'],[
            'name' => 'Remplacement de vitres',
            'description' => 'Remplacement de vitres cassées ou endommagées',
            'parent_id' => $vitrerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Installation de fenêtres'],[
            'name' => 'Installation de fenêtres',
            'description' => 'Installation de fenêtres et baies vitrées',
            'parent_id' => $vitrerie->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Miroiterie'],[
            'name' => 'Miroiterie',
            'description' => 'Fabrication et installation de miroirs sur mesure',
            'parent_id' => $vitrerie->id,
        ]);
        
        $toiture = Category::firstOrCreate(['name' => 'Toiture'],[
            'name' => 'Toiture',
            'description' => 'Services de couverture et réparation de toiture',
        ]);
        
        // Sous-catégories de Toiture
        Category::firstOrCreate(['name' => 'Réparation de toiture'],[
            'name' => 'Réparation de toiture',
            'description' => 'Réparation de fuites et dommages sur toiture',
            'parent_id' => $toiture->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Installation de toiture'],[
            'name' => 'Installation de toiture',
            'description' => 'Installation de nouvelles toitures',
            'parent_id' => $toiture->id,
        ]);
        
        Category::firstOrCreate(['name' => 'Nettoyage de toiture'],[
            'name' => 'Nettoyage de toiture',
            'description' => 'Nettoyage et démoussage de toitures',
            'parent_id' => $toiture->id,
        ]);
        
        $transport = Category::create([
            'name' => 'Transport',
            'description' => 'Services de transport et livraison',
        ]);
        
        // Sous-catégories de Transport
        Category::create([
            'name' => 'Transport de personnes',
            'description' => 'Services de transport de personnes et VTC',
            'parent_id' => $transport->id,
        ]);
        
        Category::create([
            'name' => 'Déménagement',
            'description' => 'Services de déménagement et transport de mobilier',
            'parent_id' => $transport->id,
        ]);
        
        Category::create([
            'name' => 'Livraison',
            'description' => 'Services de livraison de colis et marchandises',
            'parent_id' => $transport->id,
        ]);
        
        $evenementiel = Category::create([
            'name' => 'Événementiel',
            'description' => 'Services d\'organisation d\'événements',
        ]);
        
        // Sous-catégories d'Événementiel
        Category::create([
            'name' => 'Mariage',
            'description' => 'Organisation et coordination de mariages',
            'parent_id' => $evenementiel->id,
        ]);
        
        Category::create([
            'name' => 'Événements d\'entreprise',
            'description' => 'Organisation de séminaires, conférences et événements professionnels',
            'parent_id' => $evenementiel->id,
        ]);
        
        Category::create([
            'name' => 'Fêtes privées',
            'description' => 'Organisation d\'anniversaires et célébrations privées',
            'parent_id' => $evenementiel->id,
        ]);
        
        $coiffure_beaute = Category::create([
            'name' => 'Coiffure & Beauté',
            'description' => 'Services de coiffure et soins esthétiques',
        ]);
        
        // Sous-catégories de Coiffure & Beauté
        Category::create([
            'name' => 'Coiffure',
            'description' => 'Services de coupe, coloration et coiffure',
            'parent_id' => $coiffure_beaute->id,
        ]);
        
        Category::create([
            'name' => 'Soins esthétiques',
            'description' => 'Services de soins du visage et du corps',
            'parent_id' => $coiffure_beaute->id,
        ]);
        
        Category::create([
            'name' => 'Maquillage',
            'description' => 'Services de maquillage professionnel',
            'parent_id' => $coiffure_beaute->id,
        ]);
        
        $mecanique = Category::create([
            'name' => 'Mécanique',
            'description' => 'Services de mécanique et réparation automobile',
        ]);
        
        // Sous-catégories de Mécanique
        Category::create([
            'name' => 'Réparation automobile',
            'description' => 'Services de réparation et entretien de véhicules',
            'parent_id' => $mecanique->id,
        ]);
        
        Category::create([
            'name' => 'Diagnostic',
            'description' => 'Services de diagnostic et détection de pannes',
            'parent_id' => $mecanique->id,
        ]);
        
        Category::create([
            'name' => 'Carrosserie',
            'description' => 'Services de réparation de carrosserie et peinture',
            'parent_id' => $mecanique->id,
        ]);
        
        $renovation = Category::create([
            'name' => 'Rénovation',
            'description' => 'Services de rénovation et amélioration de l\'habitat',
        ]);
        
        // Sous-catégories de Rénovation
        Category::create([
            'name' => 'Rénovation complète',
            'description' => 'Services de rénovation totale de l\'habitat',
            'parent_id' => $renovation->id,
        ]);
        
        Category::create([
            'name' => 'Rénovation partielle',
            'description' => 'Services de rénovation de pièces spécifiques',
            'parent_id' => $renovation->id,
        ]);
        
        Category::create([
            'name' => 'Isolation',
            'description' => 'Services d\'isolation thermique et phonique',
            'parent_id' => $renovation->id,
        ]);
        
        $decoration = Category::create([
            'name' => 'Décoration',
            'description' => 'Services de décoration d\'intérieur et d\'extérieur',
        ]);
        
        // Sous-catégories de Décoration
        Category::create([
            'name' => 'Décoration intérieure',
            'description' => 'Services de décoration pour l\'intérieur de la maison',
            'parent_id' => $decoration->id,
        ]);
        
        Category::create([
            'name' => 'Décoration extérieure',
            'description' => 'Services de décoration pour l\'extérieur de la maison',
            'parent_id' => $decoration->id,
        ]);
        
        Category::create([
            'name' => 'Home staging',
            'description' => 'Services de mise en valeur de biens immobiliers',
            'parent_id' => $decoration->id,
        ]);
        
        $photographie = Category::create([
            'name' => 'Photographie',
            'description' => 'Services de photographie professionnelle',
        ]);
        
        // Sous-catégories de Photographie
        Category::create([
            'name' => 'Photographie événementielle',
            'description' => 'Services de photographie pour les événements',
            'parent_id' => $photographie->id,
        ]);
        
        Category::create([
            'name' => 'Photographie de portrait',
            'description' => 'Services de photographie de portrait',
            'parent_id' => $photographie->id,
        ]);
        
        Category::create([
            'name' => 'Photographie immobilière',
            'description' => 'Services de photographie pour l\'immobilier',
            'parent_id' => $photographie->id,
        ]);
        
        $securite = Category::create([
            'name' => 'Sécurité',
            'description' => 'Services de sécurité et surveillance',
        ]);
        
        // Sous-catégories de Sécurité
        Category::create([
            'name' => 'Systèmes d\'alarme',
            'description' => 'Installation et maintenance de systèmes d\'alarme',
            'parent_id' => $securite->id,
        ]);
        
        Category::create([
            'name' => 'Vidéosurveillance',
            'description' => 'Installation et maintenance de systèmes de vidéosurveillance',
            'parent_id' => $securite->id,
        ]);
        
        Category::create([
            'name' => 'Gardiennage',
            'description' => 'Services de gardiennage et de surveillance',
            'parent_id' => $securite->id,
        ]);
        
        $boulangerie = Category::create([
            'name' => 'Boulangerie & Pâtisserie',
            'description' => 'Services de boulangerie et pâtisserie',
        ]);
        
        // Sous-catégories de Boulangerie & Pâtisserie
        Category::create([
            'name' => 'Pains et viennoiseries',
            'description' => 'Services de fabrication de pains et viennoiseries',
            'parent_id' => $boulangerie->id,
        ]);
        
        Category::create([
            'name' => 'Pâtisseries',
            'description' => 'Services de fabrication de pâtisseries',
            'parent_id' => $boulangerie->id,
        ]);
        
        Category::create([
            'name' => 'Gâteaux sur mesure',
            'description' => 'Services de création de gâteaux personnalisés',
            'parent_id' => $boulangerie->id,
        ]);
        
        $traduction = Category::create([
            'name' => 'Traduction',
            'description' => 'Services de traduction et interprétation',
        ]);
        
        // Sous-catégories de Traduction
        Category::create([
            'name' => 'Traduction de documents',
            'description' => 'Services de traduction de documents écrits',
            'parent_id' => $traduction->id,
        ]);
        
        Category::create([
            'name' => 'Interprétation',
            'description' => 'Services d\'interprétation orale',
            'parent_id' => $traduction->id,
        ]);
        
        Category::create([
            'name' => 'Traduction technique',
            'description' => 'Services de traduction de documents techniques',
            'parent_id' => $traduction->id,
        ]);
        
        $conseil_juridique = Category::create([
            'name' => 'Conseil juridique',
            'description' => 'Services de conseil juridique',
        ]);
        
        // Sous-catégories de Conseil juridique
        Category::create([
            'name' => 'Droit des affaires',
            'description' => 'Services de conseil en droit des affaires',
            'parent_id' => $conseil_juridique->id,
        ]);
        
        Category::create([
            'name' => 'Droit immobilier',
            'description' => 'Services de conseil en droit immobilier',
            'parent_id' => $conseil_juridique->id,
        ]);
        
        Category::create([
            'name' => 'Droit de la famille',
            'description' => 'Services de conseil en droit de la famille',
            'parent_id' => $conseil_juridique->id,
        ]);
        
        $formation = Category::create([
            'name' => 'Formation',
            'description' => 'Services de formation et cours particuliers',
        ]);
        
        // Sous-catégories de Formation
        Category::create([
            'name' => 'Formation professionnelle',
            'description' => 'Services de formation pour les professionnels',
            'parent_id' => $formation->id,
        ]);
        
        Category::create([
            'name' => 'Cours particuliers',
            'description' => 'Services de cours particuliers',
            'parent_id' => $formation->id,
        ]);
        
        Category::create([
            'name' => 'Coaching scolaire',
            'description' => 'Services de coaching et soutien scolaire',
            'parent_id' => $formation->id,
        ]);
        
        $consulting = Category::create([
            'name' => 'Consulting business',
            'description' => 'Services de conseil en entreprise et stratégie',
        ]);
        
        // Sous-catégories de Consulting business
        Category::create([
            'name' => 'Stratégie d\'entreprise',
            'description' => 'Services de conseil en stratégie d\'entreprise',
            'parent_id' => $consulting->id,
        ]);
        
        Category::create([
            'name' => 'Gestion financière',
            'description' => 'Services de conseil en gestion financière',
            'parent_id' => $consulting->id,
        ]);
        
        Category::create([
            'name' => 'Ressources humaines',
            'description' => 'Services de conseil en ressources humaines',
            'parent_id' => $consulting->id,
        ]);
        
        $musique = Category::create([
            'name' => 'Musique et Spectacle',
            'description' => 'Services liés à la musique et au spectacle',
        ]);
        
        // Sous-catégories de Musique et Spectacle
        Category::create([
            'name' => 'Animation musicale',
            'description' => 'Services d\'animation musicale pour événements',
            'parent_id' => $musique->id,
        ]);
        
        Category::create([
            'name' => 'Production musicale',
            'description' => 'Services de production et enregistrement musical',
            'parent_id' => $musique->id,
        ]);
        
        Category::create([
            'name' => 'Spectacles vivants',
            'description' => 'Services d\'organisation de spectacles vivants',
            'parent_id' => $musique->id,
        ]);
        
        $sport = Category::create([
            'name' => 'Sport et Coaching',
            'description' => 'Services de coaching sportif et bien-être',
        ]);
        
        // Sous-catégories de Sport et Coaching
        Category::create([
            'name' => 'Coaching sportif',
            'description' => 'Services de coaching sportif personnalisé',
            'parent_id' => $sport->id,
        ]);
        
        Category::create([
            'name' => 'Yoga et méditation',
            'description' => 'Services de cours de yoga et méditation',
            'parent_id' => $sport->id,
        ]);
        
        Category::create([
            'name' => 'Nutrition sportive',
            'description' => 'Services de conseil en nutrition pour sportifs',
            'parent_id' => $sport->id,
        ]);
        
        $restauration = Category::create([
            'name' => 'Restauration et Traiteur',
            'description' => 'Services de restauration et traiteur',
        ]);
        
        // Sous-catégories de Restauration et Traiteur
        Category::create([
            'name' => 'Traiteur événementiel',
            'description' => 'Services de traiteur pour événements',
            'parent_id' => $restauration->id,
        ]);
        
        Category::create([
            'name' => 'Chef à domicile',
            'description' => 'Services de chef cuisinier à domicile',
            'parent_id' => $restauration->id,
        ]);
        
        Category::create([
            'name' => 'Food truck',
            'description' => 'Services de restauration mobile',
            'parent_id' => $restauration->id,
        ]);
        
        $dev_mobile = Category::create([
            'name' => 'Développement Mobile',
            'description' => 'Services de développement d\'applications mobiles',
        ]);
        
        // Sous-catégories de Développement Mobile
        Category::create([
            'name' => 'Applications iOS',
            'description' => 'Développement d\'applications pour iPhone et iPad',
            'parent_id' => $dev_mobile->id,
        ]);
        
        Category::create([
            'name' => 'Applications Android',
            'description' => 'Développement d\'applications pour appareils Android',
            'parent_id' => $dev_mobile->id,
        ]);
        
        Category::create([
            'name' => 'Applications hybrides',
            'description' => 'Développement d\'applications multiplateformes',
            'parent_id' => $dev_mobile->id,
        ]);
        
        $vente_vehicule = Category::create([
            'name' => 'Vente véhicule',
            'description' => 'Services de vente de véhicules',
        ]);
        
        // Sous-catégories de Vente véhicule
        Category::create([
            'name' => 'Voitures neuves',
            'description' => 'Services de vente de voitures neuves',
            'parent_id' => $vente_vehicule->id,
        ]);
        
        Category::create([
            'name' => 'Voitures d\'occasion',
            'description' => 'Services de vente de voitures d\'occasion',
            'parent_id' => $vente_vehicule->id,
        ]);
        
        Category::create([
            'name' => 'Motos et scooters',
            'description' => 'Services de vente de motos et scooters',
            'parent_id' => $vente_vehicule->id,
        ]);
    }
}