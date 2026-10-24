<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
if (empty($_SESSION['usuario_logado'])) {
    header('Location: index.php');
    exit;
} else {
    $perfil = $_SESSION['perfil_usuario'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Manguezal - Mesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mesas/style.css">
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
                    <?php if ($perfil !== 'atendente'): ?>
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
                    <?php if ($perfil !== 'atendente'): ?>
                        <?php if ($perfil !== 'gerente'): ?>
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
                        <?php if ($perfil !== 'gerente'): ?>
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
                <?php if ($perfil !== 'atendente'): ?>
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
                <?php if ($perfil !== 'atendente'): ?>
                    <?php if ($perfil !== 'gerente'): ?>
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
                    <?php if ($perfil !== 'gerente'): ?>
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
        <section id="mesas">
            <div class="container-mesas">
                <div class="titulo">
                    <h1>Mesas</h1>
                </div>
                <div class="content">
                    <?php if ($perfil !== 'atendente'): ?>
                        <div class="container-adicionar">
                            <h2>Adicionar mesa</h2>
                            <div class="acoes-adicionar">
                                <div style="display: flex; gap:10px; align-items:center;">
                                    <p>Número da mesa: </p>
                                    <input id="numeroMesa" type="number" placeholder="Digite o numero da mesa">
                                </div>
                                <div style="display: flex; gap:10px; align-items:center;">
                                    <p>Capacidade máxima: </p>
                                    <input id="capacidadeMesa" type="number" placeholder="Digite a capacidade maxima da mesa">
                                </div>
                                <div style="display: flex; gap:10px; align-items:center;">
                                    <p>Localização: </p>
                                    <select id="localizacaoMesa">
                                        <option value="beach">Beach Club</option>
                                        <option value="deck" selected>Deck</option>
                                        <option value="salão">Salão Principal</option>
                                    </select>

                                </div>
                                <span id="span-erro" class="spn-erro-adicionar" style='display:none;'>Preencha os campos acima!</span>
                                <button class="btn-adicionar" onclick="adicionarMesa()">Adicionar / Modificar</button>
                                <button class="btn-iterar" onclick="iterarMesas()">Iterar mesas</button>
                            </div>
                        </div>
                        <h2>Mesas adicionadas</h2>
                    <?php endif; ?>
                    <p class="legenda-mesas" style='display:flex; flex-wrap:wrap;'>
                    <div class="container-filtrar">
                        <h2>Filtrar</h2>
                        <div>
                          <div class="numero">
                            <p>Numero</p><input type="number" id="numero">
                        </div>
                        <div class="status">
                            <p>Status</p>
                            <select name="status" id="status">]
                                <option value="Selecione um local" default>Selecione um status</option>
                                <option value="Livre" default>Livre</option>
                                <option value="Reservada">Reservada</option>
                                <option value="Ocupada">Ocupada</option>
                            </select>
                        </div>
                        <div class="localizacao">
                            <p>Local</p>
                            </p> <select name="local" id="local">
                                <option value="Selecione um local" default>Selecione um local</option>
                                <option value="deck">deck</option>
                                <option value="beach">beach</option>
                                <option value="salão">salão</option>
                                
                            </select>
                        </div>  
                        </div>
                        <div class="d-flex justify-content-end w-100">
                            <button class="btn-filtro" onclick="filtrar(event)">Procurar</button>
                        </div>
                    </div>
                    <div class="d-flex">
                       <span>🟢 Livre </span>
                    <span>🟡​ Reservada</span>
                    <span>🔴​ Ocupada</p> 
                    </div>
                    
                        <div class="container-display">
                            <div class=sem-mesas>
                                <div class="img-agenda">
                                    <img src="images/icone-nenhuma-mesa.png" alt="">
                                </div>
                                <div class="text-error-table">
                                    <p>Nenhuma mesa encontrada.</p>
                                </div>
                            </div>
                            <div class="display-mesas">


                            </div>
                        </div>

                </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script>
        const perfilUsuario = "<?= $_SESSION['perfil_usuario'] ?>";
    </script>
    <script src="js/mesas/script.js"></script>
</body>

</html>