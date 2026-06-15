<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.html");
    exit;
}

include("conexao.php");
$id = $_SESSION["usuario_id"];

// 1. Apagar todos os dados financeiros vinculados ao ID do utilizador
$conn->query("DELETE FROM gastos WHERE usuario_id = $id");
$conn->query("DELETE FROM salarios WHERE usuario_id = $id");
$conn->query("DELETE FROM valores_extras WHERE usuario_id = $id");
$conn->query("DELETE FROM saldo WHERE usuario_id = $id");
$conn->query("DELETE FROM configuracoes WHERE usuario_id = $id");

// 2. Apagar o utilizador da tabela principal de acessos
$conn->query("DELETE FROM usuarios WHERE id = $id");

// 3. Destruir as variáveis de sessão ativas no navegador
session_unset();
session_destroy();

// 4. Redirecionar para a tela inicial de login/cadastro externa
header("Location: index.html");
exit;
?>