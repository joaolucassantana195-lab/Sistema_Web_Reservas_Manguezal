<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurante Manguezal</title>
  <link rel="stylesheet" href="css/index/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,500&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Semi+Expanded+Playfair+Display:wght@100;200;300;400;500;600;700;800;900&family=Fredoka:wght@300..700&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&family=Playwrite+NZ+Basic:wght@100..400&family=Varela+Round&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
</head>
<body>
  <header> 
    <nav>
      <div>
        <a href="#home" class="varela-round-regular">
           <span class="welcome-header">Bem vindo ao Restaurante Manguezal</span> 
           <div class="logo-header">
            <img src="images/coqueiro-manquezal-logo.png" alt="logo" width="100%" height="100%">
           </div>
        </a>
      </div>
      <ul class="links" class="varela-round-regular">
        <li class="nav-item">
          <a class="nav-link" href="https://restaurante-manguezal.alwaysdata.net">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cardapio.php">Cardápio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sobre.php">Sobre</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://api.whatsapp.com/send/?phone=557197129517&text&type=phone_number&app_absent=0&utm_source=ig">Contato</a>
        </li>
      </ul>
    </nav>
    <div class="menu">
      <button class="btn-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><span><img src="images/icone-menu.png" alt="menu" width="100%" height="auto"></span></button>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
      <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasRightLabel">Navegar</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
      <nav>
      <ul id="links-menu" class="varela-round-regular">
        <li class="nav-item">
          <a class="nav-link" href="https://restaurante-manguezal.alwaysdata.net">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cardapio.php">Cardápio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sobre.php">Sobre</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://api.whatsapp.com/send/?phone=557197129517&text&type=phone_number&app_absent=0&utm_source=ig">Contato</a>
        </li>
      </ul>
    </nav>
      </div>
      </div>
    </div>
  </header>
  <main>
    <section id="home">
     <div class="home-relative">
      <div class="home-info">
        <div>
          <img src="images/logo-restaurante.png" alt="">
        </div>
        <div>
          <p>Quem tem boca vai a Roma, mas quem tem bom gosto vem para o Manguezal!</p>
        </div>
        <div>
          <a href="#reserva">Reservar agora</a>
        </div>
      </div>
     </div>
     <div class="hero-overlay"></div>
     <div class="video-pc">
       <div class="home-video">
            <div style="padding:56.25% 56.25%  56.25% 56.25% ;position:relative;"><iframe src="https://player.vimeo.com/video/1235482698?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479&amp;autoplay=1&amp;muted=1&amp;loop=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="homevideo.mp4"></iframe></div>
      </div>
    </div>
    <div class="video-mobile">
      <div class="home-video">
            <div style="padding:177.78% 0 177.78%  140%;position:relative;"><iframe src="https://player.vimeo.com/video/1235482695?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479&amp;autoplay=1&amp;muted=1&amp;loop=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="homevideomobile.mp4"></iframe></div>
      </div>
    </div>
     

    </section>
    <section id="comidas">
      <div id="container-comidas" class="invisivel">
        <h2>Comida que tem gosto de férias, praia e bons momentos.</h2>
        <div id="container-cards-comidas" >
          <h3 class="mt-4">Delicias no cadarpio 😋🍽️</h3>
          <div id="container-cards-display">
                <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Drinks</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Drinks.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php" class="card-comida">
                    <div id="container-img-comida">
                    <h3>Sobremesas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Sobremesas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Saladas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Saladas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Veganos</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/veganos.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Mariscadas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/mariscada.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Moquecas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/moquecas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Drinks</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Drinks.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php" class="card-comida">
                    <div id="container-img-comida">
                    <h3>Sobremesas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Sobremesas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Saladas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/Saladas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Veganos</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/veganos.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Mariscadas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/mariscada.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
                  <a href="cardapio.php"  class="card-comida">
                    <div id="container-img-comida">
                    <h3>Moquecas</h3>
                    <div id="container-card-img">
                    <img id="card-img" src="images/moquecas.jpg" alt="imagem-154x160">
                    </div>
                    <p class="mt-3">Saiba mais +</p>
                  </div>
                  </a>
          </div>
          </div>
        </div>
    </section>
    <section id="areas">
      <div id="areas-bangalos-container" class="invisivel">
          <h2><strong>Conheça nossos espaços</strong></h2>
          <h3>Ambientes Exclusivos</h3>
          <div class="areas-diplay-grid">
          <div class="div-area">
            <div class="area-img">
              <img class="img-area" src="images/bangalos.jpg" alt="foto dos bangalo" width="100%" height="auto">
            </div>
            <div class="area-texto">
              <h4>🍹 Bangalôs & Deck</h4>
              <p>Para quem busca relaxar e tomar drinks à beira da piscina em um clima descontraído.</p>
            </div>
          </div>
          <div class="div-area">
            <div class="area-img">
              <img class="img-area" src="images/deck.jpg" alt="foto do deck" width="100%" height="auto">
            </div>
            <div class="area-texto">
              <h4>🍽️ Restaurante Coberto</h4>
              <p>Para quem busca um almoço ou jantar mais reservado, amplo e aconchegante.</p>
            </div>
          </div>
          <div class="div-area">
            <div class="area-img">
              <img class="img-area" src="images/penaareia.jpg" alt="foto da área externa" width="100%" height="auto">
            </div>
            <div class="area-texto">
              <h4>🏖️ Pé na Areia</h4>
              <p>Para curtir a brisa do mar, vista privilegiada e o melhor da praia com a família.</p>
            </div>
          </div>
          </div>
          <div class="container-sobre">
            <h2><strong>Um pouco da nossa História</strong></h2>
            <div class="container-trajetoria primeiro">
            <div class="area-texto">
            <h4>Nossa Trajetória</h4>
            <p>Com mais de 10 anos de tradição, nos consolidamos como um dos destinos mais acolhedores da região. Ao longo dessa década, aprimoramos nossas receitas e nossa estrutura para oferecer o equilíbrio perfeito entre o conforto moderno e a simplicidade da vida praiana.</p>
            </div>
            <div class="img-historia">
            <img  src="images/imagem-informacoes-mobile.jpg" alt="foto do restaurante">
            </div>
            </div>
            <div class="container-trajetoria segundo">
            <div class="img-historia">
            <img  src="images/imagem-trajetoria1.jpg" alt="foto do restaurante">
            </div>
            <div class="area-texto">
            <h4>Ambiente</h4>
            <p>Preservamos a vegetação nativa e o ecossistema local que envolve o nosso espaço, mantendo um ambiente sustentável e integrado à natureza.</p>
            </div>

            </div>
            <div class="container-trajetoria terceiro">
            <div class="area-texto">
            <h4>Lazer</h4>
            <p>Guardamos com carinho o acolhimento caloroso e familiar que faz cada cliente se sentir em casa desde a primeira visita.</p>
            </div>
            <div class="img-historia">
            <img  src="images/imagem-trajetoria2.jpg" alt="foto do restaurante">
            </div>
            </div>
            </div> 
            <div class="container-areas-video">
            <div class="area-texto">
            <h4>Por que Manguezal?</h4>

            </div>
            <div class='areas-video'>
            <div style="padding:100% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/1235482697?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="significadomanguezal.mp4"></iframe></div>
            </div>
            </div> 
            <div class="container d-flex justify-content-center mt-5">
            <a class="btn-saibamais" href="sobre.php">Mais informacoes</a>
          </div>
        
      </div>
    </section>
    <section id="reserva">
    <div id="container-reserva" class="invisivel">
      <h2>OBS: Não cobramos taxas para reservar!</h2>
      <div class="container-card-regras">
        <div class="card-regras">
        <h3>📋 Informações Importantes</h3>
        <p class="subtitulo-regras">Fique atento às nossas políticas de reserva para melhor atendê-lo:</p>

        <ul class="lista-regras">
          <li>
            <span class="icone">⏰</span>
            <div>
              <strong>Tolerância de Atraso</strong>
              <p>Mesa garantida por até 15 minutos após o horário agendado.</p>
            </div>
          </li>
          <li>
            <span class="icone">🐾</span>
            <div>
              <strong>Espaço Pet-Friendly</strong>
              <p>Seus pets são bem-vindos em nossa varanda/área externa.</p>
            </div>
          </li>
          <li>
            <span class="icone">🍾</span>
            <div>
              <strong>Taxa de Rolha</strong>
              <p>Traga seu vinho! Cobramos R$ 50,00 por garrafa aberta.</p>
            </div>
          </li>
          <li>
            <span class="icone">🎂</span>
            <div>
              <strong>Aniversários</strong>
              <p>Sinta-se à vontade para trazer o bolo, não cobramos taxa de serviço.</p>
            </div>
          </li>
          <li>
            <span class="icone">​🍴​​</span>
            <div>
              <strong>Alimentos e Bebidas Externos</strong>
              <p>É estritamente proibido entrar com coolers, isopores, alimentos ou bebidas trazidos de fora.</p>
            </div>
          </li>
          <li>
            <span class="icone">​🏝️​​​</span>
            <div>
              <strong>Beach-Club</strong>
              <p>Cobramos presencialmente uma taxa de R$30,00 por pessoa para utilização da infraestrutura da área restrita.</p>
            </div>
          </li>
        </ul>
      </div>
      </div>
    
      <div class="reserva-container">
        <h3>Reserve sua Mesa</h3>
        <p>Garanta uma experiência inesquecível conosco. Preencha os dados abaixo:</p>

        <form class="form-reserva" action="reserva.php" method="POST" id="formularioReserva">
          <div class="campo">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome" maxlength="100">
            <span id="nome-erro" style = 'color:red;'></span>
          </div>

          <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Digite o seu email" maxlength="100">
            <span id="email-erro" style = 'color:red;'></span>
          </div>

          <div class="campo">
            <label for="telefone">WhatsApp / Telefone</label>
            <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" maxlength="15">
            <span id="telefone-erro" style = 'color:red;'></span>
          </div>

          <div class="linha-dupla">
            <div class="campo">
              <label for="data">Data</label>
              <input type="text" id="data" name="data" placeholder="Insira uma data">
              <span id="data-erro" style = 'color:red;'></span>
            </div>

            <div class="campo">
            <label>Horários disponíveis</label>
            <input type="hidden" id="horario" name="horario" value="">
            <div id="container-horarios">
              <p>Insira uma data primeiro</p>
            </div>
            <span id="horario-erro" style="color:red;"></span>
          </div>

          <div class="campo">
            <label for="pessoas">Número de Pessoas</label>
            <input type="number" name="pessoas" id="pessoas" placeholder="Mínimo de 20 pessoas">
            <span id="pessoas-erro" style = 'color:red;'></span>
          </div>
          <div class="campo campo-beach">
           <label>Reserva para Beach Club?</label>
          <select id="optionBeach" name="optionBeach">
          <option value="nao">Não</option>
          <option value="sim">Sim</option>
          </select>
          <span id="beach-erro" style = 'color:red;'></span>
          </div>
          

          <div class="campo">
            <label for="observacoes">Observações ou Pedidos Especiais</label>
            <textarea id="observacoes" name="observacoes" rows="3" placeholder="Ex: Aniversário, cadeira de bebê, preferências de mesa..."></textarea>
          </div>

          <button type="submit" class="btn-reservar" id="btn-confirmar">Garantir Minha Reserva</button>
        </form>
      </div>
  </div>
  
    </section>
    <section class="depoimentos">
      <div id="container-depoimentos" class="invisivel">
              <h2>O que nossos clientes dizem</h2>
        <p class="subtitulo">A opinião de quem já provou e aprovou nossas delícias!</p>

        <div class="cards-container">
          
          <div class="card-depoimento">
            <div class="estrelas">★★★★★</div>
            <p class="texto">"Comida maravilhosa e ambiente super aconchegante. O atendimento da equipe foi nota 10!"</p>
            <div class="autor">
              <strong>— Ana Paula M.</strong>
              <span>Instagram</span>
            </div>
          </div>

          <div class="card-depoimento">
            <div class="estrelas">★★★★★</div>
            <p class="texto">"O melhor custo-benefício da região. Ambiente confortavel com MPB na trilha sonora, comidas deliciosas, drinks incriveis com frutas da epoca. Com certeza, irei mais vezes."</p>
            <div class="autor">
              <strong>— Rodrigo Lima</strong>
              <span>Instagram</span>
            </div>
          </div>

          <div class="card-depoimento">
            <div class="estrelas">★★★★★</div>
            <p class="texto">"Sobremesas impecáveis e tempero caseiro maravilhoso. Voltarei mais vezes com a família."</p>
            <div class="autor">
              <strong>— Carla Souza</strong>
              <span>Instagram</span>
            </div>
          </div>
        </div>
        <div class="faq-container">
        <div class="faq-header">
          <span class="subtitulo">Tire suas dúvidas</span>
          <h2>Perguntas Frequentes (FAQ) 💬</h2>
        </div>

        <div class="faq-lista">
          
          <div class="faq-item">
            <button class="faq-pergunta">
              <span>Preciso reservar mesa para ir ao restaurante?</span>
              <span class="faq-icone">+</span>
            </button>
            <div class="faq-resposta">
              <p>Para mesas individuais e pequenas famílias, o atendimento é por <strong>ordem de chegada</strong>. As reservas pelo site são exclusivas para <strong>grupos e excursões</strong> acima de X pessoas.</p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-pergunta">
              <span>Qual é o horário do Buffet a Kilo e do À La Carte?</span>
              <span class="faq-icone">+</span>
            </button>
            <div class="faq-resposta">
              <p>O nosso Buffet a Kilo funciona diariamente das <strong>12h00 às 15h00</strong>. Já as opções À La Carte, petiscos e drinks do Beach Club estão disponíveis durante todo o horário de funcionamento.</p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-pergunta">
              <span>O restaurante aceita Pets (Pet Friendly)?</span>
              <span class="faq-icone">+</span>
            </button>
            <div class="faq-resposta">
              <p>Sim! O Manguezal é Pet Friendly em nossas áreas abertas e de deck. Pedimos apenas que mantenha seu pet na guia por segurança e conforto de todos.</p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-pergunta">
              <span>Como funciona a estrutura do Beach Club?</span>
              <span class="faq-icone">+</span>
            </button>
            <div class="faq-resposta">
              <p>A estrutura conta com lounges, acesso à praia, piscina e bar exclusivo de drinks. O consumo de pratos e bebidas dá acesso às instalações do local.</p>
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-pergunta">
              <span>Quais são as formas de pagamento aceitas?</span>
              <span class="faq-icone">+</span>
            </button>
            <div class="faq-resposta">
              <p>Aceitamos Pix, cartões de débito e crédito das principais bandeiras. Não aceitamos cheques.</p>
            </div>
          </div>
        </div>
      </div>
      </div>
  
    </section>
    </main>
    <footer class="site-footer">
  <div class="footer-container">
    
    <div class="footer-coluna">
      <div class="container-footer-logo">
        <a href="https://restaurante-manguezal.alwaysdata.net" class="footer-logo"><img width="100%" height="auto" src="images/logo-restaurante.png" alt="logo"></a>
      <p class="footer-descricao">
        Restaurante & Beach Club à beira-mar. Gastronomia regional, frutos do mar frescos e os melhores drinks tropicais.
      </p>
      <div class="footer-redes">
        <a href="https://instagram.com/restaurantemanguezal" target="_blank" aria-label="Instagram">📸 Instagram</a>
        <a href="https://api.whatsapp.com/send/?phone=557197129517&text&type=phone_number&app_absent=0&utm_source=ig" target="_blank" aria-label="WhatsApp">💬 WhatsApp</a>
      </div>
      </div>
      
    </div>

    <div class="footer-coluna">
      <h4>Navegação</h4>
      <ul class="footer-links">
        <li><a href="https://restaurante-manguezal.alwaysdata.net">Inicio</a></li>
        <li><a href="cardapio.php">Cardápio</a></li>
        <li><a href="sobre.php">Sobre</a></li>
        <li><a href="sobre.php">Beach Club</a></li>
      </ul>
    </div>

    <div class="footer-coluna funcionamento">
      <h4>Funcionamento</h4>
      <p><strong>Restaurante:</strong> Todos os dias, das 09h às 17h.</p>
      <p><strong>Buffet a Kilo:</strong> Diariamente, das 12h às 15h.</p>
      <p><strong>Beach Club:</strong> A partir das 09h.</p>
    </div>

    <div class="footer-coluna">
      <h4>Onde Estamos</h4>
      <p>📍 R. A, 383-343 - Lot. Nova Itaparica</p>
      <p>📞 (71) 9712-9517</p>
      <p class="contato-email">contato.restaurante.manguezal@gmail.com</p>
    </div>

  </div>

  <div class="footer-bottom">
    <p>&copy; 2026 Restaurante Manguezal. Todos os direitos reservados.</p>
  </div>
    </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="js/index/animacoes.js"></script>
  <script src="js/index/formulario.js"></script>
        <script src="https://player.vimeo.com/api/player.js"></script>
        <script>
  document.addEventListener("DOMContentLoaded", function() {
    var iframe = document.querySelector('iframe[title="homevideo.mp4"]');
    var player = new Vimeo.Player(iframe);

    // Força o loop nativo
    player.setLoop(true);

    // Garante que se o vídeo terminar, ele reinicia imediatamente
    player.on('ended', function() {
      player.play();
    });
  });
</script>
</body>

</html>