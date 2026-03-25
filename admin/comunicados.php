<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM comunicados WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $success = "Comunicado eliminado correctamente.";
    } else {
        $error = "Error al eliminar.";
    }
    $action = 'list';
}

// Handle Form Submission (Create/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($action, ['add', 'edit'])) {
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $fecha_publicacion = $_POST['fecha_publicacion'] ?? '';
    $mes_anio_tag = $_POST['mes_anio_tag'] ?? '';
    $color_gradient = $_POST['color_gradient'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

    if (empty($titulo) || empty($contenido) || empty($fecha_publicacion) || empty($mes_anio_tag)) {
        $error = "Todos los campos obligatorios deben ser completados.";
    } else {
        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO comunicados (titulo, contenido, fecha_publicacion, mes_anio_tag, color_gradient) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $contenido, $fecha_publicacion, $mes_anio_tag, $color_gradient]);
            $success = "Comunicado añadido correctamente.";
        } else {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE comunicados SET titulo=?, contenido=?, fecha_publicacion=?, mes_anio_tag=?, color_gradient=? WHERE id=?");
            $stmt->execute([$titulo, $contenido, $fecha_publicacion, $mes_anio_tag, $color_gradient, $id]);
            $success = "Comunicado actualizado correctamente.";
        }
        $action = 'list';
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin:0;">Gestión de Comunicados</h2>
    <?php if ($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Comunicado</a>
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
    $stmt = $pdo->query("SELECT * FROM comunicados ORDER BY fecha_publicacion DESC");
    $comunicados = $stmt->fetchAll();
    ?>
    <div class="card table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tag (Mes/Año)</th>
                    <th>Fecha Pub.</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($comunicados as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><strong><?= htmlspecialchars($c['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($c['mes_anio_tag']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['fecha_publicacion'])) ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="?action=delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este comunicado?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($comunicados)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No hay comunicados registrados.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif (in_array($action, ['add', 'edit'])): ?>
    <?php
    $item = ['titulo'=>'', 'contenido'=>'', 'fecha_publicacion'=>date('Y-m-d'), 'mes_anio_tag'=>date('F Y'), 'color_gradient'=>'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'];
    if ($action == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM comunicados WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $item = $stmt->fetch() ?: $item;
    }
    // Traducir mes actual aprox si es nuevo
    if($action == 'add'){
        $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
        $item['mes_anio_tag'] = strtr(date('F'), $meses) . ' ' . date('Y');
    }
    ?>
    <div class="card">
        <h3><?= $action == 'add' ? 'Añadir Nuevo' : 'Editar' ?> Comunicado</h3>
        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <form method="POST" action="?action=<?= $action ?>">
            <?php if($action == 'edit'): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Título del Comunicado *</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($item['titulo']) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Fecha de Publicación (Real) *</label>
                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($item['fecha_publicacion']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Etiqueta/Tag Visual (Ej: Enero 2026) *</label>
                    <input type="text" name="mes_anio_tag" class="form-control" value="<?= htmlspecialchars($item['mes_anio_tag']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Color/Gradiente de Fondo de la Tarjeta</label>
                <select name="color_gradient" class="form-control">
                    <option value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'?'selected':'' ?>>Morado</option>
                    <option value="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'?'selected':'' ?>>Rosa/Rojo</option>
                    <option value="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'?'selected':'' ?>>Azul Claro</option>
                    <option value="linear-gradient(135deg, #fa709a 0%, #fee140 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #fa709a 0%, #fee140 100%)'?'selected':'' ?>>Naranja/Amarillo</option>
                    <option value="linear-gradient(135deg, #30cfd0 0%, #330867 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #30cfd0 0%, #330867 100%)'?'selected':'' ?>>Turquesa/Oscuro</option>
                    <option value="linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)" <?= $item['color_gradient']=='linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'?'selected':'' ?>>Pastel</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Contenido *</label>
                <textarea name="contenido" class="form-control" rows="8" required><?= htmlspecialchars($item['contenido']) ?></textarea>
                <small style="color: #718096; margin-top: 5px; display: block;">Puedes usar etiquetas HTML simples si necesitas dar formato (como &lt;br&gt; para saltos de línea o &lt;strong&gt; para negrita).</small>
            </div>

            <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">Guardar Comunicado</button>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
