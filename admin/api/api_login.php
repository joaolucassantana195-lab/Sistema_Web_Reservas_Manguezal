<?php
session_start();
ob_clean();

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (ob_get_length()) {
    ob_clean();
}

require_once __DIR__ . '/../../config/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';

if (empty($email) || empty($senha)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos!'
    ]);
    exit;
}

try{
    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE Email = :email');
    $stmt->execute([':email' => $email]);
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if($usuario && password_verify($senha, $usuario['Senha'])){
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_id'] = $usuario['ID_usuario'];
        $_SESSION['perfil_usuario'] = $usuario['tipo_perfil'];

        echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso!',
        'redirect' => 'dashboard.php'
    ]);
        exit;
    }
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Email ou senha incorretos!'
    ]);
    exit;
}catch(PDOException $e){

     echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro de conexão com o servidor!'
            ]);
    exit;
}
?>