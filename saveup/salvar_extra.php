<?php
session_start();

// Se o usuário não estiver logado, manda de volta para o ambiente de index.html
if(!isset($_SESSION["usuario_id"])){
    header("Location: index.html");
    exit;
}

include("conexao.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_usuario = $_SESSION["usuario_id"];
    $valor = $_POST["valor"];
    $descricao = $_POST["descricao"]; // Captura a descrição do extra

    // CORREÇÃO: Removida a coluna inexistente data_extra. O banco preenche data_cadastro sozinho!
    // Usando prepared statement para evitar qualquer erro de sintaxe com aspas no texto da descrição.
    $stmt = $conn->prepare("INSERT INTO valores_extras (usuario_id, valor, descricao) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $id_usuario, $valor, $descricao);

    if($stmt->execute()){
        // Redireciona com sucesso de volta para o início
        header("Location: inicio.php");
        exit;
    } else {
        // Caso queira debugar se houver outro erro externo
        echo "Erro ao salvar: " . $conn->error;
    }
}
?>