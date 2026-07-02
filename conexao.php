<?php

$host = "localhost";
$usuario = "root";
$senha = "usbw"; 
$banco = "gestao_financeira";
$porta = 3306;   

$conn = new mysqli($host, $usuario, $senha, $banco, $porta);

if($conn->connect_error){
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// --- FUNÇÃO AUXILIAR PARA PEGAR A TAXA ATUAL DA AWESOMEAPI ---
function obterTaxaAtual($moeda) {
    if ($moeda === 'BRL') return 1;
    
    $url = "https://economia.awesomeapi.com.br/json/last/" . $moeda . "-BRL";
    $resposta = @file_get_contents($url);

    if ($resposta === FALSE) {
        // Fallback (Segurança se faltar internet na apresentação)
        $cotacoesPadrao = ['USD' => 5.40, 'EUR' => 5.80];
        return $cotacoesPadrao[$moeda] ?? 1;
    }

    $dados = json_decode($resposta, true);
    $chavePar = $moeda . "BRL";
    return floatval($dados[$chavePar]['bid'] ?? 1);
}

// --- 1. USADA NAS TELAS (ENTRA BRL DO BANCO -> SAI MOEDA DO USUÁRIO) ---
function converterMoedaExibicao($valorBrl, $moedaDestino) {
    if ($moedaDestino === 'BRL') return $valorBrl;
    $taxa = obterTaxaAtual($moedaDestino);
    return $valorBrl / $taxa;
}

// --- 2. USADA NO SALVAMENTO (ENTRA VALOR DIGITADO -> SAI BRL PARA O BANCO) ---
function converterMoedaSalvamento($valorEstrangeiro, $moedaOrigem) {
    if ($moedaOrigem === 'BRL') return $valorEstrangeiro;
    $taxa = obterTaxaAtual($moedaOrigem);
    return $valorEstrangeiro * $taxa;
}
?>