document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let chartInstance = null;
    const apiUrl = '/interets'; 

    // Initialisation
    init();

    function init() {
        // Charger les données au démarrage
        loadInterestData();

        // Écouteur pour le bouton de filtre
        document.getElementById('filter-btn').addEventListener('click', function() {
            loadInterestData();
        });
    }

    // Fonction principale de chargement des données
    async function loadInterestData() {
        showLoadingState();
        
        try {
            const data = await fetchInterestData();
            if (data && data.length > 0) {
                renderTable(data);
                renderChart(data);
            } else {
                showNoDataState();
            }
        } catch (error) {
            console.error('Erreur:', error);
            showErrorState(error);
        }
    }

    // Récupération des données depuis l'API PHP
    async function fetchInterestData() {
        const response = await fetch(apiUrl);
        const rawResponse = await response.text();
        console.log("Réponse brute:", rawResponse); // Affiche la réponse complète
        try {
            return JSON.parse(rawResponse).data;
        } catch (e) {
            throw new Error(`Le serveur a retourné: ${rawResponse.substring(0, 100)}...`);
        }
    }

    // Affichage du tableau
    function renderTable(data) {
        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = '';

        let totalInterets = 0;
        const moisNoms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                         'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        data.forEach(item => {
            const [mois, annee] = item.mois_annee.split('/');
            const nomMois = moisNoms[parseInt(mois) - 1];
            
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td>${nomMois} ${annee}</td>
                <td>${formatCurrency(item.interets)}</td>
            `;
            
            tableBody.appendChild(row);
            totalInterets += parseFloat(item.interets) || 0;
        });

        // Ligne de total
        const totalRow = document.createElement('tr');
        totalRow.className = 'total-row';
        totalRow.innerHTML = `
            <td><strong>Total</strong></td>
            <td><strong>${formatCurrency(totalInterets)}</strong></td>
        `;
        tableBody.appendChild(totalRow);
    }

    // Affichage du graphique
    function renderChart(data) {
        const ctx = document.getElementById('interests-chart').getContext('2d');
        
        // Détruire l'ancien graphique si existe
        if (chartInstance) {
            chartInstance.destroy();
        }
        
        // Préparation des données
        const labels = data.map(item => {
            const [mois, annee] = item.mois_annee.split('/');
            const moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 
                             'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            return `${moisNoms[parseInt(mois)-1]} ${annee}`;
        });
        
        const values = data.map(item => item.interets);

        // Création du graphique
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Intérêts mensuels (€)',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return formatCurrency(context.raw);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }

    // Fonctions utilitaires
    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2
        }).format(amount);
    }

    function showLoadingState() {
        document.getElementById('table-body').innerHTML = `
            <tr>
                <td colspan="2" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i> Chargement en cours...
                </td>
            </tr>`;
    }

    function showNoDataState() {
        document.getElementById('table-body').innerHTML = `
            <tr>
                <td colspan="2">Aucune donnée disponible</td>
            </tr>`;
        
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }

    function showErrorState(error) {
        document.getElementById('table-body').innerHTML = `
            <tr class="error-row">
                <td colspan="2">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${error.message || 'Erreur lors du chargement'}
                </td>
            </tr>`;
        
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }
});