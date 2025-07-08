
const CONFIG = {
    API_BASE_URL: '/api',
    ANIMATION_DURATION: 300
};

const appState = {
    currentSection: 'dashboard',
    modals: {
        addLoanModal: false,
        addFundsModal: false,
        addRateModal: false
    }
};

const testData = {
    clients: [
        { id: 1, nom: 'Jean Dupont', identifiant: 'JD001', email: 'jean.dupont@email.com' },
        { id: 2, nom: 'Marie Martin', identifiant: 'MM002', email: 'marie.martin@email.com' },
        { id: 3, nom: 'Pierre Leroy', identifiant: 'PL003', email: 'pierre.leroy@email.com' },
        { id: 4, nom: 'Sophie Dubois', identifiant: 'SD004', email: 'sophie.dubois@email.com' }
    ],
    typesPret: [
        { id: 1, nom: 'Immobilier' },
        { id: 2, nom: 'Personnel' },
        { id: 3, nom: 'Auto' },
        { id: 4, nom: 'Professionnel' }
    ],
    prets: [
        {
            id: 1,
            client: 'Jean Dupont',
            typePret: 'Immobilier',
            montant: 250000,
            duree: 240,
            taux: 3.5,
            statut: 'Actif',
            dateCreation: '2024-01-15'
        },
        {
            id: 2,
            client: 'Marie Martin',
            typePret: 'Personnel',
            montant: 15000,
            duree: 60,
            taux: 7.2,
            statut: 'En attente',
            dateCreation: '2024-02-10'
        }
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    loadDashboardData();
});



// Configuration des écouteurs d'événements
function setupEventListeners() {
    // Navigation sidebar
    setupSidebarNavigation();
    
    // Gestion des modals
    setupModalEvents();
    
    // Gestion des formulaires
    setupFormHandlers();
    
    // Gestion responsive
    setupResponsiveHandlers();
    
    // Gestion des notifications
    setupNotificationHandlers();
}

// Navigation sidebar
function setupSidebarNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les liens
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Ajouter la classe active au lien cliqué
            this.parentElement.classList.add('active');
            
            // Obtenir la section cible
            const targetSection = this.getAttribute('href').substring(1);
            
            // Charger le contenu de la section
            loadSectionContent(targetSection);
        });
    });
}

// Gestion des modals
function setupModalEvents() {
    // Fermer les modals en cliquant sur l'overlay
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Fermer les modals avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

// Fonctions de gestion des modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
        appState.modals[modalId] = true;
        
        // Focus sur le premier input
        const firstInput = modal.querySelector('input, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
        
        // Désactiver le scroll du body
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            appState.modals[modalId] = false;
            
            // Réactiver le scroll du body si aucun modal n'est ouvert
            if (!Object.values(appState.modals).some(isOpen => isOpen)) {
                document.body.style.overflow = 'auto';
            }
        }, CONFIG.ANIMATION_DURATION);
        
        // Réinitialiser le formulaire
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

function closeAllModals() {
    Object.keys(appState.modals).forEach(modalId => {
        if (appState.modals[modalId]) {
            closeModal(modalId);
        }
    });
}

// Gestion des formulaires
function setupFormHandlers() {
    // Formulaire nouveau prêt
    setupLoanForm();
    
    // Formulaire ajout de fonds
    setupFundsForm();
    
    // Formulaire configuration taux
    setupRateForm();
}

function setupLoanForm() {
    const loanForm = document.querySelector('#addLoanModal form');
    if (loanForm) {
        loanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const loanData = {
                client_id: document.getElementById('clientSelect').value,
                type_pret_id: document.getElementById('loanTypeSelect').value,
                mode_remboursement: document.getElementById('repaymentMode').value,
                montant: document.getElementById('loanAmount').value
            };
            
            if (validateLoanForm(loanData)) {
                submitLoanForm(loanData);
            }
        });
    }
}

function setupFundsForm() {
    const fundsForm = document.querySelector('#addFundsModal form');
    if (fundsForm) {
        fundsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fundsData = {
                client_id: document.getElementById('clientFundsSelect').value,
                montant: document.getElementById('fundsAmount').value
            };
            
            if (validateFundsForm(fundsData)) {
                submitFundsForm(fundsData);
            }
        });
    }
}

function setupRateForm() {
    const rateForm = document.querySelector('#addRateModal form');
    if (rateForm) {
        rateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rateData = {
                type_pret: document.getElementById('loanTypeInput').value,
                min_mois: document.getElementById('minMonths').value,
                max_mois: document.getElementById('maxMonths').value,
                taux: document.getElementById('interestRate').value
            };
            
            if (validateRateForm(rateData)) {
                submitRateForm(rateData);
            }
        });
    }
}

// Validation des formulaires
function validateLoanForm(data) {
    const errors = [];
    
    if (!data.client_id) errors.push('Veuillez sélectionner un client');
    if (!data.type_pret_id) errors.push('Veuillez sélectionner un type de prêt');
    if (!data.mode_remboursement) errors.push('Veuillez sélectionner un mode de remboursement');
    if (!data.montant || data.montant <= 0) errors.push('Veuillez entrer un montant valide');
    
    if (errors.length > 0) {
        showNotification('Erreur de validation', errors.join('<br>'), 'error');
        return false;
    }
    
    return true;
}

function validateFundsForm(data) {
    const errors = [];
    
    if (!data.client_id) errors.push('Veuillez sélectionner un client');
    if (!data.montant || data.montant <= 0) errors.push('Veuillez entrer un montant valide');
    
    if (errors.length > 0) {
        showNotification('Erreur de validation', errors.join('<br>'), 'error');
        return false;
    }
    
    return true;
}

