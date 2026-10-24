<?php
require_once __DIR__ . '/../../config/conexao.php';

ob_clean();

try {
    $sql = "SELECT 
        user.Nome AS nome,
        user.Email AS login,
        user.tipo_perfil AS perfil,
        user.ID_usuario AS id
        FROM usuario user 
        WHERE user.tipo_perfil = 'atendente' OR user.tipo_perfil = 'gerente'";

    $stmtBusca = $pdo->prepare($sql);
    $stmtBusca->execute();
    $usuarios = $stmtBusca->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao buscar usuários: " . $e->getMessage()]);
    exit;
}
?>