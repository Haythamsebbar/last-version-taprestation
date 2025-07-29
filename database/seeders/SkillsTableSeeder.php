<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Compétences de développement
        Skill::create([
            'name' => 'PHP',
            'description' => 'Langage de programmation côté serveur pour le développement web',
        ]);
        
        Skill::create([
            'name' => 'Laravel',
            'description' => 'Framework PHP moderne pour le développement web',
        ]);
        
        Skill::create([
            'name' => 'JavaScript',
            'description' => 'Langage de programmation pour le développement web côté client',
        ]);
        
        Skill::create([
            'name' => 'React',
            'description' => 'Bibliothèque JavaScript pour créer des interfaces utilisateur',
        ]);
        
        Skill::create([
            'name' => 'Vue.js',
            'description' => 'Framework JavaScript progressif pour construire des interfaces utilisateur',
        ]);
        
        Skill::create([
            'name' => 'Angular',
            'description' => 'Framework JavaScript pour le développement d\'applications web',
        ]);
        
        Skill::create([
            'name' => 'Node.js',
            'description' => 'Environnement d\'exécution JavaScript côté serveur',
        ]);
        
        Skill::create([
            'name' => 'Python',
            'description' => 'Langage de programmation polyvalent',
        ]);
        
        Skill::create([
            'name' => 'Django',
            'description' => 'Framework web Python de haut niveau',
        ]);
        
        Skill::create([
            'name' => 'Ruby on Rails',
            'description' => 'Framework d\'application web écrit en Ruby',
        ]);
        
        // Compétences de design
        Skill::create([
            'name' => 'Photoshop',
            'description' => 'Logiciel d\'édition d\'images et de design graphique',
        ]);
        
        Skill::create([
            'name' => 'Illustrator',
            'description' => 'Logiciel de création graphique vectorielle',
        ]);
        
        Skill::create([
            'name' => 'Figma',
            'description' => 'Outil de conception d\'interfaces utilisateur basé sur le web',
        ]);
        
        Skill::create([
            'name' => 'Adobe XD',
            'description' => 'Outil de conception et de prototypage d\'expérience utilisateur',
        ]);
        
        Skill::create([
            'name' => 'Sketch',
            'description' => 'Application de design numérique pour macOS',
        ]);
        
        // Compétences de marketing
        Skill::create([
            'name' => 'SEO',
            'description' => 'Optimisation pour les moteurs de recherche',
        ]);
        
        Skill::create([
            'name' => 'Google Ads',
            'description' => 'Plateforme publicitaire de Google',
        ]);
        
        Skill::create([
            'name' => 'Facebook Ads',
            'description' => 'Plateforme publicitaire de Facebook',
        ]);
        
        Skill::create([
            'name' => 'Content Marketing',
            'description' => 'Stratégie de marketing axée sur la création et la distribution de contenu',
        ]);
        
        Skill::create([
            'name' => 'Email Marketing',
            'description' => 'Stratégie de marketing utilisant l\'email comme canal de communication',
        ]);
        
        // Compétences de rédaction
        Skill::create([
            'name' => 'Copywriting',
            'description' => 'Rédaction persuasive pour la publicité et le marketing',
        ]);
        
        Skill::create([
            'name' => 'Rédaction Web',
            'description' => 'Création de contenu optimisé pour le web',
        ]);
        
        Skill::create([
            'name' => 'Traduction Français-Anglais',
            'description' => 'Traduction entre le français et l\'anglais',
        ]);
        
        Skill::create([
            'name' => 'Traduction Français-Espagnol',
            'description' => 'Traduction entre le français et l\'espagnol',
        ]);
        
        // Compétences vidéo et audio
        Skill::create([
            'name' => 'Montage Vidéo',
            'description' => 'Édition et assemblage de séquences vidéo',
        ]);
        
        Skill::create([
            'name' => 'After Effects',
            'description' => 'Logiciel de montage vidéo et d\'effets visuels',
        ]);
        
        Skill::create([
            'name' => 'Premiere Pro',
            'description' => 'Logiciel de montage vidéo professionnel',
        ]);
        
        Skill::create([
            'name' => 'Motion Design',
            'description' => 'Animation graphique et design en mouvement',
        ]);
        
        Skill::create([
            'name' => 'Production Audio',
            'description' => 'Enregistrement et mixage audio',
        ]);
    }
}