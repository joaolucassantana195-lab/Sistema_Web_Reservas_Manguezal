<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();
header('Content-Type: application/json; charset=utf-8');
try{

$dados = json_decode(file_get_contents('php://input'), true);

$numero = $dados['numero'];
$capacidade = $dados['capacidade'];


$stmt = $pdo->prepare('INSERT INTO `mesa` (`Numero_mesa`, `Capacidade_max`, `Status`) VALUES (:numero, :capacidade, "Livre")');
$executou = $stmt->execute([
    ':numero' => $numero,
    ':capacidade' => $capacidade
]);

  if ($executou) {
            
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível atualizar o registro.']);
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