const CONFIG = {
  ANIMATION_DURATION: 300,
};

document.addEventListener("DOMContentLoaded", function () {
  setupModalEvents();
});


function setupModalEvents() {
  document.querySelectorAll(".modal").forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeModal(this.id);
      }
    });
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllModals();
    }
  });
}


function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("show");
    modal.style.display = "flex";
    // appState.modals[modalId] = true;

    const firstInput = modal.querySelector("input, select");
    if (firstInput) {
      setTimeout(() => firstInput.focus(), 100);
    }

    document.body.style.overflow = "hidden";
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("show");
    modal.classList.add("fade-out");

    setTimeout(() => {
      modal.style.display = "none";
      modal.classList.remove("fade-out"); 
      appState.modals[modalId] = false;

      if (!Object.values(appState.modals).some((isOpen) => isOpen)) {
        document.body.style.overflow = "auto";
      }

      const form = modal.querySelector("form");
      if (form) {
        form.reset();
        form
          .querySelectorAll('input[type="hidden"]')
          .forEach((input) => (input.value = ""));
        form.querySelectorAll("select").forEach((select) => {
          select.selectedIndex = 0;
        });
      }
    }, CONFIG.ANIMATION_DURATION);
  }
}


function closeAllModals() {
  Object.keys(appState.modals).forEach((modalId) => {
    if (appState.modals[modalId]) {
      closeModal(modalId);
    }
  });
}
