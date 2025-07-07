

function chargerFonds() {
  ajax("GET", "/ajout_fonds", null, (data) => {
    const tbody = document.querySelector("#table-fonds tbody");
    tbody.innerHTML = "";

    data.forEach((f) => {
      const dateObj = new Date(f.date_ajout);

      const options = { day: "numeric", month: "long", year: "numeric" };
      const dateFormatee = dateObj.toLocaleDateString("fr-FR", options);

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${f.nom_client}</td>
        <td>${f.montant}</td>
        <td>${dateFormatee}</td> <!-- Date formatée -->
      `;
      tbody.appendChild(tr);
    });
  });
}

function remplirFormulaire(f) {
  document.getElementById("id_ajout_fonds").value = f.id_ajout_fonds;
  document.getElementById("id_etablissement_financier").value =
    f.id_etablissement_financier;
  document.getElementById("id_client").value = f.id_client;
  document.getElementById("montant").value = f.montant;
  document.getElementById("date_ajout").value = f.date_ajout.replace(" ", "T");
}

function ajouterOuModifier() {
  const id = document.getElementById("id_ajout_fonds").value;
  const ef = document.getElementById("id_etablissement_financier").value;
  const client = document.getElementById("id_client").value;
  const montant = document.getElementById("montant").value;
  const date = document.getElementById("date_ajout").value;

  const data = `id_etablissement_financier=${ef}&id_client=${client}&montant=${montant}&date_ajout=${encodeURIComponent(
    date
  )}`;

  if (id) {
    ajax("PUT", `/ajout_fonds/${id}`, data, () => {
      resetForm();
      chargerFonds();
    });
  } else {
    ajax("POST", "/ajout_fonds", data, () => {
      resetForm();
      chargerFonds();
    });
  }
}

function supprimerFonds(id) {
  if (confirm("Supprimer cet ajout de fonds ?")) {
    ajax("DELETE", `/ajout_fonds/${id}`, null, () => {
      chargerFonds();
    });
  }
}

function resetForm() {
  document.getElementById("id_ajout_fonds").value = "";
  document.getElementById("id_etablissement_financier").value = "";
  document.getElementById("id_client").value = "";
  document.getElementById("montant").value = "";
  document.getElementById("date_ajout").value = "";
}

function chargerSelects() {
  ajax("GET", "/etablissements", null, (data) => {
    const select = document.getElementById("id_etablissement_financier");
    select.innerHTML = '<option value="">-- Établissement --</option>';
    data.forEach((e) => {
      select.innerHTML += `<option value="${e.id_etablissement_financier}">${e.nom_etablissement_financier}</option>`;
    });
  });

  ajax("GET", "/clients", null, (data) => {
    const select = document.getElementById("id_client");
    select.innerHTML = '<option value="">-- Client --</option>';
    data.forEach((c) => {
      select.innerHTML += `<option value="${c.id_client}">${c.nom_client}</option>`;
    });
  });
}

window.onload = () => {
  chargerSelects();
  chargerFonds();
};
