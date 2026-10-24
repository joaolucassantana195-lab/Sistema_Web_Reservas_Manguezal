<?php
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../../config/email_sender.php";

date_default_timezone_set('America/Sao_Paulo');

$tokenEsperado = "cronLembreteManguezal26";
$tokenRecebido = $_GET['token'] ?? ($argv[1] ?? '');

if (php_sapi_name() !== 'cli' && $tokenRecebido !== $tokenEsperado) {
    http_response_code(403);
    exit(json_encode(["error" => "Acesso Negado."]));
}

try {

    $pdo->exec("SET time_zone = '-03:00'");

    $stmt = $pdo->prepare("
        SELECT 
            r.ID_reserva,
            r.Data,
            r.Horario,
            r.QTD_pessoas AS pessoas,
            r.BEACH_CLUB,
            c.Nome,
            c.Email,
            m.Numero_mesa,
            config.horario_abertura,
            config.horario_fechamento,
            config.enviar_email_lembrete
        FROM reserva r 
        INNER JOIN cliente c ON r.FK_Cliente_ID_cliente = c.ID_cliente
        INNER JOIN configuracoes config ON config.id = 1
        LEFT JOIN mesa m ON r.FK_Mesa_ID_Mesa = m.ID_mesa
        WHERE 
            -- 0. Só envia se a configuração de e-mail de lembrete estiver ativa
            config.enviar_email_lembrete = 1
            -- 1. A hora atual já passou do momento de enviar o lembrete
            AND NOW() >= DATE_SUB(
                STR_TO_DATE(CONCAT(r.Data, ' ', r.Horario), '%Y-%m-%d %H:%i:%s'), 
                INTERVAL config.horas_antecedencia_lembrete HOUR
            )
            -- 2. A reserva ainda não aconteceu (é maior que o horário atual)
            AND STR_TO_DATE(CONCAT(r.Data, ' ', r.Horario), '%Y-%m-%d %H:%i:%s') > NOW()
            -- 3. Apenas reservas ativas e que ainda não receberam o lembrete
            AND r.Status IN ('Confirmada', 'Aguardando')
            AND r.lembrete_enviado = 0
    ");

    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $enviados = 0;
    $falhas = 0;

    foreach ($reservas as $reserva) {
        $sucesso = enviarLembreteEmail(
            $reserva['Email'],
            $reserva['Nome'],
            $reserva['Data'],
            $reserva['Horario'],
            $reserva['pessoas'],
            $reserva['Numero_mesa'],
            $reserva['BEACH_CLUB'] == 1 ? 'Sim' : 'Não'
        );

        if ($sucesso) {
            $stmtUpdate = $pdo->prepare("UPDATE reserva SET lembrete_enviado = 1 WHERE ID_reserva = :id");
            $stmtUpdate->execute([':id' => $reserva['ID_reserva']]);
            $enviados++;
        } else {
            $falhas++;
            error_log("Falha ao enviar e-mail para reserva ID: " . $reserva['ID_reserva']);
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        "status" => "sucesso",
        "reservas_encontradas" => count($reservas),
        "lembretes_enviados" => $enviados,
        "falhas_envio" => $falhas
    ]);

} catch (PDOException $e) {
    error_log("Erro no Cron de Lembretes: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "erro", "mensagem" => "Erro interno no banco de dados."]);
}