<?php

session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_usuario = $_SESSION["usuario_id"];
    
    $valorDigitado = $_POST["valor"];
    $descricao = $_POST["descricao"]; 
    $data_atual = date("Y-m-d"); 

    // 1. Descobre qual a moeda que o painel do usuário está rodando no momento
    $moedaUsuario = "BRL";
    $buscarMoeda = $conn->query("SELECT moeda FROM configuracoes WHERE usuario_id=$id_usuario");
    if($buscarMoeda && $buscarMoeda->num_rows > 0){
        $regMoeda = $buscarMoeda->fetch_assoc();
        $moedaUsuario = $regMoeda["moeda"] ?? 'BRL';
    }

    // 2. Transforma o valor inserido (Ex: €15) para Real com base na API
    $valorConvertidoParaBrl = converterMoedaSalvamento($valorDigitado, $moedaUsuario);

    // 3. Grava o valor unificado em BRL no Banco de Dados
    $conn->query("INSERT INTO gastos (usuario_id, valor, descricao, data_gasto) VALUES ($id_usuario, $valorConvertidoParaBrl, '$descricao', '$data_atual')");

    header("Location: inicio.php");
    exit;
}
?>