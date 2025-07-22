<?php

require('../aplicacao/config/conexao.php'); // abre um conexao $pdo com seu banco

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $nome = $_POST['nome'] ?? '';
        $matricula = $_POST['matricula'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        if(!$nome || !$matricula || !$senha || !$tipo){
            echo 'É obrigatório preencher todos os campos!';
            exit;
        } 

        $tiposPermitidos = ['aluno', 'professor', 'admin'];
        if(!in_array($tipo,$tiposPermitidos, true)){
            echo'Tipo de usuário inválido.';
            exit;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome,matricula,senha,tipo) 
                VALUES (:nome, :matricula, :senha, :tipo)";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                        ':nome' => $nome, 
                        ':matricula' => $matricula, 
                        ':senha' => $senhaHash, 
                        ':tipo' => $tipo
                    ]);
        
        echo'Usuario cadastrado!';
        echo'<br><a href="cadastrar_usuario.php">Voltar</a>';

    } catch (PDOException $e) {
        
        if($e -> getCode() === '23000') {
            echo'Matricula já existe';
        } else {
            echo 'Erro:' . $e->getMessage();
        }
    } 
} else {
    header('Location: cadastrar_usuario.php');
    exit;
}
?>
