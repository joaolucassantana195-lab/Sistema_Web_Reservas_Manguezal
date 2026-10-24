<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ob_get_length()) ob_clean();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno no servidor.']);
        exit;
    }
});

session_start();
if (ob_get_length()) ob_clean();

require_once __DIR__ . '/../../config/conexao.php';

$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$idUsuario = trim($dados['id']);
$novaSenha = trim($dados['novasenha'] ?? '');

if (empty($idUsuario) || empty($novaSenha)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
    exit;
}

if (strlen($novaSenha) < 8) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter pelo menos 8 caracteres.']);
    exit;
}

try {
    
    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE ID_usuario = :id');
    $stmt->execute([':id' => $idUsuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if (!$usuario) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário inexistente.']);
        exit;
    }

    
    $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

    
    $update = $pdo->prepare('UPDATE usuario SET Senha = :senha WHERE ID_usuario = :id');
    $update->execute([
        ':senha' => $novaSenhaHash,
        ':id' => $usuario['ID_usuario'] 
    ]);

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Senha alterada com sucesso!'
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>