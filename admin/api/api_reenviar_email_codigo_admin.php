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
$novoEmail = trim($dados['novoEmail'] ?? '');

if (empty($novoEmail)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, informe o novo e-mail.']);
    exit;
}

$tipoPerfil = $_SESSION['perfil_usuario'] ?? null;
$idAdmin = $_SESSION['usuario_id'] ?? null;

// Validação de permissão
if ($tipoPerfil != 'admin') {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro. Você não tem permissão para alterar o e-mail.' // Mensagem corrigida
    ]);
    exit;
}

try {
    // Geramos o código de 6 dígitos e a nova expiração (15 minutos)
    $codigoVerificacao = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Atualiza o registro no banco utilizando prioritariamente o ID da sessão
    if ($idAdmin) {
        $update = $pdo->prepare('UPDATE usuario SET codigo_verificacao = :codigo, codigo_expiracao = :expiracao WHERE ID_usuario = :id');
        $update->execute([
            ':codigo' => $codigoVerificacao,
            ':expiracao' => $expiracao,
            ':id' => $idAdmin
        ]);
    } else {
        // Caso não haja ID na sessão mas haja um e-mail base anterior salvo ou identificado, 
        // o ideal é atualizar com base no perfil ou retornar erro de sessão expirada.
        // Como o fluxo geralmente exige o ID do usuário logado, tratamos para evitar query vazia.
        echo json_encode([
            'sucesso' => false, 
            'mensagem' => 'Sessão do administrador não identificada. Faça login novamente.'
        ]);
        exit;
    }

    // Dispara o envio do e-mail para o novo endereço
    $enviado = enviarCodigo($novoEmail, $codigoVerificacao); 

    if ($enviado) {
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Novo código de verificação reenviado com sucesso!'
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao enviar o e-mail. Tente novamente mais tarde.'
        ]);
    }
    exit;

} catch (PDOException $e) {
    // Opcional: registrar o erro em log interno do servidor para debug se necessário ($e->getMessage())
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>