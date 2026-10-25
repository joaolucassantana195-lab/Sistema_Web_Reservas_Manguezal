<?php
require_once __DIR__ . '/../../config/conexao.php';

$dados = json_decode(file_get_contents('php://input'), true);
$data = $dados['data'] ?? null;
$st = $dados['st'] ?? null;
$nome = $dados['nome'] ?? null;

try {
    $where = [];
    $params = [];

    if ($data) {
        $where[] = 'DATE(r.Data) = :data';
        $params[':data'] = $data;
    }

    if (in_array($st, ['Aguardando', 'Confirmada', 'Cancelada', 'Concluída', 'Não compareceu', 'Em Atendimento'])) {
        $where[] = 'r.Status = :status';
        $params[':status'] = $st;
    }

    if ($nome) {
        $where[] = 'c.Nome LIKE :nome';
        $params[':nome'] = "%{$nome}%";
    }

    $sql = 'SELECT
        r.ID_reserva AS reserva_id,
        r.Horario AS reserva_horario,
        r.Data AS reserva_dia,
        r.QTD_pessoas AS reserva_qtd_pessoas,
        r.Status AS reserva_status,
        r.Observacoes AS reserva_observacoes,
        r.BEACH_CLUB AS reserva_beachclub,
        c.Nome AS cliente_nome,
        c.Telefone AS cliente_telefone,
        m.Numero_mesa AS mesa_numero,
        m.ID_mesa AS mesa_id
    FROM reserva r
    INNER JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_cliente
    LEFT JOIN mesa m ON r.FK_Mesa_ID_Mesa = m.ID_mesa';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY
    CASE r.Status
    WHEN "Em Atendimento" THEN 1
    WHEN "Confirmada" THEN 2
    WHEN "Aguardando" THEN 3
    WHEN "Concluída" THEN 4
    WHEN "Não compareceu" THEN 5
    WHEN "Cancelada" THEN 6
    ELSE 7
    END ASC,
    r.Data DESC, r.Horario ASC, c.Nome ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($reservas);
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'FAIL']);
}
?>