function validateRateForm(data) {
    const errors = [];
    
    if (!data.type_pret.trim()) errors.push('Veuillez entrer un type de prêt');
    if (!data.min_mois || data.min_mois <= 0) errors.push('Veuillez entrer un nombre de mois minimum valide');
    if (!data.max_mois || data.max_mois <= 0) errors.push('Veuillez entrer un nombre de mois maximum valide');
    if (!data.taux || data.taux <= 0) errors.push('Veuillez entrer un taux valide');
    
    if (data.min_mois && data.max_mois && parseInt(data.min_mois) >= parseInt(data.max_mois)) {
        errors.push('Le nombre de mois minimum doit être inférieur au maximum');
    }
    
    if (errors.length > 0) {
        showNotification('Erreur de validation', errors.join('<br>'), 'error');
        return false;
    }
    
    return true;
}

// Soumission des formulaires
function submitLoanForm(data) {
    showLoading(true);
    
    // Simulation d'appel API
    setTimeout(() => {
        console.log('Données du prêt:', data);
        showNotification('Succès', 'Prêt créé avec succès', 'success');
        closeModal('addLoanModal');
        refreshDashboard();
        showLoading(false);
    }, 1000);
}

function submitFundsForm(data) {
    showLoading(true);
    
    // Simulation d'appel API
    setTimeout(() => {
        console.log('Données des fonds:', data);
        showNotification('Succès', 'Fonds ajoutés avec succès', 'success');
        closeModal('addFundsModal');
        refreshDashboard();
        showLoading(false);
    }, 1000);
}

function submitRateForm(data) {
    showLoading(true);
    
    // Simulation d'appel API
    setTimeout(() => {
        console.log('Données du taux:', data);
        showNotification('Succès', 'Taux configuré avec succès', 'success');
        closeModal('addRateModal');
        refreshDashboard();
        showLoading(false);
    }, 1000);
}

// Gestion des notifications
function setupNotificationHandlers() {
    // Créer le container de notifications s'il n'existe pas
    if (!document.querySelector('.notification-container')) {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
}

function showNotification(title, message, type = 'info') {
    const container = document.querySelector('.notification-container');
    const notification = document.createElement('div');
    
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-header">
            <strong>${title}</strong>
            <button class="notification-close">&times;</button>
        </div>
        <div class="notification-body">${message}</div>
    `;
    
    container.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Fermeture automatique
    setTimeout(() => {
        removeNotification(notification);
    }, 5000);
    
    // Fermeture manuelle
    notification.querySelector('.notification-close').addEventListener('click', () => {
        removeNotification(notification);
    });
}

function removeNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// Gestion du loading
function showLoading(show) {
    let loader = document.querySelector('.loading-overlay');
    
    if (show) {
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'loading-overlay';
            loader.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Chargement...</span>
                </div>
            `;
            document.body.appendChild(loader);
        }
        loader.style.display = 'flex';
    } else {
        if (loader) {
            loader.style.display = 'none';
        }
    }
}

// Chargement des données du dashboard
function loadDashboardData() {
    // Simulation de chargement des données
    updateDashboardStats();
    updateRecentLoans();
}

function updateDashboardStats() {
    // Mise à jour des cartes de statistiques
    const stats = {
        activeLoans: 156,
        totalAmount: 2450000,
        activeClients: 89,
        availableFunds: 890000
    };
    
    // Mise à jour avec animation
    animateCounters(stats);
}

function animateCounters(stats) {
    const counters = document.querySelectorAll('.card-value');
    const values = [stats.activeLoans, stats.totalAmount, stats.activeClients, stats.availableFunds];
    
    counters.forEach((counter, index) => {
        const target = values[index];
        const current = parseInt(counter.textContent.replace(/[^\d]/g, '')) || 0;
        
        animateValue(counter, current, target, 1000, index === 1 || index === 3);
    });
}

function animateValue(element, start, end, duration, isCurrency = false) {
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * progress);
        
        if (isCurrency) {
            element.textContent = new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0
            }).format(current);
        } else {
            element.textContent = new Intl.NumberFormat('fr-FR').format(current);
        }
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

// Gestion responsive
function setupResponsiveHandlers() {
    // Toggle sidebar sur mobile
    const menuToggle = document.createElement('button');
    menuToggle.className = 'mobile-menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    
    const navbar = document.querySelector('.top-navbar');
    navbar.insertBefore(menuToggle, navbar.firstChild);
    
    menuToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('open');
    });
    
    // Fermer la sidebar en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

// Autres fonctions utilitaires
function loadSectionContent(section) {
    appState.currentSection = section;
    console.log(`Chargement de la section: ${section}`);
    
    // Ici vous pourriez charger le contenu dynamiquement
    // Par exemple, faire un appel AJAX vers section.php
}

function refreshDashboard() {
    loadDashboardData();
}

function checkAuthStatus() {
    // Vérification de l'authentification
    // Rediriger vers login si nécessaire
}

function initializeTooltips() {
    // Initialiser les tooltips pour les éléments avec data-tooltip
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    // Implémentation des tooltips
}

function loadUserPreferences() {
    // Charger les préférences utilisateur depuis localStorage
    const preferences = localStorage.getItem('bankflow_preferences');
    if (preferences) {
        const prefs = JSON.parse(preferences);
        // Appliquer les préférences
    }
}

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
    showNotification('Erreur', 'Une erreur est survenue. Veuillez rafraîchir la page.', 'error');
});

// Export des fonctions principales pour usage externe
window.BankFlow = {
    openModal,
    closeModal,
    showNotification,
    showLoading,
    refreshDashboard
};