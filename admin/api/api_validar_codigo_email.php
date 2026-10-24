<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set("America/Sao_Paulo");

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
$novoEmail = trim($dados['novoEmail'] ?? '');
$codigoDigitado = trim($dados['codigo'] ?? '');

if (empty($novoEmail) || empty($codigoDigitado)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe o novo e-mail e o código de verificação.'
    ]);
    exit;
}

try {

    $adminId = $_SESSION['usuario_id'] ?? null;

    if (!$adminId) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Sessão expirada. Faça login novamente.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE ID_usuario = :id');
    $stmt->execute([':id' => $adminId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário não encontrado.'
        ]);
        exit;
    }

    // 2. Valida se o código digitado confere com o salvo no banco
    if ($usuario['codigo_verificacao'] !== $codigoDigitado) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Código de verificação incorreto.'
        ]);
        exit;
    }

    // 3. Valida se o código ainda está dentro do prazo de expiração
    $agora = date('Y-m-d H:i:s');
    if ($agora > $usuario['codigo_expiracao']) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'O código expirou. Solicite uma nova alteração.'
        ]);
        exit;
    }

    // 4. Verifica se o novo e-mail já não está cadastrado em outra conta
    $stmtCheck = $pdo->prepare('SELECT ID_usuario FROM usuario WHERE Email = :email AND ID_usuario != :id');
    $stmtCheck->execute([':email' => $novoEmail, ':id' => $adminId]);
    if ($stmtCheck->fetch()) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Este e-mail já está sendo utilizado por outro usuário.'
        ]);
        exit;
    }

    // 5. Atualiza o e-mail no banco e limpa os campos de código
    $update = $pdo->prepare('UPDATE usuario SET Email = :novo_email, codigo_verificacao = NULL, codigo_expiracao = NULL WHERE ID_usuario = :id');
    $update->execute([
        ':novo_email' => $novoEmail,
        ':id' => $adminId
    ]);

    // Opcional: Atualiza a sessão se você guardar o e-mail nela
    // $_SESSION['usuario_email'] = $novoEmail;

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'E-mail alterado com sucesso!'
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro de conexão com o banco de dados.'
    ]);
    exit;
}
?>