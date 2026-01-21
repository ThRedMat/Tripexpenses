// script.js
// Variables globales
const form = document.getElementById("userInfoForm");
const inputs = form.querySelectorAll(".form-input");
const selects = form.querySelectorAll("select");
const readOnlyButtons = document.getElementById("readOnlyButtons");
const editButtons = document.getElementById("editButtons");
const editBtn = document.getElementById("editBtn");
const cancelBtn = document.getElementById("cancelBtn");
const saveBtn = document.getElementById("saveBtn");
const deleteBtn = document.getElementById("deleteBtn");

// Variable pour suivre le mode mot de passe
let isPasswordEditMode = false;

// Stockage des valeurs originales
let originalValues = {};

// Animation d'apparition
document.addEventListener("DOMContentLoaded", function () {
  const cards = document.querySelectorAll(".form-container, .form-group");
  cards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(30px)";
    setTimeout(() => {
      card.style.transition = "all 0.6s cubic-bezier(0.4, 0, 0.2, 1)";
      card.style.opacity = "1";
      card.style.transform = "translateY(0)";
    }, index * 100);
  });

  // Initialiser les valeurs originales au chargement
  saveOriginalValues();

  // Initialiser le champ mot de passe avec des points
  document.getElementById("currentPassword").value = "••••••••";
});

// Sauvegarder les valeurs originales
function saveOriginalValues() {
  originalValues = {};
  inputs.forEach((input) => {
    if (
      input.id !== "currentPassword" &&
      input.id !== "newPassword" &&
      input.id !== "confirmPassword"
    ) {
      originalValues[input.id] = input.value;
    }
  });
}

// Restaurer les valeurs originales
function restoreOriginalValues() {
  Object.keys(originalValues).forEach((id) => {
    const element = document.getElementById(id);
    if (element) {
      element.value = originalValues[id];
    }
  });
  // Restaurer le champ mot de passe
  document.getElementById("currentPassword").value = "••••••••";
}

function enableEditMode() {
  saveOriginalValues();

  inputs.forEach((input) => {
    if (
      input.id !== "currentPassword" &&
      input.id !== "newPassword" &&
      input.id !== "confirmPassword" &&
      input.id !== "currencyDisplay"
    ) {
      input.removeAttribute("readonly");
    }
  });

  selects.forEach((select) => {
    select.removeAttribute("disabled");
  });

  // Masquer l'input de devise en lecture seule et afficher le select
  const currencyDisplay = document.getElementById("currencyDisplay");
  const currencySelect = document.getElementById("currency");
  if (currencyDisplay && currencySelect) {
    currencyDisplay.style.display = "none";
    currencySelect.style.display = "block";
  }

  readOnlyButtons.classList.add("hidden");
  editButtons.classList.remove("hidden");

  const currentPassword = document.getElementById("currentPassword");
  currentPassword.addEventListener("focus", togglePasswordEdit);
}

function enableReadOnlyMode() {
  inputs.forEach((input) => {
    input.setAttribute("readonly", true);
  });

  selects.forEach((select) => {
    select.setAttribute("disabled", true);
  });

  // Afficher l'input de devise en lecture seule et masquer le select
  const currencyDisplay = document.getElementById("currencyDisplay");
  const currencySelect = document.getElementById("currency");
  if (currencyDisplay && currencySelect) {
    // Mettre à jour l'affichage avec la valeur sélectionnée
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    currencyDisplay.value = selectedOption.text;

    currencyDisplay.style.display = "block";
    currencySelect.style.display = "none";
  }

  togglePasswordEditOff();

  const currentPassword = document.getElementById("currentPassword");
  currentPassword.removeEventListener("focus", togglePasswordEdit);

  readOnlyButtons.classList.remove("hidden");
  editButtons.classList.add("hidden");
  isPasswordEditMode = false;
}

// Fonction pour activer le mode édition du mot de passe
function togglePasswordEdit() {
  const currentField = document.getElementById("currentPassword");
  const editFields = document.querySelectorAll(".password-edit-fields");

  if (currentField.readOnly) {
    // Activer le mode édition mot de passe
    currentField.readOnly = false;
    currentField.value = "";
    currentField.placeholder = "Mot de passe actuel";
    editFields.forEach((field) => (field.style.display = "block"));
    currentField.focus();
    isPasswordEditMode = true;
  }
}

// Fonction pour désactiver le mode édition du mot de passe
function togglePasswordEditOff() {
  const currentField = document.getElementById("currentPassword");
  const editFields = document.querySelectorAll(".password-edit-fields");

  currentField.readOnly = true;
  currentField.value = "••••••••";
  currentField.placeholder = " ";
  editFields.forEach((field) => (field.style.display = "none"));
  document.getElementById("newPassword").value = "";
  document.getElementById("confirmPassword").value = "";
  isPasswordEditMode = false;
}

