# Types d'événements notifiés dans l'application

Cette documentation liste tous les types de notifications implémentés dans l'application TaPrestation.

## 📧 Notifications existantes

### 1. Nouveau message dans la messagerie ✅
- **Classe**: `NewMessageNotification`
- **Déclencheur**: Réception d'un nouveau message
- **Destinataire**: Utilisateur qui reçoit le message
- **Canaux**: Email + Base de données
- **Contenu**: Nom de l'expéditeur, aperçu du message, lien vers la conversation

### 2. Demande client reçue (pour un prestataire) ✅
- **Classe**: `NewClientRequestNotification`
- **Déclencheur**: Nouvelle demande de service créée par un client
- **Destinataire**: Prestataires correspondant aux critères
- **Canaux**: Email + Base de données
- **Contenu**: Titre de la demande, budget, description, lien vers la demande

### 3. Réponse à une demande (pour un client) ✅
- **Classes**: 
  - `NewOfferNotification` - Nouvelle offre reçue
  - `RequestHasOffersNotification` - Demande avec plusieurs offres
  - `OfferAcceptedNotification` - Offre acceptée
  - `OfferRejectedNotification` - Offre rejetée
- **Déclencheur**: Actions sur les offres
- **Destinataires**: Clients et prestataires selon l'action
- **Canaux**: Email + Base de données

### 4. Avis ou note reçue ✅
- **Classe**: `NewReviewNotification`
- **Déclencheur**: Nouveau commentaire/évaluation laissé
- **Destinataire**: Prestataire évalué
- **Canaux**: Email + Base de données
- **Contenu**: Nom du client, note, commentaire, lien vers les évaluations

### 5. Validation de compte ou badge par l'administrateur ✅
- **Classe**: `PrestataireApprovedNotification`
- **Déclencheur**: Approbation du compte prestataire par l'admin
- **Destinataire**: Prestataire approuvé
- **Canaux**: Email + Base de données
- **Contenu**: Confirmation d'approbation, accès aux fonctionnalités

### 6. Publication d'une annonce validée ou refusée ✅
- **Classe**: `AnnouncementStatusNotification`
- **Déclencheur**: Validation/refus d'annonce par l'admin
- **Destinataire**: Auteur de l'annonce
- **Canaux**: Email + Base de données
- **Contenu**: Statut (validé/refusé), raison si refusé, lien vers l'annonce

## 📋 Notifications supplémentaires existantes

### Réservations et missions
- `NewBookingNotification` - Nouvelle réservation
- `BookingConfirmedNotification` - Réservation confirmée
- `MissionCompletedNotification` - Mission terminée

### Équipements
- `EquipmentRentalRequestConfirmationNotification` - Confirmation de demande de location

## 🔧 Configuration technique

### Canaux de notification
- **Email**: Notifications par email via Laravel Mail
- **Base de données**: Stockage en base pour affichage dans l'interface
- **Temps réel**: Système de polling JavaScript pour les messages

### Gestion des notifications
- Interface d'administration pour gérer toutes les notifications
- Possibilité d'envoyer des notifications personnalisées
- Statistiques et analyses des notifications
- Marquage comme lu/non lu

### Préférences utilisateur
- Configuration des notifications email dans le profil
- Options pour les notifications SMS (prévu)
- Gestion de la visibilité du profil

## 📊 Utilisation

Tous les types d'événements mentionnés dans les exigences sont maintenant couverts par le système de notifications de l'application. Les notifications sont automatiquement envoyées lors des événements correspondants et peuvent être consultées dans l'interface utilisateur.

### Exemple d'envoi de notification

```php
// Envoyer une notification de nouveau message
use App\Notifications\NewMessageNotification;

$user->notify(new NewMessageNotification($message));

// Envoyer une notification de demande client
use App\Notifications\NewClientRequestNotification;

$prestataire->notify(new NewClientRequestNotification($clientRequest));
```

## 🎯 Statut de couverture

✅ **Tous les types d'événements requis sont implémentés**

1. ✅ Nouveau message dans la messagerie
2. ✅ Demande client reçue (pour un prestataire)
3. ✅ Réponse à une demande (pour un client)
4. ✅ Avis ou note reçue
5. ✅ Validation de compte ou badge par l'administrateur
6. ✅ Publication d'une annonce validée ou refusée