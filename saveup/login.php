<?php
session_start();
include("conexao.php");

$email = $_POST["email"];
$senha = $_POST["senha"];

$sql = $conn->query("
SELECT * 
FROM usuarios 
WHERE email='$email'
");

if($sql->num_rows == 0){
    echo "erro";
    exit;
}

$usuario = $sql->fetch_assoc();

if(password_verify($senha, $usuario["senha"])){

    $_SESSION["usuario_id"] = $usuario["id"];
    $_SESSION["usuario_nome"] = $usuario["nome"];

    echo "sucesso";

}else{
    echo "erro";
}
?>