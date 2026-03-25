<?php
require_once '../admin/includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $stmt = $pdo->query("SELECT * FROM comunicados ORDER BY fecha_publicacion DESC");
    $comunicados = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $comunicados]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data']);
}
?>
