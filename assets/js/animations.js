// Animaciones y efectos visuales para todo el sistema
class AnimationManager {
  constructor() {
    this.initializeGlobalAnimations()
    this.setupScrollAnimations()
    this.setupHoverEffects()
  }

  initializeGlobalAnimations() {
    // Animación de carga de página
    document.addEventListener("DOMContentLoaded", () => {
      this.pageLoadAnimation()
      this.setupParticleBackground()
      this.initializeCounters()
    })

    // Animaciones de transición entre páginas
    this.setupPageTransitions()
  }

  pageLoadAnimation() {
    // Crear overlay de carga
    const loadingOverlay = document.createElement("div")
    loadingOverlay.id = "loadingOverlay"
    loadingOverlay.innerHTML = `
            <div class="loading-content">
                <div class="university-logo">
                    <i class="fas fa-university"></i>
                </div>
                <div class="loading-text">Universidad del Valle</div>
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
            </div>
        `

    // Estilos para el overlay
    const loadingStyles = `
            #loadingOverlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, var(--univalle-blue) 0%, var(--univalle-light-blue) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                transition: opacity 0.5s ease;
            }

            .loading-content {
                text-align: center;
                color: white;
            }

            .university-logo i {
                font-size: 4rem;
                margin-bottom: 1rem;
                animation: logoFloat 2s ease-in-out infinite;
            }

            .loading-text {
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 2rem;
                animation: textFade 2s ease-in-out infinite;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-top: 3px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto;
            }

            @keyframes logoFloat {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }

            @keyframes textFade {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `

    // Agregar estilos
    const styleSheet = document.createElement("style")
    styleSheet.textContent = loadingStyles
    document.head.appendChild(styleSheet)

    // Agregar overlay al body
    document.body.appendChild(loadingOverlay)

    // Remover overlay después de la carga
    window.addEventListener("load", () => {
      setTimeout(() => {
        loadingOverlay.style.opacity = "0"
        setTimeout(() => {
          loadingOverlay.remove()
          styleSheet.remove()
        }, 500)
      }, 1000)
    })
  }

  setupParticleBackground() {
    // Crear fondo de partículas animadas
    if (document.querySelector(".login-container") || document.querySelector('body[style*="gradient"]')) {
      this.createParticleSystem()
    }
  }

  createParticleSystem() {
    const particleContainer = document.createElement("div")
    particleContainer.id = "particleContainer"
    particleContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        `

    // Crear partículas
    for (let i = 0; i < 50; i++) {
      const particle = document.createElement("div")
      particle.className = "particle"
      particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: rgba(255, 255, 255, ${Math.random() * 0.5 + 0.2});
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 10 + 10}s linear infinite;
            `
      particleContainer.appendChild(particle)
    }

    // Agregar animación CSS
    const particleStyles = `
            @keyframes float {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `

    const particleStyleSheet = document.createElement("style")
    particleStyleSheet.textContent = particleStyles
    document.head.appendChild(particleStyleSheet)

