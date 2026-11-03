<?php

require_once __DIR__ . '/../../config/conexao.php';

if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

try {

    // ============================================================
    // HORÁRIOS PADRÃO
    // ============================================================

    $diasFuncionamento = [
        "0" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "1" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "2" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "3" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "4" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "5" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ],
        "6" => [
            "abertura" => "09:00",
            "fechamento" => "17:00"
        ]
    ];

    $regras = [
        "0" => [
            "titulo" => "🍾 Taxa de Rolha",
            "conteudo" => "Traga seu vinho! Cobramos R$ 50,00 por garrafa aberta."
        ],
        "1" => [
            "titulo" => "🍴 Alimentos e Bebidas Externos",
            "conteudo" => "É estritamente proibido entrar com coolers, isopores, alimentos ou bebidas trazidos de fora."
        ]
    ];


    // ============================================================
    // CONFIGURAÇÕES PADRÃO
    // ============================================================

    $padroes = [
        ':minPessoas'           => 1,
        ':maxPessoas'           => 15,
        ':minReservas'          => 2,
        ':antecedenciaMax'      => 30,
        ':maxReservas'          => 10,
        ':reservaBeach'         => 1,
        ':taxaBeach'            => 30,
        ':horarioLimite'        => '16:00',
        ':atraso'               => 2,
        ':confirmacaoAutomatica'=> 1,
        ':emailConfirmacao'     => 1,
        ':enviarLembrete'       => 1,
        ':antecedenciaLembrete' => 6,
        ':dias'                 => json_encode(
            $diasFuncionamento,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_FORCE_OBJECT
        ),
        ':regras' => json_encode(
            $regras,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_FORCE_OBJECT
        ),
        ':contatoRestaurante' => 7197129517,

        ':contatoEmail' => "contato.restaurante.manguezal@gmail.com",
    ];


    // ============================================================
    // FERIADOS PADRÃO
    // ============================================================

    $feriadosPadrao = [

        [
            'data' => '2000-01-01',
            'recorrente' => 1,
            'descricao' => 'Confraternização Universal'
        ],

        [
            'data' => '2000-04-21',
            'recorrente' => 1,
            'descricao' => 'Tiradentes'
        ],

        [
            'data' => '2000-05-01',
            'recorrente' => 1,
            'descricao' => 'Dia do Trabalho'
        ],

        [
            'data' => '2000-09-07',
            'recorrente' => 1,
            'descricao' => 'Independência do Brasil'
        ],

        [
            'data' => '2000-10-12',
            'recorrente' => 1,
            'descricao' => 'Nossa Senhora Aparecida'
        ],

        [
            'data' => '2000-11-02',
            'recorrente' => 1,
            'descricao' => 'Finados'
        ],

        [
            'data' => '2000-11-15',
            'recorrente' => 1,
            'descricao' => 'Proclamação da República'
        ],

        [
            'data' => '2000-11-20',
            'recorrente' => 1,
            'descricao' => 'Dia da Consciência Negra'
        ],

        [
            'data' => '2000-12-25',
            'recorrente' => 1,
            'descricao' => 'Natal'
        ]

    ];


    // ============================================================
    // TRANSAÇÃO
    // ============================================================

    $pdo->beginTransaction();


    // ============================================================
    // RESTAURAR CONFIGURAÇÕES
    // ============================================================

    $sql = "
        UPDATE configuracoes SET 
            min_pessoas_reserva = :minPessoas,
            max_pessoas_reserva = :maxPessoas,
            antecedencia_min_reserva_dias = :minReservas,
            antecedencia_max_reserva_dias = :antecedenciaMax,
            limite_max_reservas_dia = :maxReservas,
            permitir_reserva_beach_club = :reservaBeach,
            taxa_pessoa_beach_club = :taxaBeach,
            horario_limite_ultima_reserva = :horarioLimite,
            tolerancia_atraso_horas = :atraso,
            confirmar_reservas_automaticamente = :confirmacaoAutomatica,
            enviar_email_confirmacao = :emailConfirmacao,
            enviar_email_lembrete = :enviarLembrete,
            horas_antecedencia_lembrete = :antecedenciaLembrete,
            dias_funcionamento = :dias,
            regras_restaurante = :regras,
            contato_restaurante = :contatoRestaurante,
            contato_email = :contatoEmail

        WHERE id = 1
    ";

    $stmt = $pdo->prepare($sql);

    $sucesso = $stmt->execute($padroes);

    if (!$sucesso) {
        throw new Exception(
            'Não foi possível restaurar as configurações.'
        );
    }


    // ============================================================
    // APAGAR FERIADOS ATUAIS
    // ============================================================

    $sqlExcluirFeriados = "
        DELETE FROM feriados
    ";

    $stmtExcluirFeriados = $pdo->prepare($sqlExcluirFeriados);

    if (!$stmtExcluirFeriados->execute()) {
        throw new Exception(
            'Não foi possível limpar os feriados atuais.'
        );
    }


    // ============================================================
    // INSERIR FERIADOS PADRÃO
    // ============================================================

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


    foreach ($feriadosPadrao as $feriado) {

        $stmtInserirFeriado->execute([
            ':data'       => $feriado['data'],
            ':recorrente' => $feriado['recorrente'],
            ':descricao'  => $feriado['descricao']
        ]);

    }


    // ============================================================
    // FINALIZAR TRANSAÇÃO
    // ============================================================

    $pdo->commit();


    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Configurações e feriados restaurados para o padrão.'
    ]);


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'sucesso' => false,
        'erro' => $e->getMessage()
    ]);
}

exit;
?>