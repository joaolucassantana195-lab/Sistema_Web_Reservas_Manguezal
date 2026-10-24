<?php
    
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
if(empty($_SESSION['usuario_logado'] )){
    header('Location: index.php');
    exit;
}else{
    $perfil = $_SESSION['perfil_usuario'];
    if ($perfil == 'atendente'){
         header('Location: mesas.php');
    exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Manguezal - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/configuracoes/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Semi+Expanded:wght@100;200;300;400;500;600;700;800;900&family=Fredoka:wght@300..700&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&family=Playwrite+NZ+Basic:wght@100..400&family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
  </head>

<body>
       <header>
        <nav>
            <div class="header-navegacao">
                <ul class="varela-round-regular">
                    <li><a href="#">Sistema de Reservas Manguezal</a></li>
                </ul>
                <div>
                </div>
            </div>
            <div class="navegacao-topo">
                
                <div class="container-navegacao">
                    <div class="logo">
                        <img class="logo-pequena" src="images/coqueiro-manquezal-logo.png" alt="logo-do-restaurante">
                        <img class="logo-grande" src="images/logo-restaurante.png" alt="logo-do-restaurante">
                    </div>
                    <div class="menu-nav">
                        <div class="container-open-menu">
                            <button class="btn-menu" onclick="abrirMenu(event)">
                                <img src="images/icone-menu.png" alt="abrir menu" width="100%" height="auto">
                            </button>
                        </div>
                    </div>
                </div>
            <div class="menu">
                <a class="dashboard" href="dashboard.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/incone-dashboard.png" alt="dashboard" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Dashboard
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'atendente'): ?>
                <a class="reserva" href="reservas.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-reserva.png" alt="reservas" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Reservas
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <a class="mesa" href="mesas.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-mesa.png" alt="mesas" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Mesas
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'atendente'): ?>
                <?php if($perfil !== 'gerente'): ?>
                <a class="usuario" href="usuarios.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-usuario.png" alt="usuarios" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Usuários
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <a class="clientes" href="clientes.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-clientes.png" alt="clientes" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Clientes
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'gerente'): ?>
                <a class="configuracoes" href="configuracoes.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-configuracoes.png" alt="configurações" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Configurações
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <?php endif; ?>
            <a class="sair" onclick="sair()">
                <div class="container-icone">
                    <div class="icone">
                        <img src="images/exit.png" alt="relatorios" width="100%" height="auto">
                    </div>
                    <div class="icone-name">
                        Sair
                    </div>
                </div>
            </a>
        </div>
    </div>
        </nav>
    </header>
    <main>
        <div class="navegacao-lateral">
            <div class="logo">
                <img class="logo-pequena" src="images/coqueiro-manquezal-logo.png" alt="logo-do-restaurante">
                <img class="logo-grande" src="images/logo-restaurante-nome.png" alt="logo-do-restaurante">
            </div>
            <div class="menu-nav">
                <a class="dashboard" href="dashboard.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/incone-dashboard.png" alt="dashboard" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Dashboard
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'atendente'): ?>
                <a class="reserva" href="reservas.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-reserva.png" alt="reservas" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Reservas
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <a class="mesa" href="mesas.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-mesa.png" alt="mesas" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Mesas
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'atendente'): ?>
                <?php if($perfil !== 'gerente'): ?>
                <a class="usuario" href="usuarios.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-usuario.png" alt="usuarios" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Usuários
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <a class="clientes" href="clientes.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-clientes.png" alt="clientes" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Clientes
                        </div>
                    </div>
                </a>
                <?php if($perfil !== 'gerente'): ?>
                <a class="configuracoes" href="configuracoes.php">
                    <div class="container-icone">
                        <div class="icone">
                            <img src="images/icone-configuracoes.png" alt="configurações" width="100%" height="auto">
                        </div>
                        <div class="icone-name">
                            Configurações
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                <?php endif; ?>
            <a class="sair" onclick="sair()">
                <div class="container-icone">
                    <div class="icone">
                        <img src="images/exit.png" alt="relatorios" width="100%" height="auto">
                    </div>
                    <div class="icone-name">
                        Sair
                    </div>
                </div>
            </a>
            </div>
        </div>
        <section id="section-configuracoes">
            <div class="container-configuracoes">
                <div class="titulo">
                    <h1>Configurações</h1>
                </div>
                <div id="configForm">
                  <div class="config-card">
              <div class="card-header">
                <i class="fa-solid fa-user-group"></i>
                <h2>Login administrativo</h2>
              </div>
              <div class="card-body" id="card-login">
                <div class="opcao">
                  <label class="opcao-label" for="email-admin">Email</label>
                  <span class="opcao-desc">Email de acesso ao painel administrativo.</span>
                  <div class="container-email d-flex gap-2">
                    <p id="email">adminemail@email.com</p><span class="editar-credenciais" onclick="editarEmail()"><p>Editar</p></span>
                  </div>
                  <div class="container-senha d-flex gap-2">
                    <p>(Sua senha)</p><span class="editar-credenciais" onclick="editarSenha()"><p>Editar</p></span>
                  </div>
                  </div>
                </div>
              </div>
            </div>

                <div class="config-card">
              <div class="card-header">
                <i class="fa-solid fa-user-group"></i>
                <h2>Limites & Capacidade</h2>
              </div>
              <div class="card-body">
                
                <div class="opcao">
                  <label class="opcao-label" for="minimo-pessoas">Mínimo de pessoas por reserva</label>
                  <span class="opcao-desc">Quantidade mínima de convidados permitida por agendamento.</span>
                  <input type="number" name="minimo_pessoas" id="minimo-pessoas" min="1"/>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="maximo-pessoas">Máximo de pessoas por reserva</label>
                  <span class="opcao-desc">Capacidade máxima por grupo de reserva.</span>
                  <input type="number" name="maximo_pessoas" id="maximo-pessoas" min="1"/>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="maximo-reservas">Limite máximo de reservas/dia</label>
                  <span class="opcao-desc">Quantidade total de reservas aceitas em um único dia.</span>
                  <input type="number" name="maximo_reservas" id="maximo-reservas" min="1"/>
                </div>
              </div>
            </div>

            <!-- Card 2: Horários & Funcionamento -->
            <div class="config-card">
              <div class="card-header">
                <i class="fa-solid fa-clock"></i>
                <h2>Horários & Funcionamento</h2>
              </div>
              <div class="card-body">

                <div class="opcao">
                  <label class="opcao-label">Dias de funcionamento</label>
                  <span class="opcao-desc">Marque os dias em que o estabelecimento aceita reservas.</span>
                  <div id="dias-semana" class="dias-semana-grid">
                    <div class="dia-item">
                      <input type="checkbox" id="dia-seg" name="dias_funcionamento" value="1">
                      <label class="dia-label" for="dia-seg">Seg</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[1]" id="horario-abertura-1">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[1]" id="horario-fechamento-1">
                      </div>
                      
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-ter" name="dias_funcionamento" value="2">
                      <label class="dia-label" for="dia-ter">Ter</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[2]" id="horario-abertura-2">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[2]" id="horario-fechamento-2">
                      </div>
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-qua" name="dias_funcionamento" value="3">
                      <label class="dia-label" for="dia-qua">Qua</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[3]" id="horario-abertura-3">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[3]" id="horario-fechamento-3">
                      </div>
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-qui" name="dias_funcionamento" value="4">
                      <label class="dia-label" for="dia-qui">Qui</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[4]" id="horario-abertura-4">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[4]" id="horario-fechamento-4">
                      </div>
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-sex" name="dias_funcionamento" value="5">
                      <label class="dia-label" for="dia-sex">Sex</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[5]" id="horario-abertura-5">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[5]" id="horario-fechamento-5">
                      </div>
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-sab" name="dias_funcionamento" value="6">
                      <label class="dia-label" for="dia-sab">Sáb</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[6]" id="horario-abertura-6">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[6]" id="horario-fechamento-6">
                      </div>
                    </div>
                    <div class="dia-item">
                      <input type="checkbox" id="dia-dom" name="dias_funcionamento" value="0">
                      <label class="dia-label" for="dia-dom">Dom</label>
                      <div class="container-horarios">
                        <label class="opcao-label" for="horario-abertura">Horário Abertura: </label>
                      <input type="time" name="horario_abertura[0]" id="horario-abertura-0">
                      <label class="opcao-label" for="horario-fechamento">Horário Fechamento: </label>
                      <input type="time" name="horario_fechamento[0]" id="horario-fechamento-0">
                      </div>
                    </div>
                  </div>
                </div>

                 <div class="opcao">
                  <label class="opcao-label">Feriados</label>
                  <span class="opcao-desc">Marque as datas em que o estabelecimento não funcionará. </span>
                  <div class="container-feriados-grid">
                    
                  </div>
                  <div>
                    <button class="adicionar-feriado" onclick="adicionarFeriado()"><span>+</span></button>
                  </div>
                </div>
                

                <div class="opcao">
                  <label class="opcao-label" for="antecedencia-minima">Antecedência mínima p/ reservar</label>
                  <div class="input-suffix">
                  <input type="number" id="antecedencia-minima" name="antecedencia_minima"/>
                  <span>horas</span>
                  </div>
                </div>

                 <div class="opcao">
                  <label class="opcao-label" for="antecedencia-maxima">Antecedência máxima para agendar</label>
                  <span class="opcao-desc">Com quanta antecedência o cliente pode fazer a reserva.</span>
                  <div class="input-suffix">
                    <input type="number" name="antecedencia_maxima" id="antecedencia-maxima" min="1"/>
                    <span>dias</span>
                  </div>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="horario-ultima-reserva">Horário limite da última reserva</label>
                  <input type="time" name="horario_ultima_reserva" id="horario-ultima-reserva"/>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="tolerancia-atraso">Tolerância de atraso</label>
                  <div class="input-suffix">
                    <input type="number" name="tolerancia_atraso" id="tolerancia-atraso" min="0"/>
                    <span>minutos</span>
                  </div>
                </div>

              </div>
            </div>

            <!-- Card 3: Opções Beach Club & Taxas -->
            <div class="config-card">
              <div class="card-header">
                <i class="fa-solid fa-umbrella-beach"></i>
                <h2>Beach Club & Taxas</h2>
              </div>
              <div class="card-body">

                <div class="opcao">
                  <label class="opcao-label" for="opcao-beach">Permitir reservas para Beach Club</label>
                  <span class="opcao-desc">Ativa a área exclusiva de espreguiçadeiras e quiosques.</span>
                  <select name="opcao_beach" id="opcao-beach">
                    <option value="sim">Sim, permitir reservas</option>
                    <option value="nao">Não, desativar Beach Club</option>
                  </select>
                </div>

                <div class="opcao" id="taxa-beach-container">
                  <label class="opcao-label" for="taxa-beach">Taxa do Beach Club por pessoa</label>
                  <span class="opcao-desc">Valor cobrado como consumação ou entrada individual.</span>
                  <div class="input-prefix">
                    <span>R$</span>
                    <input type="number" name="taxa_beach" id="taxa-beach" step="0.01" min="0"/>
                  </div>
                </div>

              </div>
            </div>

            <!-- Card 4: Notificações Automáticas -->
            <div class="config-card">
              <div class="card-header">
                <i class="fa-solid fa-bell"></i>
                <h2>Notificações & Lembretes</h2>
              </div>
              <div class="card-body">

               <div class="opcao">
                  <div class="switch-container">
                    <div>
                      <span class="opcao-label">Confirmação automática</span>
                      <span class="opcao-desc">Confirmar reservas e atribuir mesas automaticamente.</span>
                    </div>
                    <label class="switch">
                      <input type="checkbox" id="confirmacao-automatica" name="confirmacao-automatica">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>

                <hr style="border: none; border-top: 1px dashed var(--border-color);" />

                <div class="opcao">
                  <div class="switch-container">
                    <div>
                      <span class="opcao-label">E-mail de confirmação / cancelamento</span>
                      <span class="opcao-desc">Enviar notificações automáticas ao alterar status.</span>
                    </div>
                    <label class="switch">
                      <input type="checkbox" id="email-confirmacao" name="email_confirmacao">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>

                <hr style="border: none; border-top: 1px dashed var(--border-color);" />

                <div class="opcao">
                  <div class="switch-container">
                    <div>
                      <span class="opcao-label">Enviar lembrete prévio</span>
                      <span class="opcao-desc">Notificar o cliente antes do horário marcado.</span>
                    </div>
                    <label class="switch">
                      <input type="checkbox" id="enviar-lembrete" name="enviar_lembrete">
                      <span class="slider"></span>
                    </label>
                  </div>
                </div>

                <div class="opcao" id="antecedencia-lembrete-container">
                  <label class="opcao-label" for="antecedencia-lembrete">Antecedência do lembrete</label>
                  <div class="input-suffix">
                    <input type="number" name="antecedencia_lembrete" id="antecedencia-lembrete" min="1"/>
                    <span>horas</span>
                  </div>
                </div>

              </div>
            </div>
          <div class="actions-bar">
            <button type="button" class="btn-secondary" id="btnRestaurar"  onclick="restaurarPadroes()">
              <i class="fa-solid fa-rotate-left"></i> Restaurar Padrões
            </button>
            <button type="submit" class="btn-primary" onclick="salvarAlteracoes()">
              <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
            </button>
          </div>
</div>
        </div>
    </section>
  </main>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script src="js/configuracoes/script.js"></script>
</body>

</html>