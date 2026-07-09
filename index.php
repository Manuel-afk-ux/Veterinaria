<?php
// 1. INICIAR SESIÓN (Nuestra base de datos temporal en memoria)
session_start();

// 2. DATOS POR DEFECTO (Si la sesión está vacía, cargamos unos cuantos datos iniciales)
if (!isset($_SESSION['citas'])) {
    $_SESSION['citas'] = [
        ["id" => 1, "paciente" => "Max", "especie" => "Perro (Golden)", "dueno" => "Carlos Mendoza", "hora" => "09:30 AM", "motivo" => "Vacunación Anual", "estado" => "Confirmada"],
        ["id" => 2, "paciente" => "Luna", "especie" => "Gato (Persa)", "dueno" => "María Silva", "hora" => "10:45 AM", "motivo" => "Control de rutina", "estado" => "Pendiente"],
        ["id" => 3, "paciente" => "Rocky", "especie" => "Perro (Pug)", "dueno" => "Juan Pérez", "hora" => "02:00 PM", "motivo" => "Cirugía programada", "estado" => "Confirmada"]
    ];
    $_SESSION['siguiente_id'] = 4;
}

// 3. PROCESAR ACCIÓN: AGREGAR NUEVA CITA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $nueva_cita = [
        "id" => $_SESSION['siguiente_id']++,
        "paciente" => htmlspecialchars($_POST['paciente']),
        "especie" => htmlspecialchars($_POST['especie']),
        "dueno" => htmlspecialchars($_POST['dueno']),
        "hora" => htmlspecialchars($_POST['hora']),
        "motivo" => htmlspecialchars($_POST['motivo']),
        "estado" => htmlspecialchars($_POST['estado'])
    ];
    $_SESSION['citas'][] = $nueva_cita;
    header("Location: index.php"); // Recargar para limpiar envío de formulario
    exit;
}

// 4. PROCESAR ACCIÓN: ELIMINAR CITA
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    foreach ($_SESSION['citas'] as $key => $cita) {
        if ($cita['id'] == $id_eliminar) {
            unset($_SESSION['citas'][$key]);
            break;
        }
    }
    $_SESSION['citas'] = array_values($_SESSION['citas']); // Reindexar el array
    header("Location: index.php");
    exit;
}

