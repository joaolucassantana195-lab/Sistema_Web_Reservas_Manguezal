<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!isset($dados['numero']) || !isset($dados['capacidade'])) {
        echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos.']);
        exit;
    }

    $numero = $dados['numero'];
    $capacidade = $dados['capacidade'];
    $localizacao = $dados['localizacao'];

    $stmtCheck = $pdo->prepare('SELECT ID_mesa, ativa, Status, Numero_mesa FROM `mesa` WHERE `Numero_mesa` = :numero');
    $stmtCheck->execute([':numero' => $numero]);
    $mesaExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($mesaExistente) {

    
        if ($mesaExistente['Status'] === 'Ocupada') {
            echo json_encode([
                'sucesso' => false,
                'erro' => "Não é possível alterar a mesa {$mesaExistente['Numero_mesa']} pois ela está ocupada no momento."
            ]);
            exit;
        }

         $stmtReserva = $pdo->prepare('
        SELECT ID_reserva FROM reserva 
        WHERE FK_Mesa_ID_Mesa = :id_mesa 
        AND Data = CURDATE() 
        AND Status = "Confirmada"
        ');
        $stmtReserva->execute([':id_mesa' => $mesaExistente['ID_mesa']]);
        $reservaHoje = $stmtReserva->fetch(PDO::FETCH_ASSOC);

        if($reservaHoje){
            echo json_encode([
                'sucesso' => false,
                'erro' => "Nao e possivel alterar a mesa {$mesaExistente['Numero_mesa']} pois ela esta reservada hoje."
            ]);
            exit;
        }

        // 2A. Se a mesa existe, reativa e atualiza a capacidade e o status
        $stmtUpdate = $pdo->prepare('UPDATE `mesa` SET `Capacidade_max` = :capacidade, `ativa` = 1, `Status` = "Livre", `localizacao` = :localizacao  WHERE `Numero_mesa` = :numero');
        $executou = $stmtUpdate->execute([
            ':capacidade' => $capacidade,
            ':numero' => $numero,
            ':localizacao' => $localizacao
        ]);
    } else {
        // 2B. Se a mesa não existe, insere um novo registro
        $stmtInsert = $pdo->prepare('INSERT INTO `mesa` (`Numero_mesa`, `Capacidade_max`, `ativa`, `Status`, `localizacao`) VALUES (:numero, :capacidade, 1, "Livre", :localizacao)');
        $executou = $stmtInsert->execute([
            ':numero' => $numero,
            ':capacidade' => $capacidade,
            ':localizacao' => $localizacao
        ]);
    }

    if ($executou) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar os dados da mesa.']);
    }
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false, 
        'erro' => $e->getMessage()
    ]);
    exit;
}
?>