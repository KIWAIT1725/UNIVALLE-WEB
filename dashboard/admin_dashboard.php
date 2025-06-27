<?php
// Configurar timezone al inicio del archivo
date_default_timezone_set('America/Mexico_City');

session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

require '../config/database.php';

// Obtener estadísticas
$stats = array();
$tables = array('estudiantes', 'maestros', 'asignaturas', 'asignaciones');

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats[$table] = $row['count'];
    } else {
        $stats[$table] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Universidad del Valle</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --univalle-blue: #1e40af;
            --univalle-light-blue: #3b82f6;
        }

        body {
            background-color: #f8fafc;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--univalle-blue) 0%, var(--univalle-light-blue) 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 2rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

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

        .university-logo-sidebar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .university-logo-sidebar:hover {
            transform: scale(1.1);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .logo-fallback-sidebar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <img src="../assets/images/univalle.jpg" alt="Universidad del Valle" class="university-logo-sidebar" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="logo-fallback-sidebar" style="display: none;">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
                <div>
                    <h5 class="mb-0">Universidad del Valle</h5>
                    <small class="opacity-75">Panel Administrativo</small>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="#" onclick="showSection('dashboard')" class="active" id="dashboard-link">
                <i class="fas fa-tachometer-alt me-3"></i>Dashboard
            </a>
            <a href="#" onclick="showSection('estudiantes')" id="estudiantes-link">
                <i class="fas fa-user-graduate me-3"></i>Estudiantes
            </a>
            <a href="#" onclick="showSection('maestros')" id="maestros-link">
                <i class="fas fa-chalkboard-teacher me-3"></i>Maestros
            </a>
            <a href="#" onclick="showSection('asignaturas')" id="asignaturas-link">
                <i class="fas fa-book me-3"></i>Asignaturas
            </a>
            <a href="#" onclick="showSection('asignaciones')" id="asignaciones-link">
                <i class="fas fa-calendar-alt me-3"></i>Asignaciones
            </a>
        </div>

        <div class="mt-auto p-3">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-user-circle fs-3 me-2"></i>
                <div>
                    <small class="d-block"><?php echo $_SESSION['user_name']; ?></small>
                    <small class="opacity-75">Administrador</small>
                </div>
            </div>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm w-100">
                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <img src="../assets/images/univalle.jpg" alt="Universidad del Valle" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 1rem;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <div style="display: none; width: 40px; height: 40px; border-radius: 50%; background: var(--univalle-blue); color: white; align-items: center; justify-content: center; margin-right: 1rem;">
                        <i class="fas fa-university"></i>
                    </div>
                    <h2 class="fw-bold mb-0">BIENVENIDO DE NUEVO JOSÉ</h2>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar me-2"></i><?php echo date('d/m/Y H:i'); ?> 
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #10b981, #34d399);">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="fw-bold mb-0" data-stat="estudiantes"><?php echo $stats['estudiantes']; ?></h3>
                                <p class="text-muted mb-0">Estudiantes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #3b82f6, #60a5fa);">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="fw-bold mb-0" data-stat="maestros"><?php echo $stats['maestros']; ?></h3>
                                <p class="text-muted mb-0">Maestros</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #f59e0b, #fbbf24);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="fw-bold mb-0" data-stat="asignaturas"><?php echo $stats['asignaturas']; ?></h3>
                                <p class="text-muted mb-0">Asignaturas</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #ef4444, #f87171);">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="fw-bold mb-0" data-stat="asignaciones"><?php echo $stats['asignaciones']; ?></h3>
                                <p class="text-muted mb-0">Asignaciones</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estudiantes Section -->
        <div id="estudiantes-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Gestión de Estudiantes</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="fas fa-plus me-2"></i>Agregar Estudiante
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover" id="estudiantesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando estudiantes...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Maestros Section -->
        <div id="maestros-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Gestión de Maestros</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                    <i class="fas fa-plus me-2"></i>Agregar Maestro
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover" id="maestrosTable">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Especialidad</th>
                                <th>Salario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando maestros...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Asignaturas Section -->
        <div id="asignaturas-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Gestión de Asignaturas</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="fas fa-plus me-2"></i>Agregar Asignatura
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover" id="asignaturasTable">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Créditos</th>
                                <th>Horas Semanales</th>
                                <th>Semestre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando asignaturas...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Asignaciones Section -->
        <div id="asignaciones-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Gestión de Asignaciones</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                    <i class="fas fa-plus me-2"></i>Nueva Asignación
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover" id="asignacionesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Maestro</th>
                                <th>Asignatura</th>
                                <th>Período</th>
                                <th>Año</th>
                                <th>Horario</th>
                                <th>Aula</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando asignaciones...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
<!-- Modal Agregar Estudiante -->
<form id="addStudentForm" action="../api/create_estudiante.php" method="POST" target="hiddenFrame">
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate me-2"></i>Agregar Nuevo Estudiante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Código de Estudiante *</label>
                                    <input type="text" class="form-control" name="codigo_estudiante" placeholder="EST001" required>
                                    <div class="form-text">Formato: EST + 3 números</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" placeholder="estudiante@univalle.edu.co" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" name="apellido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" name="telefono" placeholder="3001234567">
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
                                    <textarea class="form-control" name="direccion" rows="2" placeholder="Dirección completa"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" name="password" placeholder="Dejar vacío para contraseña por defecto (123456)">
                                    <div class="form-text">Si no se especifica, la contraseña será: 123456</div>
                                </div>
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createRecord('estudiante')">
                        <i class="fas fa-save me-2"></i>Guardar Estudiante
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Agregar Maestro -->
<form id="addTeacherForm" action="../api/create_maestro.php" method="POST" target="hiddenFrame">
    <div class="modal fade" id="addTeacherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Agregar Nuevo Maestro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Código de Maestro *</label>
                                    <input type="text" class="form-control" name="codigo_maestro" placeholder="MAE001" required>
                                    <div class="form-text">Formato: MAE + 3 números</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" placeholder="maestro@univalle.edu.co" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" name="apellido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" name="telefono" placeholder="3001234567">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Especialidad *</label>
                                    <input type="text" class="form-control" name="especialidad" placeholder="Matemáticas, Física, etc." required>
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
                                    <input type="number" class="form-control" name="salario" step="0.01" placeholder="4500000.00">
                                </div>
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createRecord('maestro')">
                        <i class="fas fa-save me-2"></i>Guardar Maestro
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Agregar Asignatura -->
<form id="addSubjectForm" action="../api/create_asignatura.php" method="POST" target="hiddenFrame">
    <div class="modal fade" id="addSubjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-book me-2"></i>Agregar Nueva Asignatura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Código de Asignatura *</label>
                                    <input type="text" class="form-control" name="codigo_asignatura" placeholder="MAT101" required>
                                    <div class="form-text">Formato: 3 letras + 3 números</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semestre *</label>
                                    <select class="form-control" name="semestre" required>
                                        <option value="">Seleccionar semestre</option>
                                        <option value="1">1° Semestre</option>
                                        <option value="2">2° Semestre</option>
                                        <option value="3">3° Semestre</option>
                                        <option value="4">4° Semestre</option>
                                        <option value="5">5° Semestre</option>
                                        <option value="6">6° Semestre</option>
                                        <option value="7">7° Semestre</option>
                                        <option value="8">8° Semestre</option>
                                        <option value="9">9° Semestre</option>
                                        <option value="10">10° Semestre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la Asignatura *</label>
                                    <input type="text" class="form-control" name="nombre" placeholder="Cálculo Diferencial" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="3" placeholder="Descripción de la asignatura"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Créditos *</label>
                                    <input type="number" class="form-control" name="creditos" min="1" max="10" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Horas Semanales *</label>
                                    <input type="number" class="form-control" name="horas_semanales" min="1" max="20" required>
                                </div>
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createRecord('asignatura')">
                        <i class="fas fa-save me-2"></i>Guardar Asignatura
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Agregar Asignación -->
<form id="addAssignmentForm" action="../api/create_asignacion.php" method="POST" target="hiddenFrame">
    <div class="modal fade" id="addAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Agregar Nueva Asignación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Maestro *</label>
                                    <select class="form-control" name="maestro_id" required>
                                        <option value="">Seleccionar maestro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Asignatura *</label>
                                    <select class="form-control" name="asignatura_id" required>
                                        <option value="">Seleccionar asignatura</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Período *</label>
                                    <select class="form-control" name="periodo" required>
                                        <option value="">Seleccionar período</option>
                                        <option value="2024-1">2024-1</option>
                                        <option value="2024-2">2024-2</option>
                                        <option value="2025-1">2025-1</option>
                                        <option value="2025-2">2025-2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Año *</label>
                                    <input type="number" class="form-control" name="año" min="2024" max="2030" value="2024" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Horario</label>
                                    <input type="text" class="form-control" name="horario" placeholder="Lunes y Miércoles 8:00-10:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Aula</label>
                                    <input type="text" class="form-control" name="aula" placeholder="Aula 101">
                                </div>
                            </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createRecord('asignacion')">
                        <i class="fas fa-save me-2"></i>Guardar Asignación
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modales de Edición -->

<!-- Modal Editar Estudiante -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>Editar Estudiante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editStudentForm">
                    <input type="hidden" name="id" id="edit_student_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Estudiante *</label>
                                <input type="text" class="form-control" name="codigo_estudiante" id="edit_student_codigo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="edit_student_email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" id="edit_student_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido *</label>
                                <input type="text" class="form-control" name="apellido" id="edit_student_apellido" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" id="edit_student_telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" id="edit_student_fecha_nacimiento">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control" name="direccion" id="edit_student_direccion" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" name="password" id="edit_student_password" placeholder="Dejar vacío para mantener la actual">
                                <div class="form-text">Solo completar si desea cambiar la contraseña</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRecord('estudiante')">
                    <i class="fas fa-save me-2"></i>Actualizar Estudiante
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Maestro -->
<div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Editar Maestro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTeacherForm">
                    <input type="hidden" name="id" id="edit_teacher_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Maestro *</label>
                                <input type="text" class="form-control" name="codigo_maestro" id="edit_teacher_codigo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="edit_teacher_email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" id="edit_teacher_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido *</label>
                                <input type="text" class="form-control" name="apellido" id="edit_teacher_apellido" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" id="edit_teacher_telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Especialidad *</label>
                                <input type="text" class="form-control" name="especialidad" id="edit_teacher_especialidad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Contratación</label>
                                <input type="date" class="form-control" name="fecha_contratacion" id="edit_teacher_fecha_contratacion">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Salario</label>
                                <input type="number" class="form-control" name="salario" id="edit_teacher_salario" step="0.01">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRecord('maestro')">
                    <i class="fas fa-save me-2"></i>Actualizar Maestro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Asignatura -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-book me-2"></i>Editar Asignatura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSubjectForm">
                    <input type="hidden" name="id" id="edit_subject_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Asignatura *</label>
                                <input type="text" class="form-control" name="codigo_asignatura" id="edit_subject_codigo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semestre *</label>
                                <select class="form-control" name="semestre" id="edit_subject_semestre" required>
                                    <option value="">Seleccionar semestre</option>
                                    <option value="1">1° Semestre</option>
                                    <option value="2">2° Semestre</option>
                                    <option value="3">3° Semestre</option>
                                    <option value="4">4° Semestre</option>
                                    <option value="5">5° Semestre</option>
                                    <option value="6">6° Semestre</option>
                                    <option value="7">7° Semestre</option>
                                    <option value="8">8° Semestre</option>
                                    <option value="9">9° Semestre</option>
                                    <option value="10">10° Semestre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Asignatura *</label>
                                <input type="text" class="form-control" name="nombre" id="edit_subject_nombre" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="edit_subject_descripcion" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Créditos *</label>
                                <input type="number" class="form-control" name="creditos" id="edit_subject_creditos" min="1" max="10" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Horas Semanales *</label>
                                <input type="number" class="form-control" name="horas_semanales" id="edit_subject_horas" min="1" max="20" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRecord('asignatura')">
                    <i class="fas fa-save me-2"></i>Actualizar Asignatura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Asignación -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2"></i>Editar Asignación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAssignmentForm">
                    <input type="hidden" name="id" id="edit_assignment_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maestro *</label>
                                <select class="form-control" name="maestro_id" id="edit_assignment_maestro" required>
                                    <option value="">Seleccionar maestro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asignatura *</label>
                                <select class="form-control" name="asignatura_id" id="edit_assignment_asignatura" required>
                                    <option value="">Seleccionar asignatura</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Período *</label>
                                <select class="form-control" name="periodo" id="edit_assignment_periodo" required>
                                    <option value="">Seleccionar período</option>
                                    <option value="2024-1">2024-1</option>
                                    <option value="2024-2">2024-2</option>
                                    <option value="2025-1">2025-1</option>
                                    <option value="2025-2">2025-2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Año *</label>
                                <input type="number" class="form-control" name="año" id="edit_assignment_año" min="2024" max="2030" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Horario</label>
                                <input type="text" class="form-control" name="horario" id="edit_assignment_horario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Aula</label>
                                <input type="text" class="form-control" name="aula" id="edit_assignment_aula">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRecord('asignacion')">
                    <i class="fas fa-save me-2"></i>Actualizar Asignación
                </button>
            </div>
        </div>
    </div>
</div>

<iframe name="hiddenFrame" style="display:none;"></iframe>

<script>
// Variables globales
let currentSection = 'dashboard';

function showSection(sectionName) {
    // Ocultar todas las secciones
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remover clase active de todos los enlaces
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
    });
    
    // Mostrar la sección seleccionada
    document.getElementById(sectionName + '-section').classList.add('active');
    document.getElementById(sectionName + '-link').classList.add('active');
    
    currentSection = sectionName;
    
    // Cargar datos según la sección
    if (sectionName !== 'dashboard') {
        loadTableData(sectionName);
    }
}

