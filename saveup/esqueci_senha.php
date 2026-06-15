<?php
include("conexao.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $nova_senha = $_POST["nova_senha"];

    // 1. Verifica se o e-mail realmente existe na tabela 'usuarios'
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        
        // Criptografa a senha para ficar igual ao seu cadastro/login.php
        $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // 2. Atualiza a senha usando o hash seguro
        $update = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $update->bind_param("ss", $senha_criptografada, $email);
        
        if ($update->execute()) {
            // CORREÇÃO: Aponta para index.html (o arquivo real da sua pasta)
            $mensagem = "<p style='color: #2ecc71;'>Senha alterada com sucesso! <a href='index.html?aba=login' style='color: #fff; font-weight: bold;'>Clique aqui para fazer login</a></p>";
        } else {
            $mensagem = "<p style='color: #ff4d4d;'>Erro ao atualizar a senha no banco de dados.</p>";
        }
    } else {
        $mensagem = "<p style='color: #ff4d4d;'>O e-mail digitado não está cadastrado no sistema.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - SaveUp</title>
    <link rel="stylesheet" href="estilizacao.css">
</head>
<body style="background: #121212; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; margin: 0;">

    <div style="background: #1e1e1e; padding: 30px; border-radius: 8px; border: 1px solid #333; width: 100%; max-width: 400px; box-sizing: border-box;">
        <h2 style="color: #fff; margin-top: 0; text-align: center;">Recuperar Senha</h2>
        <p style="color: #aaa; font-size: 0.9rem; text-align: center; margin-bottom: 20px;">Informe o seu e-mail cadastrado e digite a nova senha.</p>
        
        <?= $mensagem ?>

        <form action="esqueci_senha.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Seu E-mail:</label>
                <input type="email" name="email" required placeholder="exemplo@email.com" style="width: 100%; padding: 10px; background: #333; color: #fff; border: 1px solid #444; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div>
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Nova Senha:</label>
                <input type="password" name="nova_senha" required placeholder="Digite a nova senha" style="width: 100%; padding: 10px; background: #333; color: #fff; border: 1px solid #444; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: #00bcd4; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px;">
                Alterar Senha
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.html?aba=login" style="color: #00bcd4; text-decoration: none; font-size: 0.9rem;">Voltar para o Login</a>
        </div>
    </div>

</body>
</html>