// Gestionnaire du formulaire principal
form.addEventListener("submit", function (e) {
  e.preventDefault();

  // Si on est en mode édition de mot de passe
  if (isPasswordEditMode) {
    handlePasswordChange();
    return;
  }

  // Sinon, c'est la logique normale du formulaire (infos générales)
  handleGeneralInfoUpdate();
});

// Gestion du changement de mot de passe
function handlePasswordChange() {
  const currentPassword = document.getElementById("currentPassword").value;
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  // Validations côté client
  if (!currentPassword || !newPassword || !confirmPassword) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  if (newPassword !== confirmPassword) {
    alert("Les mots de passe ne correspondent pas.");
    return;
  }

  if (newPassword.length < 8) {
    //alert('Le nouveau mot de passe doit contenir au moins 8 caractères.');
    alert(newPassword.length);
    console.log(newPassword);
    return;
  }

  // Confirmation avant modification
  if (!confirm("Êtes-vous sûr de vouloir modifier votre mot de passe ?")) {
    return;
  }

  // Animation du bouton
  saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Modification...';
  saveBtn.disabled = true;

  // Envoi de la requête au serveur
  fetch("update_password.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}`,
  })
    .then((response) => response.text())
    .then((result) => {
      if (result === "Succès") {
        saveBtn.innerHTML =
          '<i class="fas fa-check"></i> Mot de passe modifié !';
        alert("Votre mot de passe a été modifié avec succès.");

        // Remettre en mode lecture seule
        togglePasswordEditOff();

        setTimeout(() => {
          saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
          saveBtn.disabled = false;
        }, 2000);
      } else {
        saveBtn.innerHTML = '<i class="fas fa-times"></i> Erreur';
        alert("Erreur lors de la modification du mot de passe : " + result);

        setTimeout(() => {
          saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
          saveBtn.disabled = false;
        }, 2000);
      }
    })
    .catch((err) => {
      console.error("Erreur:", err);
      saveBtn.innerHTML = '<i class="fas fa-times"></i> Erreur';
      alert("Erreur de communication avec le serveur.");

      setTimeout(() => {
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        saveBtn.disabled = false;
      }, 2000);
    });
}

// Gestion de la mise à jour des informations générales
function handleGeneralInfoUpdate() {
  // Animation du bouton
  saveBtn.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
  saveBtn.disabled = true;

  // Récupérer les valeurs du formulaire
  const firstName = document.getElementById("firstName").value;
  const lastName = document.getElementById("lastName").value;
  const pseudo = document.getElementById("pseudo").value;
  const email = document.getElementById("email").value;
  const pays = document.getElementById("pays").value;
  const ville = document.getElementById("ville").value;
  const currency = document.getElementById("currency").value;

  // Envoyer les données au serveur
  fetch("update_user_info.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&pseudo=${encodeURIComponent(pseudo)}&email=${encodeURIComponent(email)}&pays=${encodeURIComponent(pays)}&ville=${encodeURIComponent(ville)}&currency=${encodeURIComponent(currency)}`,
  })
    .then((response) => response.text())
    .then((result) => {
      if (result === "Succès") {
        saveBtn.innerHTML = '<i class="fas fa-check"></i> Enregistré !';

        // Mise à jour du nom dans le profil
        const profileNameElement = document.getElementById("profileName");
        if (profileNameElement) {
          profileNameElement.textContent = pseudo;
        }

        setTimeout(() => {
          saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
          saveBtn.disabled = false;
          enableReadOnlyMode();
        }, 2000);
      } else {
        saveBtn.innerHTML = '<i class="fas fa-times"></i> Erreur';
        alert("Erreur lors de la mise à jour : " + result);

        setTimeout(() => {
          saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
          saveBtn.disabled = false;
        }, 2000);
      }
    })
    .catch((err) => {
      console.error("Erreur:", err);
      saveBtn.innerHTML = '<i class="fas fa-times"></i> Erreur';
      alert("Erreur de communication avec le serveur.");

      setTimeout(() => {
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        saveBtn.disabled = false;
      }, 2000);
    });
}

// Event listeners pour les modes
editBtn.addEventListener("click", enableEditMode);

cancelBtn.addEventListener("click", () => {
  if (confirm("Êtes-vous sûr de vouloir annuler les modifications ?")) {
    restoreOriginalValues();
    enableReadOnlyMode();
  }
});

deleteBtn.addEventListener("click", () => {
  if (
    confirm(
      "Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.",
    )
  ) {
    fetch("delete_user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "confirm=1",
    })
      .then((response) => response.text())
      .then((result) => {
        if (result === "success") {
          alert("Votre compte a été supprimé avec succès.");
          window.location.href = "../../index.html";
        } else {
          alert("Erreur lors de la suppression du compte.");
        }
      })
      .catch((err) => {
        console.error(err);
        alert("Erreur de communication avec le serveur.");
      });
  }
});

// Effets au scroll pour la navbar
window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.style.background = "rgba(255, 255, 255, 0.98)";
      navbar.style.boxShadow = "0 8px 32px rgba(0, 0, 0, 0.15)";
    } else {
      navbar.style.background = "rgba(255, 255, 255, 0.95)";
      navbar.style.boxShadow = "0 8px 32px rgba(0, 0, 0, 0.1)";
    }
  }
});

