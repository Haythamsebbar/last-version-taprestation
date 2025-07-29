/**
 * Admin User Details Page JavaScript
 * Handles user management interactions with enhanced UX
 */

class UserDetailsManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.userId = null;
        this.isLoading = false;
        this.init();
    }

    init() {
        // Get user ID from URL or data attribute
        const pathParts = window.location.pathname.split('/');
        this.userId = pathParts[pathParts.length - 1];
        
        // Vérifier que l'ID utilisateur est valide
        if (!this.userId || isNaN(this.userId)) {
            console.warn('ID utilisateur invalide détecté:', this.userId);
            return;
        }
        
        // Initialize event listeners
        this.bindEvents();
        
        // Initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            this.initTooltips();
        } else {
            console.warn('Bootstrap non disponible - tooltips désactivés');
            // Fallback: attendre que Bootstrap soit chargé
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    this.initTooltips();
                }
            }, 1000);
        }
        
        // Initialize animations
        this.initAnimations();
    }

    bindEvents() {
        // Block/Unblock user button
        const toggleButton = document.getElementById('toggleBlockBtn');
        if (toggleButton) {
            toggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleBlockUser();
            });
        } else {
            console.warn('Bouton de blocage non trouvé');
        }

        // Delete user button
        const deleteButton = document.getElementById('deleteUserBtn');
        if (deleteButton) {
            deleteButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.deleteUser();
            });
        } else {
            console.warn('Bouton de suppression non trouvé');
        }

        // Refresh button
        const refreshButton = document.getElementById('refreshBtn');
        if (refreshButton) {
            refreshButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.refreshUserData();
            });
        } else {
            console.warn('Bouton de rafraîchissement non trouvé');
        }
    }

    initTooltips() {
        try {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            if (tooltipTriggerList.length > 0) {
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                console.log('Tooltips initialisés:', tooltipTriggerList.length);
            }
        } catch (error) {
            console.error('Erreur lors de l\'initialisation des tooltips:', error);
        }
    }

    initAnimations() {
        // Animate cards on load
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    async toggleBlockUser() {
        if (this.isLoading) return;
        
        const button = document.getElementById('toggleBlockBtn');
        const isBlocked = button.textContent.trim().includes('Débloquer');
        const action = isBlocked ? 'unblock' : 'block';
        
        // Show confirmation dialog
        const confirmMessage = isBlocked 
            ? 'Êtes-vous sûr de vouloir débloquer cet utilisateur ?'
            : 'Êtes-vous sûr de vouloir bloquer cet utilisateur ?';
            
        if (!await this.showConfirmDialog(confirmMessage)) {
            return;
        }

        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const response = await fetch(`/admin/users/${this.userId}/${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showToast('success', data.message || `Utilisateur ${action === 'block' ? 'bloqué' : 'débloqué'} avec succès`);
                
                // Update UI
                this.updateBlockButton(!isBlocked);
                this.updateUserStatus(!isBlocked);
                
                // Add visual feedback
                this.addSuccessAnimation(button);
            } else {
                throw new Error(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Error toggling user block status:', error);
            this.showToast('error', error.message || 'Erreur lors de la modification du statut');
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async deleteUser() {
        if (this.isLoading) return;
        
        // Show confirmation dialog
        if (!await this.showConfirmDialog(
            'Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.',
            'danger'
        )) {
            return;
        }

        const button = document.getElementById('deleteUserBtn');
        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const response = await fetch(`/admin/users/${this.userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showToast('success', data.message || 'Utilisateur supprimé avec succès');
                
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '/admin/users';
                }, 1500);
            } else {
                throw new Error(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showToast('error', error.message || 'Erreur lors de la suppression');
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async refreshUserData() {
        if (this.isLoading) return;
        
        const button = document.getElementById('refreshBtn');
        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            // Simple page reload for now, could be enhanced with AJAX
            window.location.reload();
        } catch (error) {
            console.error('Error refreshing user data:', error);
            this.showToast('error', 'Erreur lors du rafraîchissement');
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    updateBlockButton(isBlocked) {
        const button = document.getElementById('toggleBlockBtn');
        if (!button) return;

        if (isBlocked) {
            button.className = 'btn btn-warning btn-sm';
            button.innerHTML = '<i class="bi bi-unlock"></i> Débloquer';
        } else {
            button.className = 'btn btn-danger btn-sm';
            button.innerHTML = '<i class="bi bi-lock"></i> Bloquer';
        }
    }

    updateUserStatus(isBlocked) {
        const statusBadge = document.querySelector('.badge');
        if (!statusBadge) return;

        if (isBlocked) {
            statusBadge.className = 'badge bg-danger';
            statusBadge.textContent = 'Bloqué';
        } else {
            statusBadge.className = 'badge bg-success';
            statusBadge.textContent = 'Actif';
        }
    }

    setButtonLoading(button, loading) {
        if (!button) return;

        if (loading) {
            button.disabled = true;
            button.classList.add('loading');
            const icon = button.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-arrow-clockwise';
            }
        } else {
            button.disabled = false;
            button.classList.remove('loading');
        }
    }

    addSuccessAnimation(element) {
        element.style.transform = 'scale(1.05)';
        element.style.transition = 'transform 0.2s ease';
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 200);
    }

    async showConfirmDialog(message, type = 'warning') {
        // Use native confirm for now, could be enhanced with a custom modal
        return new Promise((resolve) => {
            // Create custom modal if Bootstrap is available
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                this.showBootstrapConfirm(message, type, resolve);
            } else {
                resolve(confirm(message));
            }
        });
    }

    showBootstrapConfirm(message, type, callback) {
        try {
            // Create modal HTML
            const modalHtml = `
                <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle text-${type === 'danger' ? 'danger' : 'warning'}"></i>
                                    Confirmation
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">${message}</p>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="button" class="btn btn-${type === 'danger' ? 'danger' : 'warning'}" id="confirmBtn">Confirmer</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('confirmModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            const modalElement = document.getElementById('confirmModal');
            const modal = new bootstrap.Modal(modalElement);
            const confirmBtn = document.getElementById('confirmBtn');
            
            let hasResponded = false;
            
            confirmBtn.addEventListener('click', () => {
                if (!hasResponded) {
                    hasResponded = true;
                    modal.hide();
                    callback(true);
                }
            });
            
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
                if (!hasResponded) {
                    hasResponded = true;
                    callback(false);
                }
            });
            
            modal.show();
        } catch (error) {
            console.error('Erreur lors de la création de la modale:', error);
            // Fallback vers confirm natif
            callback(confirm(message));
        }
    }

    showToast(type, message) {
        // Create toast container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${this.getToastIcon(type)} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close btn-close-sm ms-2" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'exclamation-triangle-fill',
            warning: 'exclamation-circle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || 'info-circle-fill';
    }

    // Utility method to handle keyboard shortcuts
    handleKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + R for refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.refreshUserData();
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                const modal = document.querySelector('.modal.show');
                if (modal && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }
            }
        });
    }

    // Method to handle offline/online status
    handleConnectionStatus() {
        window.addEventListener('online', () => {
            this.showToast('success', 'Connexion rétablie');
        });

        window.addEventListener('offline', () => {
            this.showToast('warning', 'Connexion perdue');
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const userManager = new UserDetailsManager();
    userManager.handleKeyboardShortcuts();
    userManager.handleConnectionStatus();
});

// Global functions for backward compatibility
function toggleBlockUser() {
    const userManager = new UserDetailsManager();
    userManager.toggleBlockUser();
}

function deleteUser() {
    const userManager = new UserDetailsManager();
    userManager.deleteUser();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UserDetailsManager;
}