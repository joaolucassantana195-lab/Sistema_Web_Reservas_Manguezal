<?php
// Exemplo do trecho a ser adicionado no seu cron-tarefas.php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . "/../../config/conexao.php";

$tokenEsperado = "cronLembreteManguezal26";
$tokenRecebido = $_GET['token'] ?? ($argv[1] ?? '');

if (php_sapi_name() !== 'cli' && $tokenRecebido !== $tokenEsperado) {
    http_response_code(403);
    exit(json_encode(["error" => "Acesso Negado."]));
}

try {

    // Atualiza as mesas para 'Reservada' se houver reserva confirmada para HOJE
    $sqlCron = "
        UPDATE mesa m
        JOIN reserva r ON m.ID_mesa = r.FK_Mesa_ID_Mesa
        SET m.Status = 'Reservada'
        WHERE r.Data = CURDATE()
          AND r.Status = 'Confirmada'
          AND LOWER(m.Status) != 'ocupada'
    ";
    
    $stmt = $pdo->prepare($sqlCron);
    $stmt->execute();
    $mesasAtualizadas = $stmt->rowCount();

    // Retorno de sucesso para o log do cron-job.org
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Mesas atualizadas com sucesso para o dia de hoje.',
        'mesas_afetadas' => $mesasAtualizadas
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'mensagem' => $e->getMessage()
    ]);
}