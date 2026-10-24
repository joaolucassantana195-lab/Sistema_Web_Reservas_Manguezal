<?php
ob_start();
require_once __DIR__ . '/../../config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

$idMesa = $dados['id'] ?? null;
$novoStatus = $dados['status'] ?? null;
$tipo_ocupacao = $dados['tipo_ocupacao'] ?? null;

if (!$idMesa || !$novoStatus) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Prepara a query dinamicamente baseada na presença do tipo_ocupacao
    if (!empty($tipo_ocupacao)) {
        $stmt = $pdo->prepare('UPDATE mesa SET Status = :status, tipo_ocupacao = :tipo_ocupacao WHERE ID_mesa = :id');
        $stmt->bindParam(':tipo_ocupacao', $tipo_ocupacao);
    } else {
        $stmt = $pdo->prepare('UPDATE mesa SET Status = :status, tipo_ocupacao = DEFAULT WHERE ID_mesa = :id');
    }

    $stmt->bindParam(':status', $novoStatus);
    $stmt->bindParam(':id', $idMesa, PDO::PARAM_INT);
    $stmt->execute();

    $pdo->commit();

    echo json_encode(['sucesso' => true]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar status da mesa.']);
}
?>