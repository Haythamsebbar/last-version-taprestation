# Guide des Messages de Test

## Vue d'ensemble

Ce guide explique comment utiliser et gérer les messages de test créés pour le système de messagerie de la plateforme.

## Utilisateurs de Test Créés

### 1. Client Test
- **Email :** `client.test@example.com`
- **Mot de passe :** `password`
- **Rôle :** `client`
- **Statut :** En ligne
- **ID :** 11

### 2. Prestataire Test
- **Email :** `prestataire.test@example.com`
- **Mot de passe :** `password`
- **Rôle :** `prestataire`
- **Statut :** Hors ligne (vu il y a 15 minutes)
- **ID :** 12

### 3. Administrateur Test
- **Email :** `admin.test@example.com`
- **Mot de passe :** `password`
- **Rôle :** `administrateur`
- **Statut :** En ligne
- **ID :** 13

## Messages de Test Créés

### Migration Initiale (10 messages)
La migration `2025_06_30_152455_create_test_messages.php` a créé :
- 7 messages entre client et prestataire
- 3 messages entre admin et client

### Seeder Supplémentaire (20 messages)
Le seeder `TestMessagesSeeder` a ajouté 5 scénarios différents :

#### 1. Négociation de Projet (4 messages)
- Demande de devis détaillé
- Proposition de prix
- Négociation budgétaire
- Contre-proposition

#### 2. Suivi de Projet (3 messages)
- Livraison de maquettes
- Validation client
- Envoi par email

#### 3. Questions Techniques (4 messages)
- Responsive design
- Solutions de paiement
- Intégration Stripe/PayPal
- **1 message non lu**

#### 4. Support Admin (5 messages)
- Problème de profil
- Diagnostic du problème
- Résolution du bug
- Confirmation de correction

#### 5. Messages Récents (4 messages)
- Questions sur le design
- Choix de couleurs
- Mise à jour du développement
- **2 messages non lus** (pour tester le temps réel)

## Fonctionnalités Testables

### ✅ Messages en Temps Réel
- Polling automatique toutes les 2 secondes
- Affichage immédiat des nouveaux messages
- Messages non lus pour tester la réception

### ✅ Statuts de Lecture
- Messages lus avec timestamps
- Messages non lus (null)
- Indicateurs visuels

### ✅ Statuts Utilisateurs
- Utilisateur en ligne (client, admin)
- Utilisateur hors ligne (prestataire)
- Dernière connexion avec timestamps

### ✅ Conversations Multiples
- Client ↔ Prestataire
- Client ↔ Admin
- Historique de conversations

## Comment Tester

### 1. Connexion avec les Comptes de Test
```bash
# Se connecter avec :
Email: client.test@example.com
Mot de passe: password

# Ou :
Email: prestataire.test@example.com
Mot de passe: password

# Ou :
Email: admin.test@example.com
Mot de passe: password
```

### 2. Accéder aux Messages
- Aller sur `/messaging`
- Sélectionner une conversation
- Observer les messages existants
- Envoyer de nouveaux messages

### 3. Tester le Temps Réel
1. Ouvrir deux navigateurs/onglets
2. Se connecter avec des comptes différents
3. Envoyer un message depuis un compte
4. Observer l'affichage automatique dans l'autre

## Commandes Utiles

### Exécuter la Migration
```bash
php artisan migrate --path=database/migrations/2025_06_30_152455_create_test_messages.php
```

### Exécuter le Seeder
```bash
php artisan db:seed --class=TestMessagesSeeder
```

### Annuler la Migration (Supprimer les Messages)
```bash
php artisan migrate:rollback --path=database/migrations/2025_06_30_152455_create_test_messages.php
```

### Vérifier les Messages en Base
```bash
php artisan tinker

# Dans tinker :
App\Models\Message::count(); // Nombre total de messages
App\Models\Message::whereNull('read_at')->count(); // Messages non lus
App\Models\User::where('email', 'like', '%.test@%')->get(); // Utilisateurs de test
```

## Personnalisation

### Ajouter Plus de Messages
Modifiez le fichier `TestMessagesSeeder.php` pour ajouter :
- Nouveaux scénarios
- Plus d'utilisateurs
- Messages avec pièces jointes
- Messages de groupe

### Créer de Nouveaux Utilisateurs de Test
```php
$newUser = User::create([
    'name' => 'Nouveau Test',
    'email' => 'nouveau.test@example.com',
    'password' => bcrypt('password'),
    'role' => 'client', // ou 'prestataire', 'administrateur'
    'is_online' => true,
    'last_seen_at' => now()
]);
```

### Scénarios Supplémentaires Suggérés
- Messages avec emojis
- Messages longs (test de l'affichage)
- Messages avec liens
- Conversations de groupe
- Messages d'urgence
- Notifications système

## Nettoyage

### Supprimer Tous les Messages de Test
```bash
# Via migration rollback
php artisan migrate:rollback --path=database/migrations/2025_06_30_152455_create_test_messages.php

# Ou via tinker
php artisan tinker
App\Models\Message::whereHas('sender', function($q) { $q->where('email', 'like', '%.test@%'); })->delete();
App\Models\Message::whereHas('receiver', function($q) { $q->where('email', 'like', '%.test@%'); })->delete();
```

### Supprimer les Utilisateurs de Test
```bash
php artisan tinker
App\Models\User::where('email', 'like', '%.test@%')->delete();
```

## Dépannage

### Problèmes Courants

1. **Erreur "role enum"**
   - Vérifier que les rôles utilisent les valeurs exactes : `client`, `prestataire`, `administrateur`

2. **Messages non affichés**
   - Vérifier que le polling est activé dans `messaging.js`
   - Contrôler les routes dans `web.php`

3. **Utilisateurs non créés**
   - Vérifier que la migration des rôles a été exécutée
   - Contrôler la structure de la table `users`

### Logs Utiles
```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs du serveur de développement
php artisan serve --verbose
```

## Performance

### Optimisations Recommandées
- Index sur `sender_id` et `receiver_id` dans la table `messages`
- Pagination des messages anciens
- Cache des conversations actives
- Nettoyage périodique des anciens messages de test

## Sécurité

⚠️ **Important :** Ces comptes de test ne doivent être utilisés qu'en développement !

- Supprimer en production
- Mots de passe par défaut
- Données fictives uniquement

Pour la production, utilisez des données anonymisées et des comptes dédiés aux tests.