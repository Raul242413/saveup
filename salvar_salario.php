<?php

session_start();
include("conexao.php");

$id = $_SESSION["usuario_id"];

$valor = $_POST["valor"];

$conn->query("
INSERT INTO salarios
(usuario_id,valor)
VALUES
($id,'$valor')
");

header("Location: inicio.php");
exit;

?>