<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");

$id_usuario = $_SESSION["usuario_id"];

// Deleta os registros das tabelas financeiras apenas do usuário logado
$conn->query("DELETE FROM gastos WHERE usuario_id = $id_usuario");
$conn->query("DELETE FROM salarios WHERE usuario_id = $id_usuario");
$conn->query("DELETE FROM valores_extras WHERE usuario_id = $id_usuario");

// Opcional: Se quiser resetar o saldo_atual da tabela saldo para 0
$conn->query("UPDATE saldo SET saldo_atual = 0 WHERE usuario_id = $id_usuario");

// Redireciona de volta para a página inicial limpa
header("Location: inicio.php");
exit;
?>