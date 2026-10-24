<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $clienteId = $_GET['id'] ?? null;

    if (!$clienteId) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do cliente não informado.']);
        exit;
    }

    // Busca as reservas com LEFT JOIN para aceitar reservas sem mesa vinculada
    $sql = '
        SELECT 
            r.Horario AS horario,
            r.Data AS data,
            r.QTD_pessoas AS quantidade_pessoas,
            COALESCE(m.Numero_mesa, "S/N") AS mesa,
            r.Status AS status
        FROM reserva r
        LEFT JOIN mesa m ON r.FK_Mesa_ID_Mesa = m.ID_mesa
        WHERE r.FK_Cliente_ID_cliente = :id
        ORDER BY r.Data DESC
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $clienteId]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estrutura a resposta JSON em um único objeto válido
    echo json_encode([
        'sucesso' => true,
        'total_reservas' => count($reservas),
        'reservas' => $reservas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar reservas']);
}
exit;
?>