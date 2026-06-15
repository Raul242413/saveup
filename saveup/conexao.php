<?php

$host = "localhost";
$usuario = "root";
$senha = "usbw"; // Alterado de "" para "usbw"
$banco = "gestao_financeira";
$porta = 3306;   // Porta padrão do UsbWebServer para o MySQL

// Criando a conexão passando também a porta
$conn = new mysqli(
    $host,
    $usuario,
    $senha,
    $banco,
    $porta
);

if($conn->connect_error){
    die(
        "Erro de conexão: "
        . $conn->connect_error
    );
}

$conn->set_charset("utf8");

?>