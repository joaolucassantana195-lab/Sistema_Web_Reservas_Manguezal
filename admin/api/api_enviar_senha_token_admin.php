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
 require_once __DIR__ . '/../../config/email_sender.php';

$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($dados['email'] ?? '');

if (empty($email)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, informe o e-mail.']);
    exit;
}

try {

    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE Email = :email');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail não encontrado no sistema.']);
        exit;
    }


    $token = bin2hex(random_bytes(32));
    

    $expiracao = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $update = $pdo->prepare('UPDATE usuario SET token_recuperacao = :token, token_expiracao = :expiracao WHERE Email = :email');
    $update->execute([
        ':token' => $token,
        ':expiracao' => $expiracao,
        ':email' => $email
    ]);

    $enviado = enviarToken($email, $token); 

    if ($enviado) {
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Link/Token de recuperação enviado para o seu e-mail!'
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao enviar o e-mail. Tente novamente mais tarde.'
        ]);
    }
    exit;

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>