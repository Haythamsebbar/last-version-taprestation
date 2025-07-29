# Guide d'implémentation WebSocket pour Messages en Temps Réel

## État Actuel

Le système de messagerie utilise actuellement un polling HTTP toutes les 2 secondes pour récupérer les nouveaux messages. Cette solution fonctionne mais peut être améliorée avec WebSockets pour une communication vraiment temps réel.

## Solution Recommandée : Laravel Echo + Pusher

### 1. Installation des Dépendances

#### Backend (Composer)
```bash
composer require pusher/pusher-php-server
```

#### Frontend (NPM)
```bash
npm install --save laravel-echo pusher-js
```

### 2. Configuration Laravel

#### Publier la configuration de broadcasting
```bash
php artisan vendor:publish --provider="Illuminate\Broadcasting\BroadcastServiceProvider"
```

#### Configurer .env
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

#### Décommenter dans config/app.php
```php
App\Providers\BroadcastServiceProvider::class,
```

### 3. Créer un Event pour les Nouveaux Messages

```bash
php artisan make:event MessageSent
```

```php
<?php
// app/Events/MessageSent.php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->sender = $message->sender;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'created_at' => $this->message->created_at,
            'formatted_time' => $this->message->created_at->format('H:i'),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar
            ]
        ];
    }
}
```

### 4. Modifier le MessageController

```php
// Dans app/Http/Controllers/MessageController.php
// Ajouter dans la méthode sendMessage après la création du message

use App\Events\MessageSent;

// Après Message::create(...)
event(new MessageSent($message));
```

### 5. Configurer les Routes de Broadcasting

```php
// routes/channels.php
Broadcast::channel('conversation.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

### 6. Configuration Frontend

#### Créer resources/js/echo.js
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

#### Modifier messaging.js pour utiliser WebSockets
```javascript
// Ajouter dans la classe MessagingSystem

initWebSocket() {
    if (typeof Echo !== 'undefined' && this.currentUserId) {
        // Écouter les nouveaux messages
        Echo.private(`conversation.${this.currentUserId}`)
            .listen('MessageSent', (e) => {
                this.handleNewMessage(e);
            });
        
        // Écouter les statuts de lecture
        Echo.private(`conversation.${this.currentUserId}`)
            .listen('MessageRead', (e) => {
                this.handleMessageRead(e);
            });
    }
}

handleNewMessage(event) {
    // Vérifier si le message n'existe pas déjà
    if (!document.querySelector(`[data-message-id="${event.id}"]`)) {
        this.addMessageToConversation(event);
        this.scrollToBottom();
        this.playNotificationSound();
        
        // Marquer automatiquement comme lu si visible
        if (this.isMessageVisible(event.id)) {
            setTimeout(() => {
                this.markMessagesAsRead([event.id]);
            }, 1000);
        }
    }
}

// Remplacer setupConversationPolling par :
setupConversationPolling() {
    if (typeof Echo !== 'undefined') {
        this.initWebSocket();
    } else {
        // Fallback vers polling si WebSocket non disponible
        this.messagesPollInterval = setInterval(() => {
            this.checkNewMessages();
        }, 5000); // Réduire la fréquence avec WebSocket
    }
}
```

### 7. Compilation des Assets

#### Modifier vite.config.js
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/echo.js'],
            refresh: true,
        }),
    ],
    define: {
        'process.env': {
            MIX_PUSHER_APP_KEY: JSON.stringify(process.env.PUSHER_APP_KEY),
            MIX_PUSHER_APP_CLUSTER: JSON.stringify(process.env.PUSHER_APP_CLUSTER)
        }
    }
});
```

#### Inclure Echo dans le template
```html
<!-- Dans conversation.blade.php -->
@vite(['resources/js/echo.js'])
```

### 8. Commandes de Déploiement

```bash
# Installer les dépendances
composer install
npm install

# Compiler les assets
npm run build

# Configurer la queue pour les broadcasts
php artisan queue:work
```

## Avantages de cette Solution

1. **Temps Réel Véritable** : Messages instantanés sans délai
2. **Moins de Charge Serveur** : Pas de polling constant
3. **Meilleure UX** : Notifications instantanées
4. **Scalabilité** : Pusher gère la montée en charge
5. **Fallback** : Le polling reste disponible si WebSocket échoue

## Alternative Gratuite : Laravel WebSockets

Pour éviter les coûts de Pusher :

```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan websockets:serve
```

Configurer .env :
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

## État Actuel du Système

Le système actuel avec polling amélioré fonctionne déjà en temps quasi-réel :
- ✅ Vérification automatique toutes les 2 secondes
- ✅ Affichage immédiat des nouveaux messages
- ✅ Marquage automatique comme lu
- ✅ Animations fluides
- ✅ Son de notification
- ✅ Gestion des erreurs

La solution WebSocket est recommandée pour une montée en charge importante ou pour une expérience utilisateur optimale.