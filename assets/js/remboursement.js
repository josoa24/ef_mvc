// remboursement.js
document.addEventListener('DOMContentLoaded', function() {
    chargerPrets();
});

async function chargerPrets() {
    try {
        console.log('Chargement des prêts...');
        
        if (typeof API_BASE_URL === 'undefined') {
            console.error('API_BASE_URL non définie');
            throw new Error('API_BASE_URL non définie. Vérifiez que url.js est chargé.');
        }
        
        const url = `${API_BASE_URL}/remboursements/clients`;
        console.log('URL complète:', url);
        
        const response = await fetch(url);
        console.log('Statut de la réponse:', response.status);
        console.log('Headers de la réponse:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Réponse d\'erreur:', errorText);
            throw new Error(`Erreur HTTP: ${response.status} - ${response.statusText}. Détails: ${errorText}`);
        }
        
        const data = await response.json();
        console.log('Prêts reçus:', data);
        
        const selectElement = document.getElementById('pret_id');
        selectElement.innerHTML = '<option value="">Sélectionnez un prêt</option>';
        
        if (data && data.length > 0) {
            data.forEach(pret => {
                const option = document.createElement('option');
                option.value = pret.id_pret;
                option.textContent = `${pret.nom_client} - Prêt #${pret.id_pret}`;
                selectElement.appendChild(option);
            });
        } else {
            selectElement.innerHTML = '<option value="">Aucun prêt en cours</option>';
        }
        
    } catch (error) {
        console.error('Erreur lors du chargement des prêts:', error);
        console.error('Stack trace:', error.stack);
        
        const selectElement = document.getElementById('pret_id');
        selectElement.innerHTML = '<option value="">Erreur lors du chargement des prêts</option>';
        
        alert(`Erreur lors du chargement des prêts: ${error.message}\n\nVérifiez la console (F12) pour plus de détails.`);
        
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = 'background-color: #ffebee; color: #c62828; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #e57373;';
        errorDiv.innerHTML = `<strong>Erreur de chargement:</strong> ${error.message}`;
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }
}

async function ajouterRemboursement() {
    const pretId = document.getElementById('pret_id').value;
    const datePaiement = document.getElementById('date_paiement').value;
    
    if (!pretId || !datePaiement) {
        alert('Veuillez sélectionner un prêt et une date de paiement');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('pret_id', pretId);
        formData.append('date_paiement', datePaiement);
        
        const response = await fetch(`${API_BASE_URL}/remboursements`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Remboursement ajouté avec succès!');
            document.getElementById('pret_id').value = '';
            document.getElementById('date_paiement').value = '';
            chargerRemboursements();
        } else {
            alert(`Erreur: ${result.error || 'Erreur inconnue'}`);
        }
        
    } catch (error) {
        console.error('Erreur lors de l\'ajout du remboursement:', error);
        alert(`Erreur lors de l'ajout du remboursement: ${error.message}`);
    }
}

async function chargerRemboursements() {
    try {
        const response = await fetch(`${API_BASE_URL}/remboursements`);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        const tbody = document.querySelector('#table-remboursements tbody');
        tbody.innerHTML = '';
        
        if (data && data.length > 0) {
            data.forEach(remboursement => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${remboursement.nom_client} - Prêt #${remboursement.pret_id}</td>
                    <td>${remboursement.date_paiement}</td>
                    <td>${remboursement.montant_attendu || 'N/A'}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3">Aucun remboursement trouvé</td></tr>';
        }
        
    } catch (error) {
        console.error('Erreur lors du chargement des remboursements:', error);
        const tbody = document.querySelector('#table-remboursements tbody');
        tbody.innerHTML = '<tr><td colspan="3">Erreur lors du chargement des remboursements</td></tr>';
    }
}