async function loadTableData(tableName) {
    try {
        showTableLoading(tableName);
        
        const response = await fetch(`../api/get_${tableName}.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        updateTable(tableName, data);
        
    } catch (error) {
        console.error('Error loading table data:', error);
        showTableError(tableName, error.message);
    }
}

function showTableLoading(tableName) {
    const tableBody = document.querySelector(`#${tableName}Table tbody`);
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
        `;
    }
}

function showTableError(tableName, errorMessage) {
    const tableBody = document.querySelector(`#${tableName}Table`);
    if (tableBody) {
        const tbody = tableBody.querySelector('tbody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fs-2 mb-2"></i>
                        <div>Error al cargar los datos</div>
                        <small>${errorMessage}</small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="loadTableData('${tableName}')">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
}

function updateTable(tableName, data) {
    const tableBody = document.querySelector(`#${tableName}Table tbody`);
    if (!tableBody) {
        console.error(`No se encontró la tabla para ${tableName}`);
        return;
    }

    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fs-2 mb-2"></i>
                    <div>No hay datos disponibles</div>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = '';

    data.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = generateTableRow(tableName, row);
        tr.style.opacity = '0';
        tr.style.transform = 'translateY(20px)';
        tableBody.appendChild(tr);

        // Animación de entrada escalonada
        setTimeout(() => {
            tr.style.transition = 'all 0.3s ease';
            tr.style.opacity = '1';
            tr.style.transform = 'translateY(0)';
        }, index * 50);
    });
}

function generateTableRow(tableName, row) {
    const actions = `
        <button class="btn btn-sm btn-outline-primary me-1" onclick="editRecord('${tableName}', ${row.id})" title="Editar">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord('${tableName}', ${row.id})" title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    `;

    switch(tableName) {
        case 'estudiantes':
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
                <td>${formatDate(row.fecha_registro)}</td>
                <td>${actions}</td>
            `;

        case 'maestros':
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
                <td class="fw-semibold text-success">$${formatNumber(row.salario)}</td>
                <td>${actions}</td>
            `;

        case 'asignaturas':
            return `
                <td><span class="badge bg-warning text-dark">${row.codigo_asignatura}</span></td>
                <td class="fw-semibold">${row.nombre}</td>
                <td><span class="badge bg-secondary">${row.creditos}</span></td>
                <td>${row.horas_semanales}h</td>
                <td><span class="badge bg-primary">${row.semestre}° Semestre</span></td>
                <td>${actions}</td>
            `;

        case 'asignaciones':
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
            `;

        default:
            return '<td colspan="100%">Tipo de tabla no reconocido</td>';
    }
}

// Función para editar registros
async function editRecord(table, id) {
    try {
        // Obtener los datos del registro
        const response = await fetch(`../api/get_record.php?table=${table}&id=${id}`);
        const data = await response.json();
        
        if (!data.success) {
            showNotification('Error', data.message, 'danger');
            return;
        }
        
        // Llenar el modal de edición con los datos
        populateEditModal(table, data.data);
        
        // Mostrar el modal correspondiente
        const modalMap = {
            'estudiantes': 'editStudentModal',
            'maestros': 'editTeacherModal',
            'asignaturas': 'editSubjectModal',
            'asignaciones': 'editAssignmentModal'
        };
        
        const modal = new bootstrap.Modal(document.getElementById(modalMap[table]));
        modal.show();
        
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error', 'Error al cargar los datos del registro', 'danger');
    }
}

// Función para llenar los modales de edición
function populateEditModal(table, data) {
    switch(table) {
        case 'estudiantes':
            document.getElementById('edit_student_id').value = data.id;
            document.getElementById('edit_student_codigo').value = data.codigo_estudiante;
            document.getElementById('edit_student_nombre').value = data.nombre;
            document.getElementById('edit_student_apellido').value = data.apellido;
            document.getElementById('edit_student_email').value = data.email;
            document.getElementById('edit_student_telefono').value = data.telefono || '';
            document.getElementById('edit_student_fecha_nacimiento').value = data.fecha_nacimiento || '';
            document.getElementById('edit_student_direccion').value = data.direccion || '';
            document.getElementById('edit_student_password').value = ''; // Siempre vacío por seguridad
            break;
            
        case 'maestros':
            document.getElementById('edit_teacher_id').value = data.id;
            document.getElementById('edit_teacher_codigo').value = data.codigo_maestro;
            document.getElementById('edit_teacher_nombre').value = data.nombre;
            document.getElementById('edit_teacher_apellido').value = data.apellido;
            document.getElementById('edit_teacher_email').value = data.email;
            document.getElementById('edit_teacher_telefono').value = data.telefono || '';
            document.getElementById('edit_teacher_especialidad').value = data.especialidad;
            document.getElementById('edit_teacher_fecha_contratacion').value = data.fecha_contratacion || '';
            document.getElementById('edit_teacher_salario').value = data.salario || '';
            break;
            
        case 'asignaturas':
            document.getElementById('edit_subject_id').value = data.id;
            document.getElementById('edit_subject_codigo').value = data.codigo_asignatura;
            document.getElementById('edit_subject_nombre').value = data.nombre;
            document.getElementById('edit_subject_descripcion').value = data.descripcion || '';
            document.getElementById('edit_subject_creditos').value = data.creditos;
            document.getElementById('edit_subject_horas').value = data.horas_semanales;
            document.getElementById('edit_subject_semestre').value = data.semestre;
            break;
            
        case 'asignaciones':
            document.getElementById('edit_assignment_id').value = data.id;
            document.getElementById('edit_assignment_periodo').value = data.periodo;
            document.getElementById('edit_assignment_año').value = data.año;
            document.getElementById('edit_assignment_horario').value = data.horario || '';
            document.getElementById('edit_assignment_aula').value = data.aula || '';
            
            // Cargar maestros y asignaturas para los selects
            loadEditSelectOptions(data.maestro_id, data.asignatura_id);
            break;
    }
}

// Función para cargar opciones en los selects de edición
async function loadEditSelectOptions(maestroId, asignaturaId) {
    try {
        // Cargar maestros
        const maestrosResponse = await fetch('../api/get_maestros_select.php');
        const maestros = await maestrosResponse.json();
        const maestroSelect = document.getElementById('edit_assignment_maestro');
        
        maestroSelect.innerHTML = '<option value="">Seleccionar maestro</option>';
        maestros.forEach(maestro => {
            const selected = maestro.id == maestroId ? 'selected' : '';
            maestroSelect.innerHTML += `<option value="${maestro.id}" ${selected}>${maestro.nombre_completo}</option>`;
        });

        // Cargar asignaturas
        const asignaturasResponse = await fetch('../api/get_asignaturas_select.php');
        const asignaturas = await asignaturasResponse.json();
        const asignaturaSelect = document.getElementById('edit_assignment_asignatura');
        
        asignaturaSelect.innerHTML = '<option value="">Seleccionar asignatura</option>';
        asignaturas.forEach(asignatura => {
            const selected = asignatura.id == asignaturaId ? 'selected' : '';
            asignaturaSelect.innerHTML += `<option value="${asignatura.id}" ${selected}>${asignatura.nombre}</option>`;
        });
    } catch (error) {
        console.error('Error cargando opciones:', error);
    }
}

// Función para actualizar registros
async function updateRecord(type) {
    const formMap = {
        'estudiante': 'editStudentForm',
        'maestro': 'editTeacherForm',
        'asignatura': 'editSubjectForm',
        'asignacion': 'editAssignmentForm'
    };
    
    const form = document.getElementById(formMap[type]);
    
    if (!form) {
        showNotification('Error', 'Formulario no encontrado', 'danger');
        return;
    }

    // Validar campos requeridos
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        showNotification('Error', 'Por favor complete todos los campos obligatorios', 'warning');
        return;
    }

    // Recopilar datos del formulario
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`../api/update_${type}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Éxito', result.message, 'success');
            
            // Cerrar modal
            const modalMap = {
                'estudiante': 'editStudentModal',
                'maestro': 'editTeacherModal',
                'asignatura': 'editSubjectModal',
                'asignacion': 'editAssignmentModal'
            };
            
            const modal = bootstrap.Modal.getInstance(document.getElementById(modalMap[type]));
            if (modal) {
                modal.hide();
            }
            
            // Recargar datos
            const sectionMap = {
                'estudiante': 'estudiantes',
                'maestro': 'maestros',
                'asignatura': 'asignaturas',
                'asignacion': 'asignaciones'
            };
            
            if (currentSection === sectionMap[type]) {
                loadTableData(currentSection);
            }
            
        } else {
            showNotification('Error', result.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error', 'Error de conexión', 'danger');
    }
}

async function deleteRecord(table, id) {
    if (!confirm('¿Está seguro de que desea eliminar este registro?')) {
        return;
    }

    try {
        const response = await fetch('../api/delete_record.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({table: table, id: id})
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Éxito', 'Registro eliminado correctamente', 'success');
            loadTableData(table);
        } else {
            showNotification('Error', data.message || 'Error al eliminar el registro', 'danger');
        }
    } catch (error) {
        showNotification('Error', 'Error de conexión', 'danger');
    }
}

// Función simplificada para crear registros
function createRecord(type) {
    const formMap = {
        'estudiante': 'addStudentForm',
        'maestro': 'addTeacherForm', 
        'asignatura': 'addSubjectForm',
        'asignacion': 'addAssignmentForm'
    };
    
    const form = document.getElementById(formMap[type]);
    
    if (!form) {
        alert('Formulario no encontrado');
        return;
    }

    // Validar campos requeridos
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }

    // Enviar formulario
    form.submit();
    
    // Cerrar modal después de un breve delay
    setTimeout(() => {
        const modalMap = {
            'estudiante': 'addStudentModal',
            'maestro': 'addTeacherModal',
            'asignatura': 'addSubjectModal', 
            'asignacion': 'addAssignmentModal'
        };
        
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalMap[type]));
        if (modal) {
            modal.hide();
            form.reset();
        }
        
        // Recargar datos si estamos en la sección correspondiente
        const sectionMap = {
            'estudiante': 'estudiantes',
            'maestro': 'maestros',
            'asignatura': 'asignaturas',
            'asignacion': 'asignaciones'
        };
        
        if (currentSection === sectionMap[type]) {
            setTimeout(() => loadTableData(currentSection), 1000);
        }
    }, 1000);
}

