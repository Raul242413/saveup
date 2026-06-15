<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_usuario = $_SESSION["usuario_id"];
    
    // É AQUI QUE O POST ENTRA: Pegando os dados digitados na tela!
    $valor = $_POST["valor"];
    $descricao = $_POST["descricao"]; 
    
    $data_atual = date("Y-m-d"); // Captura a data de hoje automaticamente

    // Insere no banco passando o valor E a descrição
    $conn->query("INSERT INTO gastos (usuario_id, valor, descricao, data_gasto) VALUES ($id_usuario, $valor, '$descricao', '$data_atual')");

    // Recarrega o início já atualizado
    header("Location: inicio.php");
    exit;
}
?>