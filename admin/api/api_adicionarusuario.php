<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();
header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

$nome   = trim($dados['nome'] ?? '');$acesso = trim($dados['acesso'] ?? $dados['login'] ?? '');
$senha  =$dados['senha'] ?? '';
$perfil = trim($dados['perfil'] ?? '');

if (empty($nome) || empty($acesso) || empty($senha) || empty($perfil)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

try {
    // 1. Verifica se o e-mail/usuário já está cadastrado
    $sqlCheck = "SELECT COUNT(*) FROM usuario WHERE Email = :acesso";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':acesso' =>$acesso]);

    if ($stmtCheck->fetchColumn() > 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Este usuário já está cadastrado.']);
        exit;
    }

    // 2. Cria o hash da senha e insere o novo usuário
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (`Nome`, `Status`, `Email`, `Senha`, `tipo_perfil`)
            VALUES (:nome, 'Ativo', :acesso, :senha, :perfil)";
    
    $stmt =$pdo->prepare($sql);$stmt->execute([
        ':nome'   => $nome,
        ':acesso' => $acesso,
        ':senha'  => $senhaHash,
        ':perfil' => $perfil
    ]);

    // Como verificar se a inserção funcionou:
    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Cadastrado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível cadastrar o usuário.']);
    }

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode(['sucesso' => false, 'erro' => 'Este e-mail/usuário já está cadastrado.']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao salvar os dados no banco.']);
    }
} catch (Throwable $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Ocorreu um erro inesperado no servidor.']);
}
exit;
?>