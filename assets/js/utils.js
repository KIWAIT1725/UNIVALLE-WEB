// Utilidades y funciones auxiliares para todo el sistema
class UtilityManager {
  constructor() {
    this.initializeUtilities()
  }

  initializeUtilities() {
    // Configurar utilidades globales
    this.setupFormValidation()
    this.setupDataFormatters()
    this.setupLocalStorage()
    this.setupErrorHandling()
  }

  // === VALIDACIÓN DE FORMULARIOS ===
  setupFormValidation() {
    this.validators = {
      email: (value) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return emailRegex.test(value)
      },

      phone: (value) => {
        const phoneRegex = /^[0-9]{10}$/
        return phoneRegex.test(value.replace(/\D/g, ""))
      },

      studentCode: (value) => {
        const codeRegex = /^[A-Z]{3}[0-9]{3}$/
        return codeRegex.test(value)
      },

      teacherCode: (value) => {
        const codeRegex = /^[A-Z]{3}[0-9]{3}$/
        return codeRegex.test(value)
      },

      subjectCode: (value) => {
        const codeRegex = /^[A-Z]{3}[0-9]{3}$/
        return codeRegex.test(value)
      },

      password: (value) => {
        return value.length >= 6
      },

      required: (value) => {
        return value && value.trim().length > 0
      },

      numeric: (value) => {
        return !isNaN(value) && !isNaN(Number.parseFloat(value))
      },

      positiveNumber: (value) => {
        return this.validators.numeric(value) && Number.parseFloat(value) > 0
      },
    }
  }

  validateField(field, rules) {
    const value = field.value
    const errors = []

    rules.forEach((rule) => {
      if (typeof rule === "string") {
        if (this.validators[rule] && !this.validators[rule](value)) {
          errors.push(this.getErrorMessage(rule, field.name))
        }
      } else if (typeof rule === "object") {
        if (this.validators[rule.type] && !this.validators[rule.type](value)) {
          errors.push(rule.message || this.getErrorMessage(rule.type, field.name))
        }
      }
    })

    return errors
  }

  getErrorMessage(rule, fieldName) {
    const messages = {
      required: `El campo ${fieldName} es obligatorio`,
      email: "Ingrese un email válido",
      phone: "Ingrese un teléfono válido (10 dígitos)",
      studentCode: "Código de estudiante inválido (formato: EST001)",
      teacherCode: "Código de maestro inválido (formato: MAE001)",
      subjectCode: "Código de asignatura inválido (formato: MAT101)",
      password: "La contraseña debe tener al menos 6 caracteres",
      numeric: "Ingrese un valor numérico válido",
      positiveNumber: "Ingrese un número positivo",
    }

    return messages[rule] || `Error de validación en ${fieldName}`
  }

  validateForm(form, validationRules) {
    const errors = {}
    let isValid = true

    Object.keys(validationRules).forEach((fieldName) => {
      const field = form.querySelector(`[name="${fieldName}"]`)
      if (field) {
        const fieldErrors = this.validateField(field, validationRules[fieldName])
        if (fieldErrors.length > 0) {
          errors[fieldName] = fieldErrors
          isValid = false
          this.showFieldError(field, fieldErrors[0])
        } else {
          this.clearFieldError(field)
        }
      }
    })

    return { isValid, errors }
  }

  showFieldError(field, message) {
    this.clearFieldError(field)

    field.classList.add("is-invalid")
    const errorDiv = document.createElement("div")
    errorDiv.className = "invalid-feedback"
    errorDiv.textContent = message

    if (field.parentNode.classList.contains("input-group")) {
      field.parentNode.parentNode.appendChild(errorDiv)
    } else {
      field.parentNode.appendChild(errorDiv)
    }
  }

  clearFieldError(field) {
    field.classList.remove("is-invalid")
    const errorDiv =
      field.parentNode.querySelector(".invalid-feedback") ||
      field.parentNode.parentNode.querySelector(".invalid-feedback")
    if (errorDiv) {
      errorDiv.remove()
    }
  }

  // === FORMATEO DE DATOS ===
  setupDataFormatters() {
    this.formatters = {
      currency: (value) => {
        return new Intl.NumberFormat("es-CO", {
          style: "currency",
          currency: "COP",
        }).format(value)
      },

      number: (value) => {
        return new Intl.NumberFormat("es-CO").format(value)
      },

      date: (value, format = "short") => {
        const date = new Date(value)
        const options = {
          short: { year: "numeric", month: "short", day: "numeric" },
          long: { year: "numeric", month: "long", day: "numeric" },
          time: {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
          },
        }

        return date.toLocaleDateString("es-CO", options[format] || options.short)
      },

      phone: (value) => {
        const cleaned = value.replace(/\D/g, "")
        if (cleaned.length === 10) {
          return `${cleaned.slice(0, 3)} ${cleaned.slice(3, 6)} ${cleaned.slice(6)}`
        }
        return value
      },

      capitalize: (value) => {
        return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase()
      },

      titleCase: (value) => {
        return value.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase())
      },
    }
  }

  formatValue(value, type, options = {}) {
    if (this.formatters[type]) {
      return this.formatters[type](value, options)
    }
    return value
  }

  // === ALMACENAMIENTO LOCAL ===
  setupLocalStorage() {
    this.storage = {
      set: (key, value) => {
        try {
          localStorage.setItem(`univalle_${key}`, JSON.stringify(value))
          return true
        } catch (error) {
          console.error("Error saving to localStorage:", error)
          return false
        }
      },

      get: (key, defaultValue = null) => {
        try {
          const item = localStorage.getItem(`univalle_${key}`)
          return item ? JSON.parse(item) : defaultValue
        } catch (error) {
          console.error("Error reading from localStorage:", error)
          return defaultValue
        }
      },

      remove: (key) => {
        try {
          localStorage.removeItem(`univalle_${key}`)
          return true
        } catch (error) {
          console.error("Error removing from localStorage:", error)
          return false
        }
      },

      clear: () => {
        try {
          Object.keys(localStorage).forEach((key) => {
            if (key.startsWith("univalle_")) {
              localStorage.removeItem(key)
            }
          })
          return true
        } catch (error) {
          console.error("Error clearing localStorage:", error)
          return false
        }
      },
    }
  }

  // === MANEJO DE ERRORES ===
  setupErrorHandling() {
    // Capturar errores JavaScript globales
    window.addEventListener("error", (event) => {
      this.logError("JavaScript Error", {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        error: event.error,
      })
    })

    // Capturar promesas rechazadas
    window.addEventListener("unhandledrejection", (event) => {
      this.logError("Unhandled Promise Rejection", {
        reason: event.reason,
      })
    })
  }

  logError(type, details) {
    const errorLog = {
      timestamp: new Date().toISOString(),
      type: type,
      details: details,
      userAgent: navigator.userAgent,
      url: window.location.href,
    }

    // Guardar en localStorage para debugging
    const errors = this.storage.get("error_log", [])
    errors.push(errorLog)

    // Mantener solo los últimos 50 errores
    if (errors.length > 50) {
      errors.splice(0, errors.length - 50)
    }

    this.storage.set("error_log", errors)

    // En desarrollo, mostrar en consola
    if (this.isDevelopment()) {
      console.error(`[${type}]`, details)
    }
  }

  isDevelopment() {
    return (
      window.location.hostname === "localhost" ||
      window.location.hostname === "127.0.0.1" ||
      window.location.hostname.includes("dev")
    )
  }

  // === UTILIDADES DE RED ===
  async makeRequest(url, options = {}) {
    const defaultOptions = {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
      timeout: 10000,
    }

    const config = { ...defaultOptions, ...options }

    try {
      const controller = new AbortController()
      const timeoutId = setTimeout(() => controller.abort(), config.timeout)

      const response = await fetch(url, {
        ...config,
        signal: controller.signal,
      })

      clearTimeout(timeoutId)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const contentType = response.headers.get("content-type")
      if (contentType && contentType.includes("application/json")) {
        return await response.json()
      } else {
        return await response.text()
      }
    } catch (error) {
      this.logError("Network Request Failed", {
        url: url,
        method: config.method,
        error: error.message,
      })
      throw error
    }
  }

  // === UTILIDADES DE DOM ===
  createElement(tag, attributes = {}, children = []) {
    const element = document.createElement(tag)

    // Establecer atributos
    Object.keys(attributes).forEach((key) => {
      if (key === "className") {
        element.className = attributes[key]
      } else if (key === "innerHTML") {
        element.innerHTML = attributes[key]
      } else if (key === "textContent") {
        element.textContent = attributes[key]
      } else if (key.startsWith("data-")) {
        element.setAttribute(key, attributes[key])
      } else {
        element[key] = attributes[key]
      }
    })

    // Agregar hijos
    children.forEach((child) => {
      if (typeof child === "string") {
        element.appendChild(document.createTextNode(child))
      } else if (child instanceof Node) {
        element.appendChild(child)
      }
    })

    return element
  }

  findElement(selector, context = document) {
    return context.querySelector(selector)
  }

  findElements(selector, context = document) {
    return Array.from(context.querySelectorAll(selector))
  }

  // === UTILIDADES DE TIEMPO ===
  debounce(func, wait, immediate = false) {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        timeout = null
        if (!immediate) func(...args)
      }
      const callNow = immediate && !timeout
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
      if (callNow) func(...args)
    }
  }

  throttle(func, limit) {
    let inThrottle
    return function (...args) {
      if (!inThrottle) {
        func.apply(this, args)
        inThrottle = true
        setTimeout(() => (inThrottle = false), limit)
      }
    }
  }

  delay(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms))
  }

  // === UTILIDADES DE DATOS ===
  deepClone(obj) {
    if (obj === null || typeof obj !== "object") return obj
    if (obj instanceof Date) return new Date(obj.getTime())
    if (obj instanceof Array) return obj.map((item) => this.deepClone(item))
    if (typeof obj === "object") {
      const clonedObj = {}
      Object.keys(obj).forEach((key) => {
        clonedObj[key] = this.deepClone(obj[key])
      })
      return clonedObj
    }
  }

  isEmpty(value) {
    if (value === null || value === undefined) return true
    if (typeof value === "string") return value.trim().length === 0
    if (Array.isArray(value)) return value.length === 0
    if (typeof value === "object") return Object.keys(value).length === 0
    return false
  }

  generateId(prefix = "id") {
    return `${prefix}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
  }

  // === UTILIDADES DE EXPORTACIÓN ===
  exportToCSV(data, filename = "export.csv") {
    if (!data || data.length === 0) return

    const headers = Object.keys(data[0])
    const csvContent = [
      headers.join(","),
      ...data.map((row) =>
        headers
          .map((header) => {
            const value = row[header]
            return typeof value === "string" && value.includes(",") ? `"${value}"` : value
          })
          .join(","),
      ),
    ].join("\n")

    this.downloadFile(csvContent, filename, "text/csv")
  }

  exportToJSON(data, filename = "export.json") {
    const jsonContent = JSON.stringify(data, null, 2)
    this.downloadFile(jsonContent, filename, "application/json")
  }

  downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType })
    const url = URL.createObjectURL(blob)
    const link = document.createElement("a")
    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  }

  // === UTILIDADES DE NOTIFICACIONES ===
  showToast(message, type = "info", duration = 5000) {
    const toastContainer = this.getOrCreateToastContainer()
    const toast = this.createElement("div", {
      className: `toast align-items-center text-white bg-${type} border-0 show`,
      role: "alert",
      innerHTML: `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `,
    })

    toastContainer.appendChild(toast)

    // Auto-remove después del tiempo especificado
    setTimeout(() => {
      if (toast.parentNode) {
        toast.remove()
      }
    }, duration)

    return toast
  }

  getOrCreateToastContainer() {
    let container = document.getElementById("toastContainer")
    if (!container) {
      container = this.createElement("div", {
        id: "toastContainer",
        className: "toast-container position-fixed top-0 end-0 p-3",
        style: "z-index: 9999;",
      })
      document.body.appendChild(container)
    }
    return container
  }

  // === UTILIDADES DE IMPRESIÓN ===
  printElement(element, title = "Documento") {
    const printWindow = window.open("", "_blank")
    const elementHTML = element.outerHTML

    printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${title}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none !important; }
                    }
                </style>
            </head>
            <body>
                ${elementHTML}
            </body>
            </html>
        `)

    printWindow.document.close()
    printWindow.focus()

    setTimeout(() => {
      printWindow.print()
      printWindow.close()
    }, 250)
  }

  // === UTILIDADES DE RESPONSIVE ===
  isMobile() {
    return window.innerWidth <= 768
  }

  isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024
  }

  isDesktop() {
    return window.innerWidth > 1024
  }

  onResize(callback, delay = 250) {
    const debouncedCallback = this.debounce(callback, delay)
    window.addEventListener("resize", debouncedCallback)
    return () => window.removeEventListener("resize", debouncedCallback)
  }

  // === UTILIDADES DE ACCESIBILIDAD ===
  announceToScreenReader(message) {
    const announcement = this.createElement("div", {
      className: "sr-only",
      setAttribute: { "aria-live": "polite" },
      textContent: message,
    })

    document.body.appendChild(announcement)

    setTimeout(() => {
      announcement.remove()
    }, 1000)
  }

  trapFocus(element) {
    const focusableElements = element.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
    )

    const firstElement = focusableElements[0]
    const lastElement = focusableElements[focusableElements.length - 1]

    const handleTabKey = (e) => {
      if (e.key === "Tab") {
        if (e.shiftKey) {
          if (document.activeElement === firstElement) {
            lastElement.focus()
            e.preventDefault()
          }
        } else {
          if (document.activeElement === lastElement) {
            firstElement.focus()
            e.preventDefault()
          }
        }
      }
    }

    element.addEventListener("keydown", handleTabKey)
    firstElement.focus()

    return () => {
      element.removeEventListener("keydown", handleTabKey)
    }
  }
}

// Inicializar utilidades globales
document.addEventListener("DOMContentLoaded", () => {
  window.utils = new UtilityManager()
})

// Exportar para uso en módulos
if (typeof module !== "undefined" && module.exports) {
  module.exports = UtilityManager
}

// Funciones de conveniencia globales
window.formatCurrency = (value) => window.utils?.formatValue(value, "currency") || value
window.formatDate = (value, format) => window.utils?.formatValue(value, "date", format) || value
window.showToast = (message, type, duration) => window.utils?.showToast(message, type, duration)
window.validateForm = (form, rules) => window.utils?.validateForm(form, rules)