// 5. CÁLCULO DE CONTADORES DINÁMICOS
$total_citas = count($_SESSION['citas']);
$pendientes = 0;
foreach ($_SESSION['citas'] as $c) {
    if ($c['estado'] == 'Pendiente') $pendientes++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetControl - Panel Operativo</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2c3e50; --bg: #f8f9fa; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg); }
        .main-content { margin-left: 270px; } /* Ajustado para que no se encima con el menú */
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .stat-card { transition: 0.2s; } .stat-card:hover { transform: translateY(-4px); }
    </style>
</head>
<body>

<!-- MENÚ LATERAL -->
<nav class="w3-sidebar w3-bar-block w3-collapse w3-card" style="width:250px; background-color:var(--primary; color:white; background:#2c3e50);" id="mySidebar">
  <div class="w3-container w3-padding-16 w3-center" style="background-color: #1a252f;">
    <h4 class="w3-margin-0"><b><i class="fa-solid fa-paw w3-text-teal"></i> VetControl</b></h4>
  </div>
  <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-teal"><i class="fa fa-th-large fa-fw w3-margin-right"></i>Dashboard</a>
  <button onclick="document.getElementById('modalCita').style.display='block'" class="w3-bar-item w3-button w3-padding-16 w3-text-aquamarine"><i class="fa fa-plus-circle fa-fw w3-margin-right"></i><b>Nueva Cita</b></button>
</nav>

<!-- CONTENIDO -->
<div class="main-content w3-padding-large">

    <!-- Header Móvil -->
    <header class="w3-container w3-top w3-hide-large w3-teal w3-xlarge w3-padding">
        <button class="w3-button w3-teal w3-left" onclick="w3_open()"><i class="fa fa-bars"></i></button>
        <span>VetControl</span>
    </header>
    <div class="w3-hide-large" style="margin-top:80px"></div>

    <!-- Título -->
    <div class="w3-container w3-padding-12">
        <h2><b>Panel Control Veterinario</b></h2>
        <p class="w3-text-grey">Gestión interactiva en tiempo real (Simulando persistencia de datos).</p>
    </div>

    <!-- CONTADORES DINÁMICOS -->
    <div class="w3-row-padding w3-margin-bottom">
        <div class="w3-third w3-margin-bottom">
            <div class="w3-container w3-teal w3-padding-16 w3-card w3-round stat-card">
                <div class="w3-left"><i class="fa fa-calendar-check w3-xxxlarge"></i></div>
                <div class="w3-right"><h3><?php echo $total_citas; ?></h3></div>
                <div class="w3-clear"></div>
                <h4>Total Citas Hoy</h4>
            </div>
        </div>
        <div class="w3-third w3-margin-bottom">
            <div class="w3-container w3-orange w3-text-white w3-padding-16 w3-card w3-round stat-card">
                <div class="w3-left"><i class="fa fa-clock w3-xxxlarge"></i></div>
                <div class="w3-right"><h3><?php echo $pendientes; ?></h3></div>
                <div class="w3-clear"></div>
                <h4>Citas Pendientes</h4>
            </div>
        </div>
        <div class="w3-third w3-margin-bottom">
            <div class="w3-container w3-blue w3-padding-16 w3-card w3-round stat-card">
                <div class="w3-left"><i class="fa fa-heartpulse w3-xxxlarge"></i></div>
                <div class="w3-right"><h3>Activo</h3></div>
                <div class="w3-clear"></div>
                <h4>Estado del Servidor</h4>
            </div>
        </div>
    </div>

    <!-- TABLA DE CITAS OPERATIVA -->
    <div class="w3-container w3-white w3-card w3-round w3-padding-large">
        <div class="w3-row">
            <div class="w3-col m8">
                <h3><i class="fa fa-list w3-text-teal w3-margin-right"></i>Monitoreo de Citas Médicas</h3>
            </div>
            <div class="w3-col m4 w3-right-align w3-padding-16">
                <button onclick="document.getElementById('modalCita').style.display='block'" class="w3-button w3-teal w3-round"><i class="fa fa-plus"></i> Agendar Cita</button>
            </div>
        </div>
        
        <div class="w3-responsive">
            <table class="w3-table-all w3-hoverable">
                <thead>
                    <tr class="w3-teal">
                        <th>Paciente</th>
                        <th>Especie / Raza</th>
                        <th>Propietario</th>
                        <th>Hora</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th class="w3-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_SESSION['citas'])): ?>
                        <tr><td colspan="7" class="w3-center w3-text-grey">No hay citas registradas para hoy.</td></tr>
                    <?php else: ?>
                        <?php foreach ($_SESSION['citas'] as $cita): ?>
                        <tr>
                            <td><b><?php echo $cita['paciente']; ?></b></td>
                            <td><?php echo $cita['especie']; ?></td>
                            <td><?php echo $cita['dueno']; ?></td>
                            <td><?php echo $cita['hora']; ?></td>
                            <td><?php echo $cita['motivo']; ?></td>
                            <td>
                                <?php 
                                if ($cita['estado'] == 'Confirmada') echo '<span class="w3-tag w3-green w3-round">Confirmada</span>';
                                elseif ($cita['estado'] == 'Pendiente') echo '<span class="w3-tag w3-orange w3-text-white w3-round">Pendiente</span>';
                                else echo '<span class="w3-tag w3-red w3-round">Cancelada</span>';
                                ?>
                            </td>
                            <td class="w3-center">
                                <a href="index.php?eliminar=<?php echo $cita['id']; ?>" class="w3-button w3-red w3-padding-small w3-round" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL / FORMULARIO FLOTANTE PARA AGREGAR CITA -->
<div id="modalCita" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-round" style="max-width:600px">
        <header class="w3-container w3-teal w3-round-top"> 
            <span onclick="document.getElementById('modalCita').style.display='none'" class="w3-button w3-display-topright w3-round-top-right">&times;</span>
            <h2><i class="fa fa-paw"></i> Registrar Nueva Cita</h2>
        </header>
        <form class="w3-container w3-padding-16" action="index.php" method="POST">
            <input type="hidden" name="accion" value="agregar">
            
            <div class="w3-row-padding">
                <div class="w3-half w3-margin-bottom">
                    <label><b>Nombre de la Mascota:</b></label>
                    <input class="w3-input w3-border w3-round" type="text" name="paciente" required placeholder="Ej. Toby">
                </div>
                <div class="w3-half w3-margin-bottom">
                    <label><b>Especie / Raza:</b></label>
                    <input class="w3-input w3-border w3-round" type="text" name="especie" required placeholder="Ej. Perro (Caniche)">
                </div>
            </div>

            <div class="w3-row-padding">
                <div class="w3-half w3-margin-bottom">
                    <label><b>Nombre del Dueño:</b></label>
                    <input class="w3-input w3-border w3-round" type="text" name="dueno" required placeholder="Ej. Laura Pozo">
                </div>
                <div class="w3-half w3-margin-bottom">
                    <label><b>Hora de la Cita:</b></label>
                    <input class="w3-input w3-border w3-round" type="text" name="hora" required placeholder="Ej. 04:00 PM">
                </div>
            </div>

            <div class="w3-container w3-margin-bottom">
                <label><b>Motivo de Consulta:</b></label>
                <input class="w3-input w3-border w3-round" type="text" name="motivo" required placeholder="Ej. Desparasitación u Operación">
            </div>

            <div class="w3-container w3-margin-bottom">
                <label><b>Estado Inicial:</b></label>
                <select class="w3-select w3-border w3-round" name="estado">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Confirmada">Confirmada</option>
                </select>
            </div>

            <footer class="w3-container w3-right-align w3-padding-16">
                <button type="button" onclick="document.getElementById('modalCita').style.display='none'" class="w3-button w3-red w3-round">Cancelar</button>
                <button type="submit" class="w3-button w3-teal w3-round">Guardar Cita</button>
            </footer>
        </form>
    </div>
</div>

<script>
function w3_open() {
  var sidebar = document.getElementById("mySidebar");
  sidebar.style.display = (sidebar.style.display === 'block') ? 'none' : 'block';
}
</script>
</body>
</html>