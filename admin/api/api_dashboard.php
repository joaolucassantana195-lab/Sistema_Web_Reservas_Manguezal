<?php
if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('America/Sao_Paulo');
$data = date('Y-m-d');

try {
    require_once __DIR__ . '/../../config/conexao.php';

    $stmt = $pdo->prepare("SELECT
            r.ID_reserva AS reserva_id,
            r.Horario AS reserva_horario,
            r.QTD_pessoas AS reserva_qtd_pessoas,
            r.Status AS reserva_status,
            r.Data AS reserva_dia,
            r.Observacoes AS reserva_observacao,
            c.Nome AS cliente_nome,
            c.Telefone AS cliente_telefone,
            COALESCE(CAST(m.Numero_mesa AS CHAR), 'S/N') AS mesa_numero,
            COALESCE(CAST(m.localizacao AS CHAR), '') AS mesa_local
        FROM reserva r
        INNER JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_Cliente 
        LEFT JOIN mesa m ON  r.FK_Mesa_ID_Mesa = m.ID_mesa
        WHERE r.Data = :dataHoje 
        AND r.Status IN ('Aguardando' , 'Confirmada', 'Em Atendimento', 'Concluída')
        ORDER BY
        CASE r.Status
        WHEN "Em Atendimento" THEN 1
        WHEN "Confirmada" THEN 2
        WHEN "Aguardando" THEN 3
        WHEN "Concluída" THEN 4
        ELSE 5
        AS ASC,
        r.Horario ASC
    ");

    $stmt->execute([':dataHoje' => $data]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reservas);
    exit;

} catch (Exception $e) {
    // Registra o erro no log do servidor
    error_log($e->getMessage());

    // Retorna a falha em formato JSON válido
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Falha ao consultar os dados no banco de dados.'
    ]);
    exit;
}
?>