// Utilidades
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatNumber(number) {
    return parseFloat(number).toLocaleString('es-MX');
}

function showNotification(title, message, type = 'info') {
    // Crear notificación toast
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong><br>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Agregar al contenedor de toasts
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remover del DOM después de que se oculte
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Cargar datos para los selects de asignaciones
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para las tarjetas de estadísticas
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Cargar maestros y asignaturas cuando se abra el modal de asignaciones
    const assignmentModal = document.getElementById('addAssignmentModal');
    if (assignmentModal) {
        assignmentModal.addEventListener('show.bs.modal', async function() {
            try {
                // Cargar maestros
                const maestrosResponse = await fetch('../api/get_maestros_select.php');
                const maestros = await maestrosResponse.json();
                const maestroSelect = document.querySelector('#addAssignmentForm select[name="maestro_id"]');
                
                maestroSelect.innerHTML = '<option value="">Seleccionar maestro</option>';
                maestros.forEach(maestro => {
                    maestroSelect.innerHTML += `<option value="${maestro.id}">${maestro.nombre_completo}</option>`;
                });

                // Cargar asignaturas
                const asignaturasResponse = await fetch('../api/get_asignaturas_select.php');
                const asignaturas = await asignaturasResponse.json();
                const asignaturaSelect = document.querySelector('#addAssignmentForm select[name="asignatura_id"]');
                
                asignaturaSelect.innerHTML = '<option value="">Seleccionar asignatura</option>';
                asignaturas.forEach(asignatura => {
                    asignaturaSelect.innerHTML += `<option value="${asignatura.id}">${asignatura.nombre}</option>`;
                });
            } catch (error) {
                console.error('Error cargando datos:', error);
            }
        });
    }
});
</script>
</body>
</html>
