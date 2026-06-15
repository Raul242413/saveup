
<?php
session_start();
include("conexao.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$nome = $_POST["nome"] ?? '';
$email = $_POST["email"] ?? '';
$senha = $_POST["senha"] ?? '';

if($nome == '' || $email == '' || $senha == ''){
    echo "Preencha todos os campos";
    exit;
}

/* verifica email */
$verifica = $conn->query("SELECT id FROM usuarios WHERE email='$email'");

if(!$verifica){
    echo "Erro SELECT: " . $conn->error;
    exit;
}

if($verifica->num_rows > 0){
    echo "E-mail já cadastrado";
    exit;
}

/* insert */
$sql = "INSERT INTO usuarios (nome, email, senha)
VALUES ('$nome', '$email', '" . password_hash($senha, PASSWORD_DEFAULT) . "')";

if(!$conn->query($sql)){
    echo "Erro INSERT: " . $conn->error;
    exit;
}

/* sessão */
$_SESSION["usuario_id"] = $conn->insert_id;

echo "sucesso";
?>