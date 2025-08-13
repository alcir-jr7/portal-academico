<?php

$host = '127.0.0.1'; // host sem porta aqui
$port = 3306;        // porta separada
$db = 'portal_academico';
$usuario = 'root';
$senha = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
