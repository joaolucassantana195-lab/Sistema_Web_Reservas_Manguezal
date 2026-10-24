<?php
ob_clean();
require_once __DIR__ . '/../../config/conexao.php';

try{
    $stmt = $pdo->prepare('SELECT Email from usuario WHERE tipo_perfil = "admin"');
    $stmt->execute();
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($dados){
        echo json_encode($dados);
        exit;
    }

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao buscar dados do admin.'
    ]);

    exit;

}catch(PDOException $e){
    echo json_encode([
        'erro' => true,
        'mensagem' => 'Erro de conexão ao buscar dados.'
    ]);
}
?>