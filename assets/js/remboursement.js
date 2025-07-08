document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("remboursement-form");
  const select = document.getElementById("id_pret");
  const message = document.getElementById("message");

  // Charger les prêts disponibles
  fetch("/remboursements/prets")
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data)) {
        data.forEach(p => {
          const option = document.createElement("option");
          option.value = p.id_pret;
          option.textContent = `${p.nom_client} - Prêt #${p.id_pret}`;
          select.appendChild(option);
        });
      } else {
        message.textContent = "Erreur lors du chargement des prêts.";
      }
    })
    .catch(err => {
      message.textContent = "Erreur réseau.";
      console.error(err);
    });

  // Envoi du formulaire
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = new URLSearchParams(formData);

    fetch("/remboursements", {
      method: "POST",
      body: data
    })
      .then(res => res.json())
      .then(result => {
        message.textContent = result.message;
        if (result.success) form.reset();
      })
      .catch(err => {
        message.textContent = "Erreur lors de l'envoi.";
        console.error(err);
      });
  });
});
