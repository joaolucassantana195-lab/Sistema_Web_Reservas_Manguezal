<?php
ob_start();
require_once __DIR__ . '/../../config/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

$numero = $dados['numero'] ?? null;
$st     = $dados['status'] ?? null;
$local  = $dados['local'] ?? null;

$dataHoje = date('Y=m=d');

$where  = [];
$params = [
    ':dataHoje' => $dataHoje
];

// Filtros dinâmicos
if (!empty($local) && in_array($local, ['beach', 'deck', 'salão'], true)) {
    $where[] = 'm.localizacao = :local';
    $params[':local'] = $local;
}

if (!empty($numero)) {
    $where[] = 'm.Numero_mesa = :numero';
    $params[':numero'] = $numero;
}

if (!empty($st) && in_array($st, ['Livre', 'Ocupada'], true)) {
    $where[] = 'm.Status = :status';
    $params[':status'] = $st;
}else if (!empty($st) && in_array($st, ['Reservada'], true)){
    $where[] = 'r_pendente.FK_Mesa_ID_Mesa = ID_mesa';
}

try {
    $sql = "
        SELECT 
            m.Numero_mesa AS mesa_numero,
            m.ID_mesa AS mesa_id,
            m.Capacidade_max AS mesa_capacidade,
            m.Status AS mesa_status,
            m.ativa AS mesa_ativa,
            m.tipo_ocupacao AS mesa_tipo_ocupacao,
            m.localizacao AS mesa_local,
            r_ativa.reserva_id AS reserva_id,
            r_pendente.reserva_id AS reserva_pendente_id,
            CASE 
                WHEN LOWER(m.Status) = 'ocupada' THEN 'Ocupada'
                WHEN LOWER(m.Status) = 'reservada' OR r_pendente.FK_Mesa_ID_Mesa IS NOT NULL OR r_ativa.FK_Mesa_ID_Mesa IS NOT NULL THEN 'Reservada'
                ELSE 'Livre'
            END AS ocupacao,
            COALESCE(c_ativo.Nome, c_pendente.Nome) AS cliente_nome
        FROM mesa m 
        
        LEFT JOIN (
            SELECT 
                FK_Mesa_ID_Mesa, 
                MAX(FK_Cliente_ID_cliente) AS FK_Cliente_ID_Cliente,
                MAX(ID_reserva) AS reserva_id
            FROM reserva 
            WHERE Data = :dataHoje AND Status = 'Em Atendimento'
            GROUP BY FK_Mesa_ID_Mesa
        ) r_ativa ON m.ID_mesa = r_ativa.FK_Mesa_ID_Mesa 
        
        LEFT JOIN (
            SELECT 
                FK_Mesa_ID_Mesa, 
                MAX(FK_Cliente_ID_cliente) AS FK_Cliente_ID_Cliente,
                MAX(ID_reserva) AS reserva_id
            FROM reserva 
            WHERE Data = :dataHoje AND Status = 'Confirmada'
            GROUP BY FK_Mesa_ID_Mesa
        ) r_pendente ON m.ID_mesa = r_pendente.FK_Mesa_ID_Mesa 
        
        LEFT JOIN cliente c_ativo ON r_ativa.FK_Cliente_ID_Cliente = c_ativo.ID_cliente
        LEFT JOIN cliente c_pendente ON r_pendente.FK_Cliente_ID_Cliente = c_pendente.ID_cliente
        WHERE m.ativa = TRUE
    ";

    // Adiciona o operador 'AND' antes de concatenar os filtros adicionais
    if (!empty($where)) {
        $sql .= ' AND ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY m.Numero_mesa ASC';

    $stmt = $pdo->prepare($sql);
    // Transmite os parâmetros acumulados para a query
    $stmt->execute($params);
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Limpa eventuais saídas acidentais antes do JSON
    ob_end_clean();
    echo json_encode($mesas);
    exit;

} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar mesas']);
    exit;
}