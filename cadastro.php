<?php
session_start();
include("conexao.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$nome = $_POST["nome"] ?? '';
$email = $_POST["email"] ?? '';
$cpf = $_POST["cpf"] ?? '';
$data_nascimento = $_POST["data_nascimento"] ?? '';
$senha = $_POST["senha"] ?? '';

if($nome == '' || $email == '' || $cpf == '' || $data_nascimento == '' || $senha == ''){
    echo "Preencha todos os campos";
    exit;
}

// --- VALIDAÇÃO DE SEGURANÇA: MAIOR DE 18 ANOS ---
$nascimento = new DateTime($data_nascimento);
$hoje = new DateTime();
$idade = $hoje->diff($nascimento)->y;

if ($idade < 18) {
    echo "Cadastro bloqueado: Você precisa ter pelo menos 18 anos para gerenciar suas finanças.";
    exit;
}

// Limpa o CPF para guardar apenas números (ex: 11122233344)
$cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

/* verifica se o email ou o cpf já existem (ignorando registros vazios do banco) */
$verifica = $conn->query("SELECT id FROM usuarios WHERE email='$email' OR (cpf='$cpf_limpo' AND cpf IS NOT NULL AND cpf != '')");

if(!$verifica){
    echo "Erro SELECT: " . $conn->error;
    exit;
}

if($verifica->num_rows > 0){
    echo "E-mail ou CPF já cadastrado";
    exit;
}

/* insert incluindo as novas colunas */
$sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha)
VALUES ('$nome', '$email', '$cpf_limpo', '$data_nascimento', '" . password_hash($senha, PASSWORD_DEFAULT) . "')";

if(!$conn->query($sql)){
    echo "Erro INSERT: " . $conn->error;
    exit;
}

/* sessão */
$_SESSION["usuario_id"] = $conn->insert_id;

echo "sucesso";
?>