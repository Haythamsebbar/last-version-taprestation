/**
 * Système de messagerie en temps réel
 * Gestion des notifications, compteurs et interactions AJAX
 */

class MessagingSystem {
    constructor() {
        console.log('🎯 Initialisation de MessagingSystem');
        this.unreadCount = 0;
        this.currentConversationUserId = null;
        this.currentUserId = null;
        this.lastMessageId = 0;
        this.pollInterval = null;
        this.statusPollInterval = null;
        this.init();
        console.log('✅ MessagingSystem initialisé');
    }

    init() {
        // Vérifier que le token CSRF est disponible
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('❌ Token CSRF manquant dans le meta tag');
            return;
        }
        
        // Initialiser l'ID utilisateur courant
        const currentUserElement = document.querySelector('[data-current-user-id]');
        if (currentUserElement) {
            this.currentUserId = currentUserElement.dataset.currentUserId;
        }
        
        this.updateUnreadCount();
        this.setupEventListeners();
        this.startPolling();
        
        // Détecter si on est sur une page de conversation
        const conversationPage = document.querySelector('[data-conversation-user-id]');
        if (conversationPage) {
            this.currentConversationUserId = conversationPage.dataset.conversationUserId;
            this.setupConversationPolling();
            this.startStatusPolling();
            this.setupMessageReadObserver();
        }
    }

    /**
     * Mettre à jour le compteur de messages non lus
     */
    async updateUnreadCount() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('❌ Token CSRF manquant pour updateUnreadCount');
                return;
            }
            
            const response = await fetch('/messaging/unread-count', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.unreadCount = data.unread_count || 0;
                this.updateBadge();
            } else {
                console.warn('⚠️ Erreur HTTP lors de la récupération du compteur:', response.status);
            }
        } catch (error) {
            console.error('❌ Erreur lors de la récupération du compteur:', error);
        }
    }

    /**
     * Mettre à jour le badge de notification
     */
    updateBadge() {
        const badge = document.getElementById('unread-messages-badge');
        if (badge) {
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                badge.classList.remove('d-none', 'hidden');
                badge.style.display = '';
                
                // Animation de notification
                badge.classList.add('animate-pulse');
                setTimeout(() => {
                    badge.classList.remove('animate-pulse');
                }, 2000);
            } else {
                badge.classList.add('d-none');
                badge.style.display = 'none';
            }
        }
    }

    /**
     * Configuration des event listeners
     */
    setupEventListeners() {
        console.log('🔧 Configuration des event listeners');
        
        // Attendre que le DOM soit complètement chargé
        setTimeout(() => {
            // Formulaire d'envoi de message AJAX
            const messageForm = document.getElementById('message-form');
            console.log('📋 Formulaire trouvé:', messageForm ? 'OUI' : 'NON');
            
            if (messageForm) {
                // Vérifier si l'event listener n'est pas déjà ajouté
                if (!messageForm.hasAttribute('data-listener-added')) {
                    messageForm.addEventListener('submit', (e) => {
                        console.log('📤 Événement submit déclenché');
                        e.preventDefault();
                        this.sendMessage(messageForm);
                    });
                    messageForm.setAttribute('data-listener-added', 'true');
                    console.log('✅ Event listener submit ajouté');
                }
            } else {
                console.warn('⚠️ Formulaire #message-form non trouvé!');
                // Ne pas réessayer indéfiniment pour éviter les boucles
                return;
            }

            // Raccourci clavier Entrée pour envoyer
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                messageInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        if (messageForm) {
                            messageForm.dispatchEvent(new Event('submit'));
                        }
                    }
                });
            }

            // Bouton d'actualisation manuelle
            const refreshButton = document.querySelector('.action-button[title="Actualiser"]');
            if (refreshButton) {
                refreshButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('🔄 Actualisation manuelle des messages');
                    this.checkNewMessages();
                });
            }
        }, 100);
    }

    /**
     * Envoyer un message via AJAX
     */
    async sendMessage(form) {
        console.log('🚀 Début de sendMessage');
        
        const formData = new FormData(form);
        const messageInput = form.querySelector('#message-input');
        const sendButton = form.querySelector('button[type="submit"]');
        
        // Debug: Vérifier les données du formulaire
        console.log('📝 Données du formulaire:');
        for (const [key, value] of formData.entries()) {
            console.log('  ' + key + ': ' + value);
        }
        
        // Debug: Vérifier le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log('🔐 Token CSRF:', csrfToken ? csrfToken.content : 'MANQUANT!');
        
        // Vérifier que le contenu n'est pas vide
        const content = formData.get('content');
        if (!content || content.trim() === '') {
            console.error('❌ Contenu du message vide');
            alert('Veuillez saisir un message.');
            return;
        }
        
        // Vérifier que receiver_id est présent
        const receiverId = formData.get('receiver_id');
        if (!receiverId) {
            console.error('❌ receiver_id manquant');
            alert('Erreur: destinataire non spécifié.');
            return;
        }
        
        // Désactiver le formulaire pendant l'envoi
        messageInput.disabled = true;
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
        
        console.log('📡 Envoi de la requête AJAX vers /messaging/send-ajax');
        
        try {
            const response = await fetch('/messaging/send-ajax', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                }
            });
            
            console.log('📨 Réponse reçue:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok
            });
            
            if (response.ok) {
                const responseText = await response.text();
                console.log('📄 Réponse brute:', responseText);
                
                try {
                    const data = JSON.parse(responseText);
                    console.log('✅ Réponse JSON:', data);
                    
                    if (data.success && data.message) {
                        // Ajouter le message à la conversation
                        this.addMessageToConversation(data.message, true);
                        
                        // Vider le champ de saisie
                        messageInput.value = '';
                        
                        // Faire défiler vers le bas
                        this.scrollToBottom();
                        
                        console.log('✅ Message envoyé avec succès!');
                    } else {
                        console.error('❌ Réponse invalide:', data);
                        throw new Error('Réponse du serveur invalide');
                    }
                } catch (jsonError) {
                    console.error('❌ Erreur de parsing JSON:', jsonError);
                    console.error('❌ Réponse reçue:', responseText);
                    throw new Error('Réponse du serveur non valide');
                }
            } else {
                const errorText = await response.text();
                console.error('❌ Erreur HTTP:', {
                    status: response.status,
                    statusText: response.statusText,
                    body: errorText
                });
                
                // Afficher des messages d'erreur plus spécifiques
                if (response.status === 401) {
                    alert('Vous devez être connecté pour envoyer un message.');
                } else if (response.status === 403) {
                    alert('Vous n\'avez pas l\'autorisation d\'envoyer ce message.');
                } else if (response.status === 422) {
                    alert('Données du formulaire invalides.');
                } else {
                    alert(`Erreur ${response.status}: ${response.statusText}`);
                }
                
                throw new Error(`Erreur ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            console.error('❌ Erreur dans sendMessage:', error);
            alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
        } finally {
            // Réactiver le formulaire
            messageInput.disabled = false;
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
            messageInput.focus();
        }
    }

    /**
     * Ajouter un message à la conversation
     */
    addMessageToConversation(message, isSent = false) {
        const messagesList = document.getElementById('messages-list');
        if (!messagesList) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = message.sender_id == document.querySelector('[data-current-user-id]')?.dataset.currentUserId ? 'message sent' : 'message received';
        messageDiv.dataset.messageId = message.id;
        
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = message.content;
        
        const messageMeta = document.createElement('div');
        messageMeta.className = 'message-meta';
        
        const messageTime = document.createElement('span');
        messageTime.className = 'message-time';
        messageTime.textContent = message.formatted_time;
        
        messageMeta.appendChild(messageTime);
        
        // Ajouter le statut pour les messages envoyés
        if (message.sender_id == document.querySelector('[data-current-user-id]')?.dataset.currentUserId) {
            const messageStatus = document.createElement('span');
            messageStatus.className = 'message-status';
            const readStatus = message.read_at ? 'read' : 'unread';
            const readIcon = message.read_at ? 
                '<i class="fas fa-check-double text-blue-500" title="Lu"></i>' : 
                '<i class="fas fa-check text-gray-400" title="Envoyé"></i>';
            messageStatus.innerHTML = readIcon;
            messageMeta.appendChild(messageStatus);
        }
        
        messageBubble.appendChild(messageContent);
        messageBubble.appendChild(messageMeta);
        messageDiv.appendChild(messageBubble);
        
        // Animation d'apparition
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(20px)';
        
        messagesList.appendChild(messageDiv);
        
        // Animation
        requestAnimationFrame(() => {
            messageDiv.style.transition = 'all 0.3s ease';
            messageDiv.style.opacity = '1';
            messageDiv.style.transform = 'translateY(0)';
        });
        
        this.lastMessageId = Math.max(this.lastMessageId, message.id);
    }

    /**
     * Faire défiler vers le bas de la conversation
     */
    scrollToBottom() {
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTo({
                top: messagesContainer.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
    
    /**
     * Jouer un son de notification
     */
    playNotificationSound() {
        // Son de notification simple (optionnel)
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
            audio.volume = 0.1;
            audio.play().catch(() => {});
        } catch (e) {
            // Ignorer les erreurs de son
        }
    }

    /**
     * Démarrer le polling pour les notifications générales
     */
    startPolling() {
        // Mettre à jour le compteur toutes les 30 secondes
        this.pollInterval = setInterval(() => {
            this.updateUnreadCount();
        }, 30000);
    }

    /**
     * Configurer le polling pour une conversation spécifique
     */
    setupConversationPolling() {
        if (!this.currentConversationUserId) return;
        
        // Récupérer le dernier message ID
        const lastMessage = document.querySelector('[data-message-id]:last-child');
        if (lastMessage) {
            this.lastMessageId = parseInt(lastMessage.dataset.messageId);
        }
        
        // Polling activé pour les messages en temps réel
        this.messagesPollInterval = setInterval(() => {
            this.checkNewMessages();
        }, 2000); // Vérifier toutes les 2 secondes
        
        // Vérifier immédiatement
        this.checkNewMessages();
    }

    /**
     * Vérifier les nouveaux messages dans la conversation
     */
    async checkNewMessages() {
        if (!this.currentConversationUserId) return;
        
        try {
            const response = await fetch(`/messaging/new-messages/${this.currentConversationUserId}?last_message_id=${this.lastMessageId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                let hasNewMessages = false;
                
                data.messages.forEach(message => {
                    // Vérifier si le message n'existe pas déjà
                    if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                        this.addMessageToConversation(message, false);
                        hasNewMessages = true;
                        
                        // Marquer automatiquement comme lu si c'est un message reçu
                        if (message.receiver_id === this.currentUserId) {
                            setTimeout(() => {
                                this.markMessagesAsRead([message.id]);
                            }, 1000);
                        }
                    }
                });
                
                if (hasNewMessages) {
                    this.scrollToBottom();
                    // Mettre à jour le compteur global
                    this.updateUnreadCount();
                    
                    // Jouer un son de notification (optionnel)
                    this.playNotificationSound();
                }
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouveaux messages:', error);
        }
    }

    /**
     * Échapper le HTML pour éviter les injections XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Démarrer la surveillance du statut en ligne
     */
    startStatusPolling() {
        if (!this.currentConversationUserId) return;
        
        // Vérifier le statut immédiatement
        this.updateUserOnlineStatus();
        
        // Puis vérifier toutes les 30 secondes
        this.statusPollInterval = setInterval(() => {
            this.updateUserOnlineStatus();
        }, 30000);
    }

    /**
     * Mettre à jour le statut en ligne de l'utilisateur
     */
    async updateUserOnlineStatus() {
        if (!this.currentConversationUserId) return;
        
        try {
            const response = await fetch(`/messaging/user-status/${this.currentConversationUserId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateOnlineStatusDisplay(data);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération du statut:', error);
        }
    }

    /**
     * Mettre à jour l'affichage du statut en ligne
     */
    updateOnlineStatusDisplay(statusData) {
        const onlineIndicator = document.querySelector('.online-indicator');
        const statusText = document.getElementById('user-online-status');
        
        if (onlineIndicator) {
            onlineIndicator.className = `online-indicator ${statusData.is_online ? 'online' : 'offline'}`;
        }
        
        if (statusText) {
            const icon = statusData.is_online ? 
                '<i class="fas fa-circle text-green-500 mr-1"></i>' : 
                '<i class="fas fa-circle text-gray-400 mr-1"></i>';
            statusText.innerHTML = icon + statusData.status_text;
        }
    }

    /**
     * Marquer les messages comme lus
     */
    async markMessagesAsRead(messageIds) {
        if (!messageIds || messageIds.length === 0) return;
        
        try {
            const response = await fetch('/messaging/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    message_ids: messageIds
                })
            });
            
            if (response.ok) {
                // Mettre à jour l'affichage des messages lus
                messageIds.forEach(messageId => {
                    this.updateMessageReadStatus(messageId);
                });
            }
        } catch (error) {
            console.error('Erreur lors du marquage des messages comme lus:', error);
        }
    }

    /**
     * Mettre à jour le statut de lecture d'un message
     */
    updateMessageReadStatus(messageId) {
        const messageStatus = document.querySelector(`[data-message-id="${messageId}"] .message-status i`);
        if (messageStatus && messageStatus.classList.contains('fa-check')) {
            messageStatus.className = 'fas fa-check-double text-blue-500';
            messageStatus.title = 'Lu';
        }
    }

    /**
     * Observer les messages pour détecter quand ils entrent dans la vue
     */
    setupMessageReadObserver() {
        const messageElements = document.querySelectorAll('.message.received');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                const visibleMessageIds = [];
                
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const messageId = entry.target.dataset.messageId;
                        if (messageId) {
                            visibleMessageIds.push(parseInt(messageId));
                        }
                    }
                });
                
                if (visibleMessageIds.length > 0) {
                    this.markMessagesAsRead(visibleMessageIds);
                }
            }, {
                threshold: 0.5,
                rootMargin: '0px 0px -50px 0px'
            });
            
            messageElements.forEach(message => {
                observer.observe(message);
            });
        }
    }

    /**
     * Nettoyer les ressources
     */
    destroy() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        if (this.statusPollInterval) {
            clearInterval(this.statusPollInterval);
        }
        if (this.messagesPollInterval) {
            clearInterval(this.messagesPollInterval);
        }
        if (this.messageReadObserver) {
            this.messageReadObserver.disconnect();
        }
    }
}

// Initialiser le système de messagerie quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    window.messagingSystem = new MessagingSystem();
});

// Nettoyer lors du déchargement de la page
window.addEventListener('beforeunload', () => {
    if (window.messagingSystem) {
        window.messagingSystem.destroy();
    }
});