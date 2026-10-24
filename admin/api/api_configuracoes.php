<?php
require_once __DIR__ . '/../../config/conexao.php';

try{
    $stmt = $pdo->prepare('SELECT * FROM configuracoes');
    $stmt->execute();
    $configuracoes = $stmt->fetch(PDO::FETCH_ASSOC);

   if($configuracoes){
     echo json_encode($configuracoes);
    exit;
   }
   
}catch(PDOException $e){

}

?>