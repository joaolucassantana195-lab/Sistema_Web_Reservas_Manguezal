<?php
$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo "<div style='color: red; padding: 20px;'>Token inválido ou ausente.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Sistema Reservas Manguezal</title>
    <link rel="stylesheet" href="css/redefinirSenha/style.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
</head>
<body>
            <div class="card-login" id="card-redefinir">
                <div>
                <img src="images/logo-restaurante.png" alt="Restaurante-Manguezal">  
            </div>
            <h2>Alterar Senha</h2>
            <form id="formNovaSenha">
                <input type="hidden" name="token" id="token-input" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="opcao">
                    <label for="nova-senha">Digite a nova senha:</label>
                    <input type="password" name="nova-senha" id="nova-senha" required placeholder="Mínimo de 8 caracteres">
                </div>
                <div class="opcao">
                    <label for="rep-nova-senha">Repita a nova senha:</label>
                    <input type="password" name="rep-nova-senha" id="rep-nova-senha" required placeholder="Mínimo de 8 caracteres">
                </div>
                <span class="erro"></span>
                <div class="opcao acoes-card-login">
                    <button type="button" class="btn btn-primary" onclick="salvarNovaSenha()">Salvar Nova Senha</button>
                </div>
            </form>
            </div> 
       
    <script>
        async function salvarNovaSenha() {
            const token = document.getElementById('token-input').value;
            const novaSenha = document.getElementById('nova-senha').value;
            const repNovaSenha = document.getElementById('rep-nova-senha').value
            const spanErro = document.querySelector('.erro');


            if (!repNovaSenha || repNovaSenha.length < 8) {
                spanErro.textContent = 'A senha deve ter pelo menos 8 caracteres.';
                return;
            }
            if (repNovaSenha != novaSenha){
                spanErro.textContent = 'As senhas não são iguais.';
                return;
            }

            try {
                const response = await fetch('../admin/api/api_atualizar_senha_admin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: token, nova_senha: repNovaSenha })
                });

                const resultado = await response.json();
                spanErro.textContent = resultado.mensagem;

                if (resultado.sucesso) {                   
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao conectar com o servidor.');
            }
        }
    </script>
</body>
</html>