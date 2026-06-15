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
$sinalMoeda = "R$"; // Padrão
$buscarMoeda = $conn->query("SELECT moeda FROM configuracoes WHERE usuario_id=$id");
if($buscarMoeda && $buscarMoeda->num_rows > 0){
    $regMoeda = $buscarMoeda->fetch_assoc();
    if($regMoeda["moeda"] == "USD") { 
        $sinalMoeda = "$"; 
    } elseif($regMoeda["moeda"] == "EUR") { 
        $sinalMoeda = "€"; 
    }
}
// ----------------------------------

// --- VERIFICAÇÃO DE META DE GASTOS MENSAL ---
$limiteDefinido = 0;
$gastosMesAtual = 0;
$exibirAlertaMeta = false;

$buscarConfig = $conn->query("SELECT limite_gastos FROM configuracoes WHERE usuario_id=$id");
if($buscarConfig && $buscarConfig->num_rows > 0) {
    $configData = $buscarConfig->fetch_assoc();
    $limiteDefinido = $configData["limite_gastos"];
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
        $gastosMesAtual = $linhaMes["total"] ?? 0;
    }
    
    if($gastosMesAtual > $limiteDefinido) {
        $exibirAlertaMeta = true;
    }
}
// --------------------------------------------

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
        <h1 class="logo-text">SaveUp</h1>
        <p class="tagline">Organize sua vida financeira</p>
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
                Seus gastos este mês (<?= $sinalMoeda ?> <?= number_format($gastosMesAtual, 2, ',', '.') ?>) passaram do teto definido de <?= $sinalMoeda ?> <?= number_format($limiteDefinido, 2, ',', '.') ?>.
            </p>
        </div>
    <?php } ?>

    <section class="hero-section">
        <div class="card-hero">
            <span class="label">Saldo Total</span>
            <h2 class="value-large"><?= $sinalMoeda ?> <?= number_format($total,2,",",".") ?></h2>
        </div>
    </section>

    <section class="cards-row">
        <div class="card-item">
            <span class="label">Salários</span>
            <p class="value-medium"><?= $sinalMoeda ?> <?= number_format($salario,2,",",".") ?></p>
        </div>

        <div class="card-item">
            <span class="label">Valores Extras</span>
            <p class="value-medium"><?= $sinalMoeda ?> <?= number_format($extra,2,",",".") ?></p>
        </div>

        <div class="card-item">
            <span class="label">Gastos</span>
            <p class="value-negative"><?= $sinalMoeda ?> <?= number_format($gasto,2,",",".") ?></p>
        </div>
    </section>

    <div class="card-hero" style="margin-top: 40px; padding: 20px;">
    </div>

    <section class="cards-row">

        <div class="card-item">
            <span class="label" style="color: #ef4444; font-weight: bold;">Registrar Gasto 🔴</span>
            <form action="salvar_gasto.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor (<?= $sinalMoeda ?>)">
                <input type="text" name="descricao" required placeholder="Com o que gastou?">
                <button type="submit" style="width: 100%; background: #ef4444;">Salvar Gasto</button>
            </form>
        </div>

        <div class="card-item">
            <span class="label" style="color: #10b981; font-weight: bold;">Registrar Salário 🟢</span>
            <form action="salvar_salario.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor (<?= $sinalMoeda ?>)">
                <div style="height: 52px;"></div> 
                <button type="submit" style="width: 100%; background: #10b981;">Salvar Salário</button>
            </form>
        </div>

        <div class="card-item">
            <span class="label" style="color: #3b82f6; font-weight: bold;">Registrar Valor Extra 🔵</span>
            <form action="salvar_extra.php" method="POST">
                <input type="number" step="0.01" name="valor" required placeholder="Valor (<?= $sinalMoeda ?>)">
                <input type="text" name="descricao" required placeholder="Origem do extra?">
                <button type="submit" style="width: 100%; background: #3b82f6;">Salvar Extra</button>
            </form>
        </div>

    </section>

    <section class="details-column" style="margin-top: 40px; border: 1px solid #7f1d1d; background: #451a03; text-align: center; padding: 20px;">
        <p style="color: #fca5a5; margin-bottom: 15px; font-size: 0.95rem;">Atenção: A ação abaixo irá apagar permanentemente todos os seus gastos, salários e extras cadastrados.</p>
        <a href="zerar_dados.php" onclick="return confirm('Tem certeza absoluta que deseja apagar todo o seu histórico financeiro?')" style="display: inline-block; padding: 12px 24px; background: #dc2626; color: #fff; text-decoration: none; font-weight: bold; border-radius: 8px;">
            ⚠️ Zerar Todo o Histórico
        </a>
    </section>

</main>

</body>
</html>