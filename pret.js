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
  //   ajax("GET", "/taux_pret", null, (data) => {
  //     const select = document.getElementById("id_taux_pret");
  //     select.innerHTML = '<option value="">-- Sélectionner un taux --</option>';
  //     data.forEach((t) => {
  //       select.innerHTML += `<option value="${t.id_taux_pret}">${t.taux}% (${t.min_mois}-${t.max_mois} mois)</option>`;
  //     });
  //   });

  const select = document.getElementById("id_taux_pret");
  select.innerHTML = '<option value="">-- Sélectionner un taux --</option>';
  select.innerHTML += `<option value="1">Test</option>`;
}

function chargerStatuts() {
  //   ajax("GET", "/statut_pret", null, (data) => {
  //     const select = document.getElementById("id_statut_pret");
  //     select.innerHTML = '<option value="">-- Sélectionner un statut --</option>';
  //     data.forEach((s) => {
  //       select.innerHTML += `<option value="${s.id_statut_pret}">${s.libelle}</option>`;
  //     });
  //   });
  const select = document.getElementById("id_statut_pret");
  select.innerHTML = '<option value="">-- Sélectionner un statut --</option>';
  select.innerHTML += `<option value="1">Andrana</option>`;
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
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(
                p
              )})'>✏️</button>
              <button onclick='supprimerPret(${p.id_pret})'>🗑️</button>
            </td>
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
      alert("akhjd");
      chargerPrets();
    });
  } else {
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
  document.getElementById("id_statut_pret").value = "";
}

window.onload = () => {
  chargerClients();
  chargerTaux();
  chargerStatuts();
  chargerPrets();
};
