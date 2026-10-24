<?php
require_once __DIR__ . '/../../config/conexao.php';

// Limpa qualquer saída no buffer antes do JSON
if (ob_get_length()) {
    ob_clean();
}

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$nome = $dados['nome'] ?? null;
$email = $dados['email'] ?? null;
$telefone = $dados['telefone'] ?? null;

try {
    $where = [];
    $params = [];

    if (!empty($nome)) {
        $where[] = 'c.Nome LIKE :nome';
        $params[':nome'] = "%{$nome}%";
    }

    if (!empty($email)) {
        $where[] = 'c.Email LIKE :email';
        $params[':email'] = "%{$email}%";
    }

    if (!empty($telefone)) {
        $where[] = 'c.Telefone LIKE :telefone';
        $params[':telefone'] = "%{$telefone}%";
    }
    
    $sql = '
        SELECT 
            c.Nome AS nome,
            c.ID_cliente AS id,
            c.Telefone AS telefone,
            c.Email AS email
        FROM cliente c
    ';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clientes);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar clientes']);
    exit;
}
?>