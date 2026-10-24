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
    <title>Restaurante Manguezal - Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/reservas/style.css">
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
        <section id="reservas">
            <div class="content-reservas">
                <div class="titulo">
                    <h1>Reservas</h1>
                </div>
                <div class="content">
                    <div class="container-filtrar">
                        <h2>Filtrar tabela</h2>
                        <div class="data"><p>Data</p><input type="date" id="data"></div>
                        <div class="status">
                            <p>Status</p>
                            <select name="status" id="status">
                                <option value="todos">Todos</option>
                                <option value="Aguardando">Aguardando</option>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Cancelada">Cancelada</option>
                                <option value="Concluída">Concluída</option>
                                <option value="Não compareceu">Não compareceu</option>
                                <option value="Em Atendimento">Em Atendimento</option>
                            </select>
                        </div>
                        <div class="cliente"><p>Cliente</p></p><input type="text" name="cliente" id="cliente" placeholder="Insira o nome do cliente"></div>
                        <button class="btn-filtro" onclick="filtrar(event)">Procurar</button>
                    </div>
                    <h2>Resultado</h2>
                        
                        <div id="body-reservas" class="body-reservas">
                        </div>
                             <div class=sem-reservas>
                                <div class="img-agenda">
                                    <img src="images/icone-caderneta.png" alt="">
                                </div>
                                <div class="text-error-table">
                                <p>Nenhuma reserva encontrada.</p>
                                </div>
                                </div>
                        
                    </div>

                </div>
            </div>
        </section>
    </main>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script src="js/reservas/script.js"></script></body>
</html>