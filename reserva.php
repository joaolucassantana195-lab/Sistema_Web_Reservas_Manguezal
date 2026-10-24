<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/config/conexao.php";
require_once __DIR__ . "/config/email_sender.php";

date_default_timezone_set('America/Sao_Paulo');

$dataAtual = date('Y-m-d');
$horaAtual = date('H:i');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim(strip_tags($_POST['nome'] ?? ''));
    $email = trim(strip_tags($_POST['email'] ?? ''));
    $telefone = trim(strip_tags($_POST['telefone'] ?? ''));
    $observacoes = trim(strip_tags($_POST['observacoes'] ?? ''));

    $dataBr = $_POST['data'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $pessoas = $_POST['pessoas'] ?? '';
    $beach = $_POST['optionBeach'] ?? '';

    $optionBeach = null;
    $idCliente = null;

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS
    |--------------------------------------------------------------------------
    */

    if (
        empty($nome) ||
        empty($email) ||
        empty($telefone) ||
        empty($dataBr) ||
        empty($horario) ||
        empty($pessoas)
    ) {
        header("Location: confirmacao.php?status=erro");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CONVERSÃO DA DATA
    |--------------------------------------------------------------------------
    */

    $dataObjeto = DateTime::createFromFormat('d/m/Y', $dataBr);

    if (!$dataObjeto) {
        $dataObjeto = DateTime::createFromFormat('Y-m-d', $dataBr);
    }

    if (!$dataObjeto) {
        header("Location: confirmacao.php?status=erro");
        exit;
    }

    $data = $dataObjeto->format('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DO E-MAIL
    |--------------------------------------------------------------------------
    */

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: confirmacao.php?status=erro");
        exit;
    }

    // Extrai o domínio do e-mail
    $dominioEmail = substr(strrchr($email, "@"), 1);

    // Verifica se o domínio possui registros MX ou A válidos para receber e-mails
    if (!checkdnsrr($dominioEmail, 'MX') && !checkdnsrr($dominioEmail, 'A')) {
        header("Location: confirmacao.php?status=email_invalido");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | NÃO PERMITIR DATA PASSADA
    |--------------------------------------------------------------------------
    */

    if ($data < $dataAtual) {
        header("Location: confirmacao.php?status=data_passado");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | NÃO PERMITIR HORÁRIO PASSADO NO DIA ATUAL
    |--------------------------------------------------------------------------
    */

    if (
        $data === $dataAtual &&
        strtotime($horario) <= strtotime($horaAtual)
    ) {
        header("Location: confirmacao.php?status=data_passado");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DA QUANTIDADE DE PESSOAS
    |--------------------------------------------------------------------------
    */

    if (!is_numeric($pessoas) || $pessoas <= 0) {
        header("Location: confirmacao.php?status=pessoas_invalido");
        exit;
    }

    $pessoas = (int) $pessoas;

    /*
    |--------------------------------------------------------------------------
    | OPÇÃO BEACH CLUB
    |--------------------------------------------------------------------------
    */

    if (in_array($beach, ['sim'], true)) {
        $optionBeach = true;
    } else {
        $optionBeach = false;
    }

    try {

        /*
        |--------------------------------------------------------------------------
        | BUSCAR CONFIGURAÇÕES
        |--------------------------------------------------------------------------
        */

        $stmtConfiguracoes = $pdo->prepare(
            'SELECT * FROM configuracoes WHERE id = 1'
        );

        $stmtConfiguracoes->execute();

        $configuracoes = $stmtConfiguracoes->fetch(PDO::FETCH_ASSOC);

        if (!$configuracoes) {
            header("Location: confirmacao.php?status=erro");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR LIMITE DE PESSOAS
        |--------------------------------------------------------------------------
        */

        if (
            isset($configuracoes['min_pessoas_reserva']) &&
            $pessoas < (int) $configuracoes['min_pessoas_reserva']
        ) {
            header("Location: confirmacao.php?status=pessoas_invalido");
            exit;
        }

        if (
            isset($configuracoes['max_pessoas_reserva']) &&
            $pessoas > (int) $configuracoes['max_pessoas_reserva']
        ) {
            header("Location: confirmacao.php?status=pessoas_invalido");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR LIMITE DE RESERVAS DO DIA
        |--------------------------------------------------------------------------
        */

        $stmtReservas = $pdo->prepare(
            'SELECT COUNT(*) AS contagem
             FROM reserva
             WHERE Data = :data'
        );

        $stmtReservas->execute([
            ':data' => $data
        ]);

        $totalReservas = $stmtReservas->fetch(PDO::FETCH_ASSOC);

        if (!$totalReservas) {
            header("Location: confirmacao.php?status=erro");
            exit;
        }

        if (
            $totalReservas['contagem'] >=
            (int) $configuracoes['limite_max_reservas_dia']
        ) {
            header("Location: confirmacao.php?status=limite_reservas");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR FERIADO
        |--------------------------------------------------------------------------
        |
        | Existem dois tipos:
        |
        | 1. recorrente = 0
        |    Bloqueia somente a data cadastrada.
        |
        | 2. recorrente = 1
        |    Bloqueia o mesmo dia/mês todos os anos.
        |
        */

        $stmtFeriado = $pdo->prepare(
            'SELECT data, descricao, recorrente
             FROM feriados
             WHERE
                 (
                     recorrente = 0
                     AND data = :data
                 )
                 OR
                 (
                     recorrente = 1
                     AND MONTH(data) = MONTH(:data_recorrente)
                     AND DAY(data) = DAY(:data_recorrente)
                 )
             LIMIT 1'
        );

        $stmtFeriado->execute([
            ':data' => $data,
            ':data_recorrente' => $data
        ]);

        $feriado = $stmtFeriado->fetch(PDO::FETCH_ASSOC);

        if ($feriado) {

            header("Location: confirmacao.php?status=feriado");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | DIA DA SEMANA
        |--------------------------------------------------------------------------
        |
        | 0 = Domingo
        | 1 = Segunda
        | 2 = Terça
        | 3 = Quarta
        | 4 = Quinta
        | 5 = Sexta
        | 6 = Sábado
        |
        */

        $diaSemana = (int) date('w', strtotime($data));

        /*
        |--------------------------------------------------------------------------
        | INTERPRETAR dias_funcionamento
        |--------------------------------------------------------------------------
        */

        $diasFuncionamentoBanco = $configuracoes['dias_funcionamento'];

        if (is_string($diasFuncionamentoBanco)) {

            $diasFuncionamento = json_decode(
                $diasFuncionamentoBanco,
                true
            );

            if (
                json_last_error() !== JSON_ERROR_NONE ||
                !is_array($diasFuncionamento)
            ) {
                $diasFuncionamento = [];
            }
        } elseif (is_array($diasFuncionamentoBanco)) {

            $diasFuncionamento = $diasFuncionamentoBanco;
        } else {

            $diasFuncionamento = [];
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SE O DIA ESTÁ CONFIGURADO
        |--------------------------------------------------------------------------
        */

        $configDia = $diasFuncionamento[(string) $diaSemana] ?? null;

        if (!is_array($configDia)) {
            header("Location: confirmacao.php?status=dia_fechado");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | HORÁRIO DE ABERTURA E FECHAMENTO DO DIA
        |--------------------------------------------------------------------------
        */

        $horarioAbertura = $configDia['abertura'] ?? null;
        $horarioFechamento = $configDia['fechamento'] ?? null;

        if (
            empty($horarioAbertura) ||
            empty($horarioFechamento)
        ) {
            header("Location: confirmacao.php?status=dia_fechado");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR HORÁRIOS
        |--------------------------------------------------------------------------
        */

        $horarioAbertura = substr($horarioAbertura, 0, 5);
        $horarioFechamento = substr($horarioFechamento, 0, 5);
        $horario = substr($horario, 0, 5);

        $timeEnviado = strtotime($horario);
        $timeAbertura = strtotime($horarioAbertura);
        $timeFechamento = strtotime($horarioFechamento);

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SE O HORÁRIO ESTÁ DENTRO DO FUNCIONAMENTO
        |--------------------------------------------------------------------------
        */

        if (
            $timeEnviado === false ||
            $timeAbertura === false ||
            $timeFechamento === false
        ) {
            header("Location: confirmacao.php?status=horario_invalido");
            exit;
        }

        if (
            $timeEnviado < $timeAbertura ||
            $timeEnviado > $timeFechamento
        ) {
            header("Location: confirmacao.php?status=horario_invalido");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | LIMITE DA ÚLTIMA RESERVA
        |--------------------------------------------------------------------------
        |
        | Caso exista horario_limite_ultima_reserva,
        | ele também será respeitado.
        |
        */

        $horarioLimiteUltimaReserva =
            $configuracoes['horario_limite_ultima_reserva'] ?? null;

        if (!empty($horarioLimiteUltimaReserva)) {

            $horarioLimiteUltimaReserva =
                substr($horarioLimiteUltimaReserva, 0, 5);

            $timeLimite = strtotime($horarioLimiteUltimaReserva);

            if (
                $timeLimite !== false &&
                $timeEnviado > $timeLimite
            ) {
                header("Location: confirmacao.php?status=horario_invalido");
                exit;
            }
        }


        if ($data === $dataAtual) {

            $antecedenciaMinima =
                (int) ($configuracoes['antecedencia_min_reserva_horas'] ?? 0);

            if ($antecedenciaMinima > 0) {

                $agora = new DateTime();

                $agora->modify(
                    '+' . $antecedenciaMinima . ' hours'
                );

                $dataHoraReserva = DateTime::createFromFormat(
                    'Y-m-d H:i',
                    $data . ' ' . $horario
                );

                if (
                    $dataHoraReserva &&
                    $dataHoraReserva < $agora
                ) {
                    header("Location: confirmacao.php?status=antecedencia");
                    exit;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BUSCAR CLIENTE
        |--------------------------------------------------------------------------
        */

        $stmtBusca = $pdo->prepare(
            'SELECT ID_cliente
             FROM cliente
             WHERE Email = :email'
        );

        $stmtBusca->execute([
            ':email' => $email
        ]);

        $clienteExistente = $stmtBusca->fetch(PDO::FETCH_ASSOC);

        $idCliente = $clienteExistente
            ? $clienteExistente['ID_cliente']
            : null;

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SE O CLIENTE JÁ POSSUI RESERVA NA DATA
        |--------------------------------------------------------------------------
        */

        if ($idCliente) {

            $stmtVerificar = $pdo->prepare(
                "SELECT ID_reserva
                 FROM reserva
                 WHERE FK_Cliente_ID_cliente = :id_cliente
                   AND Data = :data
                   AND Status != 'Concluida'"
            );

            $stmtVerificar->execute([
                ':id_cliente' => $idCliente,
                ':data' => $data
            ]);

            $reservaExistente = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if ($reservaExistente) {

                header(
                    "Location: confirmacao.php?status=reserva_existente"
                );

                exit;
            }

            /*
            |--------------------------------------------------------------------------
            | ATUALIZAR TELEFONE DO CLIENTE
            |--------------------------------------------------------------------------
            */

            $stmtUpdate = $pdo->prepare(
                "UPDATE cliente
                 SET Telefone = :telefone
                 WHERE ID_cliente = :id"
            );

            $stmtUpdate->execute([
                ':telefone' => $telefone,
                ':id' => $idCliente
            ]);
        } else {

            /*
            |--------------------------------------------------------------------------
            | CRIAR NOVO CLIENTE
            |--------------------------------------------------------------------------
            */

            $stmtCliente = $pdo->prepare(
                "INSERT INTO cliente
                    (Nome, Email, Telefone)
                 VALUES
                    (:Nome, :Email, :Telefone)"
            );

            $stmtCliente->execute([
                ':Nome' => $nome,
                ':Email' => $email,
                ':Telefone' => $telefone
            ]);

            $idCliente = $pdo->lastInsertId();
        }

        /*
        |--------------------------------------------------------------------------
        | BUSCAR MESA
        |--------------------------------------------------------------------------
        */

        if ($optionBeach === true) {

            $stmtMesas = $pdo->prepare(
                'SELECT *
                 FROM mesa
                 WHERE Capacidade_max >= :pessoas
                   AND ativa = 1
                   AND Status = "Livre"
                   AND localizacao = "beach"
                   AND ID_mesa NOT IN (
                       SELECT FK_Mesa_ID_Mesa
                       FROM reserva
                       WHERE Data = :dia
                   )
                 ORDER BY Capacidade_max ASC
                 LIMIT 1'
            );

            $stmtMesas->execute([
                ':pessoas' => $pessoas,
                ':dia' => $data
            ]);
        } else {

            $stmtMesas = $pdo->prepare(
                'SELECT *
                 FROM mesa
                 WHERE Capacidade_max >= :pessoas
                   AND ativa = 1
                   AND Status = "Livre"
                   AND localizacao IN ("deck", "salão")
                   AND ID_mesa NOT IN (
                       SELECT FK_Mesa_ID_Mesa
                       FROM reserva
                       WHERE Data = :dia
                   )
                 ORDER BY Capacidade_max ASC
                 LIMIT 1'
            );

            $stmtMesas->execute([
                ':pessoas' => $pessoas,
                ':dia' => $data
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | MESA ENCONTRADA
        |--------------------------------------------------------------------------
        */

        $IdMesa = $stmtMesas->fetch(PDO::FETCH_ASSOC);

        $numeroMesaAtribuida = $IdMesa
            ? $IdMesa['Numero_mesa']
            : null;

        $idMesaAtribuida = $IdMesa
        ? $IdMesa['ID_mesa'] 
        : null;

        /*
        |--------------------------------------------------------------------------
        | STATUS DA RESERVA
        |--------------------------------------------------------------------------
        */

        if ($configuracoes['confirmar_reservas_automaticamente'] &&  $numeroMesaAtribuida) {

            $statusReserva = 'Confirmada';
        } else {

            $statusReserva = 'Aguardando';

             $numeroMesaAtribuida = null;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERIR RESERVA
        |--------------------------------------------------------------------------
        */

        $paramsReserva = [
            ':Horario' => $horario,
            ':QTD_pessoas' => $pessoas,
            ':dia' => $data,
            ':Observacoes' => $observacoes,
            ':status' => $statusReserva,
            ':id_cliente' => $idCliente,
            ':option' => $optionBeach ? 1 : 0,
            ':idmesa' =>  $idMesaAtribuida
        ];

        $stmtReserva = $pdo->prepare(
            "INSERT INTO reserva
                (
                    Horario,
                    QTD_pessoas,
                    Data,
                    Observacoes,
                    Status,
                    FK_Cliente_ID_cliente,
                    BEACH_CLUB,
                    FK_Mesa_ID_Mesa
                )
             VALUES
                (
                    :Horario,
                    :QTD_pessoas,
                    :dia,
                    :Observacoes,
                    :status,
                    :id_cliente,
                    :option,
                    :idmesa
                )"
        );

        $stmtReserva->execute($paramsReserva);

        /*
        |--------------------------------------------------------------------------
        | ENVIAR E-MAIL DE CONFIRMAÇÃO
        |--------------------------------------------------------------------------
        */

        if ($statusReserva === 'Confirmada'){
            if($configuracoes['enviar_email_confirmacao']){
           enviarEmailConfirmacao(
                $email,
                $nome,
                date('d/m/Y', strtotime($data)),
                $horario,
                $pessoas,
                 $numeroMesaAtribuida,
                $optionBeach ? 'Sim' : 'Não'
            );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUCESSO
        |--------------------------------------------------------------------------
        */

        header("Location: confirmacao.php?status=sucesso");
        exit;
    } catch (PDOException $e) {
        header("Location: confirmacao.php?status=server_error");
        exit;
    }
}
