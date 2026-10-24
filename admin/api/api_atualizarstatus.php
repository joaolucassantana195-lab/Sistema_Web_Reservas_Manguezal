<?php
require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../config/email_sender.php';

if (ob_get_length()) {
    ob_clean();
}
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

$idReserva  = $dados['id'] ?? null;
$novoStatus = $dados['status'] ?? null;
$novaMesa   = $dados['mesa'] ?? null;
$motivo = $dados['motivo'] ?? '';

if(!$motivo || $motivo == ''){
    $motivo = "Indisponibilidade de agenda/vaga para a data e horário solicitados.";
}

if ($idReserva && $novoStatus){
    try {
        $pdo->beginTransaction();

        // 1. Busca os dados da reserva ANTES de alterar para saber a mesa e data antiga
        $stmtAtual = $pdo->prepare('SELECT FK_Mesa_ID_Mesa, Data FROM reserva WHERE ID_reserva = :id');
        $stmtAtual->execute([':id' => $idReserva]);
        $reservaAtual = $stmtAtual->fetch(PDO::FETCH_ASSOC);

        if (!$reservaAtual) {
            echo json_encode(['sucesso' => false, 'erro' => 'Reserva não encontrada.']);
            exit;
        }

        $antigaMesaId = $reservaAtual['FK_Mesa_ID_Mesa'];
        $idMesa = $novaMesa ?? $antigaMesaId;
        
        $diaReserva = date('Y-m-d', strtotime($reservaAtual['Data']));
        $hoje       = date('Y-m-d');
        $horaAtual  = date('H:i');

        // 2. Executa o UPDATE principal da reserva
        if ($novaMesa !== null) {
            $sql = 'UPDATE reserva SET Status = :status, FK_Mesa_ID_Mesa = :mesa WHERE ID_reserva = :id';
            $params = [':id' => $idReserva, ':status' => $novoStatus, ':mesa' => $novaMesa];
        } else {
            $sql = 'UPDATE reserva SET Status = :status WHERE ID_reserva = :id';
            $params = [':id' => $idReserva, ':status' => $novoStatus];
        }

        $stmt = $pdo->prepare($sql);
        $executou = $stmt->execute($params);

        // Usamos >= 0 para garantir que passe mesmo se o status for igual e só a mesa mudar
        if ($executou) {

            // 3. IMPLEMENTAÇÃO DA LÓGICA DE GESTÃO DE STATUS DA MESA

            // A) Se houve troca de mesa (e a mesa nova é diferente da antiga)
            if (!empty($novaMesa) && $novaMesa != $antigaMesaId) {
                
                // Libera a mesa antiga (se não houver nenhuma outra reserva ativa nela hoje)
                if (!empty($antigaMesaId)) {
                    $stmtCheckHoje = $pdo->prepare("
                        SELECT COUNT(*) 
                        FROM reserva 
                        WHERE FK_Mesa_ID_Mesa = :mesaId 
                          AND DATE(Data) = :hoje 
                          AND Status IN ('Confirmada', 'Em Atendimento')
                          AND ID_reserva != :idReserva
                    ");
                    $stmtCheckHoje->execute([
                        ':mesaId'    => $antigaMesaId,
                        ':hoje'      => $hoje,
                        ':idReserva' => $idReserva
                    ]);
                    $outrasAtivas = $stmtCheckHoje->fetchColumn() > 0;

                    if (!$outrasAtivas) {
                        $stmtLiberaAntiga = $pdo->prepare("UPDATE mesa SET Status = 'Livre' WHERE ID_mesa = :mesaId");
                        $stmtLiberaAntiga->execute([':mesaId' => $antigaMesaId]);
                    }
                }

                // Atualiza o status da nova mesa para hoje
                if ($diaReserva === $hoje) {
                    $statusNovaMesa = ($novoStatus === 'Em Atendimento') ? 'Ocupada' : 'Reservada';
                    $stmtOcupaNova = $pdo->prepare("UPDATE mesa SET Status = :statusMesa WHERE ID_mesa = :idMesa");
                    $stmtOcupaNova->execute([
                        ':statusMesa' => $statusNovaMesa,
                        ':idMesa'     => $novaMesa
                    ]);
                }
            }
            // B) Se a reserva foi Confirmada para HOJE (comportamento padrão sem troca de mesa)
            else if ($novoStatus === 'Confirmada' && $diaReserva === $hoje && !empty($idMesa)) {
                $stmtMesa = $pdo->prepare("
                    UPDATE mesa 
                    SET Status = 'Reservada' 
                    WHERE ID_mesa = :idMesa AND LOWER(Status) != 'ocupada'
                ");
                $stmtMesa->execute([':idMesa' => $idMesa]);
            }
            // C) Se a reserva foi Confirmada para uma data FUTURA
            else if ($novoStatus === 'Confirmada' && $diaReserva > $hoje && !empty($idMesa)) {
                $stmtMesa = $pdo->prepare("
                    UPDATE mesa 
                    SET Status = 'Livre' 
                    WHERE ID_mesa = :idMesa AND LOWER(Status) != 'ocupada'
                ");
                $stmtMesa->execute([':idMesa' => $idMesa]);
            }
            // D) Se a reserva foi Cancelada, Concluída ou Não compareceu, verifica se libera a mesa antiga
            else if (in_array($novoStatus, ['Cancelada', 'Concluída', 'Não compareceu']) && !empty($antigaMesaId)) {
                $stmtCheckHoje = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM reserva 
                    WHERE FK_Mesa_ID_Mesa = :mesaId 
                      AND DATE(Data) = :hoje 
                      AND Status IN ('Confirmada', 'Em Atendimento')
                      AND ID_reserva != :idReserva
                ");
                $stmtCheckHoje->execute([
                    ':mesaId'    => $antigaMesaId,
                    ':hoje'      => $hoje,
                    ':idReserva' => $idReserva
                ]);
                $outrasConfirmadas = $stmtCheckHoje->fetchColumn() > 0;

                if (!$outrasConfirmadas) {
                    $stmtMesa = $pdo->prepare("
                        UPDATE mesa 
                        SET Status = 'Livre' 
                        WHERE ID_mesa = :mesaId
                    ");
                    $stmtMesa->execute([':mesaId' => $antigaMesaId]);
                }
            }

            // 4. Validações de Horário e Disparos de E-mail
            if ($novoStatus === 'Confirmada') {
                $stmtConfig = $pdo->prepare('SELECT * FROM configuracoes WHERE id = 1');
                $stmtConfig->execute();
                $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

                $stmtDados = $pdo->prepare('
                    SELECT c.Nome, c.Email, r.Data, r.Horario, r.QTD_pessoas, r.BEACH_CLUB, r.email_confirmacao, m.Numero_mesa
                    FROM reserva r
                    JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_cliente
                    LEFT JOIN mesa m ON r.FK_Mesa_ID_Mesa = m.ID_mesa
                    WHERE r.ID_reserva = :id
                ');
                $stmtDados->execute([':id' => $idReserva]);
                $dadosReserva = $stmtDados->fetch(PDO::FETCH_ASSOC);

                if ($dadosReserva) {
                    $dataReservaFmt = date('Y-m-d', strtotime($dadosReserva['Data']));
                    $horarioReserva = substr($dadosReserva['Horario'], 0, 5);

                    if ($dataReservaFmt < $hoje) {
                        $pdo->rollBack();
                        echo json_encode(['sucesso' => false, 'erro' => 'Não é possível confirmar uma reserva com data anterior a hoje.']);
                        exit;
                    }

                    if ($dataReservaFmt === $hoje && $horarioReserva < $horaAtual) {
                        $pdo->rollBack();
                        echo json_encode(['sucesso' => false, 'erro' => "Não é possível confirmar uma reserva em horário retroativo para hoje."]);
                        exit;
                    }

                    if ($config) {
                        $abertura = substr($config['horario_abertura'], 0, 5);
                        $limite   = substr($config['horario_limite_ultima_reserva'], 0, 5);

                        if ($horarioReserva < $abertura || $horarioReserva > $limite) {
                            $pdo->rollBack();
                            echo json_encode(['sucesso' => false, 'erro' => "O horário da reserva ({$horarioReserva}h) está fora do horário permitido."]);
                            exit;
                        }
                    }

                    if ($config && ($config['enviar_email_confirmacao'] == 1 || $config['enviar_email_confirmacao'] === true)) {
                        if (!empty($dadosReserva['Email']) && (int)$dadosReserva['email_confirmacao'] === 0) {
                            enviarEmailConfirmacao(
                                $dadosReserva['Email'],
                                $dadosReserva['Nome'],
                                date('d/m/Y', strtotime($dadosReserva['Data'])),
                                $horarioReserva,
                                $dadosReserva['QTD_pessoas'],
                                $dadosReserva['Numero_mesa'] ?? 'A definir',
                                $dadosReserva['BEACH_CLUB'] == 1 ? 'Sim' : 'Não'
                            );

                            $stmtMarkEmail = $pdo->prepare('UPDATE reserva SET email_confirmacao = 1 WHERE ID_reserva = :id');
                            $stmtMarkEmail->execute([':id' => $idReserva]);
                        }
                    }
                }
            } else if ($novoStatus === 'Cancelada'){
                $stmtConfig = $pdo->prepare('SELECT * FROM configuracoes WHERE id = 1');
                $stmtConfig->execute();
                $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

                $stmtDados = $pdo->prepare('
                    SELECT c.Nome, c.Email, r.Data, r.Horario, r.QTD_pessoas, r.BEACH_CLUB, m.Numero_mesa
                    FROM reserva r
                    JOIN cliente c ON r.FK_Cliente_ID_Cliente = c.ID_cliente
                    LEFT JOIN mesa m ON r.FK_Mesa_ID_Mesa = m.ID_mesa
                    WHERE r.ID_reserva = :id
                ');
                $stmtDados->execute([':id' => $idReserva]);
                $dadosReserva = $stmtDados->fetch(PDO::FETCH_ASSOC);

                if ($dadosReserva) {
                    $horarioReserva = substr($dadosReserva['Horario'], 0, 5);

                    if ($config && ($config['enviar_email_confirmacao'] == 1 || $config['enviar_email_confirmacao'] === true)) {
                        if (!empty($dadosReserva['Email'])) {
                           enviarEmailCancelamento(
                                $dadosReserva['Email'],
                                $dadosReserva['Nome'],
                                date('d/m/Y', strtotime($dadosReserva['Data'])),
                                $horarioReserva,
                                $dadosReserva['QTD_pessoas'],
                                $dadosReserva['BEACH_CLUB'] == 1 ? 'Sim' : 'Não',
                                $motivo
                            );
                        }
                    }
                }
            }

            $pdo->commit();
            echo json_encode(['sucesso' => true, 'linhas_afetadas' => $stmt->rowCount()]);

        } else {
            $pdo->rollBack();
            echo json_encode(['sucesso' => false, 'erro' => 'Nenhum registro foi alterado.']);
        }

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['sucesso' => false, 'erro' => 'Erro no servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos recebidos pelo servidor.']);
}
exit;
?>
