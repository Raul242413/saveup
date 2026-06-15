<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.html");
    exit;
}

include("conexao.php");
$id = $_SESSION["usuario_id"];

// Define o cabeçalho do arquivo para forçar o navegador a fazer o download de um arquivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=historico_saveup_' . date('Y-m-d') . '.csv');

// Cria o arquivo de saída na memória do PHP
$arquivo = fopen('php://output', 'w');

// Coloca o "BOM" no início do arquivo para que o Excel entenda os acentos em português corretamente
fprintf($arquivo, chr(0xEF).chr(0xBB).chr(0xBF));

// Escreve os títulos das colunas na primeira linha do Excel
fputcsv($arquivo, ['Tipo de Registro', 'Descrição/Origem', 'Valor', 'Data de Cadastro'], ';');

// Busca todos os Gastos e Valores Extras em uma única consulta organizada por data
$query = "
    SELECT 'Gasto' AS tipo, descricao, valor, data_gasto AS data_registro 
    FROM gastos 
    WHERE usuario_id = $id
    UNION ALL
    SELECT 'Valor Extra' AS tipo, descricao, valor, data_cadastro AS data_registro 
    FROM valores_extras 
    WHERE usuario_id = $id
    ORDER BY data_registro DESC
";

$resultado = $conn->query($query);

if($resultado) {
    while($linha = $resultado->fetch_assoc()) {
        // Formata a data para o padrão brasileiro (dd/mm/aaaa)
        $dataFormatada = date('d/m/Y', strtotime($linha['data_registro']));
        
        // Formata o valor com duas casas decimais e vírgula para o Excel reconhecer como dinheiro
        $valorFormatado = number_format($linha['valor'], 2, ',', '');

        // Adiciona a linha no arquivo CSV separando por ponto e vírgula
        fputcsv($arquivo, [
            $linha['tipo'],
            $linha['descricao'],
            $valorFormatado,
            $dataFormatada
        ], ';');
    }
}

// Fecha o arquivo e encerra o script
fclose($arquivo);
exit;
?>