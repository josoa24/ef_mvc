function chargerMontantsParMois() {
  const dateDebutInput = document.getElementById("dateDebut").value;
  const dateFinInput = document.getElementById("dateFin").value;

  if (!dateDebutInput || !dateFinInput) {
    alert("Veuillez sélectionner les deux dates de début et de fin.");
    return;
  }

  const dateDebut = dateDebutInput + "-01";
  const dateFin = dateFinInput + "-31";

  const url = `/montants_disponibles?dateDebut=${encodeURIComponent(
    dateDebut
  )}&dateFin=${encodeURIComponent(dateFin)}`;

  ajax("GET", url, null, (data) => {
    const tbody = document.querySelector("#table-montants tbody");
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Aucun résultat</td></tr>`;
      return;
    }

    data.forEach((ligne) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${ligne.mois}</td>
        <td>${Number(ligne.montant_disponible).toLocaleString("fr-FR", {
          minimumFractionDigits: 2,
        })}</td>
        <td>${Number(ligne.remboursements).toLocaleString("fr-FR", {
          minimumFractionDigits: 2,
        })}</td>
        <td>${Number(ligne.prets_engages).toLocaleString("fr-FR", {
          minimumFractionDigits: 2,
        })}</td>
      `;
      tbody.appendChild(tr);
    });
  });
}
