<?php

session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");

$id = $_SESSION["usuario_id"];

$ganhos = 0;
$gastos = 0;

$sqlGanhos = $conn->query("
SELECT
(
    IFNULL((SELECT SUM(valor) FROM salarios WHERE usuario_id=$id),0)
    +
    IFNULL((SELECT SUM(valor) FROM valores_extras WHERE usuario_id=$id),0)
) AS total
");

if($linha = $sqlGanhos->fetch_assoc()){
    $ganhos = $linha["total"];
}

$sqlGastos = $conn->query("
SELECT IFNULL(SUM(valor),0) AS total
FROM gastos
WHERE usuario_id=$id
");

if($linha = $sqlGastos->fetch_assoc()){
    $gastos = $linha["total"];
}

$saldoFinal = $ganhos - $gastos;

$listaGastos = $conn->query("
SELECT descricao, valor, data_gasto
FROM gastos
WHERE usuario_id=$id
ORDER BY id DESC
");

$listaExtras = $conn->query("
SELECT descricao, valor, data_cadastro
FROM valores_extras
WHERE usuario_id=$id
ORDER BY id DESC
");

// --- DETECTAR MOEDA DO USUÁRIO ---
$sinalMoeda = "R$"; 
$moedaUsuario = "BRL";
$buscarMoeda = $conn->query("SELECT moeda FROM configuracoes WHERE usuario_id=$id");
if($buscarMoeda && $buscarMoeda->num_rows > 0){
    $regMoeda = $buscarMoeda->fetch_assoc();
    $moedaUsuario = $regMoeda["moeda"] ?? 'BRL';
    if($moedaUsuario == "USD") { 
        $sinalMoeda = "$"; 
    } elseif($moedaUsuario == "EUR") { 
        $sinalMoeda = "€"; 
    }
}

// --- CONVERSÃO DOS TOTAIS DO CARD ---
$ganhosConvertidos = converterMoedaExibicao($ganhos, $moedaUsuario);
$gastosConvertidos = converterMoedaExibicao($gastos, $moedaUsuario);
$saldoFinalConvertido = converterMoedaExibicao($saldoFinal, $moedaUsuario);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SaveUp - Relatórios</title>
<link rel="stylesheet" href="estilizacao.css">
</head>
<body>

<header class="navbar">
    <div class="logo-container">
        <h1 class="logo-text"><img src="loguinho.jpeg" alt="SaveUp Logo" class="logo-split"width="100"></h1>
        <p style="font-size: 20px;" class="tagline">Organize sua vida financeira</p>
    </div>
<nav class="menu-links">
<a href="inicio.php">Início🏠</a>
<a href="relatorio.php">Relatórios🪧</a>
<a href="config.php">Configurações⚙️</a>
</nav>
</header>

<main class="dashboard-grid">

<div class="card-hero">
<h2>Resumo Financeiro</h2>
</div>

<div class="cards-row">
<div class="card-item">
<span class="label">Ganhos</span>
<p class="value-medium"><?= $sinalMoeda ?> <?= number_format($ganhosConvertidos,2,",",".") ?></p>
</div>
<div class="card-item">
<span class="label">Gastos</span>
<p class="value-negative"><?= $sinalMoeda ?> <?= number_format($gastosConvertidos,2,",",".") ?></p>
</div>
<div class="card-item">
<span class="label">Saldo Final</span>
<p class="value-medium"><?= $sinalMoeda ?> <?= number_format($saldoFinalConvertido,2,",",".") ?></p>
</div>
</div>

<div class="details-column">
<h3>Histórico de Gastos</h3>
<table width="100%">
<tr><th>Descrição</th><th>Valor</th><th>Data</th></tr>
<?php while($gasto = $listaGastos->fetch_assoc()){ 
    $gastoConvertido = converterMoedaExibicao($gasto["valor"], $moedaUsuario);
?>
<tr>
<td><?= htmlspecialchars($gasto["descricao"]) ?></td>
<td><?= $sinalMoeda ?> <?= number_format($gastoConvertido,2,",",".") ?></td>
<td><?= date("d/m/Y", strtotime($gasto["data_gasto"])) ?></td>
</tr>
<?php } ?>
</table>

<br><br>
<h3>Histórico de Valores Extras</h3>
<table width="100%">
<tr><th>Origem / Descrição</th><th>Valor</th><th>Data</th></tr>
<?php while($extra = $listaExtras->fetch_assoc()){ 
    $extraConvertido = converterMoedaExibicao($extra["valor"], $moedaUsuario);
?>
<tr>
<td><?= htmlspecialchars($extra["descricao"] ?? 'Sem descrição') ?></td>
<td style="color: #10b981;"><?= $sinalMoeda ?> <?= number_format($extraConvertido,2,",",".") ?></td>
<td><?= date("d/m/Y", strtotime($extra["data_cadastro"])) ?></td>
</tr>
<?php } ?>
</table>
</div>
</main>
</body>
</html>