// Funciones para el sistema de login
class LoginManager {
  constructor() {
    this.initializeEventListeners()
    this.initializeAnimations()
  }

  initializeEventListeners() {
    // Event listeners para los tabs
    const studentTab = document.getElementById("studentTab")
    const adminTab = document.getElementById("adminTab")

    if (studentTab) {
      studentTab.addEventListener("click", () => this.showTab("student"))
    }
    if (adminTab) {
      adminTab.addEventListener("click", () => this.showTab("admin"))
    }

    // Validación de formularios
    const forms = document.querySelectorAll("form")
    forms.forEach((form) => {
      form.addEventListener("submit", (e) => this.validateForm(e))
    })

    // Efectos hover en inputs
    this.addInputEffects()
  }

  showTab(type) {
    const studentForm = document.getElementById("studentForm")
    const adminForm = document.getElementById("adminForm")
    const studentTab = document.getElementById("studentTab")
    const adminTab = document.getElementById("adminTab")

    // Animación de salida
    const currentForm = type === "student" ? adminForm : studentForm
    if (currentForm && currentForm.style.display !== "none") {
      this.fadeOut(currentForm, () => {
        currentForm.style.display = "none"
        this.showFormWithAnimation(type)
      })
    } else {
      this.showFormWithAnimation(type)
    }

    // Actualizar tabs
    if (type === "student") {
      studentTab?.classList.add("active")
      adminTab?.classList.remove("active")
    } else {
      adminTab?.classList.add("active")
      studentTab?.classList.remove("active")
    }
  }

  showFormWithAnimation(type) {
    const targetForm =
      type === "student" ? document.getElementById("studentForm") : document.getElementById("adminForm")

    if (targetForm) {
      targetForm.style.display = "block"
      this.fadeIn(targetForm)
    }
  }

  validateForm(event) {
    const form = event.target
    const inputs = form.querySelectorAll("input[required]")
    let isValid = true

    inputs.forEach((input) => {
      if (!input.value.trim()) {
        this.showInputError(input, "Este campo es obligatorio")
        isValid = false
      } else {
        this.clearInputError(input)
      }
    })

    // Validación específica para email
    const emailInputs = form.querySelectorAll('input[type="email"]')
    emailInputs.forEach((input) => {
      if (input.value && !this.isValidEmail(input.value)) {
        this.showInputError(input, "Email no válido")
        isValid = false
      }
    })

    if (!isValid) {
      event.preventDefault()
      this.shakeForm(form)
    } else {
      this.showLoadingState(form)
    }
  }

  showInputError(input, message) {
    this.clearInputError(input)

    input.classList.add("is-invalid")
    const errorDiv = document.createElement("div")
    errorDiv.className = "invalid-feedback"
    errorDiv.textContent = message
    input.parentNode.appendChild(errorDiv)
  }

  clearInputError(input) {
    input.classList.remove("is-invalid")
    const errorDiv = input.parentNode.querySelector(".invalid-feedback")
    if (errorDiv) {
      errorDiv.remove()
    }
  }

  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  }

  shakeForm(form) {
    form.style.animation = "shake 0.5s ease-in-out"
    setTimeout(() => {
      form.style.animation = ""
    }, 500)
  }

  showLoadingState(form) {
    const submitBtn = form.querySelector('button[type="submit"]')
    if (submitBtn) {
      const originalText = submitBtn.innerHTML
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...'
      submitBtn.disabled = true

      // Restaurar después de 3 segundos si no hay redirección
      setTimeout(() => {
        submitBtn.innerHTML = originalText
        submitBtn.disabled = false
      }, 3000)
    }
  }

  addInputEffects() {
    const inputs = document.querySelectorAll(".form-control")
    inputs.forEach((input) => {
      input.addEventListener("focus", () => {
        input.parentNode.style.transform = "scale(1.02)"
        input.parentNode.style.transition = "transform 0.2s ease"
      })

      input.addEventListener("blur", () => {
        input.parentNode.style.transform = "scale(1)"
      })
    })
  }

  initializeAnimations() {
    // Animación de entrada del contenedor
    document.addEventListener("DOMContentLoaded", () => {
      const container = document.querySelector(".login-container")
      if (container) {
        container.style.opacity = "0"
        container.style.transform = "translateY(30px)"

        setTimeout(() => {
          container.style.transition = "all 0.6s ease"
          container.style.opacity = "1"
          container.style.transform = "translateY(0)"
        }, 100)
      }

      // Animación del logo
      this.animateLogo()
    })
  }

  animateLogo() {
    const logo = document.querySelector(".logo-animation i")
    if (logo) {
      setInterval(() => {
        logo.style.transform = "scale(1.1) rotate(5deg)"
        setTimeout(() => {
          logo.style.transform = "scale(1) rotate(0deg)"
        }, 200)
      }, 3000)
    }
  }

  fadeOut(element, callback) {
    element.style.transition = "opacity 0.3s ease"
    element.style.opacity = "0"
    setTimeout(() => {
      if (callback) callback()
    }, 300)
  }

  fadeIn(element) {
    element.style.opacity = "0"
    element.style.transition = "opacity 0.3s ease"
    setTimeout(() => {
      element.style.opacity = "1"
    }, 50)
  }
}

// CSS adicional para animaciones
const additionalStyles = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .form-control:focus {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }

    .login-container {
        transition: all 0.3s ease;
    }

    .tab-button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tab-button:hover {
        transform: translateY(-2px);
    }
`

// Inyectar estilos adicionales
const styleSheet = document.createElement("style")
styleSheet.textContent = additionalStyles
document.head.appendChild(styleSheet)

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  new LoginManager()
})

// Funciones globales para compatibilidad
function showTab(type) {
  if (window.loginManager) {
    window.loginManager.showTab(type)
  }
}

// Exportar para uso global
window.loginManager = new LoginManager()
