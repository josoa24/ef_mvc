async function ajouterRemboursement() {
  const pretId = document.getElementById("pret_id").value;
  const datePaiement = document.getElementById("date_paiement").value;

  if (!pretId || !datePaiement) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  try {
    const response = await fetch('/remboursements', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        pret_id: pretId,
        date_paiement: datePaiement
      })
    });

    const data = await response.json();

    if (response.ok) {
      alert('Remboursement enregistré avec succès');
      ajouterDansTable(data);
    } else {
      alert("Erreur : " + data.error);
    }

  } catch (error) {
    alert("Erreur de connexion au serveur.");
    console.error(error);
  }
}

function ajouterDansTable(remboursement) {
  const table = document.querySelector("#table-remboursements tbody");
  const row = document.createElement("tr");
  row.innerHTML = `
    <td>${remboursement.id_remboursement}</td>
    <td>${remboursement.date_paiement || '—'}</td>
    <td>${remboursement.annuite_attendue || '—'} Ar</td>
  `;
  table.appendChild(row);
}

window.addEventListener('DOMContentLoaded', async () => {
  const select = document.getElementById("pret_id");

  try {
    const res = await fetch("/remboursements/clients");
    const data = await res.json();

    data.forEach(p => {
      const option = document.createElement("option");
      option.value = p.id_pret;
      option.textContent = `${p.nom_client} - prêt #${p.id_pret}`;
      select.appendChild(option);
    });

  } catch (e) {
    alert("Erreur lors du chargement des prêts.");
    console.error(e);
  }
});

