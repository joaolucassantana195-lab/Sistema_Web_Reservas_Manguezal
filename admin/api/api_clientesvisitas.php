<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();
header('Content-Type: application/json');

try {
    
    $clienteId = $_GET['id'] ?? null;

    if (!$clienteId) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do cliente não fornecido.']);
        exit;
    }

   
    $stmt = $pdo->prepare('
        SELECT COUNT(*) AS total_linhas
        FROM reserva r
        INNER JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_cliente
        WHERE c.ID_cliente = :cliente_id AND r.Status IN ("Concluída", "Em Atendimento")
    ');

    $stmt->execute([':cliente_id' => $clienteId]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao contar as reservas!']);
}
?>