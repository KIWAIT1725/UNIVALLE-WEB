<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Estudiantil - Universidad del Valle</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --univalle-blue: #1e40af;
            --univalle-light-blue: #3b82f6;
        }

        body {
            background: linear-gradient(135deg, var(--univalle-blue) 0%, var(--univalle-light-blue) 100%);
            min-height: 100vh;
        }

        .student-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 2rem;
            padding: 3rem;
        }

        .welcome-card {
            background: linear-gradient(45deg, var(--univalle-blue), var(--univalle-light-blue));
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: white;
            border: 2px solid var(--univalle-light-blue);
            border-radius: 15px;
            padding: 2rem;
            text-decoration: none;
            color: var(--univalle-blue);
            transition: all 0.3s ease;
            display: block;
            height: 100%;
        }

        .action-btn:hover {
            background: var(--univalle-light-blue);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3);
        }

        .content-section {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .content-section.active {
            display: block;
        }

        .back-btn {
            background: var(--univalle-blue);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }

        .back-btn:hover {
            background: var(--univalle-light-blue);
            color: white;
        }

        .table-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .grade-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }

        .grade-excellent { background: #d4edda; color: #155724; }
        .grade-good { background: #d1ecf1; color: #0c5460; }
        .grade-regular { background: #fff3cd; color: #856404; }
        .grade-poor { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="student-container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-graduate fs-1 me-4"></i>
                    <div>
                        <h2 class="mb-1">¡Bienvenido, <?php echo $_SESSION['user_name']; ?>!</h2>
                        <p class="mb-0 opacity-75">Código: <?php echo $_SESSION['user_code']; ?></p>
                        <p class="mb-0 opacity-75">Portal Estudiantil - Universidad del Valle</p>
                    </div>
                    <div class="ms-auto">
                        <a href="../auth/logout.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Menu -->
            <div id="main-menu">
                <div class="row g-4">
                    <div class="col-md-4">
                        <button class="action-btn w-100" onclick="showSection('materias')">
                            <div class="text-center">
                                <i class="fas fa-book-open fs-1 mb-3"></i>
                                <h5>Mis Materias</h5>
                                <p class="text-muted mb-0">Consulta tus materias inscritas y detalles del curso</p>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="action-btn w-100" onclick="showSection('horarios')">
                            <div class="text-center">
                                <i class="fas fa-calendar-alt fs-1 mb-3"></i>
                                <h5>Horarios</h5>
                                <p class="text-muted mb-0">Revisa tu horario académico y ubicación de aulas</p>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="action-btn w-100" onclick="showSection('calificaciones')">
                            <div class="text-center">
                                <i class="fas fa-chart-line fs-1 mb-3"></i>
                                <h5>Calificaciones</h5>
                                <p class="text-muted mb-0">Consulta tus notas y rendimiento académico</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Materias Section -->
            <div id="materias-section" class="content-section">
                <button class="back-btn" onclick="showMainMenu()">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                </button>
                <h3><i class="fas fa-book-open me-2"></i>Mis Materias</h3>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Código</th>
                                    <th>Materia</th>
                                    <th>Créditos</th>
                                    <th>Profesor</th>
                                    <th>Horario</th>
                                    <th>Aula</th>
                                </tr>
                            </thead>
                            <tbody id="materias-tbody">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Cargando materias...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Horarios Section -->
            <div id="horarios-section" class="content-section">
                <button class="back-btn" onclick="showMainMenu()">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                </button>
                <h3><i class="fas fa-calendar-alt me-2"></i>Mi Horario Académico</h3>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>Materia</th>
                                    <th>Código</th>
                                    <th>Profesor</th>
                                    <th>Horario</th>
                                    <th>Aula</th>
                                    <th>Período</th>
                                </tr>
                            </thead>
                            <tbody id="horarios-tbody">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Cargando horarios...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Calificaciones Section -->
            <div id="calificaciones-section" class="content-section">
                <button class="back-btn" onclick="showMainMenu()">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                </button>
                <h3><i class="fas fa-chart-line me-2"></i>Mis Calificaciones</h3>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-warning">
                                <tr>
                                    <th>Código</th>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Nota 1</th>
                                    <th>Nota 2</th>
                                    <th>Nota 3</th>
                                    <th>Promedio</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="calificaciones-tbody">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Cargando calificaciones...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Promedio General</h5>
                                    <h2 class="text-primary" id="promedio-general">-</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Materias Aprobadas</h5>
                                    <h2 class="text-success" id="materias-aprobadas">-</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showSection(sectionName) {
            // Ocultar menú principal
            document.getElementById('main-menu').style.display = 'none';
            
            // Ocultar todas las secciones
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(sectionName + '-section').classList.add('active');
            
            // Cargar datos según la sección
            loadSectionData(sectionName);
        }

        function showMainMenu() {
            // Mostrar menú principal
            document.getElementById('main-menu').style.display = 'block';
            
            // Ocultar todas las secciones
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
        }

        async function loadSectionData(section) {
            try {
                const response = await fetch(`../api/get_student_${section}.php`);
                const data = await response.json();
                
                switch(section) {
                    case 'materias':
                        displayMaterias(data);
                        break;
                    case 'horarios':
                        displayHorarios(data);
                        break;
                    case 'calificaciones':
                        displayCalificaciones(data);
                        break;
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
                showError(section);
            }
        }

        function displayMaterias(materias) {
            const tbody = document.getElementById('materias-tbody');
            
            if (materias.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay materias inscritas</td></tr>';
                return;
            }

            tbody.innerHTML = materias.map(materia => `
                <tr>
                    <td><span class="badge bg-primary">${materia.codigo_asignatura}</span></td>
                    <td class="fw-semibold">${materia.nombre}</td>
                    <td><span class="badge bg-secondary">${materia.creditos} créditos</span></td>
                    <td>${materia.profesor}</td>
                    <td>${materia.horario || 'Por definir'}</td>
                    <td>${materia.aula || 'Por asignar'}</td>
                </tr>
            `).join('');
        }

        function displayHorarios(horarios) {
            const tbody = document.getElementById('horarios-tbody');
            
            if (horarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay horarios definidos</td></tr>';
                return;
            }

            tbody.innerHTML = horarios.map(horario => `
                <tr>
                    <td class="fw-semibold">${horario.materia}</td>
                    <td><span class="badge bg-info">${horario.codigo_asignatura}</span></td>
                    <td>${horario.profesor}</td>
                    <td><strong>${horario.horario}</strong></td>
                    <td><span class="badge bg-warning text-dark">${horario.aula}</span></td>
                    <td>${horario.periodo}</td>
                </tr>
            `).join('');
        }

        function displayCalificaciones(calificaciones) {
            const tbody = document.getElementById('calificaciones-tbody');
            
            if (calificaciones.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay calificaciones disponibles</td></tr>';
                return;
            }

            let totalPromedio = 0;
            let materiasAprobadas = 0;

            tbody.innerHTML = calificaciones.map(cal => {
                totalPromedio += cal.promedio;
                if (cal.promedio >= 3.0) materiasAprobadas++;

                const gradeClass = getGradeClass(cal.promedio);
                const estadoClass = cal.estado === 'Aprobado' ? 'text-success' : 'text-danger';

                return `
                    <tr>
                        <td><span class="badge bg-primary">${cal.codigo}</span></td>
                        <td class="fw-semibold">${cal.materia}</td>
                        <td>${cal.profesor}</td>
                        <td><span class="grade-badge ${getGradeClass(cal.nota1)}">${cal.nota1}</span></td>
                        <td><span class="grade-badge ${getGradeClass(cal.nota2)}">${cal.nota2}</span></td>
                        <td><span class="grade-badge ${getGradeClass(cal.nota3)}">${cal.nota3}</span></td>
                        <td><strong class="grade-badge ${gradeClass}">${cal.promedio}</strong></td>
                        <td><span class="fw-bold ${estadoClass}">${cal.estado}</span></td>
                    </tr>
                `;
            }).join('');

            // Actualizar estadísticas
            const promedioGeneral = (totalPromedio / calificaciones.length).toFixed(2);
            document.getElementById('promedio-general').textContent = promedioGeneral;
            document.getElementById('materias-aprobadas').textContent = `${materiasAprobadas}/${calificaciones.length}`;
        }

        function getGradeClass(nota) {
            if (nota >= 4.5) return 'grade-excellent';
            if (nota >= 4.0) return 'grade-good';
            if (nota >= 3.0) return 'grade-regular';
            return 'grade-poor';
        }

        function showError(section) {
            const tbody = document.getElementById(`${section}-tbody`);
            tbody.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los datos. Por favor, intente nuevamente.
                    </td>
                </tr>
            `;
        }

        // Animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const actionBtns = document.querySelectorAll('.action-btn');
            actionBtns.forEach((btn, index) => {
                btn.style.opacity = '0';
                btn.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    btn.style.transition = 'all 0.5s ease';
                    btn.style.opacity = '1';
                    btn.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>