// Animation des inputs
document.querySelectorAll(".form-input").forEach((input) => {
  input.addEventListener("focus", function () {
    if (!this.hasAttribute("readonly") && !this.hasAttribute("disabled")) {
      this.parentElement.style.transform = "scale(1.02)";
      this.parentElement.style.transition = "transform 0.2s ease";
    }
  });

  input.addEventListener("blur", function () {
    this.parentElement.style.transform = "scale(1)";
  });
});

const avatarDiv = document.getElementById("profileAvatar");
const avatarInput = document.getElementById("avatarInput");

avatarDiv.addEventListener("click", () => {
  avatarInput.click();
});

avatarInput.addEventListener("change", (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      avatarDiv.innerHTML = `
                <img src="${e.target.result}" 
                     style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
            `;
    };
    reader.readAsDataURL(file);

    uploadAvatar(file);
  }
});

// Fonction pour uploader l’avatar
function uploadAvatar(file) {
  const formData = new FormData();
  formData.append("avatar", file);

  fetch("upload_avatar.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((data) => {
      console.log("Réponse serveur:", data);
      if (data === "Succès") {
        alert("Avatar mis à jour avec succès !");

        // Vérifie si le bouton supprimer n’existe pas déjà
        if (!document.getElementById("deleteAvatarBtn")) {
          const btn = document.createElement("button");
          btn.id = "deleteAvatarBtn";
          btn.className = "delete-btn";
          btn.innerHTML = '<i class="fas fa-trash"></i> Supprimer l’avatar';
          avatarDiv.insertAdjacentElement("afterend", btn);

          // Ajoute l’event listener au nouveau bouton
          btn.addEventListener("click", () => {
            if (confirm("Voulez-vous vraiment supprimer votre avatar ?")) {
              fetch("delete_avatar.php", {
                method: "POST",
              })
                .then((res) => res.text())
                .then((data) => {
                  console.log("Réponse serveur:", data);
                  location.reload(); // ou mise à jour DOM sans reload
                })
                .catch((err) => console.error("Erreur suppression:", err));
            }
          });
        }
      } else {
        alert("Erreur lors de la mise à jour de l'avatar : " + data);
      }
    })
    .catch((err) => {
      console.error("Erreur upload:", err);
      alert("Erreur de communication avec le serveur.");
    });
}

// Fonction pour supprimer l’avatar
const deleteAvatar = document.getElementById("deleteAvatarBtn");

if (deleteAvatar) {
  deleteAvatar.addEventListener("click", () => {
    if (confirm("Voulez-vous vraiment supprimer votre avatar ?")) {
      fetch("delete_avatar.php", {
        method: "POST",
      })
        .then((res) => res.text())
        .then((data) => {
          console.log("Réponse serveur:", data);
          // Recharger la page pour afficher l’icône par défaut
          location.reload();
        })
        .catch((err) => {
          console.error("Erreur suppression:", err);
        });
    }
  });
}

const villeInput = document.getElementById("ville");
const paysInput = document.getElementById("pays");
const suggestions = document.getElementById("villeSuggestions");

// Liste simple pour l'instant
const villes = [
  "Paris, France",
  "Bordeaux, France",
  "Lyon, France",
  "Marseille, France",
  "New York, USA",
  "Los Angeles, USA",
  "Toronto, Canada",
  "Vancouver, Canada",
  "Bruxelles, Belgique",
];

villeInput.addEventListener("input", () => {
  const value = villeInput.value.toLowerCase();
  suggestions.innerHTML = "";
  if (!value) return;

  const filtered = villes.filter((v) => v.toLowerCase().startsWith(value));
  filtered.forEach((v) => {
    const div = document.createElement("div");
    div.textContent = v;
    div.classList.add("autocomplete-suggestion");
    div.addEventListener("click", () => {
      const parts = v.split(", ");
      villeInput.value = parts[0];
      paysInput.value = parts[1] || "";
      suggestions.innerHTML = "";
    });
    suggestions.appendChild(div);
  });
});

document.addEventListener("click", (e) => {
  if (e.target !== villeInput) suggestions.innerHTML = "";
});

// Détection automatique via IP si vide
if (!villeInput.value) {
  fetch("https://ipapi.co/json/")
    .then((res) => res.json())
    .then((data) => {
      if (data) {
        villeInput.value = data.city || "";
        paysInput.value = data.country_name || "";
      }
    })
    .catch((err) => console.error(err));
}

document.getElementById("currentPassword").classList.add("filled");

function togglePasswordVisibility(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const navbar = document.querySelector(".navbar");

  // Détection du scroll ultra-sensible
  window.addEventListener("scroll", () => {
    // Dès qu'on descend de 10 pixels, on passe en mode "Bleu Nuit"
    if (window.scrollY > 10) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });
});
