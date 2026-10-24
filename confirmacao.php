
<?php

$mensagens = [
    'sucesso' => 
       '<div class="confirmacao status-sucesso">
            <h1>Reserva enviada com sucesso! 🥂✅</h1>
            <p>Agradecemos por escolher o Restaurante Manguezal. Recebemos o seu pedido e já estamos preparando tudo para recebê-lo(a).</p>
            <h2>📩 O que acontece agora?</h2>
            <p>Em breve, enviaremos um e-mail com a confirmação definitiva e os detalhes da sua mesa.</p>

            <div class="alerta-nota">
                <strong>Nota:</strong> Se não encontrar o e-mail em sua caixa de entrada, verifique a pasta de Spam ou Lixo Eletrônico.
            </div>

            <div class="opcoes">
                <a href="index.php" class="btn btn-principal">Voltar ao Início</a>
                <a href="cardapio.php" class="btn btn-secundario">Ver Cardápio</a>
            </div>
        </div>'
    ,
    'reserva_existente' => 
        '<div class="confirmacao status-alerta">
            <h1>⚠️️ Reserva em duplicidade identificada</h1>
            <p>Notamos que você já possui uma solicitação em andamento para esta data.</p>
            <p>Para evitar reservas duplicadas por engano, não concluímos este novo pedido. Se você deseja cancelar a sua reserva existente, por favor entre em contato conosco ou verifique a mensagem de confirmação no seu e-mail.</p>
            <div class="opcoes">
                <a href="index.php" class="btn btn-principal">Voltar ao Início</a>
            </div>
        </div>'
    ,
    'data_passado' => 
        '<div class="confirmacao status-alerta">
            <h1>📅 Data ou horário indisponível</h1>
            <p>A data/horário selecionado já passou ou é inválido. Por favor, escolha uma data/horário a partir de hoje e dentro do funcionamento do restaurante para continuar com a sua reserva.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Alterar data</a>
            </div>
        </div>' 
    ,
    'dia_fechado' => 
        '<div class="confirmacao status-alerta">
            <h1>📅 Data indisponível</h1>
            <p>A data selecionada não está dentro do funcionamento. Por favor, escolha outra data dentro do funcionamento do restaurante para continuar com a sua reserva.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Alterar data</a>
            </div>
        </div>' 
    ,
    'feriado' => 
        '<div class="confirmacao status-alerta">
            <h1>📅 Ops! A data escolhida é um feriado.</h1>
            <p>Não funcionaremos na data selecionada. Por favor, escolha outra data dentro do funcionamento do restaurante para continuar com a sua reserva.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Alterar data</a>
            </div>
        </div>' 
    ,
    'horario_invalido' => 
        '<div class="confirmacao status-alerta">
            <h1>⚠️ Horário Indisponível</h1>
            <p>Por favor, selecione um horário válido dentro do expediente de funcionamento do restaurante.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Alterar horário</a>
            </div>
        </div>'
    ,
    'pessoas_invalido' => 
        '<div class="confirmacao status-alerta">
            <h1>👥 Quantidade de pessoas inválida</h1>
            <p>Aceitamos reservas online para grupos a partir de 20 pessoas. Para atendimentos individuais, não é necessário reservar: basta comparecer diretamente ao restaurante!</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Voltar ao Formulário</a>
            </div>
        </div>'
    ,
    'email_invalido' => 
        '<div class="confirmacao status-alerta">
            <h1>✉️ E-mail inválido ou inexistente</h1>
            <p>O domínio do e-mail informado parece não existir ou não está configurado para receber mensagens. Por favor, verifique se há erros de digitação.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Corrigir e-mail</a>
            </div>
        </div>'
    ,
    'server_error' => 
        '<div class="confirmacao status-erro">
            <h1>🛠️ Servidor Indisponível no Momento</h1>
            <p>Nossos sistemas estão passando por uma instabilidade momentânea. Já fomos notificados e estamos trabalhando para resolver o problema.</p>
            <p>Por favor, tente enviar sua reserva novamente em alguns minutos.</p>
            <div class="opcoes">
                <a href="index.php" class="btn btn-principal">Voltar ao Início</a>
            </div>
        </div>'
    ,
    'limite_reservas' => 
       '<div class="confirmacao status-erro">
            <h1>🛑 Limite máximo de reservas atingido</h1>
            <p>Atingimos o limite máximo de reservas para a data solicitada. Por isso, não foi possível concluir o seu agendamento.</p>
            <p>Por favor, retorne à página anterior e selecione outra data ou horário disponível.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Alterar data</a>
            </div>
        </div>',
    'erro' => 
       '<div class="confirmacao status-erro">
            <h1>🛑 Atenção aos dados informados</h1>
            <p>Não conseguimos enviar seu pedido porque algumas informações estão incompletas ou incorretas (como e-mail ou telefone).</p>
            <p>Por favor, revise os campos destacados no formulário e tente novamente.</p>
            <div class="opcoes">
                <a href="javascript:history.back()" class="btn btn-principal">Corrigir dados</a>
            </div>
        </div>'
];

$status = $_GET['status'] ?? 'erro';
$info = $mensagens[$status] ?? $mensagens['erro'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Manguezal - Confirmação</title>

    <!-- Fonte Google Inter para tipografia moderna -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <!-- CSS da página -->
    <link rel="stylesheet" href="css/confirmacao/style.css">
</head>
<body>
    <main>
        <section id="secao-confirmacao">
            <?php echo $info; ?>
        </section>
    </main>
</body>
</html>