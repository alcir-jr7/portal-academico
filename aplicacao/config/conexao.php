<?php

$host = 'localhost'; //o banco está rodando na sua própria máquina.
$db = 'portal_academico'; //nome do seu banco de dados.
$usuario = 'root'; //usuário do MySQL.
$senha = ''; //senha do usuário.

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $usuario,$senha);/*Tenta fazer conexão usado a classe PDO, '$pdo = new PDO' criação da classe
                                                                                    String de conexão, dizendo que é MySQL, qual host e banco, e que vai usar UTF-8. */ 

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);/* Define o modo de erro do PDO, após consulta com o SQL,
                                                                        o PHP vai lançar uma exceção (Exception), o que facilita tratar erros.*/
    }
    catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());//Esse bloco pega qualquer erro que acontecer na conexão e mostra a mensagem de erro.
}

?>