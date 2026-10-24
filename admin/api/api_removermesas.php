<?php
require_once __DIR__ . '/../../config/conexao.php';

// Limpa qualquer buffer ou espaço em branco anterior
ob_clean();
header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

$numero = $dados['numero'];

if($numero) {
    try {
        if ($numero !== null) {
            $stmtMesas = $pdo->prepare('SELECT ID_mesa, Numero_mesa, Status FROM mesa  WHERE ID_mesa = :numero');
            $parametros = $stmtMesas->execute([
                ':numero' => $numero
            ]);
            $mesa = $stmtMesas->fetch(PDO::FETCH_ASSOC);


            if ($mesa){

            if($mesa['Status'] === 'Ocupada'){
                echo json_encode([
                'sucesso' => false,
                'erro' => "Não é possível remover a mesa {$mesa['Numero_mesa']} pois ela está ocupada no momento."
                ]);
                exit;
            }

            $stmtReserva = $pdo->prepare('
            SELECT ID_reserva FROM reserva 
            WHERE FK_Mesa_ID_Mesa = :id_mesa 
            AND Status IN ("Confirmada", "Em Atendimento")
            ');
            $stmtReserva->execute([':id_mesa' => $mesa['ID_mesa']]);
            $reservaHoje = $stmtReserva->fetch(PDO::FETCH_ASSOC);

            if($reservaHoje){
            echo json_encode([
                'sucesso' => false,
                'erro' => "Não é possível remover a mesa {$mesa['Numero_mesa']} pois ela está reservada."
            ]);
            exit;
            }

            $stmt = $pdo->prepare('UPDATE mesa SET ativa = FALSE WHERE ID_mesa = :numero');
            $executou = $stmt->execute([
                ':numero' => $numero
            ]);

            if ($executou) {
            
            echo json_encode(['sucesso' => true]);
            } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível atualizar o registro.']);
            }
            exit;
            }
        }
        exit;
        } catch (PDOException $e) {
        echo json_encode([
            'sucesso' => false, 
            'erro' => $e->getMessage()
        ]);
        exit;
        
        }

} else {
    echo json_encode([
        'sucesso' => false, 
        'erro' => 'Dados incompletos recebidos pelo servidor.',
        'recebido' => ['numero' => $numero]
    ]);
    exit;
}
?>
