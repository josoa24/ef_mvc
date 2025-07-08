document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");

  togglePassword.addEventListener("click", function () {
    const type =
      passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.innerHTML =
      type === "password"
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
  });

  loginForm.addEventListener("submit", function (e) {

    const username = document.getElementById("username").value.trim();
    const password = passwordInput.value;
    const rememberMe = document.querySelector('input[name="remember"]').checked;

    if (!username || !password) {
      showAlert("Veuillez remplir tous les champs", "error");
      return;
    }

    simulateLogin(username, password, rememberMe);
  });

  function simulateLogin(username, password, remember) {
   
    console.log("Tentative de connexion avec:", {
      username,
      password,
      remember,
    });

    const submitBtn = loginForm.querySelector('button[type="submit"]');
    submitBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Connexion en cours...';
    submitBtn.disabled = true;

    setTimeout(() => {
      if (username === "admin123" && password === "123") {
        showAlert("Connexion réussie! Redirection en cours...", "success");
        setTimeout(() => {
          window.location.href = apiBase + "accueil.html";
        }, 15);
      } else {
        showAlert("Identifiants incorrects", "error");
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Se connecter';
        submitBtn.disabled = false;
      }
    }, 1500);
  }

  function showAlert(message, type) {
    const oldAlert = document.querySelector(".alert-message");
    if (oldAlert) oldAlert.remove();

    const alert = document.createElement("div");
    alert.className = `alert-message alert-${type}`;
    alert.innerHTML = `
            <i class="fas fa-${
              type === "success" ? "check-circle" : "exclamation-circle"
            }"></i>
            ${message}
        `;

    loginForm.prepend(alert);

    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  }
});
