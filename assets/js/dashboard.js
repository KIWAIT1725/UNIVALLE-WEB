// Funciones para el dashboard administrativo
class DashboardManager {
  constructor() {
    this.currentSection = "dashboard"
    this.tableData = {}
    this.initializeEventListeners()
    this.initializeAnimations()
    this.loadInitialData()
  }

  initializeEventListeners() {
    // Event listeners para el sidebar
    const sidebarLinks = document.querySelectorAll(".sidebar-menu a")
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const section = this.getSectionFromLink(link)
        if (section) {
          this.showSection(section)
        }
      })
    })

    // Event listeners para modales
    this.initializeModalEvents()

    // Event listeners para búsqueda en tablas
    this.initializeSearchEvents()

    // Auto-refresh de datos cada 30 segundos
    setInterval(() => {
      if (this.currentSection !== "dashboard") {
        this.loadTableData(this.currentSection)
      }
    }, 30000)
  }

  getSectionFromLink(link) {
    const id = link.getAttribute("id")
    return id ? id.replace("-link", "") : null
  }

  showSection(sectionName) {
    // Animación de salida de la sección actual
    const currentSection = document.querySelector(".content-section.active")
    if (currentSection) {
      this.fadeOut(currentSection, () => {
        currentSection.classList.remove("active")
        this.showSectionWithAnimation(sectionName)
      })
    } else {
      this.showSectionWithAnimation(sectionName)
    }

    // Actualizar sidebar
    this.updateSidebarActive(sectionName)
    this.currentSection = sectionName
  }

  showSectionWithAnimation(sectionName) {
    const targetSection = document.getElementById(sectionName + "-section")
    if (targetSection) {
      targetSection.classList.add("active")
      this.fadeIn(targetSection)

      // Cargar datos si no es dashboard
      if (sectionName !== "dashboard") {
        this.loadTableData(sectionName)
      }
    }
  }

  updateSidebarActive(sectionName) {
    // Remover clase active de todos los enlaces
    document.querySelectorAll(".sidebar-menu a").forEach((link) => {
      link.classList.remove("active")
    })

    // Agregar clase active al enlace seleccionado
    const activeLink = document.getElementById(sectionName + "-link")
    if (activeLink) {
      activeLink.classList.add("active")
    }
  }

  async loadTableData(tableName) {
    try {
      this.showTableLoading(tableName)

      const response = await fetch(`../api/get_${tableName}.php`)
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      this.tableData[tableName] = data
      this.updateTable(tableName, data)
      this.hideTableLoading(tableName)
    } catch (error) {
      console.error("Error loading table data:", error)
      this.showTableError(tableName, error.message)
    }
  }

  showTableLoading(tableName) {
    const tableBody = document.querySelector(`#${tableName}Table tbody`)
    if (tableBody) {
      tableBody.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <div class="mt-2">Cargando datos...</div>
                    </td>
                </tr>
            `
    }
  }

  hideTableLoading(tableName) {
    // El loading se oculta automáticamente al actualizar la tabla
  }

  showTableError(tableName, errorMessage) {
    const tableBody = document.querySelector(`#${tableName}Table tbody`)
    if (tableBody) {
      tableBody.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fs-2 mb-2"></i>
                        <div>Error al cargar los datos</div>
                        <small>${errorMessage}</small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="dashboardManager.loadTableData('${tableName}')">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    </td>
                </tr>
            `
    }
  }

  updateTable(tableName, data) {
    const tableBody = document.querySelector(`#${tableName}Table tbody`)
    if (!tableBody) return

    if (data.length === 0) {
      tableBody.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fs-2 mb-2"></i>
                        <div>No hay datos disponibles</div>
                    </td>
                </tr>
            `
      return
    }

    tableBody.innerHTML = ""

    data.forEach((row, index) => {
      const tr = document.createElement("tr")
      tr.innerHTML = this.generateTableRow(tableName, row)
      tr.style.opacity = "0"
      tr.style.transform = "translateY(20px)"
      tableBody.appendChild(tr)

      // Animación de entrada escalonada
      setTimeout(() => {
        tr.style.transition = "all 0.3s ease"
        tr.style.opacity = "1"
        tr.style.transform = "translateY(0)"
      }, index * 50)
    })
  }

  generateTableRow(tableName, row) {
    const actions = `
            <button class="btn btn-sm btn-outline-primary me-1" onclick="dashboardManager.editRecord('${tableName}', ${row.id})" title="Editar">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="dashboardManager.deleteRecord('${tableName}', ${row.id})" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        `

    switch (tableName) {
      case "estudiantes":
        return `
                    <td><span class="badge bg-primary">${row.codigo_estudiante}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">${row.nombre.charAt(0)}${row.apellido.charAt(0)}</div>
                            <div>
                                <div class="fw-semibold">${row.nombre} ${row.apellido}</div>
                                <small class="text-muted">${row.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>${row.email}</td>
                    <td>${row.telefono || '<span class="text-muted">N/A</span>'}</td>
                    <td>${this.formatDate(row.fecha_registro)}</td>
                    <td>${actions}</td>
                `

      case "maestros":
        return `
                    <td><span class="badge bg-success">${row.codigo_maestro}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">${row.nombre.charAt(0)}${row.apellido.charAt(0)}</div>
                            <div>
                                <div class="fw-semibold">${row.nombre} ${row.apellido}</div>
                                <small class="text-muted">${row.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>${row.email}</td>
                    <td><span class="badge bg-info">${row.especialidad}</span></td>
                    <td class="fw-semibold text-success">$${this.formatNumber(row.salario)}</td>
                    <td>${actions}</td>
                `

      case "asignaturas":
        return `
                    <td><span class="badge bg-warning text-dark">${row.codigo_asignatura}</span></td>
                    <td class="fw-semibold">${row.nombre}</td>
                    <td><span class="badge bg-secondary">${row.creditos}</span></td>
                    <td>${row.horas_semanales}h</td>
                    <td><span class="badge bg-primary">${row.semestre}° Semestre</span></td>
                    <td>${actions}</td>
                `

      case "asignaciones":
        return `
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                            ${row.maestro_nombre}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book text-warning me-2"></i>
                            ${row.asignatura_nombre}
                        </div>
                    </td>
                    <td><span class="badge bg-info">${row.periodo}</span></td>
                    <td class="fw-semibold">${row.año}</td>
                    <td>${row.horario || '<span class="text-muted">N/A</span>'}</td>
                    <td>${row.aula || '<span class="text-muted">N/A</span>'}</td>
                    <td>${actions}</td>
                `

      default:
        return '<td colspan="100%">Tipo de tabla no reconocido</td>'
    }
  }

  async editRecord(table, id) {
    // Implementar modal de edición
    const record = this.tableData[table]?.find((item) => item.id == id)
    if (record) {
      this.showEditModal(table, record)
    } else {
      this.showNotification("Error", "No se pudo encontrar el registro", "error")
    }
  }

  async deleteRecord(table, id) {
    const result = await this.showConfirmDialog(
      "¿Confirmar eliminación?",
      "¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.",
      "danger",
    )

    if (result) {
      try {
        const response = await fetch("../api/delete_record.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ table: table, id: id }),
        })

        const data = await response.json()

        if (data.success) {
          this.showNotification("Éxito", "Registro eliminado correctamente", "success")
          this.loadTableData(table)
        } else {
          this.showNotification("Error", data.message || "Error al eliminar el registro", "error")
        }
      } catch (error) {
        this.showNotification("Error", "Error de conexión", "error")
      }
    }
  }

  showEditModal(table, record) {
    // Crear modal dinámico para edición
    const modalId = `editModal_${table}`
    let modal = document.getElementById(modalId)

    if (!modal) {
      modal = this.createEditModal(table, modalId)
      document.body.appendChild(modal)
    }

    // Llenar el modal con los datos del registro
    this.populateEditModal(modal, record)

    // Mostrar modal
    const bsModal = new window.bootstrap.Modal(modal)
    bsModal.show()
  }

  createEditModal(table, modalId) {
    const modal = document.createElement("div")
    modal.className = "modal fade"
    modal.id = modalId
    modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar ${this.getTableDisplayName(table)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${this.generateEditForm(table)}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="dashboardManager.saveRecord('${table}')">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        `
    return modal
  }

  generateEditForm(table) {
    // Generar formulario dinámico basado en la tabla
    const forms = {
      estudiantes: `
                <form id="editForm_${table}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Estudiante</label>
                                <input type="text" class="form-control" name="codigo_estudiante" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control" name="direccion" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id">
                </form>
            `,
      maestros: `
                <form id="editForm_${table}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Maestro</label>
                                <input type="text" class="form-control" name="codigo_maestro" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Especialidad</label>
                                <input type="text" class="form-control" name="especialidad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Contratación</label>
                                <input type="date" class="form-control" name="fecha_contratacion">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Salario</label>
                                <input type="number" class="form-control" name="salario" step="0.01">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id">
                </form>
            `,
    }

    return forms[table] || "<p>Formulario no disponible para esta tabla</p>"
  }

  populateEditModal(modal, record) {
    const form = modal.querySelector("form")
    if (form) {
      Object.keys(record).forEach((key) => {
        const input = form.querySelector(`[name="${key}"]`)
        if (input) {
          input.value = record[key] || ""
        }
      })
    }
  }

  async saveRecord(table) {
    const form = document.getElementById(`editForm_${table}`)
    if (!form) return

    const formData = new FormData(form)
    const data = Object.fromEntries(formData.entries())

    try {
      const response = await fetch(`../api/update_${table}.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })

      const result = await response.json()

      if (result.success) {
        this.showNotification("Éxito", "Registro actualizado correctamente", "success")
        this.loadTableData(table)

        // Cerrar modal
        const modal = window.bootstrap.Modal.getInstance(document.getElementById(`editModal_${table}`))
        modal?.hide()
      } else {
        this.showNotification("Error", result.message || "Error al actualizar el registro", "error")
      }
    } catch (error) {
      this.showNotification("Error", "Error de conexión", "error")
    }
  }

  getTableDisplayName(table) {
    const names = {
      estudiantes: "Estudiante",
      maestros: "Maestro",
      asignaturas: "Asignatura",
      asignaciones: "Asignación",
    }
    return names[table] || table
  }

  initializeModalEvents() {
    // Event listeners para modales de agregar registros
    // Se implementarían aquí los eventos para los modales de creación
  }

  initializeSearchEvents() {
    // Implementar búsqueda en tablas
    const searchInputs = document.querySelectorAll(".table-search")
    searchInputs.forEach((input) => {
      input.addEventListener("input", (e) => {
        this.filterTable(e.target.dataset.table, e.target.value)
      })
    })
  }

  filterTable(tableName, searchTerm) {
    const table = document.getElementById(`${tableName}Table`)
    if (!table) return

    const rows = table.querySelectorAll("tbody tr")
    const term = searchTerm.toLowerCase()

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      const shouldShow = text.includes(term)

      row.style.display = shouldShow ? "" : "none"

      if (shouldShow) {
        row.style.animation = "fadeIn 0.3s ease"
      }
    })
  }

  initializeAnimations() {
    // Animaciones para las tarjetas de estadísticas
    document.addEventListener("DOMContentLoaded", () => {
      const statCards = document.querySelectorAll(".stat-card")
      statCards.forEach((card, index) => {
        card.style.opacity = "0"
        card.style.transform = "translateY(20px)"

        setTimeout(() => {
          card.style.transition = "all 0.5s ease"
          card.style.opacity = "1"
          card.style.transform = "translateY(0)"
        }, index * 100)
      })

      // Animación del sidebar
      this.animateSidebar()
    })
  }

  animateSidebar() {
    const sidebarItems = document.querySelectorAll(".sidebar-menu a")
    sidebarItems.forEach((item, index) => {
      item.style.opacity = "0"
      item.style.transform = "translateX(-20px)"

      setTimeout(() => {
        item.style.transition = "all 0.3s ease"
        item.style.opacity = "1"
        item.style.transform = "translateX(0)"
      }, index * 50)
    })
  }

  loadInitialData() {
    // Cargar datos iniciales del dashboard
    this.loadDashboardStats()
  }

  async loadDashboardStats() {
    try {
      const response = await fetch("../api/get_dashboard_stats.php")
      const stats = await response.json()
      this.updateDashboardStats(stats)
    } catch (error) {
      console.error("Error loading dashboard stats:", error)
    }
  }

  updateDashboardStats(stats) {
    // Actualizar las tarjetas de estadísticas con animación
    Object.keys(stats).forEach((key) => {
      const element = document.querySelector(`[data-stat="${key}"]`)
      if (element) {
        this.animateNumber(element, 0, stats[key], 1000)
      }
    })
  }

  animateNumber(element, start, end, duration) {
    const startTime = performance.now()
    const animate = (currentTime) => {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)

      const current = Math.floor(start + (end - start) * progress)
      element.textContent = current

      if (progress < 1) {
        requestAnimationFrame(animate)
      }
    }
    requestAnimationFrame(animate)
  }

  // Utilidades
  formatDate(dateString) {
    const date = new Date(dateString)
    return date.toLocaleDateString("es-ES", {
      year: "numeric",
      month: "short",
      day: "numeric",
    })
  }

  formatNumber(number) {
    return Number.parseFloat(number).toLocaleString("es-ES")
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

  showNotification(title, message, type = "info") {
    // Crear notificación toast
    const toast = document.createElement("div")
    toast.className = `toast align-items-center text-white bg-${type === "error" ? "danger" : type} border-0`
    toast.setAttribute("role", "alert")
    toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `

    // Agregar al contenedor de toasts
    let toastContainer = document.getElementById("toastContainer")
    if (!toastContainer) {
      toastContainer = document.createElement("div")
      toastContainer.id = "toastContainer"
      toastContainer.className = "toast-container position-fixed top-0 end-0 p-3"
      toastContainer.style.zIndex = "9999"
      document.body.appendChild(toastContainer)
    }

    toastContainer.appendChild(toast)

    const bsToast = new window.bootstrap.Toast(toast)
    bsToast.show()

    // Remover del DOM después de que se oculte
    toast.addEventListener("hidden.bs.toast", () => {
      toast.remove()
    })
  }

  async showConfirmDialog(title, message, type = "primary") {
    return new Promise((resolve) => {
      const modal = document.createElement("div")
      modal.className = "modal fade"
      modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-${type}" id="confirmBtn">Confirmar</button>
                        </div>
                    </div>
                </div>
            `

      document.body.appendChild(modal)
      const bsModal = new window.bootstrap.Modal(modal)

      modal.querySelector("#confirmBtn").addEventListener("click", () => {
        resolve(true)
        bsModal.hide()
      })

      modal.addEventListener("hidden.bs.modal", () => {
        if (!modal.dataset.confirmed) {
          resolve(false)
        }
        modal.remove()
      })

      modal.querySelector("#confirmBtn").addEventListener("click", () => {
        modal.dataset.confirmed = "true"
      })

      bsModal.show()
    })
  }
}

// CSS adicional para el dashboard
const dashboardStyles = `
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, var(--univalle-blue), var(--univalle-light-blue));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
        transform: translateX(5px);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .sidebar-menu a {
        transition: all 0.3s ease;
    }

    .sidebar-menu a:hover {
        padding-left: 2rem;
        background: rgba(255, 255, 255, 0.1);
    }
`

// Inyectar estilos del dashboard
const dashboardStyleSheet = document.createElement("style")
dashboardStyleSheet.textContent = dashboardStyles
document.head.appendChild(dashboardStyleSheet)

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector(".sidebar")) {
    window.dashboardManager = new DashboardManager()
  }
})

// Funciones globales para compatibilidad
function showSection(sectionName) {
  if (window.dashboardManager) {
    window.dashboardManager.showSection(sectionName)
  }
}

function loadTableData(tableName) {
  if (window.dashboardManager) {
    window.dashboardManager.loadTableData(tableName)
  }
}
