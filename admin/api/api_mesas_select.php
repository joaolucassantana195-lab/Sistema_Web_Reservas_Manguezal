<?php
ob_start();
require_once __DIR__ . '/../../config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

// Recebe a data ou usa a data atual como padrão
$data = $_GET['data'] ?? date('Y-m-d');

try {
    $stmt = $pdo->prepare("
        SELECT 
            m.Numero_mesa AS mesa_numero,
            m.ID_mesa AS mesa_id,
            m.Capacidade_max AS mesa_capacidade,
            m.Status AS mesa_status,
            m.ativa AS mesa_ativa,
            m.localizacao AS mesa_local,
            CASE 
                WHEN LOWER(m.Status) = 'ocupada' THEN 'Ocupada'
                WHEN LOWER(m.Status) = 'reservada' OR r.FK_Mesa_ID_Mesa IS NOT NULL THEN 'Reservada'
                ELSE 'Livre'
            END AS ocupacao,
            c.Nome AS cliente_nome
        FROM mesa m 
        LEFT JOIN reserva r ON m.ID_mesa = r.FK_Mesa_ID_Mesa 
                            AND r.Data = :data
                            AND r.Status = 'Confirmada'
        LEFT JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_cliente
        WHERE m.ativa = TRUE
        GROUP BY m.ID_mesa
        ORDER BY m.Numero_mesa ASC
    ");

    $stmt->execute([':data' => $data]);
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mesas, JSON_UNESCAPED_UNICODE);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar mesas']);
    exit;
}