<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM documentos WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $success = "Documento eliminado correctamente.";
    } else {
        $error = "Error al eliminar.";
    }
    $action = 'list';
}

// Handle Form Submission (Create/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($action, ['add', 'edit'])) {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo = $_POST['tipo'] ?? 'reglamento'; // reglamento, guia_preb, guia_basica
    $nivel = $_POST['nivel'] ?? ''; 
    $enlace = $_POST['enlace'] ?? '';
    $icono = $_POST['icono'] ?? 'fas fa-file-alt';
    $color_gradient = $_POST['color_gradient'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

    if (empty($titulo) || empty($enlace)) {
        $error = "El título y el enlace son obligatorios.";
    } else {
        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO documentos (titulo, descripcion, tipo, nivel, enlace, icono, color_gradient) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $tipo, ($nivel !== '' ? $nivel : null), $enlace, $icono, $color_gradient]);
            $success = "Registro añadido correctamente.";
        } else {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE documentos SET titulo=?, descripcion=?, tipo=?, nivel=?, enlace=?, icono=?, color_gradient=? WHERE id=?");
            $stmt->execute([$titulo, $descripcion, $tipo, ($nivel !== '' ? $nivel : null), $enlace, $icono, $color_gradient, $id]);
            $success = "Registro actualizado correctamente.";
        }
        $action = 'list';
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin:0;">Gestión de Reglamentos y Guías</h2>
    <?php if ($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Registro</a>
    <?php else: ?>
        <a href="?action=list" class="btn" style="background: #e2e8f0; color: #4a5568;"><i class="fas fa-arrow-left"></i> Volver</a>
    <?php endif; ?>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <?php
    $stmt = $pdo->query("SELECT * FROM documentos ORDER BY tipo DESC, id ASC");
    $documentos = $stmt->fetchAll();
    ?>
    <div class="card table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Nivel / Curso</th>
                    <th>Título</th>
                    <th>Enlace</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($documentos as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td>
                        <?php 
                        if($d['tipo']=='reglamento') echo 'Reglamento';
                        if($d['tipo']=='guia_preb') echo 'Guía Prebásica';
                        if($d['tipo']=='guia_basica') echo 'Guía Básica';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($d['nivel'] ?? '-') ?></td>
                    <td><strong><?= htmlspecialchars($d['titulo']) ?></strong></td>
                    <td><a href="<?= htmlspecialchars($d['enlace']) ?>" target="_blank" style="color:var(--primary-light);">Ver Link</a></td>
                    <td>
                        <a href="?action=edit&id=<?= $d['id'] ?>" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="?action=delete&id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este registro?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($documentos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay documentos registrados.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif (in_array($action, ['add', 'edit'])): ?>
    <?php
    $item = [
        'titulo'=>'', 'descripcion'=>'', 'tipo'=>'reglamento', 'nivel'=>'', 'enlace'=>'#', 
        'icono'=>'fas fa-file-alt', 'color_gradient'=>'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ];
    if ($action == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $item = $stmt->fetch() ?: $item;
    }
    ?>
    <div class="card">
        <h3><?= $action == 'add' ? 'Añadir Nuevo' : 'Editar' ?> Registro</h3>
        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <form method="POST" action="?action=<?= $action ?>">
            <?php if($action == 'edit'): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Tipo de Sección *</label>
                    <select name="tipo" class="form-control" id="tipoSelect" onchange="toggleNivel()">
                        <option value="reglamento" <?= $item['tipo']=='reglamento'?'selected':'' ?>>Documento Institucional / Reglamento</option>
                        <option value="guia_preb" <?= $item['tipo']=='guia_preb'?'selected':'' ?>>Guía de Educación Parvularia</option>
                        <option value="guia_basica" <?= $item['tipo']=='guia_basica'?'selected':'' ?>>Guía de Educación Básica</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nivel / Curso (Solo para guías)</label>
                    <input type="text" name="nivel" id="nivelInput" class="form-control" value="<?= htmlspecialchars($item['nivel'] ?? '') ?>" placeholder="Ej: 1º Básico (Opcional)">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Título *</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($item['titulo']) ?>" placeholder="Ej: Reglamento Interno o 1º Básico" required>
            </div>

            <div class="form-group">
                <label class="form-label">Enlace URL *</label>
                <input type="url" name="enlace" class="form-control" value="<?= htmlspecialchars($item['enlace']) ?>" placeholder="Enlace al PDF o carpeta de Google Drive" required>
                <small style="color: #718096; margin-top: 5px; display: block;">Debes copiar y pegar aquí el enlace de Google Drive del documento o carpeta.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción Breve</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($item['descripcion']) ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Icono (FontAwesome)</label>
                    <input type="text" name="icono" class="form-control" value="<?= htmlspecialchars($item['icono']) ?>" placeholder="fas fa-file-alt">
                </div>
                <div class="form-group">
                    <label class="form-label">Color/Gradiente Visual</label>
                    <select name="color_gradient" class="form-control">
                        <option value="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'?'selected':'' ?>>Azul Claro (Básica 1,3,7)</option>
                        <option value="linear-gradient(135deg, #fa709a 0%, #fee140 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #fa709a 0%, #fee140 100%)'?'selected':'' ?>>Naranja/Amarillo (Básica 2,8)</option>
                        <option value="linear-gradient(135deg, #30cfd0 0%, #330867 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #30cfd0 0%, #330867 100%)'?'selected':'' ?>>Turquesa/Oscuro (Básica 3)</option>
                        <option value="linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'?'selected':'' ?>>Pastel (Básica 4)</option>
                        <option value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'?'selected':'' ?>>Morado (Reglamento / Pre-K / Básico 5)</option>
                        <option value="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'?'selected':'' ?>>Rosa/Rojo (Reglamento / Kinder / Básico 6)</option>
                        <option value="var(--gradient-primary)" <?= $item['color_gradient']=='var(--gradient-primary)'?'selected':'' ?>>Gradiente Primario Cole (Reglamento)</option>
                        <option value="var(--gradient-secondary)" <?= $item['color_gradient']=='var(--gradient-secondary)'?'selected':'' ?>>Gradiente Secundario Cole (Reglamento)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">Guardar Registro</button>
        </form>
    </div>
    
    <script>
    function toggleNivel() {
        var tipo = document.getElementById('tipoSelect').value;
        var nivelInput = document.getElementById('nivelInput');
        if (tipo === 'reglamento') {
            // No necesita nivel
            nivelInput.value = '';
            nivelInput.parentElement.style.opacity = '0.5';
        } else {
            nivelInput.parentElement.style.opacity = '1';
        }
    }
    document.addEventListener('DOMContentLoaded', toggleNivel);
    </script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
