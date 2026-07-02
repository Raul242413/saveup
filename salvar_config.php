<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.html");
    exit;
}

include("conexao.php");
$id = $_SESSION["usuario_id"];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $moeda = $_POST["moeda"];
    $limite_gastos = $_POST["limite_gastos"];

    // Atualiza moeda e limite usando prepared statements seguros
    $stmt = $conn->prepare("UPDATE configuracoes SET moeda = ?, limite_gastos = ? WHERE usuario_id = ?");
    $stmt->bind_param("sdi", $moeda, $limite_gastos, $id);
    
    if($stmt->execute()){
        header("Location: config.php?msg=sucesso");
        exit;
    } else {
        echo "Erro ao salvar configurações: " . $conn->error;
    }
}
?>