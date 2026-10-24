<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');
// Tratador para evitar saída em branco ou HTML caso ocorra erro fatal
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

// Recebe os dados do fetch (JSON)
$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($dados['email'] ?? '');
$novoEmail = trim($dados['novoEmail'] ?? '');
$senha = trim($dados['senha'] ?? '');

if (empty($email) ) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, informe o e-mail.']);
    exit;
}
if (empty($novoEmail) ) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, informe o e-mail.']);
    exit;
}
if (empty($senha) ) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, informe a senha.']);
    exit;
}

try {
    // 1. Verifica se o e-mail existe no banco
    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE Email = :email');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail não encontrado no sistema.']);
        exit;
    }

    if(!password_verify($senha, $usuario['Senha'])){
        echo json_encode(['sucesso' => false, 'mensagem' => 'Senha incorreta.']);
        exit;
    }

    // 2. Gera o código de 6 dígitos e a expiração (15 minutos)
    $codigoVerificacao = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // 3. Salva no banco de dados
    $update = $pdo->prepare('UPDATE usuario SET codigo_verificacao = :codigo, codigo_expiracao = :expiracao WHERE Email = :email');
    $update->execute([
        ':codigo' => $codigoVerificacao,
        ':expiracao' => $expiracao,
        ':email' => $email
    ]);

    // 4. Chama a sua função de envio de e-mail existente
    // (Certifique-se de que a função retorna true em caso de sucesso e false em caso de falha)
    $enviado = enviarCodigo($novoEmail, $codigoVerificacao); 

    if ($enviado) {
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Código de verificação enviado para o seu e-mail!'
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao enviar o e-mail. Verifique a configuração do servidor.'
        ]);
    }
    exit;

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>