<?php
session_start();

if(!isset($_SESSION["usuario_id"])){
    header("Location: index.php");
    exit;
}

include("conexao.php");
$id = $_SESSION["usuario_id"];

$config = $conn->query("SELECT moeda, limite_gastos FROM configuracoes WHERE usuario_id=$id");
if($config->num_rows > 0){
    $dados = $config->fetch_assoc();
} else {
    $conn->query("INSERT INTO configuracoes (usuario_id, moeda, limite_gastos) VALUES ($id, 'BRL', 0.00)");
    $dados = ["moeda" => "BRL", "limite_gastos" => 0.00];
}

$mensagem = isset($_GET['msg']) ? $_GET['msg'] : "";

$sinalMoeda = "R$"; 
$moedaUsuario = "BRL"; 
if(isset($dados["moeda"])) {
    $moedaUsuario = $dados["moeda"];
    if($dados["moeda"] == "USD") { 
        $sinalMoeda = "$"; 
    } elseif($dados["moeda"] == "EUR") { 
        $sinalMoeda = "€"; 
    }
}

$limiteOriginalBrl = $dados["limite_gastos"] ?? 0.00;
$limiteConvertidoParaInput = converterMoedaExibicao($limiteOriginalBrl, $moedaUsuario);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaveUp - Configurações</title>
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
        <h2>Painel de Configurações</h2>
        <p style="color: #94a3b8; margin-top: 5px;">Ajuste as preferências reais do seu controle financeiro</p>
    </div>

    <?php if($mensagem == "sucesso"){ ?>
        <p style="color: #10b981; text-align: center; margin-top: 15px; font-weight: bold;">Configurações salvas com sucesso!</p>
    <?php } ?>

    <div class="cards-row" style="margin-top: 20px; justify-content: center; gap: 20px;">
        <div class="card-item" style="max-width: 450px; width: 100%;">
            <span class="label" style="color: #3b82f6; font-weight: bold;">Preferências do Painel 🪙</span>
            
            <form action="salvar_config.php" method="POST" style="margin-top: 15px;">
                <label style="font-size: 0.9rem; color: #94a3b8; display: block; margin-bottom: 5px;">Moeda do sistema:</label>
                <select name="moeda" required>
                    <option value="BRL" <?= $moedaUsuario == 'BRL' ? 'selected' : '' ?>>Real (R$)</option>
                    <option value="USD" <?= $moedaUsuario == 'USD' ? 'selected' : '' ?>>Dólar ($)</option>
                    <option value="EUR" <?= $moedaUsuario == 'EUR' ? 'selected' : '' ?>>Euro (€)</option>
                </select>

                <label style="font-size: 0.9rem; color: #94a3b8; display: block; margin-top: 15px; margin-bottom: 5px;">Meta de Gastos Mensal (em <?= $moedaUsuario ?>):</label>
                <input type="number" step="0.01" name="limite_gastos" value="<?= round($limiteConvertidoParaInput, 2) ?>" required placeholder="Ex: 150.00">
                <small style="color: #64748b; display: block; margin-top: 5px;">Deixe 0.00 para desativar o aviso.</small>

                <button type="submit" style="width: 100%; background: #3b82f6; margin-top: 20px;">Salvar Preferências</button>
            </form>
        </div>

        <div class="card-item" style="max-width: 450px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span class="label" style="color: #10b981; font-weight: bold;">Exportar Dados 📊</span>
                <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 15px; line-height: 1.4;">
                    Baixe uma planilha contendo todo o seu histórico de gastos e valores extras registrados na sua conta para abrir no Excel ou Google Planilhas.
                </p>
            </div>
            <a href="exportar_csv.php" style="display: block; text-align: center; margin-top: 20px; padding: 12px; background: #10b981; color: white; text-decoration: none; font-weight: bold; border-radius: 8px;">
                📥 Baixar Planilha (.csv)
            </a>
        </div>
    </div>
</main>
</body>
</html>