    document.body.appendChild(particleContainer)
  }

  setupScrollAnimations() {
    // Animaciones basadas en scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in")
        }
      })
    }, observerOptions)

    // Observar elementos que deben animarse
    document.addEventListener("DOMContentLoaded", () => {
      const animateElements = document.querySelectorAll(".stat-card, .table-container, .card")
      animateElements.forEach((el) => {
        el.classList.add("animate-on-scroll")
        observer.observe(el)
      })
    })

    // CSS para animaciones de scroll
    const scrollAnimationStyles = `
            .animate-on-scroll {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease;
            }

            .animate-on-scroll.animate-in {
                opacity: 1;
                transform: translateY(0);
            }
        `

    const scrollStyleSheet = document.createElement("style")
    scrollStyleSheet.textContent = scrollAnimationStyles
    document.head.appendChild(scrollStyleSheet)
  }

  setupHoverEffects() {
    // Efectos hover avanzados
    document.addEventListener("DOMContentLoaded", () => {
      this.setupButtonHoverEffects()
      this.setupCardHoverEffects()
      this.setupTableRowEffects()
    })
  }

  setupButtonHoverEffects() {
    const buttons = document.querySelectorAll(".btn")
    buttons.forEach((button) => {
      button.addEventListener("mouseenter", (e) => {
        this.createRippleEffect(e)
      })
    })
  }

  createRippleEffect(event) {
    const button = event.currentTarget
    const ripple = document.createElement("span")
    const rect = button.getBoundingClientRect()
    const size = Math.max(rect.width, rect.height)
    const x = event.clientX - rect.left - size / 2
    const y = event.clientY - rect.top - size / 2

    ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `

    // Asegurar posición relativa del botón
    if (getComputedStyle(button).position === "static") {
      button.style.position = "relative"
    }
    button.style.overflow = "hidden"

    button.appendChild(ripple)

    // Remover el ripple después de la animación
    setTimeout(() => {
      ripple.remove()
    }, 600)

    // CSS para el efecto ripple
    if (!document.getElementById("rippleStyles")) {
      const rippleStyles = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `
      const rippleStyleSheet = document.createElement("style")
      rippleStyleSheet.id = "rippleStyles"
      rippleStyleSheet.textContent = rippleStyles
      document.head.appendChild(rippleStyleSheet)
    }
  }

  setupCardHoverEffects() {
    const cards = document.querySelectorAll(".card, .stat-card")
    cards.forEach((card) => {
      card.addEventListener("mouseenter", () => {
        card.style.transform = "translateY(-5px) scale(1.02)"
        card.style.boxShadow = "0 15px 35px rgba(0, 0, 0, 0.15)"
      })

      card.addEventListener("mouseleave", () => {
        card.style.transform = "translateY(0) scale(1)"
        card.style.boxShadow = ""
      })
    })
  }

  setupTableRowEffects() {
    document.addEventListener("DOMContentLoaded", () => {
      const tables = document.querySelectorAll("table tbody")
      tables.forEach((tbody) => {
        tbody.addEventListener(
          "mouseenter",
          (e) => {
            if (e.target.tagName === "TR" || e.target.closest("tr")) {
              const row = e.target.tagName === "TR" ? e.target : e.target.closest("tr")
              row.style.transform = "translateX(5px)"
              row.style.backgroundColor = "rgba(59, 130, 246, 0.05)"
            }
          },
          true,
        )

        tbody.addEventListener(
          "mouseleave",
          (e) => {
            if (e.target.tagName === "TR" || e.target.closest("tr")) {
              const row = e.target.tagName === "TR" ? e.target : e.target.closest("tr")
              row.style.transform = "translateX(0)"
              row.style.backgroundColor = ""
            }
          },
          true,
        )
      })
    })
  }

  setupPageTransitions() {
    // Transiciones suaves entre páginas
    const links = document.querySelectorAll("a[href]")
    links.forEach((link) => {
      link.addEventListener("click", (e) => {
        const href = link.getAttribute("href")
        if (href && !href.startsWith("#") && !href.startsWith("javascript:")) {
          e.preventDefault()
          this.transitionToPage(href)
        }
      })
    })
  }

  transitionToPage(url) {
    // Crear overlay de transición
    const transitionOverlay = document.createElement("div")
    transitionOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--univalle-blue) 0%, var(--univalle-light-blue) 100%);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        `

    transitionOverlay.innerHTML = `
            <div>
                <i class="fas fa-spinner fa-spin me-2"></i>
                Cargando...
            </div>
        `

    document.body.appendChild(transitionOverlay)

    // Mostrar overlay
    setTimeout(() => {
      transitionOverlay.style.opacity = "1"
    }, 10)

    // Navegar después de la animación
    setTimeout(() => {
      window.location.href = url
    }, 300)
  }

  initializeCounters() {
    // Animación de contadores numéricos
    const counters = document.querySelectorAll("[data-count]")
    counters.forEach((counter) => {
      const target = Number.parseInt(counter.getAttribute("data-count"))
      this.animateCounter(counter, 0, target, 2000)
    })
  }

  animateCounter(element, start, end, duration) {
    const startTime = performance.now()
    const animate = (currentTime) => {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)

      // Usar easing function para suavizar la animación
      const easeOutQuart = 1 - Math.pow(1 - progress, 4)
      const current = Math.floor(start + (end - start) * easeOutQuart)

      element.textContent = current.toLocaleString()

      if (progress < 1) {
        requestAnimationFrame(animate)
      }
    }
    requestAnimationFrame(animate)
  }

  // Método para crear animaciones personalizadas
  createCustomAnimation(element, keyframes, options = {}) {
    const defaultOptions = {
      duration: 1000,
      easing: "ease",
      fill: "forwards",
    }

    const animationOptions = { ...defaultOptions, ...options }

    if (element.animate) {
      return element.animate(keyframes, animationOptions)
    } else {
      // Fallback para navegadores que no soportan Web Animations API
      console.warn("Web Animations API no soportada")
      return null
    }
  }

  // Método para pausar/reanudar animaciones
  pauseAnimations() {
    const animatedElements = document.querySelectorAll('[style*="animation"]')
    animatedElements.forEach((el) => {
      el.style.animationPlayState = "paused"
    })
  }

  resumeAnimations() {
    const animatedElements = document.querySelectorAll('[style*="animation"]')
    animatedElements.forEach((el) => {
      el.style.animationPlayState = "running"
    })
  }

  // Método para limpiar animaciones
  cleanupAnimations() {
    // Remover partículas
    const particleContainer = document.getElementById("particleContainer")
    if (particleContainer) {
      particleContainer.remove()
    }

    // Remover overlays
    const overlays = document.querySelectorAll('#loadingOverlay, [id*="transition"]')
    overlays.forEach((overlay) => overlay.remove())
  }
}

// Inicializar el gestor de animaciones
document.addEventListener("DOMContentLoaded", () => {
  window.animationManager = new AnimationManager()
})

// Limpiar animaciones al salir de la página
window.addEventListener("beforeunload", () => {
  if (window.animationManager) {
    window.animationManager.cleanupAnimations()
  }
})

// Exportar para uso global
window.AnimationManager = AnimationManager
