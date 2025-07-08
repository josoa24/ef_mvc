function chargerClients() {
  ajax("GET", "/clients", null, (data) => {
    const select = document.getElementById("id_client");
    select.innerHTML = '<option value="">-- Sélectionner un client --</option>';
    data.forEach((c) => {
      select.innerHTML += `<option value="${c.id_client}">${c.nom_client}</option>`;
    });
  });
}

function chargerTaux() {
  ajax("GET", "/getAllWidthType", null, (data) => {
    const select = document.getElementById("id_taux_pret");
    select.innerHTML = '<option value="">-- Sélectionner un taux --</option>';
    data.forEach((t) => {
      select.innerHTML += `<option value="${t.id_taux_pret}">${t.nom_type_pret}</option>`;
    });
  });
}

function chargerStatuts() {
  document.getElementById("id_statut_pret").value = "1";
}

function chargerPrets() {
  ajax("GET", "/pret", null, (data) => {
    const tbody = document.querySelector("#table-prets tbody");
    tbody.innerHTML = "";

    data.forEach((p) => {
      const tr = document.createElement("tr");
      const datePret = new Date(p.date_pret).toLocaleDateString("fr-FR");
      tr.innerHTML = `
        <td>${p.nom_client}</td>
        <td>${p.taux}</td>
        <td>${p.montant}</td>
        <td>${p.duree_mois}</td>
        <td>${datePret}</td>
        <td>${p.statut}</td>
      `;
      tbody.appendChild(tr);
    });
  });
}

function remplirFormulaire(p) {
  document.getElementById("id_pret").value = p.id_pret;
  document.getElementById("id_client").value = p.id_client;
  document.getElementById("id_taux_pret").value = p.id_taux_pret;
  document.getElementById("montant").value = p.montant;
  document.getElementById("duree_mois").value = p.duree_mois;
  document.getElementById("date_pret").value = p.date_pret;
  document.getElementById("id_statut_pret").value = p.id_statut_pret;
}

function ajouterOuModifier() {
  const id = document.getElementById("id_pret").value;
  const client = document.getElementById("id_client").value;
  const taux = document.getElementById("id_taux_pret").value;
  const montant = document.getElementById("montant").value;
  const duree = document.getElementById("duree_mois").value;
  const datePret = document.getElementById("date_pret").value;
  const statut = document.getElementById("id_statut_pret").value;

  if (!client || !taux || !montant || !duree || !datePret || !statut) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  const data = `id_client=${client}&id_taux_pret=${taux}&montant=${montant}&duree_mois=${duree}&date_pret=${datePret}&id_statut_pret=${statut}`;

  if (id) {
    ajax("PUT", `/pret/${id}`, data, () => {
      resetForm();
      chargerPrets();
    });
  } else {
    envoyerPret(duree, montant, client, taux);
    ajax("POST", "/pret", data, () => {
      resetForm();
      chargerPrets();
    });
  }
}

function supprimerPret(id) {
  if (confirm("Supprimer ce prêt ?")) {
    ajax("DELETE", `/pret/${id}`, null, () => {
      chargerPrets();
    });
  }
}

function resetForm() {
  document.getElementById("id_pret").value = "";
  document.getElementById("id_client").value = "";
  document.getElementById("id_taux_pret").value = "";
  document.getElementById("montant").value = "";
  document.getElementById("duree_mois").value = "";
  document.getElementById("date_pret").value = "";
  document.getElementById("id_statut_pret").value = "1";
}

function envoyerPret(nombreRemboursements, montant, id_client, id_type_pret) {
  const formData = new FormData();
  formData.append("montant", montant);
  formData.append("mois", nombreRemboursements);
  formData.append("id_client", id_client);
  formData.append("id_type_pret", id_type_pret);

  fetch(apiBase + "/pret/creerpdf", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.blob())
    .then((blob) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "pret.pdf";
      a.click();
    })
    .catch((err) => console.error("Erreur PDF :", err));
}

function setupTauxAuto() {
  const dureeInput = document.getElementById("duree_mois");
  const tauxSelect = document.getElementById("id_taux_pret");
  const montantInput = document.getElementById("montant");
  const infoDiv = document.getElementById("info_calcul");

  async function fetchTaux() {
    const duree = parseInt(dureeInput.value, 10);
    const idTypePret = tauxSelect.value;
    const montant = parseFloat(montantInput.value);

    if (!idTypePret || !duree || duree <= 0 || !montant || montant <= 0) {
      infoDiv.textContent = "";
      return;
    }

    try {
      const response = await fetch(
        apiBase + `/taux_pret/duree/${idTypePret}/${duree}`
      );
      if (!response.ok)
        throw new Error("Erreur lors de la récupération du taux");
      const data = await response.json();

      if (data) {
        const tauxAnnuel = data.taux;
        const interetsAnnuel = (montant * tauxAnnuel) / 100;
        const interetsTotal = interetsAnnuel * (duree / 12);
        const montantTotal = montant + interetsTotal;
        const paiementMensuel = montantTotal / duree;

        infoDiv.innerHTML = `
          Taux annuel applicable : <strong>${tauxAnnuel}%</strong> (durée entre ${
          data.min_mois
        } et ${data.max_mois} mois)<br>
          Montant total à rembourser : <strong>${montantTotal.toFixed(
            2
          )} Ar</strong><br>
          Paiement mensuel estimé : <strong>${paiementMensuel.toFixed(
            2
          )} Ar</strong>
        `;
      } else {
        infoDiv.textContent = "Aucun taux trouvé pour cette durée.";
      }
    } catch (error) {
      infoDiv.textContent = "Erreur serveur, impossible de récupérer le taux.";
      console.error(error);
    }
  }

  dureeInput.addEventListener("input", fetchTaux);
  tauxSelect.addEventListener("change", fetchTaux);
  montantInput.addEventListener("input", fetchTaux);
}

window.onload = () => {
  chargerClients();
  chargerTaux();
  chargerStatuts();
  chargerPrets();
  setupTauxAuto();
};
