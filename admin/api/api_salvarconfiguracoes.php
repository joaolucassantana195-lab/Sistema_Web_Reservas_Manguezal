<?php

require_once __DIR__ . '/../../config/conexao.php';

if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Dados JSON inválidos.'
    ]);

    exit;
}

// ============================================================
// CONFIGURAÇÕES
// ============================================================

$minPessoas = $dados['minPessoas'] ?? 20;
$maxPessoas = $dados['maxPessoas'] ?? 40;
$maxReservas = $dados['maxReservas'] ?? 3;
$antecendenciaMax = $dados['antecendenciaMax'] ?? 20;
$antecedenciaMin = $dados['antecedenciaMin'] ?? 30;

$horarioUltimaReserva =
    $dados['horarioUltimaReserva'] ?? '19:00';

$toleranciaAtraso =
    $dados['toleranciaAtraso'] ?? 20;

$opcaoBeach =
    $dados['opcaoBeach'] ?? 1;

$taxaBeach =
    $dados['taxaBeach'] ?? 30;

$confirmacaoAutomatica =
    $dados['confirmacaoAutomatica'] ?? 1;

$emailConfirmacao =
    $dados['emailConfirmacao'] ?? 1;

$enviarLembrete =
    $dados['enviarLembrete'] ?? 1;

$lembrete =
    $dados['lembrete'] ?? 1;


// ============================================================
// DIAS + HORÁRIOS
// ============================================================

$diasFuncionamento = $dados['dias'] ?? [];

if (!is_array($diasFuncionamento)) {
    $diasFuncionamento = [];
}

$diasFuncionamento = json_encode(
    $diasFuncionamento,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_FORCE_OBJECT
);

if ($diasFuncionamento === false) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Não foi possível gerar o JSON dos dias de funcionamento.'
    ]);

    exit;
}


// ============================================================
// FERIADOS
// ============================================================

$feriados = $dados['feriados'] ?? [];

if (!is_array($feriados)) {
    $feriados = [];
}


// ============================================================
// TRANSAÇÃO
// ============================================================

try {

    $pdo->beginTransaction();


    // ========================================================
    // SALVAR CONFIGURAÇÕES
    // ========================================================

    $sql = "
        UPDATE configuracoes SET

            min_pessoas_reserva =
                :minPessoas,

            max_pessoas_reserva =
                :maxPessoas,

            antecedencia_min_reserva_horas =
                :minReservas,

            antecedencia_max_reserva_dias =
                :antecedenciaMax,

            limite_max_reservas_dia =
                :maxReservas,

            permitir_reserva_beach_club =
                :reservaBeach,

            taxa_pessoa_beach_club =
                :taxaBeach,

            horario_limite_ultima_reserva =
                :horarioLimite,

            tolerancia_atraso_minutos =
                :atraso,

            confirmar_reservas_automaticamente =
                :confirmacaoAutomatica,

            enviar_email_confirmacao =
                :emailConfirmacao,

            enviar_email_lembrete =
                :enviarLembrete,

            horas_antecedencia_lembrete =
                :antecedenciaLembrete,

            dias_funcionamento =
                :dias

        WHERE id = 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':minPessoas' =>
            $minPessoas,

        ':maxPessoas' =>
            $maxPessoas,

        ':minReservas' =>
            $antecedenciaMin,

        ':antecedenciaMax' =>
            $antecendenciaMax,

        ':maxReservas' =>
            $maxReservas,

        ':reservaBeach' =>
            $opcaoBeach,

        ':taxaBeach' =>
            $taxaBeach,

        ':horarioLimite' =>
            $horarioUltimaReserva,

        ':atraso' =>
            $toleranciaAtraso,

        ':confirmacaoAutomatica' =>
            $confirmacaoAutomatica,

        ':emailConfirmacao' =>
            $emailConfirmacao,

        ':enviarLembrete' =>
            $enviarLembrete,

        ':antecedenciaLembrete' =>
            $lembrete,

        ':dias' =>
            $diasFuncionamento
    ]);


    // ========================================================
    // REMOVER FERIADOS ANTIGOS
    // ========================================================

    $sqlExcluirFeriados = "
        DELETE FROM feriados
    ";

    $stmtExcluirFeriados = $pdo->prepare($sqlExcluirFeriados);

    $stmtExcluirFeriados->execute();


    // ========================================================
    // INSERIR FERIADOS ATUAIS
    // ========================================================

    $sqlInserirFeriado = "
        INSERT INTO feriados (
            data,
            recorrente,
            descricao
        )
        VALUES (
            :data,
            :recorrente,
            :descricao
        )
    ";

    $stmtInserirFeriado = $pdo->prepare($sqlInserirFeriado);


    foreach ($feriados as $feriado) {

        if (!is_array($feriado)) {
            continue;
        }

        $data = $feriado['data'] ?? '';
        $descricao = $feriado['descricao'] ?? '';
        $recorrente = !empty($feriado['recorrente']) ? 1 : 0;


        // Não salva feriado sem data
        if (empty($data)) {
            continue;
        }


        $stmtInserirFeriado->execute([
            ':data' =>
                $data,

            ':recorrente' =>
                $recorrente,

            ':descricao' =>
                $descricao
        ]);
    }


    // ========================================================
    // CONFIRMAR TRANSAÇÃO
    // ========================================================

    $pdo->commit();


    // ========================================================
    // RESPOSTA
    // ========================================================

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Configurações e feriados foram salvos com sucesso!'
    ]);

} catch (PDOException $e) {

    // ========================================================
    // DESFAZER ALTERAÇÕES
    // ========================================================

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);

    exit;
}

?>