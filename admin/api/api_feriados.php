<?php
require_once __DIR__ . '/../../config/conexao.php';
 header('Content-Type: application/json; charset=utf-8');
 
 try { 
    
    $stmt = $pdo->prepare(" SELECT 
    id, 
    data, 
    descricao, 
    recorrente FROM feriados ORDER BY data ASC ");
 
 $stmt->execute();
 
 $feriados = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
 echo json_encode([ "sucesso" => true, "feriados" => $feriados ], JSON_UNESCAPED_UNICODE);
 
 } catch (PDOException $e) { http_response_code(500);
 
 echo json_encode(
    [ "sucesso" => false, "erro" => "Erro ao buscar feriados." ], JSON_UNESCAPED_UNICODE
    ); 
 
 } ?>