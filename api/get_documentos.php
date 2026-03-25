<?php
require_once '../admin/includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $tipo = $_GET['tipo'] ?? null;
    $query = "SELECT * FROM documentos";
    $params = [];

    if ($tipo) {
        $query .= " WHERE tipo = ?";
        $params[] = $tipo;
    }

    $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $documentos = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $documentos]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data']);
}
?>
