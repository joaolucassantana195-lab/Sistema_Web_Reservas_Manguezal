<?php
ob_clean();

require_once __DIR__ . '/../../config/conexao.php';


header('Content-Type:application/json; charset=utf-8;');

$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$id = $dados['id'] ?? null;

if(empty($id)){
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não encontrado.'
    ]);
    exit;
}

try{
    $stmt = $pdo->prepare("UPDATE usuario SET Status = 'ativo' WHERE ID_usuario = :id");
    $stmt -> execute([':id' => $id]);

    if($stmt->rowCount() > 0){
         echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Usuário ativado.'
        ]);
        exit;
    }

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao ativar usuário.'
        ]);

    exit;

}catch(PDOException $e){
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro na conexão.'
        ]);
}
?>