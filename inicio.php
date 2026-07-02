<?php

session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");

$id = $_SESSION["usuario_id"];

$salario = 0;
$extra = 0;
$gasto = 0;

$sql = $conn->query("
    SELECT saldo_atual
    FROM saldo
    WHERE usuario_id=$id
");

if($sql->num_rows > 0){
    $dado = $sql->fetch_assoc();
    $saldo = $dado["saldo_atual"];
}else{
    $saldo = 0;
}

$salarios = $conn->query("
    SELECT SUM(valor) total
    FROM salarios
    WHERE usuario_id=$id
");

if($linha=$salarios->fetch_assoc()){
    $salario = $linha["total"] ?? 0;
}

$extras = $conn->query("
    SELECT SUM(valor) total
    FROM valores_extras
    WHERE usuario_id=$id
");

if($linha=$extras->fetch_assoc()){
    $extra = $linha["total"] ?? 0;
}

$gastos = $conn->query("
    SELECT SUM(valor) total
    FROM gastos
    WHERE usuario_id=$id
");

if($linha=$gastos->fetch_assoc()){
    $gasto = $linha["total"] ?? 0;
}

$total = $saldo + $salario + $extra - $gasto;

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

// --- VERIFICAÇÃO DE META DE GASTOS MENSAL ---
$limiteDefinido = 0;
$gastosMesAtual = 0;
$exibirAlertaMeta = false;

$buscarConfig = $conn->query("SELECT limite_gastos FROM configuracoes WHERE usuario_id=$id");
if($buscarConfig && $buscarConfig->num_rows > 0) {
    $configData = $buscarConfig->fetch_assoc();
    $limiteDefinido = $configData["limite_gastos"]; // Limite está salvo em BRL
}

if($limiteDefinido > 0) {
    $mesAtual = date('m');
    $anoAtual = date('Y');
    
    $sqlGastosMes = $conn->query("
        SELECT SUM(valor) total 
        FROM gastos 
        WHERE usuario_id=$id AND MONTH(data_gasto) = '$mesAtual' AND YEAR(data_gasto) = '$anoAtual'
    ");
    
    if($linhaMes = $sqlGastosMes->fetch_assoc()) {
        $gastosMesAtual = $linhaMes["total"] ?? 0; // Gastos em BRL
    }
    
    // Comparação precisa direta em BRL
    if($gastosMesAtual > $limiteDefinido) {
        $exibirAlertaMeta = true;
    }
}

// --- CONVERSÃO DE EXIBIÇÃO VIA API ---
$totalConvertido = converterMoedaExibicao($total, $moedaUsuario);
$salarioConvertido = converterMoedaExibicao($salario, $moedaUsuario);
$extraConvertido = converterMoedaExibicao($extra, $moedaUsuario);
$gastoConvertido = converterMoedaExibicao($gasto, $moedaUsuario);
$gastosMesConvertido = converterMoedaExibicao($gastosMesAtual, $moedaUsuario);
$limiteConvertido = converterMoedaExibicao($limiteDefinido, $moedaUsuario);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaveUp - Início</title>
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

    <?php if($exibirAlertaMeta){ ?>
        <div class="card-hero" style="background: #7f1d1d; border: 1px solid #dc2626; padding: 15px; margin-bottom: 20px; text-align: center;">
            <h3 style="color: #fca5a5; margin: 0;">⚠️ Limite de Gastos Ultrapassado!</h3>
            <p style="color: #f87171; font-size: 0.9rem; margin-top: 5px; margin-bottom: 0;">
                Seus gastos este mês (<?= $sinalMoeda ?> <?= number_format($gastosMesConvertido, 2, ',', '.') ?>) passaram do teto definido de <?= $sinalMoeda ?> <?= number_format($limiteConvertido, 2, ',', '.') ?>.
            </p>
        </div>
    <?php } ?>

    <section class="hero-section">
        <div class="card-hero">
            <span class="label">Saldo Total</span>
            <h2 class="value-large"><?= $sinalMoeda ?> <?= number_format($totalConvertido, 2, ",", ".") ?></h2>
        </div>
    </section>

    <section class="cards-row">
        <div class="card-item">
            <span class="label">Salários</span>
            <p class="value-medium"><?= $sinalMoeda ?> <?= number_format($salarioConvertido, 2, ",", ".") ?></p>
        </div>

        <div class="card-item">
            <span class="label">Gastos</span>
            <p class="value-medium"><?= $sinalMoeda ?> <?= number_format($gastoConvertido, 2, ",", ".") ?></p>
        </div>

        <div class="card-item">
            <span class="label">Valores Extras</span>
            <p class="value-medium"><?= $sinalMoeda ?> <?= number_format($extraConvertido, 2, ",", ".") ?></p>
        </div>
    </section>

    <div class="card-hero" style="margin-top: 40px; padding: 20px;"></div>

    <section class="cards-row">
        <div class="card-item">
            <span class="label" style="color: #10b981; font-weight: bold;">Registrar Salário </span>
            <form action="salvar_salario.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor em (<?= $moedaUsuario ?>)">
                <div style="height: 52px;"></div> <button type="submit" style="width: 100%; background: #10b981;">Salvar Salário</button>
            </form>
        </div>

        <div class="card-item">
            <span class="label" style="color: #ff9500; font-weight: bold;">Registrar Gasto </span>
            <form action="salvar_gasto.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor em (<?= $moedaUsuario ?>)">
                <input type="text" name="descricao" required placeholder="Com o que gastou?">
                <button type="submit" style="width: 100%; background: #ff9500;">Salvar Gasto</button>
            </form>
        </div>

        <div class="card-item">
            <span class="label" style="color: #3b82f6; font-weight: bold;">Registrar Valor Extra </span>
            <form action="salvar_extra.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor em (<?= $moedaUsuario ?>)">
                <input type="text" name="descricao" required placeholder="Origem do extra?">
                <button type="submit" style="width: 100%; background: #3b82f6;">Salvar Extra</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>