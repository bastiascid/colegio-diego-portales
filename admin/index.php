<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Contar comunicados
$stmt = $pdo->query("SELECT COUNT(*) FROM comunicados");
$total_comunicados = $stmt->fetchColumn();

// Contar documentos
$stmt = $pdo->query("SELECT COUNT(*) FROM documentos");
$total_documentos = $stmt->fetchColumn();
?>

<div class="card">
    <h2>Bienvenido al Panel de Administración</h2>
    <p style="margin-top: 10px; color: #4a5568;">
        Desde aquí puedes agregar, editar o eliminar los comunicados y documentos que se muestran en el sitio web del colegio.
    </p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <div class="card" style="border-left: 4px solid #4facfe;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="color: #4a5568; font-size: 1rem;">Total Comunicados</h3>
                <p style="font-size: 2rem; font-weight: bold; margin-top: 10px;"><?= $total_comunicados ?></p>
            </div>
            <i class="fas fa-bullhorn" style="font-size: 3rem; color: #e2e8f0;"></i>
        </div>
        <a href="comunicados.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%; text-align: center;">Gestionar Comunicados</a>
    </div>

    <div class="card" style="border-left: 4px solid #f5576c;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="color: #4a5568; font-size: 1rem;">Total Reg. y Guías</h3>
                <p style="font-size: 2rem; font-weight: bold; margin-top: 10px;"><?= $total_documentos ?></p>
            </div>
            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #e2e8f0;"></i>
        </div>
        <a href="documentos.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%; text-align: center;">Gestionar Doc/Guías</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
