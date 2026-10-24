<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Manguezal</title>
    <link rel="stylesheet" href="css/cardapio/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Semi+Expanded:wght@100;200;300;400;500;600;700;800;900&family=Fredoka:wght@300..700&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&family=Playwrite+NZ+Basic:wght@100..400&family=Varela+Round&display=swap" rel="stylesheet">
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
    <section id="secao-cardapio" class="cardapio-container">
  <div class="cardapio-header">
    <h2>Nosso Cardápio</h2>
    <p>Comida que tem gosto de férias, praia e bons momentos!</p>
  </div>

  <div class="buffet-banner">
    <div class="buffet-info">
      <span class="tag-horario">12h00 às 15h00</span>
      <h3>Buffet a Kilo</h3>
      <p>Uma variedade incrível de pratos regionais, peixes e saladas frescas.</p>
    </div>
    <div class="buffet-preco">
      <span>R$ 139,90</span>
      <small>/ kg</small>
    </div>
  </div>

  <div class="destaques-grid">
    <div class="card-destaque">
      <div class="card-icone">🥘</div>
      <h3>Moquecas & Frutos do Mar</h3>
      <p>Opções para 2 a 3 pessoas acompanhadas de pirão, arroz e farofa.</p>
      <span class="preco-apartir">A partir de R$ 160,00</span>
    </div>

    <div class="card-destaque">
      <div class="card-icone">🍹</div>
      <h3>Drinks Tropicais</h3>
      <p>Piña Colada, Caipirinhas de frutas da época, Gin Tônica e coquetéis exclusivos.</p>
      <span class="preco-apartir">A partir de R$ 35,00</span>
    </div>

    <div class="card-destaque">
      <div class="card-icone">🍺</div>
      <h3>Cervejas & Bebidas</h3>
      <p>Cervejas 600ml trincando de geladas, opções sem glúten, sucos e refrigerantes.</p>
      <span class="preco-apartir">A partir de R$ 6,00</span>
    </div>
  </div>

  <div class="cardapio-cta">
    <a href="arquivos/cardapio_restaurantemanguezal.pdf" target="_blank" class="btn-pdf">
      📖 Ver Cardápio Completo (PDF)
    </a>
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
  <script src="js/cardapio/script.js"></script>
</body>
</html>