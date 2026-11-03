<?php
require_once __DIR__ . '/../vendor/autoload.php';

$host = '127.0.0.1:3307';
$user = 'root';
$pass = '';
$db = 'sistema_de_reservas';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   

}catch(PDOException $e){
    die(header("Location: confirmacao.php?status=server_error"));
}

?>
