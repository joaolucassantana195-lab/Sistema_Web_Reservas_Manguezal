<?php
require_once __DIR__ . '/../../config/conexao.php';

// Limpa qualquer saída anterior para evitar corromper o JSON
if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');


$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['id']) || empty($dados['id'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'ID do usuário não foi fornecido.']);
    exit;
}

$id = $dados['id'];

try {
    $sql = "DELETE FROM usuario WHERE ID_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário removido com sucesso!']);
    } else {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'erro' => 'Nenhum usuário encontrado com esse ID.']);
    }
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao remover usuário: ' . $e->getMessage()]);
    exit;
}
?>