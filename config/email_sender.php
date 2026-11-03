<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/email.php';

function criarInstanciaMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = EMAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = EMAIL_USER;
    $mail->Password   = EMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = EMAIL_PORT;
    $mail->CharSet    = 'UTF-8';
    
    
    $mail->SMTPKeepAlive = true; 

    $mail->setFrom('contato.restaurante.manguezal@gmail.com', 'Restaurante Manguezal');
    $mail->isHTML(true);

    return $mail;
}

function enviarEmailConfirmacao($emailCliente, $nomeCliente, $data, $horario, $pessoas, $mesa, $beach, $regrasJson, $taxabeach, $contatoRestaurante, $contatoEmail){

    $regras = json_decode($regrasJson, true);
    $htmlRegras = '';

    if (is_array($regras)) {
        foreach ($regras as $regra) {
            $htmlRegras .= '
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>' . $regra['titulo'] . '</strong>
                        <p style="margin: 2px 0 0 0;">' . $regra['conteudo'] . '</p>
                    </td>
                </tr>';
        }
    }
    
    $mail = criarInstanciaMailer();

    try{
       
        $mail->addAddress($emailCliente, $nomeCliente);
        $mail->Subject = 'Reserva Confirmada — Restaurante Manguezal';
        
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="background-color: #ffffff !important; margin: 0; padding: 0;">
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff !important;">
    <!-- Topo Header -->
    <div style="background: #0d3b5e; padding: 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo do restaurante" style="margin: 0; width: 200px; display: block; margin: 0 auto;">
    </div>

    <!-- Conteúdo Principal -->
    <div style="padding: 30px; background: #f9f9f9 !important;">
        <h2 style="color: #0d3b5e; margin-top: 0;">Reserva Confirmada!</h2>
        <p>Olá, <strong>{$nomeCliente}</strong>! Sua reserva foi confirmada com sucesso.</p>

        <!-- Container da Reserva -->
        <div style="color: #022a55; background: #cfcfcf !important; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #207711; border-bottom: 4px solid #207711;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        <p style="margin: 5px 0;"><strong>📅 Data:</strong> {$data}</p>
                        <p style="margin: 5px 0;"><strong>🕐 Horário:</strong> {$horario}</p>
                        <p style="margin: 5px 0;"><strong>👥 Pessoas:</strong> {$pessoas}</p>
                        <p style="margin: 5px 0;"><strong>🪑 Mesa:</strong> {$mesa}</p>
                        <p style="margin: 5px 0;"><strong>🏖️ Beach Club:</strong> {$beach}</p>
                    </td>
                   
                </tr>
            </table>
        </div>

        <!-- Regras -->
        <div style="margin-top: 40px;">
            <h3 style="color: #fd0404; margin-bottom: 20px;">Atenção às regras!</h3>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>⏰ Tolerância de Atraso</strong>
                        <p style="margin: 2px 0 0 0;">Mesa garantida por até 15 minutos após o horário agendado.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>🏝️ Beach-Club</strong>
                        <p style="margin: 2px 0 0 0;">Cobramos presencialmente uma taxa de R$ {$taxabeach} por pessoa para utilização da infraestrutura da área restrita.</p>
                    </td>
                </tr>
                {$htmlRegras}
            </table>
        </div>

        <!-- Botão WhatsApp -->
        <div style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 20px;">Em caso de dúvidas e cancelamento, entre em contato pelo whatsapp.</p>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" style="color: #ffffff; text-decoration: none; background-color: #ff9100; padding: 12px 24px; border-radius: 20px; font-size: 18px; display: inline-block;">Entrar em contato</a>
        </div>

        <p style="color: #888888; font-size: 12px; margin-top: 50px; text-align: center;">Restaurante Manguezal — Itaparica, BA</p>
    </div>

    <!-- Rodapé -->
    <div style="background-color: #082832; color: #ffffff; padding: 30px 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo" style="width: 180px; margin-bottom: 20px; display: block; margin: 0 auto 20px auto;">
        
        <p style="line-height: 1.6; margin-bottom: 30px; font-size: 0.95rem;">
            Restaurante & Beach Club à beira-mar. Gastronomia regional, frutos do mar frescos e os melhores drinks tropicais.
        </p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Contato</h4>
        <div style="margin-bottom: 30px;">
            <a href="https://instagram.com/restaurantemanguezal" target="_blank" style="text-decoration: none; margin-right: 10px;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/instagramlogo.png" alt="Instagram">
            </a>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" target="_blank" style="text-decoration: none;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/whatsapplogo.png" alt="Whatsapp">
            </a>
        </div>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Funcionamento</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Restaurante:</strong> Todos os dias, das 9h às 17h.</p>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Buffet a Kilo:</strong> Diariamente, das 12h às 15h.</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;"><strong>Beach Club:</strong> A partir das 09h.</p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Onde Estamos</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;">📍 R. A, 383-343 - Lot. Nova Itaparica</p>
        <p style="margin: 5px 0; font-size: 0.9rem;">📞 {$contatoRestaurante}</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;">✉️ {$contatoEmail}</p>

        <div style="border-top: 1px solid #144353; padding-top: 15px; margin-top: 20px;">
            <p style="font-size: 0.8rem; margin: 0;">&copy; 2026 Restaurante Manguezal. Todos os direitos reservados.</p>
        </div>
    </div>
</div>
</body>
</html>
HTML;

        $mail->send();
        return true;

    }catch(Exception $e){
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}

function enviarEmailCancelamento($emailCliente, $nomeCliente, $data, $horario, $pessoas, $beach, $motivo, $contatoRestaurante, $contatoEmail){
    
    $mail = criarInstanciaMailer();

    try{
        $mail->addAddress($emailCliente, $nomeCliente);

        $mail->isHTML(true);
        $mail->Subject = 'Reserva Cancelada — Restaurante Manguezal';
        
        // Uso de Heredoc (<<<HTML ... HTML;) para renderizar variáveis e manter a leitura limpa
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="background-color: #ffffff !important; margin: 0; padding: 0;">
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff !important;">
    <!-- Topo Header -->
    <div style="background: #0d3b5e; padding: 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo" style="margin: 0; width: 200px; display: block; margin: 0 auto;">
    </div>

    <!-- Conteúdo Principal -->
    <div style="padding: 30px; background: #f9f9f9 !important;">
        <h2 style="color: #0d3b5e; margin-top: 0;">Reserva Cancelada!</h2>
        <p>Olá, <strong>{$nomeCliente}</strong>! Sua reserva foi <strong style="color:#fd0404;">cancelada</strong>.</p>

        <!-- Container da Reserva -->
        <div style="color: #000000; background: #cfcfcf !important; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #fd0404; border-bottom: 4px solid #fd0404;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        <p style="margin: 5px 0;"><strong>📅 Data:</strong> {$data}</p>
                        <p style="margin: 5px 0;"><strong>🕐 Horário:</strong> {$horario}</p>
                        <p style="margin: 5px 0;"><strong>👥 Pessoas:</strong> {$pessoas}</p>
                        <p style="margin: 5px 0;"><strong>🪑 Mesa: S/N</strong> </p>
                        <p style="margin: 5px 0;"><strong>🏖️ Beach Club:</strong> {$beach}</p>
                    </td>
                   
                </tr>
            </table>
        </div>

        <!-- Regras -->

        <div style="margin-top: 40px;">
            <h3 style="color: #fd0404; margin-bottom: 20px;">Motivo do cancelamento: </h3>
            <div style='width:100%;'>
                {$motivo}
            </div>
        </div>

        <!-- Botão WhatsApp -->
        <div style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 20px;">Em caso de dúvidas, entre em contato pelo whatsapp.</p>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" style="color: #ffffff; text-decoration: none; background-color: #ff9100; padding: 12px 24px; border-radius: 20px; font-size: 18px; display: inline-block;">Entrar em contato</a>
        </div>

        <p style="color: #888888; font-size: 12px; margin-top: 50px; text-align: center;">Restaurante Manguezal — Itaparica, BA</p>
    </div>

    <!-- Rodapé -->
    <div style="background-color: #082832; color: #ffffff; padding: 30px 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo" style="width: 180px; margin-bottom: 20px; display: block; margin: 0 auto 20px auto;">
        
        <p style="line-height: 1.6; margin-bottom: 30px; font-size: 0.95rem;">
            Restaurante & Beach Club à beira-mar. Gastronomia regional, frutos do mar frescos e os melhores drinks tropicais.
        </p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Contato</h4>
        <div style="margin-bottom: 30px;">
            <a href="https://instagram.com/restaurantemanguezal" target="_blank" style="text-decoration: none; margin-right: 10px;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/instagramlogo.png" alt="Instagram">
            </a>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" target="_blank" style="text-decoration: none;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/whatsapplogo.png" alt="Whatsapp">
            </a>
        </div>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Funcionamento</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Restaurante:</strong> Todos os dias, das 9h às 17h.</p>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Buffet a Kilo:</strong> Diariamente, das 12h às 15h.</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;"><strong>Beach Club:</strong> A partir das 09h.</p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Onde Estamos</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;">📍 R. A, 383-343 - Lot. Nova Itaparica</p>
        <p style="margin: 5px 0; font-size: 0.9rem;">📞 {$contatoRestaurante}</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;">✉️ {$contatoEmail}</p>

        <div style="border-top: 1px solid #144353; padding-top: 15px; margin-top: 20px;">
            <p style="font-size: 0.8rem; margin: 0;">&copy; 2026 Restaurante Manguezal. Todos os direitos reservados.</p>
        </div>
    </div>
</div>
</body>
</html>
HTML;

        $mail->send();
        return true;

    }catch(Exception $e){
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}

function enviarLembreteEmail($emailCliente, $nomeCliente, $data, $horario, $pessoas, $mesa, $beach, $regrasJson, $taxabeach, $contatoRestaurante, $contatoEmail){

    $regras = json_decode($regrasJson, true);
    $htmlRegras = '';

    if (is_array($regras)) {
        foreach ($regras as $regra) {
            $htmlRegras .= '
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>' . $regra['titulo'] . '</strong>
                        <p style="margin: 2px 0 0 0;">' . $regra['conteudo'] . '</p>
                    </td>
                </tr>';
        }
    }

    $mail = criarInstanciaMailer();

    try{
        $mail->addAddress($emailCliente, $nomeCliente);
        $mail->Subject = 'Lembrete Reserva — Restaurante Manguezal';
        
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="background-color: #ffffff !important; margin: 0; padding: 0;">
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff !important;">
    
    <div style="background: #0d3b5e; padding: 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo" style="margin: 0; width: 200px; display: block; margin: 0 auto;">
    </div>

    
    <div style="padding: 30px; background: #ececec !important;">
        <h2 style="color: #0d3b5e; margin-top: 0;">Não esqueça da sua reserva!</h2>
        <p>Olá, <strong>{$nomeCliente}</strong>! Você possui uma reserva <strong style="color: #20ac00;">confirmada</strong>.</p>

        
        <div style="color: #000000; background:  #f9f9f9 !important; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #ffae00; border-bottom: 4px solid #ffae00;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        <p style="margin: 5px 0;"><strong>📅 Data:</strong> {$data}</p>
                        <p style="margin: 5px 0;"><strong>🕐 Horário:</strong> {$horario}</p>
                        <p style="margin: 5px 0;"><strong>👥 Pessoas:</strong> {$pessoas}</p>
                        <p style="margin: 5px 0;"><strong>🪑 Mesa: {$mesa} </p>
                        <p style="margin: 5px 0;"><strong>🏖️ Beach Club:</strong> {$beach}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 40px;">
            <h3 style="color: #fd0404; margin-bottom: 20px;">Atenção às regras!</h3>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>⏰ Tolerância de Atraso</strong>
                        <p style="margin: 2px 0 0 0;">Mesa garantida por até 15 minutos após o horário agendado.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 15px;">
                        <strong>🏝️ Beach-Club</strong>
                        <p style="margin: 2px 0 0 0;">Cobramos presencialmente uma taxa de R$ {$taxabeach} por pessoa para utilização da infraestrutura da área restrita.</p>
                    </td>
                </tr>
                {$htmlRegras}
            </table>
        </div>

        <!-- Botão WhatsApp -->
        <div style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 20px;">Em caso de dúvidas, entre em contato pelo whatsapp.</p>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" style="color: #ffffff; text-decoration: none; background-color: #ff9100; padding: 12px 24px; border-radius: 20px; font-size: 18px; display: inline-block;">Entrar em contato</a>
        </div>

        <p style="color: #888888; font-size: 12px; margin-top: 50px; text-align: center;">Restaurante Manguezal — Itaparica, BA</p>
    </div>

    <!-- Rodapé -->
    <div style="background-color: #082832; color: #ffffff; padding: 30px 20px; text-align: center;">
        <img src="https://restaurante-manguezal.alwaysdata.net/images/logo-restaurante.png" alt="logo" style="width: 180px; margin-bottom: 20px; display: block; margin: 0 auto 20px auto;">
        
        <p style="line-height: 1.6; margin-bottom: 30px; font-size: 0.95rem;">
            Restaurante & Beach Club à beira-mar. Gastronomia regional, frutos do mar frescos e os melhores drinks tropicais.
        </p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Contato</h4>
        <div style="margin-bottom: 30px;">
            <a href="https://instagram.com/restaurantemanguezal" target="_blank" style="text-decoration: none; margin-right: 10px;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/instagramlogo.png" alt="Instagram">
            </a>
            <a href="https://api.whatsapp.com/send/?phone={$contatoRestaurante}&text&type=phone_number&app_absent=0&utm_source=ig" target="_blank" style="text-decoration: none;">
                <img style="width: 35px; vertical-align: middle;" src="https://restaurante-manguezal.alwaysdata.net/images/whatsapplogo.png" alt="Whatsapp">
            </a>
        </div>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Funcionamento</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Restaurante:</strong> Todos os dias, das 9h às 17h.</p>
        <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Buffet a Kilo:</strong> Diariamente, das 12h às 15h.</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;"><strong>Beach Club:</strong> A partir das 09h.</p>

        <h4 style="margin-bottom: 10px; color: #ffffff;">Onde Estamos</h4>
        <p style="margin: 5px 0; font-size: 0.9rem;">📍 R. A, 383-343 - Lot. Nova Itaparica</p>
        <p style="margin: 5px 0; font-size: 0.9rem;">📞 {$contatoRestaurante}</p>
        <p style="margin: 5px 0 30px 0; font-size: 0.9rem;">✉️ {$contatoEmail}</p>

        <div style="border-top: 1px solid #144353; padding-top: 15px; margin-top: 20px;">
            <p style="font-size: 0.8rem; margin: 0;">&copy; 2026 Restaurante Manguezal. Todos os direitos reservados.</p>
        </div>
    </div>
</div>
</body>
</html>
HTML;

        $mail->send();
        return true;

    }catch(Exception $e){
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}

function enviarCodigo($emailAdmin, $codigoVerificacao){
    $mail = criarInstanciaMailer();

    try{
        $mail->addAddress($emailAdmin);
        $mail->Subject = 'Código de verificação — Sistema de reservas';
        
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="background-color: #ffffff !important; margin: 0; padding: 0;">
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff !important;">
            <div style="display: flex; justify-content: center;">
                <img src="https://restaurante-manguezal.alwaysdata.net/admin/images/banner-email-verificação.png" alt="Código de verificação" width="100%" height="auto">  
            </div>
        <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
            <h2>Recuperação de Acesso</h2>
            <p>Você solicitou a alteração de email para o painel administrativo.</p>
            <p>Utilize o código abaixo para prosseguir. Ele é válido por <strong>15 minutos</strong>:</p>
            <div style='background: #f4f4f4 !important; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; width: 200px; margin: 20px 0;'>
                {$codigoVerificacao}
            </div>
            <p>Se você não solicitou isso, ignore este e-mail.</p>
            </div>
        </div>
</body>
</html>
HTML;

        $mail->send();
        return true;

    }catch(Exception $e){
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}
function enviarToken($emailAdmin, $token){
    $mail = criarInstanciaMailer();

    try{
        $mail->addAddress($emailAdmin);

        $linkRedefinicao = "https://restaurante-manguezal.alwaysdata.net/admin/redefinir_senha.php?token=" . $token;

        $mail->Subject = 'Token de Redefinição — Sistema de reservas';
        
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="background-color: #ffffff !important; margin: 0; padding: 0;">
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff !important;">
            <div style="display: flex; justify-content: center;">
                <img src="https://restaurante-manguezal.alwaysdata.net/admin/images/banner-alteracao-senha.png" alt="Código de verificação" width="100%" height="auto">  
            </div>
        <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
            <h2>Link para redefinir senha</h2>
            <p>Você solicitou a alteração de senha para o painel administrativo.</p>
            <p>Clique no botão abaixo para prosseguir. Ele é válido por <strong>30 minutos</strong>:</p>
            <div style='background: #f4f4f4 !important; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; width: auto; margin: 20px 0;'>
                <a href="{$linkRedefinicao}" style="text-decoration: none; letter-spacing: normal; color: white; background-color: rgb(255, 169, 83); padding: 10px 20px; border-radius: 10px;">
                    Recuperar senha
                </a>
            </div>
            <p>Se você não solicitou isso, ignore este e-mail.</p>
            </div>
        </div>
</body>
</html>
HTML;

        $mail->send();
        return true;

    }catch(Exception $e){
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}